<?php

namespace WeChat\Providers;

use WeChat\Utils\WeChat\QRCode\QRCode;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class QRCodeService.
 */
class QRCodeServiceProvider implements ServiceProviderInterface
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
        $pimple['qrcode'] = function ($pimple) {
            return new QRCode($pimple['access_token']);
        };
    }
}
