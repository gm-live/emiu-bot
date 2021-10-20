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
    use \App\Traits\DiceTrait;
    use \App\Traits\InOutChatRoomTrait;
    use \App\Traits\TrashTalkTrait;
    use \App\Traits\QueryTrait;

    const EMIU_USER_ID = 1382889010;
    const PAIPAI_USER_ID = 1330462756;

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

    public function getTagUserString($iUserId, $sTagString)
    {
        return "[{$sTagString}](tg://user?id={$iUserId})";
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

    public function setBotWebhook()
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

        } catch (TelegramException $e) {}

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
        $this->sendSticker($iChatId, config('bot.be_tagged_sticker'));
    }

}
