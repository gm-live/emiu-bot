<?php
declare (strict_types = 1);

use Hyperf\Utils\ApplicationContext;
use Hyperf\Server\ServerFactory;

if (! function_exists('container')) {
    function container()
    {
        return ApplicationContext::getContainer();
    }
}

if (! function_exists('server')) {
    function server()
    {
        return container()->get(ServerFactory::class)->getServer()->getServer();
    }
}

if (! function_exists('app')) {
    function app($sClass)
    {
        return container()->get($sClass);
    }
}
