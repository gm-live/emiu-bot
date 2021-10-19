<?php

declare (strict_types = 1);

use Hyperf\HttpServer\Router\Router;


Router::post('/bot', 'App\Controller\BotController@handleMsg');
Router::get('/send-msg-to-room', 'App\Controller\BotController@sendMsgToRoom');
