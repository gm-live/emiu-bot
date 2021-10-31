<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class RoomConst extends AbstractConstants
{
    const STATUS_ACTIVE = 1;
    const STATUS_UNACTIVE = 2;
}
