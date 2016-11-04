<?php
/**
 * User: <macro_fengye@163.com> Macro Chen
 * Date: 16-9-8
 * Time: 上午8:38
 */

namespace Core\Providers;

use Core\Session\Session;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SessionService implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['session'] = function (Container $container) {
            ini_set('session.save_handler', 'files');
            $sessionHandler = $container['config']->get('session_handler.cls');
            $handler = new $sessionHandler($container['config']->get('session_handler.params'));
            session_set_save_handler($handler, true);
            $session = new Session();
            $session->start();
            return $session;
        };
    }
}