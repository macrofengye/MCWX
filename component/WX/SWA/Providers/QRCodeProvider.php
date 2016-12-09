<?php

namespace MComponent\WX\SWA\Providers;

use MComponent\WX\SWA\WeChat\QRCode\QRCode;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class QRCodeProvider.
 */
class QRCodeProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['qRCode'] = function ($pimple) {
            return new QRCode($pimple['access_token']);
        };
    }
}
