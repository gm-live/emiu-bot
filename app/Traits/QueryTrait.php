<?php

declare (strict_types = 1);

namespace App\Traits;

use Longman\TelegramBot\Request;

trait QueryTrait
{
	public function handleQueryChatId($iChatId, $sText): void
    {
        if ($sText == 'chatid') {
            Request::sendMessage([
                'chat_id' => $iChatId,
                'text'    => $iChatId,
            ]);
        }
    }

}