<?php
date_default_timezone_set('Asia/Shanghai');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
define('ROOT_PATH', __DIR__);
define('APP_NAME', 'task');
define('APP_PATH', ROOT_PATH . '/' . APP_NAME . '/');
define('CONFIG_PATH', ROOT_PATH . '/' . APP_NAME . '/');
require ROOT_PATH . '/vendor/autoload.php';
$app = new \Polymer\Boot\Application();
$app->startConsole();
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(app()->component('db1', 'Mapping'));