<?php

namespace MComponent\WX\SWA\WeChat\OpenPlatform\EventHandlers;

use MComponent\WX\SWA\WeChat\Support\Collection;

class ComponentVerifyTicket extends EventHandler
{
    /**
     * {@inheritdoc}.
     */
    public function handle(Collection $message)
    {
        $this->verifyTicket->cache($message);

        return 'success';
    }
}
