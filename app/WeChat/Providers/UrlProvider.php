<?php

namespace WeChat\Providers;

use WeChat\Utils\WeChat\Url\Url;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class UrlProvider.
 */
class UrlProvider implements ServiceProviderInterface
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
        $pimple['url'] = function ($pimple) {
            return new Url($pimple['access_token']);
        };
    }
}
