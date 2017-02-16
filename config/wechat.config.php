<?php
$weChatConfig = [
    //微信配置
    'wechat' => [
        'default' => [
            'debug' => 1,//是否开启调试模式
            'app_id' => 'wx7cba84e8e7343a5c',
            'secret' => '67242b76b760ca40442375817304bb29',
            'token' => 'macrowxtoken',
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => '/home/hello',
            ],
            'aes_key' => '52z36K5M4Nd1CDA9doCCSjL8AYIAgUzubx2spt8kxpn' //微信后台设置的EncodingAESKey
        ],
        'luke' => [
            'debug' => 1,//是否开启调试模式
            'app_id' => 'wx7cba84e8e7343a5c',
            'secret' => '67242b76b760ca40442375817304bb29',
            'token' => 'macrowxtoken',
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => '/home/hello',
            ],
            'aes_key' => '52z36K5M4Nd1CDA9doCCSjL8AYIAgUzubx2spt8kxpn' //微信后台设置的EncodingAESKey
        ]
    ],

    //APP_ID映射为微信配置
    'wx_app_id' => [
        'default',
        'luke'
    ],
];

return $weChatConfig;