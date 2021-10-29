<?php

declare (strict_types = 1);

namespace App\BotCommands;

use App\Maker\Interfaces\TelegramInterface;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class ListCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'list';

    /**
     * @var string
     */
    protected $description = '列出所有命令.';

    /**
     * @var string
     */
    protected $usage = '/list';

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
        $iChatId = $oMessage->getChat()->getId();
        
        $sText = "所有命令:\n";
        $oTelegram = app(TelegramInterface::class);
        $aCommands = $oTelegram->getCommandsList();
        foreach ($aCommands as $sCommandName => $oCommand) {
            if ($oCommand->getUsage()) {
                $sText .= '    ' . $oCommand->getUsage() . ' - ' . $oCommand->getDescription() . "\n";
            }
        }

        $data = [
            'chat_id' => $iChatId,
            'text'    => $sText,
        ];

        return Request::sendMessage($data);
    }
}
