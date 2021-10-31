<?php

declare (strict_types = 1);

namespace App\BotCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Services\BotService;

class HelpCommand extends UserCommand
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
    protected $usage = '/help';

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
        
        // 命令類
        $sText = "命令:\n";
        $oTelegram = $this->getTelegram();
        $aCommands = $oTelegram->getCommandsList();
        foreach ($aCommands as $sCommandName => $oCommand) {
            if ($oCommand->getUsage()) {
                $sText .= '    ' . $oCommand->getUsage() . ' - ' . $oCommand->getDescription() . "\n";
            }
        }

        // 遊戲類
        $sText .= "遊戲:\n";
        $sText .= "    🎲 - " . join(',', config('game.dice')) . "\n";
        $sText .= "    🎯 - " . join(',', config('game.dart')) . "\n";
        $sText .= "    🎳 - " . join(',', config('game.bowling')) . "\n";

        $data = [
            'chat_id' => $iChatId,
            'text'    => $sText,
        ];

        return Request::sendMessage($data);
    }
}
