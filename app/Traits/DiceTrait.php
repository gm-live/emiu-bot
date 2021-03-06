<?php

declare (strict_types = 1);

namespace App\Traits;

trait DiceTrait
{

    public function getDiceRedisKey($iChatId)
    {
        return sprintf(config('redisKeys.dice_redis_key'), $iChatId);
    }

	public function handleDiceStart($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';
        if (! in_array($sText, config('game.dice.start_keyword'))) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iChatId  = $aMessage['chat']['id'];

        $oResult = $this->oTgRequest::sendDice([
            'chat_id' => $iChatId,
            'reply_to_message_id' => $iMessageId,
        ]);

        if (! $oResult->ok) {
            $this->oStdLogger->error(json_encode($oResult));
            return;
        } 

        $iDiceValue = $oResult->result->dice['value'];
        $sKey = $this->getDiceRedisKey($iChatId);
        $this->oRedis->setex($sKey, 180, $iDiceValue);
    }

    public function handleDiceResult($aMessage): void
    {        
        if (empty($aMessage['dice']['emoji']) || $aMessage['dice']['emoji'] != '🎲') {
            return;
        }

        $iChatId = $aMessage['chat']['id'];
        $sKey = $this->getDiceRedisKey($iChatId);
        $iDiceValue = $this->oRedis->get($sKey);
        if (! $iDiceValue) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserDiceValue = $aMessage['dice']['value'];

        $sResText = match(true) {
            $iDiceValue > $iUserDiceValue  => '廢物\!',
            $iDiceValue == $iUserDiceValue => '你還是沒贏，快認輸吧\!',
            $iDiceValue < $iUserDiceValue  => '我認輸了\!',
        };

        $this->oTgRequest::sendMessage([
            'text' => $sResText,
            'chat_id' => $iChatId,
            'reply_to_message_id' => $iMessageId,
            'parse_mode' => 'MarkdownV2',
        ]);
        
    }

}