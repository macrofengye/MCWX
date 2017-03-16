<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform\EventHandlers;

use MComponent\WX\SWA\WeChat\Support\Collection;

class Unauthorized extends Authorized
{
    /**
     * @inheritdoc
     */
    public function handle(Collection $message)
    {
        $this->authorization->setFromAuthMessage($message);
        $this->authorization->removeAuthorizerAccessToken();
        $this->authorization->removeAuthorizerRefreshToken();
        return $message;
    }
}
