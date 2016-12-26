<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 19:48
 */

namespace MComponent\WX\EWA\Providers;


use MComponent\WX\EWA\WeChat\User\Group;
use MComponent\WX\EWA\WeChat\User\Tag;
use MComponent\WX\EWA\WeChat\User\User;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserProvider implements ServiceProviderInterface
{
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