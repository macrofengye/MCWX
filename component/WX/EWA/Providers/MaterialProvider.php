<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 21:24
 */

namespace MComponent\WX\EWA\Providers;


use MComponent\WX\EWA\WeChat\Material\Material;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MaterialProvider implements ServiceProviderInterface
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
        $pimple['material'] = function ($pimple) {
            return new Material();
        };

        /*$temporary = function ($pimple) {
            return new Temporary($pimple['access_token']);
        };*/

        //$pimple['material_temporary'] = $temporary;
        //$pimple['material.temporary'] = $temporary;
    }
}