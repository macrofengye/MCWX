<?php
date_default_timezone_set("Asia/Shanghai");

define("ROOT_PATH", dirname(dirname(__DIR__)));
define("APP_NAME", "WeChat");
define('WX_TYPE' , 'SWA'); // 微信的类型
define('TEMPLATE_PATH', ROOT_PATH . "/app/" . APP_NAME . "/Templates/");
define("APP_PATH", ROOT_PATH . "/app/" . APP_NAME . "/");
define('ENTITY_NAMESPACE', 'Entity\\Models');//数据模型的命名空间
require ROOT_PATH . '/vendor/autoload.php';
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
$app = new \Polymer\Boot\Application();
$app->start();