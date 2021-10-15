<?php

declare(strict_types=1);

use Longman\TelegramBot\Entities\Update;

return [

    'token' => env('BOT_TOKEN', ''),

    'username' => env('BOT_USERNAME', ''),

    'web_hook_url' => env('WEBHOOK_URL', '') . '/bot',

    'allow_update_type' => [
        Update::TYPE_MESSAGE,
    ],
];
