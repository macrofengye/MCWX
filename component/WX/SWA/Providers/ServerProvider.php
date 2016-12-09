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
            return new Encryptor(
                $pimple['config']['wechat']['app_id'],
                $pimple['config']['wechat']['token'],
                $pimple['config']['wechat']['aes_key']
            );
        };

        $pimple['server'] = function ($pimple) {
            $server = new Guard($pimple['config']['wechat']['token'], $pimple['request'], $pimple['response']);
            $server->debug($pimple['config']['wechat']['debug']);
            $server->setEncryptor($pimple['encryptor']);

            return $server;
        };
    }
}
