<?php
/**
 * 所有控制器必须集成该类
 *
 * @author macro chen <macro_fengye@163.com>
 */
namespace Core\Controller;

use Core\Utils\CoreUtils;

class Controller
{
    const LOG = 'log';    // 一般日志
    const ERROR = 'error';  // 错误日志
    const INFO = 'info';   // 信息日志
    const WARN = 'warn';   // 警告日志
    const TRACE = 'trace';  // 输入日志同时会打出调用栈
    const ALERT = 'alert';  // 将日志以alert方式弹出
    const LOG_CSS = 'log';    // 自定义日志的样式，第三个参数为css样式

    /**
     * 模板渲染
     * @author macro chen <macro_fengye@163.com>
     * @param $response 响应的对象
     * @param $template 模板文件
     * @param $data 传递到模板的数据
     */
    protected function render($response, $template, $data)
    {
        return CoreUtils::getContainer('view')->render($response, $template, $data);
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
        if (extension_loaded('curl') && CoreUtils::getConfig('debug')['is_open_socket_log_debug']) {
            $slog = new \Slog();
            $slog->config(CoreUtils::getConfig('debug')['socket_log'], 'config');
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

?>