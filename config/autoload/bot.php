<?php

declare (strict_types = 1);

use Longman\TelegramBot\Entities\Update;

return [

    'token'          => env('BOT_TOKEN', ''),

    'username'       => env('BOT_USERNAME', ''),

    'webhook_url'   => env('WEBHOOK_URL', '') . '/bot',

    'webhook_option' => [
        // 'allow_update_type' => [
        //     Update::TYPE_MESSAGE,
        // ],
    ],

    'raw_messages_redis_key' => 'raw_messages',

];
