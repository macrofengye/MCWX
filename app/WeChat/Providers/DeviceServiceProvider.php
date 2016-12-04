<?php
namespace WeChat\Providers;

use WeChat\Utils\WeChat\Device\Device;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class DeviceServiceProvider.
 */
class DeviceServiceProvider implements ServiceProviderInterface
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
        $pimple['device'] = function ($pimple) {
            return new Device($pimple['access_token'], $pimple['config']->get('device', []));
        };
    }
}
