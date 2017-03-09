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
                $weChatConfig = weChatConfig();
                return new Encryptor($weChatConfig['app_id'], $weChatConfig['token'], $weChatConfig['aes_key']);
            } catch (\Exception $e) {
                return null;
            }
        };

        $pimple['server'] = function ($pimple) {
            $weChatConfig = weChatConfig();
            $server = new Guard($weChatConfig['token'], $pimple['request'], $pimple['response']);
            $server->debug($weChatConfig['debug']);
            $server->setEncryptor($pimple['encryptor']);
            return $server;
        };
    }
}
