<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-26
 * Time: 下午4:28
 */

namespace Core\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RouterFileService implements ServiceProviderInterface
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
        $pimple['routerFile'] = function (Container $container) {
            if (!file_exists(APP_PATH . '/Routers/router.lock') || APPLICATION_ENV == "development") {
                if (file_exists($container['application']->config('customer.router_cache_file'))) @unlink($container['application']->config('customer.router_cache_file'));
                $router_file_contents = '<?php ' . "\n" . '$app = $container[\'application\']->component(\'app\')';
                if ($container['application']->config('middleware')) {
                    foreach ($container['application']->config('middleware') as $key => $middleware) {
                        if (function_exists($key) && is_callable($key)) {
                            $router_file_contents .= '->add("' . $key . '")';
                        } elseif (class_exists($middleware)) {
                            $router_file_contents .= '->add($container[\'application\']->component("' . $key . '"))';
                        }
                    }
                }
                $router_file_contents .= ';' . "\n";
                foreach (glob(APP_PATH . 'Routers/*_router.php') as $key => $file_name) {
                    $contents = file_get_contents($file_name);
                    preg_match_all("/app->.*/", $contents, $matches);
                    foreach ($matches[0] as $kk => $vv) {
                        $router_file_contents .= '$' . $vv . "\n";
                    }
                }
                file_put_contents(APP_PATH . 'Routers/router.php', $router_file_contents);
                $container['router']->setCacheFile($container['application']->config('customer.router_cache_file'));
                touch(APP_PATH . '/Routers/router.lock');
            }
            require_once APP_PATH . 'Routers/router.php';
        };
    }

}