<?php

declare (strict_types = 1);

namespace App\Controller;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;


class BotController extends AbstractController
{

    public function init()
    {
        $aAllowedUpdateTypes = [
            Update::TYPE_MESSAGE,
        ];

        try {
            
            $sHookUrl = env('WEBHOOK_URL') . '/bot';

            // Create Telegram API object 
            $oTelegram = new Telegram(env('BOT_TOKEN', ''), env('BOT_USERNAME'));

            // Set webhook
            $result = $oTelegram->setWebhook($sHookUrl, ['allowed_updates' => $aAllowedUpdateTypes]);
            if ($result->isOk()) {
                $result->getDescription();
            }

            // set handle
            $oTelegram->handle();

        } catch (TelegramException $e) {
            echo $e->getMessage();
        }

        return 'success';
    }


    public function handleMsg()
    {
        $oTelegram = new Telegram(env('BOT_TOKEN', ''), env('BOT_USERNAME'));

        Request::initialize($oTelegram);

        $aParams = $this->request->all();
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
