<?php

declare (strict_types = 1);

namespace App\BotCommands;

use App\Maker\Interfaces\TelegramInterface;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class UserIdCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'UserID';

    /**
     * @var string
     */
    protected $description = '取得使用者ID.';

    /**
     * @var string
     */
    protected $usage = '/userid';

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
        $iUserId = $oMessage->getFrom()->getId();

        $data = [
            'chat_id' => $iChatId,
            'text'    => $iUserId,
            'reply_to_message_id' => $iMsgId,
        ];

        return Request::sendMessage($data);
    }
}
