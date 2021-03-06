<?php

declare (strict_types = 1);

namespace App\Traits;

trait DartTrait
{

    public function getDartRedisKey($iChatId)
    {
        return sprintf(config('redisKeys.dart_redis_key'), $iChatId);
    }

	public function handleDartStart($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';
        if (! in_array($sText, config('game.dart.start_keyword'))) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iChatId  = $aMessage['chat']['id'];

        $oResult = $this->oTgRequest::sendDice([
            'chat_id' => $iChatId,
            'reply_to_message_id' => $iMessageId,
            'emoji' => '🎯',
        ]);

        if (! $oResult->ok) {
            $this->oStdLogger->error(json_encode($oResult));
            return;
        } 

        $iDartValue = $oResult->result->dice['value'];
        $sKey = $this->getDartRedisKey($iChatId);
        $this->oRedis->setex($sKey, 180, $iDartValue);
    }

    public function handleDartResult($aMessage): void
    {        
        if (empty($aMessage['dice']['emoji']) || $aMessage['dice']['emoji'] != '🎯') {
            return;
        }

        $iChatId = $aMessage['chat']['id'];
        $sKey = $this->getDartRedisKey($iChatId);
        $iDartValue = $this->oRedis->get($sKey);
        if (! $iDartValue) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserDartValue = $aMessage['dice']['value'];

        $sResText = match(true) {
            $iDartValue > $iUserDartValue  => '廢物\! 射都射不准\!',
            $iDartValue == $iUserDartValue => '你還是沒贏，快認輸吧\!',
            $iDartValue < $iUserDartValue  => '我認輸\!',
        };

        $this->oTgRequest::sendMessage([
            'text' => $sResText,
            'chat_id' => $iChatId,
            'reply_to_message_id' => $iMessageId,
            'parse_mode' => 'MarkdownV2',
        ]);
        
    }

}