<?php
//数据库配置
$db = [
    'db' => array(
        // 开发模式
        'development' => array(
            "db1" => array(
                'wrapperClass' => 'Doctrine\DBAL\Sharding\PoolingShardConnection',
                'shardChoser' => 'Doctrine\DBAL\Sharding\ShardChoser\MultiTenantShardChoser',
                'driver' => 'pdo_mysql',
                'host' => '10.0.25.2',
                'port' => 3308,
                'user' => 'root',
                'password' => '111111',
                'dbname' => 'zdai',
                "charset" => "UTF8",
                'global' => array(
                    'driver' => 'pdo_mysql',
                    'host' => '10.0.25.2',
                    'port' => 3308,
                    'dbname' => 'zdai',
                    'user' => 'root',
                    'password' => '111111',
                    'charset' => 'UTF8'
                ),
                'shards' => array(
                    array(
                        'id' => 1,
                        'driver' => 'pdo_mysql',
                        'host' => '10.0.25.2',
                        'user' => 'root',
                        'password' => '111111',
                        'dbname' => 'zdai',
                        'charset' => 'UTF8',
                        'port' => 3308
                    ),
                    array(
                        'id' => 2,
                        'driver' => 'pdo_mysql',
                        'host' => '10.0.25.2',
                        'user' => 'root',
                        'password' => '111111',
                        'dbname' => 'test',
                        'charset' => 'UTF8',
                        'port' => 3308
                    )
                ),
                "useSimpleAnnotationReader" => true,
            ),

            "db2" => array(
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'dbname' => 'xiaofei',
                "charset" => "UTF8",
                'sharding' => array(
                    'federationName' => 'my_database',
                    'distributionKey' => 'customer_id',
                ),
                "useSimpleAnnotationReader" => false
            ),
            "db3" => array(
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'dbname' => 'xiaofei',
                "charset" => "UTF8",
                'sharding' => array(
                    'federationName' => 'my_database',
                    'distributionKey' => 'customer_id',
                ),
                "useSimpleAnnotationReader" => true
            )),
        // 生产模式
        'production' => array(
            "db1" => array(
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'dbname' => 'xiaofei',
                "charset" => "UTF8",
                'sharding' => array(
                    'federationName' => 'my_database',
                    'distributionKey' => 'customer_id',
                ),
                "useSimpleAnnotationReader" => false
            ),
            "db2" => array(
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'username',
                'password' => 'password',
                'dbname' => 'production_dbname',
                "charset" => "UTF8",
                'sharding' => array(
                    'federationName' => 'my_database',
                    'distributionKey' => 'customer_id',
                ),
                "useSimpleAnnotationReader" => true
            )
        ),
    ),
];

return $db;