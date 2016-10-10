<?php
//自定义配置
$customer_config = [
    'middleware' => [
        'sessionMiddleware' => \Core\Middleware\SessionMiddleware::class
    ],
];

return $customer_config;