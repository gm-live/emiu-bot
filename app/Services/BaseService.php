<?php

declare (strict_types = 1);

namespace App\Services;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;

class BaseService 
{
	/**
     * @Inject
     * @var StdoutLoggerInterface
     */
    protected $oStdLogger;

}