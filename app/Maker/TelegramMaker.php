<?php

declare (strict_types = 1);

namespace App\Maker;

use Longman\TelegramBot\Telegram;
use Psr\Container\ContainerInterface;

class TelegramMaker
{
    public function __invoke(ContainerInterface $oContainer)
    {        
        $oTelegram = make(Telegram::class, [
        	'api_key' => config('bot.token'), 
        	'bot_username' => config('bot.username')
        ]);

        $oTelegram->addCommandsPath(APP_PATH . '/BotCommands', false);

        return $oTelegram;
    }

}