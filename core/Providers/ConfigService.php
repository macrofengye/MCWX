<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-26
 * Time: 下午4:07
 */

namespace Core\Providers;


use Noodlehaus\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigService implements ServiceProviderInterface
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
        $pimple['config'] = function ($container) {
            return new Config([APP_PATH . 'Config', ROOT_PATH . '/core/Config']);
        };
    }
}