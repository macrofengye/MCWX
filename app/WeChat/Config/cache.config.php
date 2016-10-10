<?php
//缓存的配置
$cache = [
    'cache' => [
        'memcache' => [
            'server1' => [
                'resource_id' => 1,
                'servers' => [
                    'server01' => [
                        'host' => '127.0.0.1',
                        'port' => 11211,
                        'timeout' => 10,
                    ],
                    'server02' => [
                        'host' => '127.0.0.1',
                        'port' => 11211,
                        'timeout' => 10,
                    ]
                ],
                'namespace' => APP_NAME . '-memcache01',
            ],
            'server2' => [
                'resource_id' => 2,
                'servers' => [
                    'server' => [
                        'host' => '10.0.25.1',
                        'port' => 11211,
                        'timeout' => 10,
                    ]
                ],
                'namespace' => APP_NAME . '-memcache02',
            ]
        ],
        'redis' => [
            'server1' => [
                'resource_id' => 1,
                'server' => [
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'timeout' => 10,
                    'database' => 0,
                ],
                'namespace' => APP_NAME . '-redis01',
            ],
            'server2' => [
                'resource_id' => 2,
                'server' => [
                    'host' => "10.0.25.1",
                    'port' => 6379,
                    'timeout' => 10,
                    'database' => 0,
                ],
                'namespace' => APP_NAME . '-redis02',
            ]
        ],
        "memcached" => [
            'server1' => [
                'resource_id' => 1,
                'servers' => [
                    'server' => [
                        [
                            'host' => '127.0.0.1',
                            'port' => 11211,
                            'timeout' => 10,
                        ]
                    ]
                ],
                'namespace' => APP_NAME . '-memcache01',
            ],
            'server2' => [
                'resource_id' => 2,
                'servers' => [
                    'server' => [
                        'host' => '10.0.25.1',
                        'port' => 11211,
                        'timeout' => 10,
                    ]
                ],
                'namespace' => APP_NAME . '-memcache02',
            ]
        ],
    ],
];

return $cache;