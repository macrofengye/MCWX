<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-26
 * Time: 下午4:07
 */

namespace Polymer\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Noodlehaus\Config;

class ConfigProvider implements ServiceProviderInterface
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
            $config = [ROOT_PATH . '/framework/Config'];
            if (file_exists(ROOT_PATH . '/config')) {
                $config[] = ROOT_PATH . '/config';
            }
            if (file_exists(APP_PATH . '/Config')) {
                $config[] = APP_PATH . '/Config';
            }
            return new Config($config);
        };
    }
}