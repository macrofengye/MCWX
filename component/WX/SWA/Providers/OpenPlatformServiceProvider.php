<?php

namespace MComponent\WX\SWA\Providers;

use MComponent\WX\SWA\WeChat\Encryption\Encryptor;
use MComponent\WX\SWA\WeChat\OpenPlatform\AccessToken;
use MComponent\WX\SWA\WeChat\OpenPlatform\Guard;
use MComponent\WX\SWA\WeChat\OpenPlatform\OpenPlatform;
use MComponent\WX\SWA\WeChat\OpenPlatform\VerifyTicket;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class OpenPlatformServiceProvider.
 */
class OpenPlatformServiceProvider implements ServiceProviderInterface
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
        $pimple['component_verify_ticket'] = function ($pimple) {
            return new VerifyTicket(
                app()->config('open_platform'),
                $pimple['cache']
            );
        };

        $pimple['open_platform_access_token'] = function ($pimple) {
            return new AccessToken(
                app()->config('open_platform.app_id'),
                app()->config('open_platform.secret'),
                $pimple['component_verify_ticket'],
                $pimple['cache']
            );
        };

        $pimple['open_platform_encryptor'] = function ($pimple) {
            return new Encryptor(
                app()->config('open_platform.app_id'),
                app()->config('open_platform.token'),
                app()->config('open_platform.aes_key')
            );
        };

        $pimple['open_platform'] = function ($pimple) {
            $server = new Guard(app()->config('open_platform.token'), $pimple['component_verify_ticket']);

            $server->debug(app()->config('open_platform.debug'));

            $server->setEncryptor($pimple['open_platform_encryptor']);

            return new OpenPlatform(
                $server,
                $pimple['open_platform_access_token'],
                app()->config('open_platform')
            );
        };
    }
}
