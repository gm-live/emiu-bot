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
    use \App\Traits\InOutChatRoomTrait;
    use \App\Traits\TrashTalkTrait;
    use \App\Traits\QueryTrait;
    use \App\Traits\DiceTrait;
    use \App\Traits\DartTrait;

    const EMIU_USER_ID   = 1382889010;

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

    public function sendSticker($iChatId, $sStickerFileId, $iReplyMsgId = null)
    {
        $aParams = [
            'chat_id' => $iChatId,
            'sticker' => $sStickerFileId,
        ];

        if ($iReplyMsgId) {
            $aParams['reply_to_message_id'] = $iReplyMsgId;
        }
        
        Request::sendSticker($aParams);
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
        $sKey = config('redisKeys.raw_messages_redis_key');
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
        $aMessage = $aParams['message'] ?? [];
        $aEnableHandlers = config('bot.enable_handlers');
        foreach ($aEnableHandlers as $sHandle) {
            $this->$sHandle($aMessage);
        }
    }

    public function handleTagMe($aMessage): void
    {
        $iChatId = $aMessage['chat']['id'];
        $sText   = $aMessage['text'] ?? '';
        if (strpos($sText, '@' . config('bot.username')) === false) {
            return;
        }
        $this->sendSticker($iChatId, config('bot.be_tagged_sticker'));
    }

}
