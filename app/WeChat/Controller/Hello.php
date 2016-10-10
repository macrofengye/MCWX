<?php
namespace Blog\Controller;


use Core\Controller\Controller;
use Core\Utils\CoreUtils;

class Hello extends Controller
{
    public function show($request, $response, $args)
    {
        echo "aaa";
        print_r(CoreUtils::getContainer('session')->get('user_info'));
    }

    public function show1($request, $response, $args)
    {
        $app = Bootstrap::getApp();
        $view = $app->getContainer()->get('view');
        $view->render($response, 'profile.twig', [
            'name' => "aaaaaa"
        ]);
        echo "aaaa";
    }

    public function getItems($request, $response, $args)
    {
        $em = $this->getDbInstance(self::ENTITY, "db1");
        print_r($em);
        $query = $em->createQuery('SELECT u FROM Blog\Entity\Actor u WHERE u.actor_id = ?1');
        $query->setParameter(1, 1);
        $query->setResultCacheDriver($this->getPimple("redisCacheDriver"));
        $query->useResultCache(true)
            ->setResultCacheLifeTime($seconds = 3600);
        //$result = $query->getResult(); // cache miss
        //$query->expireResultCache(true);
        //$result = $query->getResult(); // forced expire, cache miss
        //$query->setResultCacheId('aaaaaaa');
        $result = $query->getResult(); // saved in given result cache id.
        $redis = $this->getPimple("redisCache");
        foreach ($result as $value) {
            print_r($value->getLastName());
        }
        $redis->setItem("key1", "Key Key...");
        $redis->setItem("key2", "sdadadasd");
        echo $redis->getItem("key1");

        $this->getPimple("serviceManager");
        return;
// or call useResultCache() with all parameters:
        $query->useResultCache(true, $seconds = 3600, 'my_query_result');
        $result = $query->getResult(); // cache hit!

// Introspection
        $queryCacheProfile = $query->getQueryCacheProfile();
        $cacheDriver = $query->getResultCacheDriver();
        $lifetime = $query->getLifetime();


    }

}