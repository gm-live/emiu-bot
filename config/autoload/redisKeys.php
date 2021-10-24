<?php

declare (strict_types = 1);

return [

    // 檢查用戶是否重複紀錄
    'user_repeat_check' => 'user_repeat_check',

    // 檢查群組是否重新被紀錄
    'room_repeat_check' => 'room_repeat_check',

    // 原始訊息
    'raw_messages_redis_key' => 'raw_messages',

    // 骰子遊戲 點數暫存
    'dice_redis_key' => 'dice_game:%s',

    // 飛鏢遊戲
    'dart_redis_key' => 'dart_game:%s',

];
