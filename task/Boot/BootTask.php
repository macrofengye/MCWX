<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 21:36
 */

namespace Task\Boot;

use Core\Boot\Application;

class BootTask
{
    /**
     * 日志记录相关
     */
    const LOG = 'log';    // 一般日志
    const ERROR = 'error';  // 错误日志
    const INFO = 'info';   // 信息日志
    const WARN = 'warn';   // 警告日志
    const TRACE = 'trace';  // 输入日志同时会打出调用栈
    const ALERT = 'alert';  // 将日志以alert方式弹出
    const LOG_CSS = 'log';    // 自定义日志的样式，第三个参数为css样式

    /**
     * 数据存储相关
     */
    const ENTITY = "entityManager";
    const REDIS = "redis";
    const MEMCACHE = "memcache";
    const MEMCACHED = 'memcached';

    /**
     * 整个框架的应用
     * @var \Core\Boot\Application
     */
    protected $app;

    public function __construct()
    {
        $this->app = new Application();
        $this->app->startConsole();
    }

    /**
     * console 控制台输出
     * @param string $log_level
     * @param $tips
     * @param $data
     * @param string $style
     */
    protected function consoleDebug($log_level = self::LOG, $tips, $data, $style = '')
    {
        if (extension_loaded('curl') && $this->app->config('debug')['is_open_socket_log_debug']) {
            $slog = new \Slog();
            $slog->config($this->app->config('debug')['socket_log'], 'config');
            $log = [
                'tips' => $tips,
                'log' => $data
            ];
            if ($log_level == self::LOG_CSS && !empty($style)) {
                $slog->$log_level($log, $style);
            } else {
                $slog->$log_level($log);
            }
        }
    }
}