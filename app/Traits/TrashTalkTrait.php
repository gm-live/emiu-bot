<?php

declare (strict_types = 1);

namespace App\Traits;

trait TrashTalkTrait
{
    protected $iEmiuUserID = 1382889010;
    protected $iPaiPaiUserID = 1330462756;

    protected $iElevenFloorChatId = -1001301971976;
    // protected $iElevenFloorChatId = -718317972;

	public function beefNoodle($aMessage): void
	{
		$iChatId    = $aMessage['chat']['id'];
        $sText      = $aMessage['text'] ?? '';
        $iMessageId = $aMessage['message_id'];
		if (
			(
                strpos($sText, 'www.ubereats.com') === false &&
    			strpos($sText, 'eats.uber.com') === false
            ) ||
            strpos($sText, '牛肉麵') === false
		) {
            return;
        }

        $sTagString = $this->getTagUserString($this->iEmiuUserID, 'Emiu');
        $sMsg = "{$sTagString} \!  牛肉麵吃起來\!";
        $this->sendMsg($iChatId, $sMsg, $iMessageId);
	}

    public function braisedPorkOnRice($aMessage): void
    {
        $iChatId    = $aMessage['chat']['id'];
        $sText      = $aMessage['text'] ?? '';
        $iMessageId = $aMessage['message_id'];
        if (
            (
                strpos($sText, 'www.ubereats.com') === false &&
                strpos($sText, 'eats.uber.com') === false
            ) ||
            (
                strpos($sText, '魯肉飯') === false &&
                strpos($sText, '小吃') === false &&
                strpos($sText, '肉燥') === false &&
                strpos($sText, '滷肉飯') === false &&
                strpos($sText, '肉燥飯') === false
            )
        ) {
            return;
        }

        $sTagString = $this->getTagUserString($this->iEmiuUserID, 'Emiu');
        $sMsg = "{$sTagString} \!  魯肉飯吃起來\!";
        $this->sendMsg($iChatId, $sMsg, $iMessageId);
    }

	public function paipai($aMessage): void
	{
        $iMessageId = $aMessage['message_id'];
        $iUserId  = $aMessage['from']['id'];
        
        if ($iUserId != $this->iPaiPaiUserID) {
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

        $sTagString = $this->getTagUserString($this->iPaiPaiUserID, '派派哥');
        $sMsg = $sTagString . '！ 甘蔗！';
        $this->sendMsg($iChatId, $sMsg, $iMessageId);
	}

    public function emiuGoOffWork()
    {
        $sTagString = $this->getTagUserString($this->iEmiuUserID, 'Emiu');
        $sMsg = "本 {$sTagString} 要下班了\!";
        $this->sendMsg($this->iElevenFloorChatId, $sMsg);
    }

}