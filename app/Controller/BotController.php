<?php

declare (strict_types = 1);

namespace App\Controller;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use Hyperf\Di\Annotation\Inject;
use App\Services\BotService;


class BotController extends AbstractController
{
    /**
     * @Inject
     * @var BotService
     */
    protected $oBotService;

    public function handleMsg()
    {
        $aParams = $this->request->all();
        $this->oBotService->handleMsg($aParams);
    }

    public function sendMsgToRoom()
    {
        $iChatId = $this->request->input('chat_id', -718317972);
        $sMsg = $this->request->input('msg', '');
        $this->oBotService->sendMsg($iChatId, $sMsg);
    }
}
