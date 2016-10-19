<?php
/**
 * User: macro
 * Date: 16-4-29
 * Time: 上午10:35
 */
include_once 'Boot/bootstrap.task.php';

class Test extends \Task\Boot\BootTask
{
    private $shard_id = 1;

    public function run()
    {
        /*$em = $this->getDbInstance('entityManager', 'db1');
        $shardManager = new \Doctrine\DBAL\Sharding\PoolingShardManager($em->getConnection());
        $shardManager->selectGlobal();
        $shardManager->selectShard($this->shard_id);
        $query = $em->createQuery("SELECT admin FROM Admin\\Entity\\Admin admin ORDER BY admin.id ASC")->setFirstResult(0)->setMaxResults(100);
        $query->setResultCacheLifetime(3600);
        $query->setResultCacheId('admin_index_query_result' . $this->shard_id);
        $data = new \Doctrine\ORM\Tools\Pagination\Paginator($query, true);
        $count = count($data);
        print_r($count);*/

        /*$redis = $this->getContainer("redisCache");
        $redis = $redis->getOptions()->getResourceManager()->getResource('default');
        $redis->rpush('submit_order' , '201605250859498864');*/
        //echo "asasa";
        // writeLog('test_message' , ['name'=>'jack'] , APP_PATH.'/log.log');
        //$redis = \Core\Utils\CoreUtils::getContainer('redis' , ['server_name'=>'server1']);
        //$redis->set("key2" , "value2value2value2value2");
        $db1 = $this->app->db('db1');
        //$result = $db1->getConnection()->query('select * from address limit 1')->fetch();
        print_r($db1);
        //$e = $this->getContainer('session');
        //print_r($e);
        echo "aaaa";
    }
}

$test = new Test();
$test->run();