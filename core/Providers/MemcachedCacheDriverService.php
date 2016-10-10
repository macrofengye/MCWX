<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/1
 * Time: 20:12
 */

namespace Core\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MemcachedCacheDriverService implements ServiceProviderInterface
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
        $pimple["memcachedCacheDriver"] = function (Container $container) {
            $namespace = 'memcachedCacheDriver';
            if ($container['application']->getContainer('namespace')) {
                $namespace = $container['application']->getContainer('namespace');
            }
            $memcachedCacheDriver = new MemcacheCached();
            $memcachedCacheDriver->setNamespace($namespace);
            $memcachedCacheDriver->setMemcache($container['application']->getContainer('memcached'));
            return $memcachedCacheDriver;
        };
    }
}