<?php

declare (strict_types = 1);

namespace App\BotCommands;

use App\Maker\Interfaces\TelegramInterface;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Services\BotService;

class ChatRoomsCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'chatrooms';

    /**
     * @var string
     */
    protected $description = '所有所在群組.';

    /**
     * @var string
     */
    protected $usage = '/chatrooms';

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

        $oService = app(BotService::class);
        $aActiveRooms = $oService->getActiveRooms();

        $sText = '';
        $aAdminUserIds = $oService->getBotAdmins();
        if (in_array($iUserId, $aAdminUserIds)) {
            $sText .= "所有房間:\n";
            foreach ($aActiveRooms as $oRoom) {
                $sText .= $oRoom->chat_title . ' (' . $oRoom->chat_id . ") \n";
            }
            
        } else {
            $sText = '403';
        }

        $data = [
            'chat_id' => $iChatId,
            'text'    => $sText,
            'reply_to_message_id' => $iMsgId,
        ];

        return Request::sendMessage($data);
    }
}
