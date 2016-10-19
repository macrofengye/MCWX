<?php
/**
 * 所有控制器必须集成该类
 *
 * @author macro chen <macro_fengye@163.com>
 */
namespace Core\Controller;

use Core\Utils\Constants;
use Core\Utils\CoreUtils;
use Interop\Container\ContainerInterface;

class Controller
{
    /**
     * Slim框架自动注册的Container
     * @var ContainerInterface
     */

    protected $ci;

    /**
     * 整个框架的应用
     * @var \Core\Boot\Application
     */

    protected $app;

    public function __construct(ContainerInterface $ci)
    {
        $this->app = $ci->application;
    }

    /**
     * 模板渲染
     * @author macro chen <macro_fengye@163.com>
     * @param $response 响应的对象
     * @param $template 模板文件
     * @param $data 传递到模板的数据
     */
    protected function render($response, $template, $data)
    {
        return $this->app->component('view')->render($response, $template, $data);
    }

    /**
     * console 控制台输出
     * @param string $log_level
     * @param $tips
     * @param $data
     * @param string $style
     */
    protected function consoleDebug($log_level = Constants::LOG, $tips, $data, $style = '')
    {
        if (extension_loaded('curl') && $this->app->config('debug')['is_open_socket_log_debug']) {
            $slog = new \Slog();
            $slog->config($this->app->config('debug')['socket_log'], 'config');
            $log = [
                'tips' => $tips,
                'log' => $data
            ];
            if ($log_level == Constants::LOG_CSS && !empty($style)) {
                $slog->$log_level($log, $style);
            } else {
                $slog->$log_level($log);
            }
        }
    }
}

?>