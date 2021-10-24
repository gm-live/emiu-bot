<?php

declare (strict_types = 1);

namespace App\Traits;

use Longman\TelegramBot\Request;

trait QueryTrait
{
	public function handleQueryChatId($aMessage): void
    {
    	$iChatId  = $aMessage['chat']['id'];
        $sText    = $aMessage['text'] ?? '';
        
        if ($sText == 'chatid') {
            $this->sendMsg($iChatId, $iChatId);            
        }
    }

}