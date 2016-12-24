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
            $cfg = $pimple['config']['wechat'];
            return new User($cfg['app_id'], $cfg['app_secret']);
        };

        $group = function ($pimple) {
            $cfg = $pimple['config']['wechat'];
            return new Group($cfg['app_id'], $cfg['app_secret']);
        };

        $tag = function ($pimple) {
            $cfg = $pimple['config']['wechat'];
            return new Tag($cfg['app_id'], $cfg['app_secret']);
        };

        $pimple['user_group'] = $group;
        $pimple['user.group'] = $group;

        $pimple['user_tag'] = $tag;
        $pimple['user.tag'] = $tag;
    }

}