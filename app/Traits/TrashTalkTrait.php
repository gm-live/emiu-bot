<?php

declare (strict_types = 1);

namespace App\Traits;

trait TrashTalkTrait
{

	public function beefNoodle($aMessage): void
	{
		$iChatId    = $aMessage['chat']['id'];
        $sText      = $aMessage['text'] ?? '';
        $iMessageId = $aMessage['message_id'];
		if (
			strpos($sText, 'www.ubereats.com') === false &&
			strpos($sText, 'eats.uber.com') === false
		) {
            return;
        }

        $sTagString = $this->getTagUserString(self::EMIU_USER_ID, 'Emiu');
        $sMsg = "{$sTagString} \!  牛肉麵吃起來\!";
        $this->sendMsg($iChatId, $sMsg, $iMessageId);
	}

	public function paipai($aMessage): void
	{
        $iMessageId = $aMessage['message_id'];
        $iUserId  = $aMessage['from']['id'];
        
        if ($iUserId != self::PAIPAI_USER_ID) {
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

        $sTagString = $this->getTagUserString(self::PAIPAI_USER_ID, '派派哥');
        $sMsg = $sTagString . '！ 甘蔗！';
        $this->sendMsg($iChatId, $sMsg, $iMessageId);
	}

}