<?php

namespace App\Crontab;

use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use App\Services\BotService; 

/**
 * @Crontab(
 * 	name="EmiuGoOffWork", 
 * 	singleton=true,
 *  rule="30 19 * * 1-5",
 * 	callback="execute",
 * 	enable="isEnable",
 * 	memo="Emiu下班提醒"
 * 	)
 */
class EmiuGoOffWork
{
	/**
     * @Inject
     * @var BotService
     */
    protected $oBotService;

    public function execute()
    {
        $this->oBotService->emiuGoOffWork();
    }

    public function isEnable(): bool
    {
        return true;
    }
}