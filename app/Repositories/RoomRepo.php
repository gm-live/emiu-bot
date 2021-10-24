<?php

declare (strict_types = 1);

namespace App\Repositories;

use App\Model\Room;
use Hyperf\Di\Annotation\Inject;

class RoomRepo extends BaseRepo
{
    /**
     * @Inject
     * @var Room
     */
    protected $oRoom;

    public function findById($iId)
    {
        return $this->oRoom->find($iId);
    }

    public function create($iChatId, $sChatTitle)
    {
        $oRoom             = new $this->oRoom();
        $oRoom->chat_id    = $iChatId;
        $oRoom->chat_title = $sChatTitle;
        $oRoom->saveOrFail();
    }

    public function checkRoomExist($iChatId)
    {
        $sKey    = config('redisKeys.room_repeat_check');
        $iResult = $this->oRedis->hget($sKey, (string) $iChatId);
        if ($iResult) {
            return true;
        }

        $oRoom = $this->findById($iChatId);
        if ($oRoom) {
            return true;
        }

        return false;
    }

}
