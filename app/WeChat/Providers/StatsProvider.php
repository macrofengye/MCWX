<?php

namespace WeChat\Providers;

use WeChat\Utils\WeChat\Stats\Stats;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class StatsProvider.
 */
class StatsProvider implements ServiceProviderInterface
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
        $pimple['stats'] = function ($pimple) {
            return new Stats($pimple['access_token']);
        };
    }
}
