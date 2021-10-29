<?php

namespace App\Crontab;

use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use App\Services\BotService; 

/**
 * @Crontab(
 * 	name="EmiuOrderMeal", 
 * 	singleton=true,
 *  rule="0 18 * * 1-5",
 * 	callback="execute",
 * 	enable="isEnable",
 * 	memo="Emiu訂餐提醒"
 * 	)
 */
class EmiuOrderMeal
{
	/**
     * @Inject
     * @var BotService
     */
    protected $oBotService;

    public function execute()
    {
        $this->oBotService->emiuOrderMeal();
    }

    public function isEnable(): bool
    {
        return true;
    }
}