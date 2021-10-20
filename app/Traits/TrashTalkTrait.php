<?php

declare (strict_types = 1);

namespace App\Traits;

use Longman\TelegramBot\Request;

trait TrashTalkTrait
{
	public $sPaipaiUserId = 1330462756;

	public function beefNoodle($aMessage): void
	{
		$iChatId = $aMessage['chat']['id'];
        $sText   = $aMessage['text'] ?? '';
		if (
			strpos($sText, 'www.ubereats.com') === false &&
			strpos($sText, 'eats.uber.com') === false
		) {
            return;
        }

        $sTagString = $this->getTagUserString(self::EMIU_USER_ID, 'Emiu');
        $sMsg = "{$sTagString} \!  牛肉麵吃起來\!";
        $this->sendMsg($iChatId, $sMsg);
	}

	public function paipai($aMessage): void
	{
        $iMessageId = $aMessage['message_id'];
        $iUserId  = $aMessage['from']['id'];

        if ($iUserId != $this->sPaipaiUserId) {
        	return;
        }

		$iChatId = $aMessage['chat']['id'];
        $sText   = $aMessage['text'] ?? '';

        if (strpos($sText, '@' . config('bot.username')) === false) {
            return;
        }

        $this->sendSticker(
        	$iChatId, 
        	'CAACAgUAAxkBAAICGWFv9Z-duPPt6P4wEx50avKwH5L4AAICAAORWGIqT2SuySGu6v8hBA',
            $iMessageId,
        );
	}

}