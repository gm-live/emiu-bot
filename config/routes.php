<?php

declare (strict_types = 1);

use Hyperf\HttpServer\Router\Router;


Router::get('/init', 'App\Controller\BotController@init');
Router::post('/bot', 'App\Controller\BotController@handleMsg');
