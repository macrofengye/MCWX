<?php
namespace WeChat\Providers;

use WeChat\Utils\WeChat\Js\Js;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class JsService.
 */
class JsService implements ServiceProviderInterface
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
        $pimple['js'] = function ($pimple) {
            $js = new Js($pimple['access_token']);
            $js->setCache($pimple['cache']);

            return $js;
        };
    }
}
