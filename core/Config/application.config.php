<?php
$common_config = [
    //应用配置
    'slim' => [
        'mode' => APPLICATION_ENV,
        'settings' => [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'addContentLengthHeader' => 1,
            'routerCacheFile' => APP_PATH . 'Routers/routerCacheFile.router',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'logger' => [
                'name' => 'macro_php',
                'level' => Monolog\Logger::DEBUG,
                'path' => APP_PATH . 'log/error.log',
            ]
        ],
    ],

    //Doctrine配置
    'doctrine' => [
        'query_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCacheDriver',
            'database' => 15
        ],
        'result_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCacheDriver',
            'database' => 15
        ],
        'metadata_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCacheDriver',
            'database' => 15
        ],
    ],

    // Cookie配置
    'cookies' => [
        'expires' => '60 minutes',
        'path' => '/',
        'domain' => null,
        // 'secure' => true,
        'httponly' => true,
        'name' => 'macro_php',
        'secret' => '',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC,
    ],

    // Session配置
    'session' => [
        'manager' => [
            'remember_me_seconds' => 1200,
            'name' => 'macro_php',
            // 'phpSaveHandler' => 'redis',
            // 'savePath' => 'tcp://127.0.0.1:6379?weight=1&timeout=1',
            'use_cookies' => true,
            //'cookie_secure'=>true,
            'cookie_domain' => 'xiaofei.com',
            'cookie_httponly' => true,
            //'cookie_lifetime' => 3600
        ],
        'container' => [
            'namespace' => 'macro_php',
        ],
    ],

    //自定义配置
    'customer' => [
        'encrypt_key' => 'xxxxx',//加密的KEY
        'cache_router' => true, //是否缓存路由文件
        'router_cache_file' => APP_PATH . 'Routers/routerCacheFile.router', //路由缓存文件的路径
        'is_rest' => true, //接口形式提供服务
        'is_api_rate_limit' => false,  // API速率限制
        'show_use_memory' => false,
        'initial_epoch' => 1476614506000, //用于SnowFlake产生ID
    ],

    //微信配置
    'wechat' => [
        'debug' => 0,//是否开启调试模式
        'app_id' => 'wx7cba84e8e7343a5c',
        'secret' => '67242b76b760ca40442375817304bb29',
        'token' => 'testmcwx',
        'oauth.scopes' => ['snsapi_userinfo'],
        'oauth.callback' => '/home/hello',
        'aes_key' => 'aaapp00' //微信后台设置的EncodingAESKey
    ],

    //定义数据缓存的Cache
    'data_cache' => [
        'redis_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCache'
        ],
        'memcache_cache' => [
            'is_open' => true,
            'cache_name' => 'memcacheCache'
        ],
    ],
];

return $common_config;