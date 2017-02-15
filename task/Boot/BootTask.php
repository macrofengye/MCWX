<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 21:36
 */

namespace Task\Boot;

use Polymer\Boot\Application;
use Polymer\Utils\Constants;

class BootTask
{
    /**
     * 整个框架的应用
     * @var \Polymer\Boot\Application
     */
    protected $app;

    public function __construct()
    {
        $this->app = Application::getInstance();
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