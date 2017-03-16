<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform\Traits;

use MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket;

trait VerifyTicketTrait
{
    /**
     * Verify Ticket.
     *
     * @var \MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * Set verify ticket instance.
     *
     * @param \MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket $verifyTicket
     *
     * @return $this
     */
    public function setVerifyTicket(VerifyTicket $verifyTicket)
    {
        $this->verifyTicket = $verifyTicket;
        return $this;
    }

    /**
     * Get verify ticket instance.
     *
     * @return \MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket
     */
    public function getVerifyTicket()
    {
        return $this->verifyTicket;
    }
}
