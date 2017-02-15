<?php
namespace MComponent\WX\SWA\Providers;

use MComponent\WX\SWA\WeChat\Encryption\Encryptor;
use MComponent\WX\SWA\WeChat\Server\Guard;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServerProvider.
 */
class ServerProvider implements ServiceProviderInterface
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
        $pimple['encryptor'] = function ($pimple) {
            try {
                $weChatName = weChatConfig();
                return new Encryptor(
                    app()->config('wechat.' . $weChatName . '.app_id'),
                    app()->config('wechat.' . $weChatName . '.token'),
                    app()->config('wechat.' . $weChatName . '.aes_key')
                );
            } catch (\Exception $e) {
                return null;
            }
        };

        $pimple['server'] = function ($pimple) {
            $weChatName = weChatConfig();
            logger(__FUNCTION__, [$pimple['request']->getParams(), $weChatName], APP_PATH . '/log/aaab.log');
            $server = new Guard(app()->config('wechat.' . $weChatName . '.token'), $pimple['request'], $pimple['response']);
            $server->debug(app()->config('wechat.' . $weChatName . '.debug'));
            $server->setEncryptor($pimple['encryptor']);

            return $server;
        };
    }
}
