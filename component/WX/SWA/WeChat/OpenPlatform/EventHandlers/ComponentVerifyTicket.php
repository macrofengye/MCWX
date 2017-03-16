<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform\EventHandlers;

use MComponent\WX\SWA\WeChat\OpenPlatform\Traits\VerifyTicketTrait;
use MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket;
use MComponent\WX\SWA\WeChat\Support\Collection;

class ComponentVerifyTicket implements EventHandler
{
    use VerifyTicketTrait;

    public function __construct(VerifyTicket $verifyTicket)
    {
        $this->setVerifyTicket($verifyTicket);
    }

    /**
     * {@inheritdoc}.
     */
    public function handle(Collection $message)
    {
        $this->getVerifyTicket()->cache($message);
        return $message;
    }
}
