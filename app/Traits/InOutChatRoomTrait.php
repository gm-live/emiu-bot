<?php

declare (strict_types = 1);

namespace App\Traits;

use Longman\TelegramBot\Request;

trait InOutChatRoomTrait
{
    protected $sKickerMsg = '踢屁\!渣男\!';

	public function handleInChatRoom($aMessage): void
    {
        $aNewMembers = $aMessage['new_chat_members'] ?? [];
        if (empty($aNewMembers)) {
            return;
        }

        $iChatId = $aMessage['chat']['id'];
        $iBotId  = $this->getBotId();
        foreach ($aNewMembers as $aUser) {
            if ($iBotId == $aUser['id']) {
                continue;
            }

            $sTagUser = $this->getTagUserString($aUser['id'], $aUser['first_name']);
            $this->sendMsg($iChatId, "歡迎 {$sTagUser} \!\!");
        }
    }

    public function handleOutChatRoom($aMessage): void
    {
        if (empty($aMessage['left_chat_participant'])) {
            return;
        }

        $iBotId      = $this->getBotId();
        $iLeftUserId = $aMessage['left_chat_participant']['id'];
        if ($iLeftUserId != $iBotId) {
            return;
        }

        $iKickerUserId = $aMessage['from']['id'];
        $this->sendMsg($iKickerUserId, $this->sKickerMsg);
    }

}