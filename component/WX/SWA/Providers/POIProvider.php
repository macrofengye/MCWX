<?php

namespace MComponent\WX\SWA\Providers;

use MComponent\WX\SWA\WeChat\POI\POI;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class POIProvider.
 */
class POIProvider implements ServiceProviderInterface
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
        $pimple['poi'] = function ($pimple) {
            return new POI($pimple['access_token']);
        };
    }
}
