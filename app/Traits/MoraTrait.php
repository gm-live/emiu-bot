<?php

declare (strict_types = 1);

namespace App\Traits;

trait MoraTrait
{

    protected $aMoraKeyboardConfig = [
        'keyboard' => [
            [
                ['text' => 'βοΈ'],
                ['text' => 'π'],
                ['text' => 'π'],
            ],
        ],
        'one_time_keyboard' => true,
        'resize_keyboard'   => true,
        'selective'         => true,
    ];

    protected $aSymbols = ['βοΈ', 'π', 'π'];

    protected $iRoundCount = 3;

    public function getMoraRedisKey($iChatId, $iUserId)
    {
        return sprintf(config('redisKeys.mora_redis_key'), $iChatId, $iUserId);
    }

    public function handleMoraStart($aMessage): void
    {
        $sText = $aMessage['text'] ?? '';
        if (!in_array($sText, config('game.mora.start_keyword'))) {
            return;
        }

        $iUserId    = $aMessage['from']['id'];
        $iMessageId = $aMessage['message_id'];
        $iChatId    = $aMessage['chat']['id'];

        $oResult = $this->oTgRequest::sendMessage([
            'chat_id'             => $iChatId,
            'text'                => 'δΎηζ³',
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
        
        // ζͺ’ζ₯ε©εΉΎζζ³
        $sKey       = $this->getMoraRedisKey($iChatId, $iUserId);
        $iDelCount  = $this->oRedis->decr($sKey);
        if ($iDelCount < 0) {
            return;
        }

        // ζ±Ίε?θ¦εΊδ»ιΊΌ
        $sBotMora = $this->aSymbols[array_rand($this->aSymbols)];

        // εΊζ³
        $this->sendMsg($iChatId, $sBotMora, $iMessageId);

        // ε€ε?+ε΄η ²
        $sResText = match(true) {
            $sBotMora == 'π' &&  $sText == 'βοΈ'  => 'ε»’η©\!',
            $sBotMora == 'π' &&  $sText == 'π'  => 'ε»’η©\!',
            $sBotMora == 'βοΈ' &&  $sText == 'π'  => 'ε»’η©\!',
            $sBotMora == $sText                   => 'εΉ³ζ\!',
            $sText == 'π' &&  $sBotMora == 'βοΈ'  => 'Emiu θͺθΌΈδΊ\!',
            $sText == 'π' &&  $sBotMora == 'π'  => 'Emiu θͺθΌΈδΊ\!',
            $sText == 'βοΈ' &&  $sBotMora == 'π'  => 'Emiu θͺθΌΈδΊ\!',
        };

        $aMsgParams = [
            'chat_id'             => $iChatId,
            'text'                => $sResText,
            'reply_to_message_id' => $iMessageId,
            'parse_mode'          => 'MarkdownV2',
        ];

        if ($iDelCount == 0) {
            // ζεΎδΈζ¬‘θ¦ζΆι΅η€
            $aMsgParams['reply_markup'] = [
                'remove_keyboard' => true,
                'selective'       => true,
            ];
        } else {
            // ζ―ζ¬‘ι½ζιι΅η€
            $aMsgParams['reply_markup'] = $this->aMoraKeyboardConfig;
        }

        $this->oTgRequest::sendMessage($aMsgParams);
    }

}
