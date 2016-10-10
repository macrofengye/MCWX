<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-9-8
 * Time: 上午8:35
 */

namespace Core\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Core\Middleware\SessionMiddleware;

class SessionMiddlewareService implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['sessionMiddleware'] = function (Container $container) {
            return new SessionMiddleware();
        };
    }
}