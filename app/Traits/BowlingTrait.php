<?php

declare (strict_types = 1);

namespace App\Traits;

trait BowlingTrait
{
    public function getBowlingRedisKey($iChatId)
    {
        return sprintf(config('redisKeys.bowling_redis_key'), $iChatId);
    }

    public function handleBowlingStart($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';
        if (! in_array($sText, config('game.bowling'))) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iChatId  = $aMessage['chat']['id'];

        $oResult = $this->oTgRequest::sendDice([
            'chat_id' => $iChatId,
            'reply_to_message_id' => $iMessageId,
            'emoji' => '🎳',
        ]);

        if (! $oResult->ok) {
            $this->oStdLogger->error(json_encode($oResult));
            return;
        } 

        $iBotScore = $oResult->result->dice['value'];
        $sKey = $this->getBowlingRedisKey($iChatId);
        $this->oRedis->setex($sKey, 180, $iBotScore);
    }

    public function handleBowlingResult($aMessage): void
    {        
        if (empty($aMessage['dice']['emoji']) || $aMessage['dice']['emoji'] != '🎳') {
            return;
        }

        $iChatId = $aMessage['chat']['id'];
        $sKey = $this->getBowlingRedisKey($iChatId);
        $iBotScore = $this->oRedis->get($sKey);
        if (! $iBotScore) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserBowlingValue = $aMessage['dice']['value'];

        $sResText = match(true) {
            $iBotScore > $iUserBowlingValue  => '廢物\!',
            $iBotScore == $iUserBowlingValue => '你還是沒贏，快認輸吧\!',
            $iBotScore < $iUserBowlingValue  => '我認輸了\!',
        };
        $this->sendMsg($iChatId, $sResText, $iMessageId);
    }

}