<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform\EventHandlers;

use MComponent\WX\SWA\WeChat\OpenPlatform\Authorization;
use MComponent\WX\SWA\WeChat\Support\Collection;

class Authorized implements EventHandler
{
    /**
     * @var Authorization
     */
    protected $authorization;

    public function __construct(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * {@inheritdoc}.
     */
    public function handle(Collection $message)
    {
        $this->authorization->setFromAuthMessage($message);
        return $this->authorization->handleAuthorization();
    }
}
