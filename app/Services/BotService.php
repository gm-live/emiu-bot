<?php

declare (strict_types = 1);

namespace App\Services;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

class BotService extends BaseService
{

    public function getTelegram()
    {
        return new Telegram(config('bot.token'), config('bot.username'));
    }

    public function botWebhookSet()
    {
        try {
            
            // Create Telegram API object 
            $oTelegram = $this->getTelegram();

            $sWebhookUrl = config('bot.web_hook_url');

            // Set webhook
            $result = $oTelegram->setWebhook(
                $sWebhookUrl, 
                [
                    'allowed_updates' => config('bot.allow_update_type')
                ]
            );

            if ($result->isOk()) {
                $result->getDescription();
            }

            // set handle
            $oTelegram->handle();

        } catch (TelegramException $e) {
            // echo $e->getMessage();
        }

        $this->oStdLogger->info("bot webhook Done! ($sWebhookUrl)");
    }

    public function handleMsg($aParams)
    {
        $oTelegram = $this->getTelegram();
        Request::initialize($oTelegram);

        $iChatId = $aParams['message']['chat']['id'] ?? null;
        $sText   = $aParams['message']['text'] ?? '';

        if ($sText == 'emiu') {
            Request::sendMessage([
                'chat_id' => $iChatId,
                'text'    => '不要吵！',
            ]);
        }

        if (strtolower($sText) == 'hey emiu') {
            Request::sendMessage([
                'chat_id' => $iChatId,
                'text'    => 'Hi!',
            ]);
        }
    }


    
}
