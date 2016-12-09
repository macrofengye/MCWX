<?php
namespace MComponent\WX\SWA\Providers;

use MComponent\WX\SWA\WeChat\User\Group;
use MComponent\WX\SWA\WeChat\User\Tag;
use MComponent\WX\SWA\WeChat\User\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class UserService.
 */
class UserProvider implements ServiceProviderInterface
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
        $pimple['user'] = function ($pimple) {
            return new User($pimple['access_token']);
        };

        $group = function ($pimple) {
            return new Group($pimple['access_token']);
        };

        $tag = function ($pimple) {
            return new Tag($pimple['access_token']);
        };

        $pimple['user_group'] = $group;
        $pimple['user.group'] = $group;

        $pimple['user_tag'] = $tag;
        $pimple['user.tag'] = $tag;
    }
}
