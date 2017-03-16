<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform\EventHandlers;

use MComponent\WX\SWA\WeChat\Support\Collection;

interface EventHandler
{
    /**
     * Handle event.
     *
     * @param Collection $message
     *
     * @return mixed
     */
    public function handle(Collection $message);

}
