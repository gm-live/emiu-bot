<?php

declare (strict_types = 1);

namespace App\BotCommands;

use App\Maker\Interfaces\TelegramInterface;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ChatIdCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'chatID';

    /**
     * @var string
     */
    protected $description = '取得聊天室ID.';

    /**
     * @var string
     */
    protected $usage = '/chatid';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $oMessage = $this->getMessage();
        $iMsgId = $oMessage->getMessageId();
        $iChatId = $oMessage->getChat()->getId();

        $data = [
            'chat_id' => $iChatId,
            'text'    => $iChatId,
            'reply_to_message_id' => $iMsgId,
        ];

        return Request::sendMessage($data);
    }
}
