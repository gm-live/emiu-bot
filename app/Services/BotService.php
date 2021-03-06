<?php

declare (strict_types = 1);

namespace App\Services;

use App\Repositories\RoomRepo;
use App\Repositories\UserRepo;
use Hyperf\Di\Annotation\Inject;
use Longman\TelegramBot\Entities\Update;
use Hyperf\Guzzle\ClientFactory;

class BotService extends BaseService
{
    use \App\Traits\InOutChatRoomTrait;
    use \App\Traits\TrashTalkTrait;
    use \App\Traits\NormalTalkTrait;
    use \App\Traits\QueryTrait;
    use \App\Traits\DiceTrait;
    use \App\Traits\DartTrait;
    use \App\Traits\BowlingTrait;
    use \App\Traits\MoraTrait;
    use \App\Traits\UltimatePwdTrait;

    /**
     * @Inject
     * @var UserRepo
     */
    protected $oUserRepo;

    /**
     * @Inject
     * @var RoomRepo
     */
    protected $oRoomRepo;

    /**
     * @Inject
     * @var ClientFactory
     */
    protected $oClientFactory;

    public function getTagUserString($iUserId, $sTagString)
    {
        return "[{$sTagString}](tg://user?id={$iUserId})";
    }

    public function sendMsg($iChatId, $sMsg, $iReplyMsgId = null)
    {
        $aParams = [
            'chat_id'    => $iChatId,
            'text'       => $sMsg,
            'parse_mode' => 'MarkdownV2',
        ];

        if ($iReplyMsgId) {
            $aParams['reply_to_message_id'] = $iReplyMsgId;
        }

        $this->oTgRequest::sendMessage($aParams);
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

        $this->oTgRequest::sendSticker($aParams);
    }

    public function setBotWebhook()
    {
        $sWebhookUrl = config('bot.webhook_url');

        // Set webhook
        $this->oTelegram->setWebhook(
            $sWebhookUrl,
            config('bot.webhook_option')
        );

        $iBotId = $this->getBotId();
        $this->oStdLogger->info("bot webhook Done! ($sWebhookUrl)");
        $this->oStdLogger->info("bot id:$iBotId");
    }

    public function handleMsg($aParams)
    {
        // save raw msg
        $this->saveRawMsg($aParams);

        // command
        $this->handleCommand($aParams);

        // enable handler
        $aMessage        = $aParams['message'] ?? [];
        $aEnableHandlers = config('bot.enable_handlers');
        foreach ($aEnableHandlers as $sHandle) {
            go(function () use ($sHandle, $aMessage) {
                $this->$sHandle($aMessage);
            });
        }
    }

    public function handleCommand($aParams)
    {
        $oUpdate = new Update($aParams, config('bot.username'));
        $this->oTelegram->processUpdate($oUpdate);
    }

    public function handleTagMe($aMessage): void
    {
        $iChatId = $aMessage['chat']['id'];
        $sText   = $aMessage['text'] ?? '';
        $sTypeString = $aMessage['entities'][0]['type'] ?? ''; 
        $bTagged = 'mention' == $sTypeString;
        if (! $bTagged) {
            return;
        }
        if (strpos($sText, '@' . config('bot.username')) === false) {
            return;
        }

        $this->sendSticker($iChatId, config('bot.be_tagged_sticker'));
    }

    public function saveRawMsg($aRequestParams)
    {
        $aRequestParams = json_encode($aRequestParams, JSON_UNESCAPED_UNICODE);
        $sKey           = config('redisKeys.raw_messages_redis_key');
        $this->oRedis->lpush($sKey, $aRequestParams);
    }

    public function saveUserByMsg($aMessage)
    {
        $iUserId = $aMessage['from']['id'];

        $bCheck = $this->oUserRepo->checkUserExist($iUserId);
        $sKey   = config('redisKeys.user_repeat_check');
        if ($bCheck) {
            $this->oRedis->hset($sKey, (string) $iUserId, 1);
            return;
        }

        $sFirstName = $aMessage['from']['first_name'];
        $sLastName  = $aMessage['from']['last_name'] ?? '';
        $sUsername  = $aMessage['from']['username'] ?? null;
        $sLang      = $aMessage['from']['language_code'] ?? 'zh-hant';
        $this->oUserRepo->create(
            $iUserId,
            $sFirstName,
            $sLastName,
            $sUsername,
            $sLang
        );
        $this->oRedis->hset($sKey, (string) $iUserId, 1);
    }

    public function saveRoomsByMsg($aMessage)
    {
        $iChatId = $aMessage['chat']['id'];
        if ($iChatId > 0) {
            return;
        }

        $bCheck = $this->oRoomRepo->checkRoomExist($iChatId);
        $sKey   = config('redisKeys.room_repeat_check');
        if ($bCheck) {
            $this->oRedis->hset($sKey, (string) $iChatId, 1);
            return;
        }

        $sChatName = $aMessage['chat']['title'] ?? '';
        $this->oRoomRepo->create($iChatId, $sChatName);
        $this->oRedis->hset($sKey, (string) $iChatId, 1);
    }


    public function getActiveRooms()
    {
        return $this->oRoomRepo->getAcvtiveRooms();
    }

}
