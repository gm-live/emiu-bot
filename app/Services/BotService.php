<?php

declare (strict_types = 1);

namespace App\Services;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

class BotService 
{
    public function getTelegram()
    {
        return new Telegram(config('bot.token'), config('bot.username'));
    }

    public function init()
    {
        try {
            
            // Create Telegram API object 
            $oTelegram = $this->getTelegram();

            // Set webhook
            $result = $oTelegram->setWebhook(
                config('bot.web_hook_url'), 
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
