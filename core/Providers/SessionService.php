<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-9-8
 * Time: 上午8:38
 */

namespace Core\Providers;

use Core\Middleware\Session;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SessionService implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['session'] = function (Container $container) {
            $session = new Session();
            $session->set('initialize_time', time());
            return $session;
        };
    }
}