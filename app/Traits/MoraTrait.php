<?php

declare (strict_types = 1);

namespace App\Traits;

trait MoraTrait
{
    public function getMoraRedisKey($iChatId, $iUserId)
    {
        return sprintf(config('redisKeys.mora_redis_key'), $iChatId, $iUserId);
    }

    public function handleMoraStart($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';
        if (!in_array($sText, config('game.mora'))) {
            return;
        }

        $iUserId    = $aMessage['from']['id'];
        $iMessageId = $aMessage['message_id'];
        $iChatId    = $aMessage['chat']['id'];

        $oResult = $this->oTgRequest::sendMessage([
            'chat_id'             => $iChatId,
            'text'                => '來猜拳',
            'reply_to_message_id' => $iMessageId,
            'reply_markup'        => [
                'keyboard'          => [
                    [
                        ['text' => '✌️'],
                        ['text' => '👊'],
                        ['text' => '🖐'],
                    ],
                ],
                'resize_keyboard'   => true,
                'selective'         => true,
            ],
        ]);

        if (!$oResult->ok) {
            $this->oStdLogger->error(json_encode($oResult));
            return;
        }

        $sKey = $this->getMoraRedisKey($iChatId, $iUserId);
        $this->oRedis->setex($sKey, 180, 3);
    }

    public function handleMoraResult($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';

        if (
            empty($sText) ||
            (
                $sText != '✌️' &&
                $sText != '👊' &&
                $sText != '🖐'
            )

        ) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserId    = $aMessage['from']['id'];
        $iChatId    = $aMessage['chat']['id'];
        $sKey       = $this->getMoraRedisKey($iChatId, $iUserId);
        $iDelCount  = $this->oRedis->decr($sKey);
        if ($iDelCount < 0) {
            return;
        }

        $aMoraRange = ['✌️', '👊', '🖐'];
        $iRand      = rand(0, 2);
        $sBotMora   = $aMoraRange[$iRand];
        $aMsgParams = [
            'chat_id'             => $iChatId,
            'text'                => $sBotMora,
            'reply_to_message_id' => $iMessageId,
        ];
        if ($iDelCount == 0) {
            $aMsgParams['reply_markup'] = [
                'remove_keyboard' => true,
                'selective'       => true,
            ];
        }
        $this->oTgRequest::sendMessage($aMsgParams);

        $sResText = match(true) {
            $sBotMora == '👊' &&  $sText == '✌️'  => '廢物\!',
            $sBotMora == '🖐' &&  $sText == '👊'  => '廢物\!',
            $sBotMora == '✌️' &&  $sText == '🖐'  => '廢物\!',
            $sBotMora == $sText                   => '平手\!',
            $sText == '👊' &&  $sBotMora == '✌️'  => 'Emiu 認輸了\!',
            $sText == '🖐' &&  $sBotMora == '👊'  => 'Emiu 認輸了\!',
            $sText == '✌️' &&  $sBotMora == '🖐'  => 'Emiu 認輸了\!',
        };
        $this->sendMsg($iChatId, $sResText, $iMessageId);
    }

}
