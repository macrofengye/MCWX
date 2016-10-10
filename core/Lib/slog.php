<?php

/**
 * github: https://github.com/luofei614/SocketLog
 * @author luofei614<weibo.com/luofei614>
 */
class Slog
{
    public $start_time = 0;
    public $start_memory = 0;
    public $port = 1116;//SocketLog 服务的http的端口号
    public $log_types = array('log', 'info', 'error', 'warn', 'table', 'group', 'groupCollapsed', 'groupEnd', 'alert');

    protected $_allowForceClientIds = array();    //配置强制推送且被授权的client_id

    protected $_instance;

    protected $config = array(
        'enable' => true, //是否记录日志的开关
        'host' => 'localhost',
        //是否显示利于优化的参数，如果允许时间，消耗内存等
        'optimize' => false,
        'show_included_files' => false,
        'error_handler' => false,
        //日志强制记录到配置的client_id
        'force_client_ids' => array(),
        //限制允许读取日志的client_id
        'allow_client_ids' => array()
    );

    protected $logs = array();

    protected $css = array(
        'sql' => 'color:#009bb4;',
        'sql_warn' => 'color:#009bb4;font-size:14px;',
        'error_handler' => 'color:#f4006b;font-size:14px;',
        'page' => 'color:#40e2ff;background:#171717;'
    );

    public function __call($method, $args)
    {
        if (in_array($method, $this->log_types)) {
            array_unshift($args, $method);
            return call_user_func_array(array($this, 'record'), $args);
        }
    }

    public function sql($sql, $link)
    {
        if (is_object($link) && 'mysqli' == get_class($link)) {
            return $this->mysqlilog($sql, $link);
        }

        if (is_resource($link) && ('mysql link' == get_resource_type($link) || 'mysql link persistent' == get_resource_type($link))) {
            return $this->mysqllog($sql, $link);
        }


        if (is_object($link) && 'PDO' == get_class($link)) {
            return $this->pdolog($sql, $link);
        }

        throw new Exception('SocketLog can not support this database link');
    }


    public function big($log)
    {
        $this->log($log, 'font-size:20px;color:red;');
    }

    public function trace($msg, $trace_level = 1, $css = '')
    {
        if (!$this->check()) {
            return;
        }
        $this->groupCollapsed($msg, $css);
        $traces = debug_backtrace(false);
        $traces = array_reverse($traces);
        $max = count($traces) - $trace_level;
        for ($i = 0; $i < $max; $i++) {
            $trace = $traces[$i];
            $fun = isset($trace['class']) ? $trace['class'] . '::' . $trace['function'] : $trace['function'];
            $file = isset($trace['file']) ? $trace['file'] : 'unknown file';
            $line = isset($trace['line']) ? $trace['line'] : 'unknown line';
            $trace_msg = '#' . $i . '  ' . $fun . ' called at [' . $file . ':' . $line . ']';
            if (!empty($trace['args'])) {
                $this->groupCollapsed($trace_msg);
                $this->log($trace['args']);
                $this->groupEnd();
            } else {
                $this->log($trace_msg);
            }
        }
        $this->groupEnd();
    }


    public function mysqlilog($sql, $db)
    {
        if (!$this->check()) {
            return;
        }

        $css = $this->css['sql'];
        if (preg_match('/^SELECT /i', $sql)) {
            //explain
            $query = @mysqli_query($db, "EXPLAIN " . $sql);
            $arr = mysqli_fetch_array($query);
            $this->sqlexplain($arr, $sql, $css);
        }
        $this->sqlwhere($sql, $css);
        $this->trace($sql, 2, $css);
    }


    public function mysqllog($sql, $db)
    {
        if (!$this->check()) {
            return;
        }
        $css = $this->css['sql'];
        if (preg_match('/^SELECT /i', $sql)) {
            //explain
            $query = @mysql_query("EXPLAIN " . $sql, $db);
            $arr = mysql_fetch_array($query);
            $this->sqlexplain($arr, $sql, $css);
        }
        //判断sql语句是否有where
        $this->sqlwhere($sql, $css);
        $this->trace($sql, 2, $css);
    }


