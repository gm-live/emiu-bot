<?php

use Hyperf\Crontab\Crontab;

return [
    // 是否開啟定時任務
    'enable' => env('CRONTAB_ENABLE', false),
];