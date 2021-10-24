<?php

declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;
use App\Model\User;

class UserRepo extends BaseRepo
{
	/**
     * @Inject
     * @var User
     */
    protected $oUser;

    public function findById($iId)
    {
    	return $this->oUser->find($iId);
    }

	public function create(
        $iUserId,
        $sFirstName,
        $sLastName,
        $sUsername,
        $sLang
    ) {
		$oUser = new $this->oUser();
		$oUser->id = $iUserId;
		$oUser->first_name = $sFirstName;
		$oUser->last_name = $sLastName;
		$oUser->username = $sUsername;
		$oUser->language_code = $sLang;
		$oUser->saveOrFail();
	}

	public function checkUserExist($iUserId)
	{
		$sKey = config('redisKeys.user_repeat_check');
        $iResult = $this->oRedis->hget($sKey, (string)$iUserId);
        if ($iResult) {
            return true;
        }

        $oUser = $this->findById($iUserId);
        if ($oUser) {
            return true;
        }

        return false;
	}

}