    public function pdolog($sql, $pdo)
    {
        if (!$this->check()) {
            return;
        }
        $css = $this->css['sql'];
        if (preg_match('/^SELECT /i', $sql)) {
            //explain
            try {
                $obj = $pdo->query("EXPLAIN " . $sql);
                if (is_object($obj) && method_exists($obj, 'fetch')) {
                    $arr = $obj->fetch(\PDO::FETCH_ASSOC);
                    $this->sqlexplain($arr, $sql, $css);
                }
            } catch (Exception $e) {

            }
        }
        $this->sqlwhere($sql, $css);
        $this->trace($sql, 2, $css);
    }

    private function sqlexplain($arr, &$sql, &$css)
    {
        $arr = array_change_key_case($arr, CASE_LOWER);
        if (false !== strpos($arr['extra'], 'Using filesort')) {
            $sql .= ' <---################[Using filesort]';
            $css = $this->css['sql_warn'];
        }
        if (false !== strpos($arr['extra'], 'Using temporary')) {
            $sql .= ' <---################[Using temporary]';
            $css = $this->css['sql_warn'];
        }
    }

    private function sqlwhere(&$sql, &$css)
    {
        //判断sql语句是否有where
        if (preg_match('/^UPDATE |DELETE /i', $sql) && !preg_match('/WHERE.*(=|>|<|LIKE|IN)/i', $sql)) {
            $sql .= '<---###########[NO WHERE]';
            $css = $this->css['sql_warn'];
        }
    }


    /**
     * 接管报错
     */
    public function registerErrorHandler()
    {
        if (!$this->check()) {
            return;
        }

        set_error_handler(array($this, 'error_handler'));
        register_shutdown_function(array($this, 'fatalError'));
    }

