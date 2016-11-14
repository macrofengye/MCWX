<?php

namespace WeChat\Providers;

use WeChat\Utils\WeChat\Semantic\Semantic;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class SemanticProvider.
 */
class SemanticProvider implements ServiceProviderInterface
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
        $pimple['semantic'] = function ($pimple) {
            return new Semantic($pimple['access_token']);
        };
    }
}
