<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-10-10
 * Time: 下午1:38
 */

namespace MComponent\WX\SWA\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use MComponent\WX\SWA\WeChat\Core\AccessToken;
use Doctrine\Common\Cache\FilesystemCache;

class AccessTokenProvider implements ServiceProviderInterface
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
        $pimple['access_token'] = function (Container $container) {
            try {
                $weChatConfig = weChatConfig();
                $cache = new FilesystemCache(ROOT_PATH . '/component/WX/' . WX_TYPE . '/cache/' . $weChatConfig['name']);
                $container['cache'] = $cache;
                $cls = 'MComponent\WX\\' . WX_TYPE . '\WeChat\Core\AccessToken';
                return new $cls($weChatConfig['app_id'], $weChatConfig['secret'], $cache);
            } catch (\InvalidArgumentException $e) {
                return $container['response']
                    ->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')->write($e->getMessage());
            }
        };
    }
}