    public function error_handler($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_WARNING:
                $severity = 'E_WARNING';
                break;
            case E_NOTICE:
                $severity = 'E_NOTICE';
                break;
            case E_USER_ERROR:
                $severity = 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $severity = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $severity = 'E_USER_NOTICE';
                break;
            case E_STRICT:
                $severity = 'E_STRICT';
                break;
            case E_RECOVERABLE_ERROR:
                $severity = 'E_RECOVERABLE_ERROR';
                break;
            case E_DEPRECATED:
                $severity = 'E_DEPRECATED';
                break;
            case E_USER_DEPRECATED:
                $severity = 'E_USER_DEPRECATED';
                break;
            case E_ERROR:
                $severity = 'E_ERR';
                break;
            case E_PARSE:
                $severity = 'E_PARSE';
                break;
            case E_CORE_ERROR:
                $severity = 'E_CORE_ERROR';
                break;
            case E_COMPILE_ERROR:
                $severity = 'E_COMPILE_ERROR';
                break;
            case E_USER_ERROR:
                $severity = 'E_USER_ERROR';
                break;
            default:
                $severity = 'E_UNKNOWN_ERROR_' . $errno;
                break;
        }
        $msg = "{$severity}: {$errstr} in {$errfile} on line {$errline} -- SocketLog error handler";
        $this->trace($msg, 2, $this->css['error_handler']);
    }

    public function fatalError()
    {
        // 保存日志记录
        if ($e = error_get_last()) {
            $this->error_handler($e['type'], $e['message'], $e['file'], $e['line']);
            $this->sendLog();//此类终止不会调用类的 __destruct 方法，所以此处手动sendLog
        }
    }


    protected function check()
    {
        if (!$this->getConfig('enable')) {
            return false;
        }
        $tabid = $this->getClientArg('tabid');
        //是否记录日志的检查
        if (!$tabid && !$this->getConfig('force_client_ids')) {
            return false;
        }
        //用户认证
        $allow_client_ids = $this->getConfig('allow_client_ids');
        if (!empty($allow_client_ids)) {
            //通过数组交集得出授权强制推送的client_id
            $this->_allowForceClientIds = array_intersect($allow_client_ids, $this->getConfig('force_client_ids'));
            if (!$tabid && count($this->_allowForceClientIds)) {
                return true;
            }

            $client_id = $this->getClientArg('client_id');
            if (!in_array($client_id, $allow_client_ids)) {
                return false;
            }
        } else {
            $this->_allowForceClientIds = $this->getConfig('force_client_ids');
        }
        return true;
    }

    protected function getClientArg($name)
    {
        static $args = array();

        $key = 'HTTP_USER_AGENT';

        if (isset($_SERVER['HTTP_SOCKETLOG'])) {
            $key = 'HTTP_SOCKETLOG';
        }

        if (!isset($_SERVER[$key])) {
            return null;
        }
        if (empty($args)) {
            if (!preg_match('/SocketLog\((.*?)\)/', $_SERVER[$key], $match)) {
                $args = array('tabid' => null);
                return null;
            }
            parse_str($match[1], $args);
        }
        if (isset($args[$name])) {
            return $args[$name];
        }
        return null;
    }


    //设置配置
    public function config($config)
    {
        $config = array_merge($this->config, $config);
        if (isset($config['force_client_id'])) {
            //兼容老配置
            $config['force_client_ids'] = array_merge($config['force_client_ids'], array($config['force_client_id']));
        }
        $this->config = $config;
        if ($this->check()) {
            $this->getInstance(); //强制初始化SocketLog实例
            if ($config['optimize']) {
                $this->start_time = microtime(true);
                $this->start_memory = memory_get_usage();
            }

            if ($config['error_handler']) {
                $this->registerErrorHandler();
            }
        }
    }


    //获得配置
    public function getConfig($name)
    {
        if (isset($this->config[$name]))
            return $this->config[$name];
        return null;
    }

    //记录日志
    public function record($type, $msg = '', $css = '')
    {
        if (!$this->check()) {
            return;
        }

        $this->logs[] = array(
            'type' => $type,
            'msg' => $msg,
            'css' => $css
        );
    }

    /**
     * @param null $host - $host of socket server
     * @param string $message - 发送的消息
     * @param string $address - 地址
     * @return bool
     */
    public function send($host, $message = '', $address = '/')
    {
        $url = 'http://' . $host . ':' . $this->port . $address;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $headers = array(
            "Content-Type: application/json;charset=UTF-8"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置header
        $txt = curl_exec($ch);
        print_r($txt);
        return true;
    }

    public function sendLog()
    {
        /* if (!$this->check()) {
             return;
         }*/

        $time_str = '';
        $memory_str = '';
        if ($this->start_time) {
            $runtime = microtime(true) - $this->start_time;
            $reqs = number_format(1 / $runtime, 2);
            $time_str = "[运行时间：{$runtime}s][吞吐率：{$reqs}req/s]";
        }
        if ($this->start_memory) {
            $memory_use = number_format((memory_get_usage() - $this->start_memory) / 1024, 2);
            $memory_str = "[内存消耗：{$memory_use}kb]";
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $current_uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $current_uri = "cmd:" . implode(' ', $_SERVER['argv']);
        }
        array_unshift($this->logs, array(
            'type' => 'group',
            'msg' => $current_uri . $time_str . $memory_str,
            'css' => $this->css['page']
        ));

        if ($this->getConfig('show_included_files')) {
            $this->logs[] = array(
                'type' => 'groupCollapsed',
                'msg' => 'included_files',
                'css' => ''
            );
            $this->logs[] = array(
                'type' => 'log',
                'msg' => implode("\n", get_included_files()),
                'css' => ''
            );
            $this->logs[] = array(
                'type' => 'groupEnd',
                'msg' => '',
                'css' => '',
            );
        }

        $this->logs[] = array(
            'type' => 'groupEnd',
            'msg' => '',
            'css' => '',
        );

        $tabid = $this->getClientArg('tabid');
        if (!$client_id = $this->getClientArg('client_id')) {
            $client_id = '';
        }
        if (!empty($this->_allowForceClientIds)) {
            //强制推送到多个client_id
            foreach ($this->_allowForceClientIds as $force_client_id) {
                $client_id = $force_client_id;
                $this->sendToClient($tabid, $client_id, $this->logs, $force_client_id);
            }
        } else {
            $this->sendToClient($tabid, $client_id, $this->logs, '');
        }
    }

    /**
     * 发送给指定客户端
     * @author Zjmainstay
     * @param $tabid
     * @param $client_id
     * @param $logs
     * @param $force_client_id
     */
    protected function sendToClient($tabid, $client_id, $logs, $force_client_id)
    {
        $logs = array(
            'tabid' => $tabid,
            'client_id' => $client_id,
            'logs' => $logs,
            'force_client_id' => $force_client_id,
        );
        $msg = @json_encode($logs);
        $address = '/' . $client_id; //将client_id作为地址， server端通过地址判断将日志发布给谁
        $this->send($this->getConfig('host'), $msg, $address);
    }

    public function __destruct()
    {
        $this->sendLog();
    }
}

