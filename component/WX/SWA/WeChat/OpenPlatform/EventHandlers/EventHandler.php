<?php

namespace MComponent\WX\SWA\WeChat\OpenPlatform\EventHandlers;

use MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket;
use MComponent\WX\SWA\WeChat\Support\Collection;

abstract class EventHandler
{
    /**
     * Component verify ticket instance.
     *
     * @var \MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * EventHandler constructor.
     *
     * @param VerifyTicket $verifyTicket
     */
    public function __construct(VerifyTicket $verifyTicket)
    {
        $this->verifyTicket = $verifyTicket;
    }

    /**
     * Handle event.
     *
     * @param Collection $message
     *
     * @return mixed
     */
    abstract public function handle(Collection $message);

    /**
     * Forward handle.
     *
     * @param Collection $message
     *
     * @return Collection
     */
    public function forward(Collection $message)
    {
        return $message;
    }
}
