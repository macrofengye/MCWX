<?php

namespace WeChat\Providers;

use WeChat\Utils\WeChat\Reply\Reply;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ReplyProvider.
 */
class ReplyProvider implements ServiceProviderInterface
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
        $pimple['reply'] = function ($pimple) {
            return new Reply($pimple['access_token']);
        };
    }
}
