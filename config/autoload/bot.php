<?php

declare (strict_types = 1);

return [

    'token'             => env('BOT_TOKEN', ''),

    'username'          => env('BOT_USERNAME', ''),

    'webhook_url'       => env('WEBHOOK_URL', '') . '/bot',

    'webhook_option'    => [
        // 'allow_update_type' => [
        //     Update::TYPE_MESSAGE,
        // ],
    ],

    'admins' => env('BOT_ADMIN_ID', ''),

    'be_tagged_sticker' => 'CAACAgEAAxkBAAIBP2Fu3qFROqPwLSckIJftref8AAGEAQACRwEAAhRZkERyDIjsROmTCCEE',

    // 機器人已啟用的處理訊息方法
    'enable_handlers'   => [

        'saveUserByMsg', // 存用戶資料
        'saveRoomsByMsg', // 存房間資料

        'handleQueryChatId', // 查詢chatID
        'handleInChatRoom', // 入群訊息處理
        'handleOutChatRoom', // 被踢處理
        'handleTagMe', // 被tag時
        'handleDiceStart', // 發起骰子遊戲
        'handleDiceResult', // 骰子遊戲比對結果
        'handleDartStart', // 發起飛鏢遊戲
        'handleDartResult', // 飛鏢遊戲比對結果
        'handleBowlingStart', // bowling start
        'handleBowlingResult', // bowling result
        'handleMoraStart', // 猜拳
        'handleMoraResult', // 猜拳結果結果

        // trash talk
        'beefNoodle',
        'braisedPorkOnRice',
        'paipai',
    ],
];
