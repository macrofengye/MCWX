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
use Doctrine\Common\Cache\MemcacheCache;

class MemcacheCacheDriverService implements ServiceProviderInterface
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
        $pimple["memcacheCacheDriver"] = function (Container $container) {
            $namespace = 'memcacheCacheDriver';
            if ($container['application']->getContainer('namespace')) {
                $namespace = $container['application']->getContainer('namespace');
            }
            $memcacheCacheDriver = new MemcacheCache();
            $memcacheCacheDriver->setNamespace($namespace);
            $memcacheCacheDriver->setMemcache($container['application']->getContainer('memcache'));
            return $memcacheCacheDriver;
        };
    }
}