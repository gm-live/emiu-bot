<?php

declare (strict_types = 1);

namespace App\Services;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Longman\TelegramBot\Request;
use App\Maker\Interfaces\TelegramInterface;

class BaseService 
{
	/**
     * @Inject
     * @var StdoutLoggerInterface
     */
    protected $oStdLogger;

    /**
     * @Inject
     * @var Redis
     */
    protected $oRedis;

    /**
     * @Inject
     * @var TelegramInterface
     */
    protected $oTelegram;

    /**
     * @Inject
     * @var Request
     */
    protected $oTgRequest;

    public function __construct()
    {
        $this->oTgRequest::initialize($this->oTelegram);
    }

    public function getBotId()
    {
        $sToken = config('bot.token');
        return explode(':', $sToken)[0] ?? '';
    }

    public function getBotAdmins()
    {
        $sAdmins = config('bot.admins', '');
        return array_filter(explode(',',$sAdmins)) ?? [];
    }

}