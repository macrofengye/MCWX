<?php
use \Core\Boot\Application;

/**
 * 字节转换
 *
 * @param $size
 * @return string
 */
function convert($size)
{
    $unit = array('b', 'KB', 'MB', 'GB', 'TB', 'PB');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

/**
 * URL参数安全base64
 *
 * @author macro chen <macro_fengye@163.com>
 * @param string $string
 * @param string $operation ENCODE|DECODE
 * @return string
 */
function urlSafeBase64Code($string, $operation = 'ENCODE')
{
    $searchKws = array('+', '/', '=');
    $replaceKws = array('-', '_', '');
    if ($operation == 'DECODE') {
        $ret = base64_decode(str_replace($replaceKws, $searchKws, $string));
    } else {
        $ret = str_replace($searchKws, $replaceKws, base64_encode($string));
    }
    return $ret;
}

/**
 * 获取客户端真实IP
 *
 * @author macro chen <macro_fengye@163.com>
 */
function getIP()
{
    if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
        $IP = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
        $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'])
        $IP = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'])
        $IP = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'])
        $IP = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'])
        $IP = $_SERVER['REMOTE_ADDR'];
    else
        $IP = '0.0.0.0';
    return $IP;
}

/**
 * PHP错误处理函数
 *
 * @author <macro_fengye@163.com> macro chen
 */
function fatal_handler()
{
    $error = error_get_last();
    if ($error["type"] == E_ERROR) {
        if (\Core\Utils\CoreUtils::getContainer('logger')) {
            $msg = 'Type : ' . $error["type"] . '\nMessage : ' . $error["message"] . '\nFile : ' . $error["file"] . '\nLine : ' . $error["line"];
            \Core\Utils\CoreUtils::getContainer('logger')->error($msg);
        } else {
            $msg = 'Type : ' . $error["type"] . ' , Message : ' . $error["message"] . ' , File : ' . $error["file"] . ' , Line : ' . $error["line"];
            writeLog('Fatal Error : ', [$msg], APP_PATH . '/log/fatal_error.log', Monolog\Logger::ERROR);
            if (file_exists(TEMPLATE_PATH . 'error.twig')) {
                echo @file_get_contents(TEMPLATE_PATH . 'error.twig');
            } else {
                echo \GuzzleHttp\json_encode(['code' => 2000, 'msg' => 'Error', 'data' => []]);
            }
        }
    }
}

/**
 * 验证来源IP合法性，是否在允许IP列表内
 * checkFromIpValidity('127.0.0.1', array('127.0.0.1', '192.168.0.'))
 * 允许IP列表支持不完全匹配
 *
 * @author fengxu
 * @param string $fromIp 来源IP
 * @param array $allowIps 允许IP列表
 * @return boolean
 */
function checkFromIPValidity($fromIp = '', array $allowIps = array())
{
    $fromIp = $fromIp ? $fromIp : getIp();
    $res = false;
    if ($allowIps) {
        foreach ($allowIps as $allowIp) {
            if (!strncmp($fromIp, $allowIp, strlen($allowIp))) {
                $res = true;
                break;
            }
        }
    }
    return $res;
}

/**
 *
 * 验证密码复杂度
 *
 * @author fengxu
 * @param string $password
 * @param integer $minPwdLen 密码最小长度
 * @return integer 密码复杂度等级，安位求或
 */
function verifyPwdComplexity($password, $minPwdLen = 6)
{
    $complexity = 0;
    if (strlen($password) >= (int)$minPwdLen) {
        $complexity = 1;
        if (preg_match('@[a-zA-Z]+@', $password)) {
            $complexity |= 2;
        }
        if (preg_match('@[0-9]+@', $password)) {
            $complexity |= 4;
        }
        if (preg_match('@[A-Z]+@', $password)) {
            $complexity |= 8;
        }
        if (preg_match('@[\W]+@', $password)) { // 字母数字外的其他字符
            $complexity |= 16;
        }
    }
    return $complexity;
}

/**
 * 过滤不可见(不可打印)的字符
 *
 * @param $str
 * @return mixed
 */
function filterInvisibleCharacter($str)
{
    return preg_replace('/[^[:print:]]/', '', $str);
}

/**
 * 记录日志，便于调试
 *
 * @param $message
 * @param array $content
 * @param string $file
 * @param string $log_name
 * @param int $level
 */
function writeLog($message, array $content, $file = '', $log_name = "LOG", $level = \Monolog\Logger::WARNING)
{
    $levels = [
        100 => 'debug',
        200 => 'info',
        250 => 'notice',
        300 => 'warning',
        400 => 'error',
        500 => 'critical',
        550 => 'alert',
        600 => 'emergency'
    ];
    $logger = new \Monolog\Logger($log_name);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($file ? $file : APP_PATH . '/log/log.log', $level));
    $function_name = $levels[$level];
    $logger->$function_name($message, $content);
}

/**
 * 获取应用实例
 *
 * @author macro chen <macro_fengye@163.com>
 * @return static
 */
function app()
{
    return Application::getInstance();
}