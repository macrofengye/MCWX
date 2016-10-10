<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Core\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\Common\EventManager;

class DoctrineEventManagerService implements ServiceProviderInterface
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
        $pimple["doctrineEventManager"] = function (Container $container) {
            return new EventManager();
        };
    }
}