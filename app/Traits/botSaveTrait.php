<?php

declare (strict_types = 1);

namespace App\Traits;

trait botSaveTrait
{
	public function saveRawMsg($aMessage)
    {
        $aMessage = json_encode($aMessage, JSON_UNESCAPED_UNICODE);
        $sKey = config('redisKeys.raw_messages_redis_key');
        $this->oRedis->lpush($sKey, $aMessage);
    }

    public function saveUserByMsg($aMessage)
    {
        $sKey = config('redisKeys.users');
        $iUserId = $aMessage['from']['id'];
        $sUsername = $aMessage['from']['first_name'];
        $this->oRedis->hset($sKey, (string)$iUserId, $sUsername);
    }

    public function saveRoomsByMsg($aMessage)
    {
        $sKey = config('redisKeys.rooms');
        $iChatId = $aMessage['chat']['id'];
        $sChatName = $aMessage['chat']['title'] ?? '';
        if ($sChatName) {
            $this->oRedis->hset($sKey, (string)$iChatId, $sChatName);
        }
    }
}