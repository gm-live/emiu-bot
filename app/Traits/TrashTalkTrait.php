<?php

declare (strict_types = 1);

namespace App\Traits;

use Longman\TelegramBot\Request;

trait TrashTalkTrait
{

	public function beefNoodle($aMessage): void
	{
		$iChatId = $aMessage['chat']['id'];
        $sText   = $aMessage['text'] ?? '';
		if (strpos($sText, 'www.ubereats.com') === false) {
            return;
        }

        $sTagString = $this->getTagUserString(self::EMIU_USER_ID, 'Emiu');
        $sMsg = "{$sTagString} \!  牛肉麵吃起來\!";
        $this->sendMsg($iChatId, $sMsg);
	}

}