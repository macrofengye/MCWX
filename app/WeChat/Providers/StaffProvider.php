<?php

namespace WeChat\Providers;

use WeChat\Utils\WeChat\Staff\Session;
use WeChat\Utils\WeChat\Staff\Staff;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class StaffProvider.
 */
class StaffProvider implements ServiceProviderInterface
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
        $pimple['staff'] = function ($pimple) {
            return new Staff($pimple['access_token']);
        };

        $pimple['staff_session'] = $pimple['staff.session'] = function ($pimple) {
            return new Session($pimple['access_token']);
        };
    }
}
