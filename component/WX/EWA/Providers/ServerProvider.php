<?php
/**
 * User: Macro Chen <macro_fengye@163.com>
 * Date: 2016/12/23
 * Time: 17:58
 */

namespace MComponent\WX\EWA\Providers;

use MComponent\WX\EWA\WeChat\Server\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['server'] = function ($pimple) {
            return new Server();
        };
    }
}