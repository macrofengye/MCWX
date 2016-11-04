<?php
$app->map(['GET', 'POST'], '/', APP_NAME . '\\Controller\\Home:index')->setName(APP_NAME . '.home.index');
$app->map(['GET', 'POST'], '/home/index', APP_NAME . '\\Controller\\Home:index')->setName(APP_NAME . '.home.index');
$app->map(['GET', 'POST'], '/home/hello', APP_NAME . '\\Controller\\Home:hello')->setName(APP_NAME . '.home.hello');