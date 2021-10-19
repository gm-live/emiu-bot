<?php

declare (strict_types = 1);

namespace App\Services;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class BotService extends BaseService
{
    const EMIU_USER_ID = 1382889010;
    const PAIPAI_USER_ID = 1330462756;

    // 被tag時 發的貼圖
    const STICKER_IN_TAG = "CAACAgEAAxkBAAIBP2Fu3qFROqPwLSckIJftref8AAGEAQACRwEAAhRZkERyDIjsROmTCCEE";

    // 賭局開始
    const BET_BEGIN = '賭骰子';

    /**
     * @Inject
     * @var Redis
     */
    protected $oRedis;

    protected $oTelegram = null;

    public function __construct()
    {
        $this->oTelegram = new Telegram(config('bot.token'), config('bot.username'));
        Request::initialize($this->oTelegram);
    }

    public function getDiceRedisKey($iChatId)
    {
        return sprintf(config('bot.dice_redis_key'), $iChatId);
    }

    public function getTagUserString($iUserId, $sTagString)
    {
        return "[{$sTagString}](tg://user?id={$iUserId})";
    }

    public function tagUser($iChatId, $iUserId, $sTagString)
    {
        Request::sendMessage([
            'chat_id'    => $iChatId,
            'text'       => $this->getTagUserString($iUserId, $sTagString),
            'parse_mode' => 'MarkdownV2',
        ]);
    }

    public function sendMsg($iChatId, $sMsg)
    {
        Request::sendMessage([
            'chat_id'    => $iChatId,
            'text'       => $sMsg,
            'parse_mode' => 'MarkdownV2',
        ]);
    }

    public function sendSticker($iChatId, $sStickerFileId)
    {
        Request::sendSticker([
            'chat_id' => $iChatId,
            'sticker' => $sStickerFileId,
        ]);
    }

    public function getBotId()
    {
        $sToken = config('bot.token');
        return explode(':', $sToken)[0] ?? '';
    }

    public function logRawMsg($mMsg)
    {
        if (!is_string($mMsg)) {
            $mMsg = json_encode($mMsg, JSON_UNESCAPED_UNICODE);
        }
        $sKey = config('bot.raw_messages_redis_key');
        $this->oRedis->lpush($sKey, $mMsg);
    }

    public function botWebhookSet()
    {
        try {

            $sWebhookUrl = config('bot.webhook_url');

            // Set webhook
            $result = $this->oTelegram->setWebhook(
                $sWebhookUrl,
                config('bot.webhook_option')
            );

            if ($result->isOk()) {
                $result->getDescription();
            }

            // set handle
            $this->oTelegram->handle();

        } catch (TelegramException $e) {
            // echo $e->getMessage();
        }

        $iBotId = $this->getBotId();
        $this->oStdLogger->info("bot webhook Done! ($sWebhookUrl)");
        $this->oStdLogger->info("bot id:$iBotId");
    }

    public function handleMsg($aParams)
    {
        // 紀錄 raw msg
        $this->logRawMsg($aParams);

        $iBotId   = $this->getBotId();
        $aMessage = $aParams['message'] ?? [];
        $iChatId  = $aMessage['chat']['id'];
        $sText    = $aMessage['text'] ?? '';

        // 查詢chatID
        $this->handleQueryChatId($iChatId, $sText);

        // 入群訊息處理
        $this->handleInChatRoom($aMessage);

        // 被踢處理
        $this->handleOutChatRoom($aMessage);

        // 被tag時
        $this->handleTagMe($iChatId, $sText);

        // dice game start
        $this->handleDiceStart($aMessage);

        // dice game result
        $this->handleDiceResult($aMessage);

    }

    public function handleTagMe($iChatId, $sText): void
    {
        if (strpos($sText, '@' . config('bot.username')) === false) {
            return;
        }

        $this->sendSticker($iChatId, self::STICKER_IN_TAG);
    }

    public function handleInChatRoom($aMessage): void
    {
        $aNewMembers = $aMessage['new_chat_members'] ?? [];
        if (empty($aNewMembers)) {
            return;
        }

        $iChatId = $aMessage['chat']['id'];
        $iBotId  = $this->getBotId();
        foreach ($aNewMembers as $aUser) {
            if ($iBotId == $aUser['id']) {
                continue;
            }

            $sTagUser = $this->getTagUserString($aUser['id'], $aUser['first_name']);
            $this->sendMsg($iChatId, "歡迎 {$sTagUser} \!\!");
        }
    }

    public function handleOutChatRoom($aMessage): void
    {
        if (empty($aMessage['left_chat_participant'])) {
            return;
        }

        $iBotId      = $this->getBotId();
        $iLeftUserId = $aMessage['left_chat_participant']['id'];
        if ($iLeftUserId != $iBotId) {
            return;
        }

        $iKickerUserId = $aMessage['from']['id'];
        $this->sendMsg($iKickerUserId, '踢屁！');
    }

    public function handleQueryChatId($iChatId, $sText): void
    {
        if ($sText == 'chatid') {
            Request::sendMessage([
                'chat_id' => $iChatId,
                'text'    => $iChatId,
            ]);
        }
    }

    public function handleDiceStart($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';
        if ($sText != self::BET_BEGIN) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserId  = $aMessage['from']['id'];
        $iChatId  = $aMessage['chat']['id'];

        $oResult = Request::sendDice([
            'chat_id' => $iChatId,
            'reply_to_message_id' => $iMessageId,
        ]);

        if (! $oResult->ok) {
            $this->oStdLogger->error(json_encode($oResult));
            return;
        } 

        $iDiceValue = $oResult->result->dice['value'];
        $sKey = $this->getDiceRedisKey($iChatId);
        $this->oRedis->setex($sKey, 180, $iDiceValue);
    }

    public function handleDiceResult($aMessage): void
    {        
        if (empty($aMessage['dice'])) {
            return;
        }

        $iChatId = $aMessage['chat']['id'];
        $sKey = $this->getDiceRedisKey($iChatId);
        $iDiceValue = $this->oRedis->get($sKey);
        if (! $iDiceValue) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserId  = $aMessage['from']['id'];
        $iUserDiceValue = $aMessage['dice']['value'];

        $sResText = match(true) {
            $iDiceValue > $iUserDiceValue  => '廢物!',
            $iDiceValue == $iUserDiceValue => '你沒贏別囂張',
            $iDiceValue < $iUserDiceValue  => '你只是贏了Emiu',
        };

        Request::sendMessage([
            'text' => $sResText,
            'chat_id' => $iChatId,
            'reply_to_message_id' => $iMessageId,
        ]);
        
    }


}
