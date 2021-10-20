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

    'be_tagged_sticker' => 'CAACAgEAAxkBAAIBP2Fu3qFROqPwLSckIJftref8AAGEAQACRwEAAhRZkERyDIjsROmTCCEE',


    // 機器人已啟用的處理訊息方法
    'enable_handlers' => [     
        'handleQueryChatId', // 查詢chatID
        'handleInChatRoom', // 入群訊息處理
        'handleOutChatRoom', // 被踢處理
        'handleTagMe', // 被tag時
        'handleDiceStart', // dice game start
        'handleDiceResult', // dice game result
    ],
];
