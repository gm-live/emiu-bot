<?php

declare (strict_types = 1);

namespace App\ServerListener;

use App\Services\BotService;
use Hyperf\Di\Annotation\Inject;

class OnManagerStartListener
{
    /**
     * @Inject
     * @var BotService
     */
    protected $oBotService;

    public function handle()
    {
        $this->oBotService->botWebhookSet();
    }

}
