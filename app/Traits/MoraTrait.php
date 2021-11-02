<?php

declare (strict_types = 1);

namespace App\Traits;

trait MoraTrait
{

    protected $aMoraKeyboardConfig = [
        'keyboard' => [
            [
                ['text' => '✌️'],
                ['text' => '👊'],
                ['text' => '🖐'],
            ],
        ],
        'one_time_keyboard' => true,
        'resize_keyboard'   => true,
        'selective'         => true,
    ];

    protected $aSymbols = ['✌️', '👊', '🖐'];

    protected $iRoundCount = 3;

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
            'reply_markup'        => $this->aMoraKeyboardConfig,
        ]);

        if (!$oResult->ok) {
            $this->oStdLogger->error(json_encode($oResult));
            return;
        }

        $sKey = $this->getMoraRedisKey($iChatId, $iUserId);
        $this->oRedis->setex($sKey, 180, $this->iRoundCount);
    }

    public function handleMoraResult($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';

        if (
            empty($sText) ||
            ! in_array($sText, $this->aSymbols)
        ) {
            return;
        }

        $iMessageId = $aMessage['message_id'];
        $iUserId    = $aMessage['from']['id'];
        $iChatId    = $aMessage['chat']['id'];
        
        // 檢查剩幾把拳
        $sKey       = $this->getMoraRedisKey($iChatId, $iUserId);
        $iDelCount  = $this->oRedis->decr($sKey);
        if ($iDelCount < 0) {
            return;
        }

        // 決定要出什麼
        $sBotMora = $this->aSymbols[array_rand($this->aSymbols)];

        // 預設返回
        $aMsgParams = [
            'chat_id'             => $iChatId,
            'text'                => $sBotMora,
            'reply_to_message_id' => $iMessageId,
        ];
        
        if ($iDelCount == 0) {
            // 最後一次要收鍵盤
            $aMsgParams['reply_markup'] = [
                'remove_keyboard' => true,
                'selective'       => true,
            ];
        } else {
            // 每次都打開鍵盤
            $aMsgParams['reply_markup'] = $this->aMoraKeyboardConfig;
        }

        // 出拳
        $this->oTgRequest::sendMessage($aMsgParams);

        // 判定+嘴砲
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
