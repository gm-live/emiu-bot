<?php

declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;

class BaseRepo
{
	/**
     * @Inject
     * @var Redis
     */
    protected $oRedis;
}