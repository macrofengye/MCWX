<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21
 * Time: 18:02
 */

namespace Core\Boot;

use Doctrine\Common\Cache\ArrayCache;
use Slim\Container;
use Core\Providers\InitAppService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Monolog\Logger;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Tools\Setup;

final class Application
{
    const REDIS = "redis";
    const MEMCACHE = "memcache";
    const MEMCACHED = 'memcached';

    /**
     * 整个应用的实例
     *
     * @var $this
     */
    protected static $instance;

    /**
     * 应用的服务容器
     *
     * @var null
     */
    private $container = NULL;

    /**
     * 引导WEB应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public function start()
    {
        try {
            $this->initEnvironment();
            $this->getContainer('routerFile');
            $this->getContainer('app')->run();
        } catch (\Exception $e) {
            echo \GuzzleHttp\json_encode(['code' => 1000, 'msg' => $e->getMessage(), 'data' => []]);
            return false;
        }
        if ($this->getConfig('customer')['show_use_memory']) {
            echo "分配内存量 : " . convert(memory_get_usage(true));
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "内存的峰值 : " . convert(memory_get_peak_usage(true));
        }
    }

    /**
     * 启动控制台，包括单元测试及其他的控制台程序(定时任务等...)
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public function startConsole()
    {
        try {
            $this->initEnvironment();
        } catch (\Exception $e) {
            echo \GuzzleHttp\json_encode(['code' => 1000, 'msg' => $e->getMessage(), 'data' => []]);
            return false;
        }
        if ($this->getConfig('customer')['show_use_memory']) {
            echo "分配内存量 : " . convert(memory_get_usage(true));
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "内存的峰值 : " . convert(memory_get_peak_usage(true));
        }
    }


    /**
     * 初始化应用的环境
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private function initEnvironment()
    {
        if (APPLICATION_ENV == 'production') {
            if (!ini_get('display_errors')) {
                ini_set('display_errors', 'off');
            }
            error_reporting(0);
        } else {
            if (!ini_get('display_errors')) {
                ini_set('display_errors', 'on');
            }
            error_reporting(E_ALL);
        }
        register_shutdown_function('fatal_handler');
        $this->container = new Container();
        $this->container->register(new InitAppService());
        $this->container['application'] = $this;
        static::setInstance($this);
    }

    /**
     * 根据不同的数据库链接类型，实例化不同的数据库链接对象
     *
     * @param $dbName string
     * @throws \Doctrine\ORM\ORMException
     * @return EntityManager
     */
    public function getDbInstance($dbName)
    {
        if (!$this->getContainer('entityManager-' . $dbName)) {
            $dbConfig = $this->getConfig('db')[APPLICATION_ENV];
            $entityManager = NULL;
            if (isset($dbConfig[$dbName]) && $dbConfig[$dbName]) {
                $doctrineConfig = $this->getConfig('doctrine');
                $connConfig = $dbConfig[$dbName] ? $dbConfig[$dbName] : [];
                $useSimpleAnnotationReader = $connConfig['useSimpleAnnotationReader'];
                unset($connConfig['useSimpleAnnotationReader']);
                if (APPLICATION_ENV == "development") {
                    $cache = new ArrayCache();
                } else {
                    $cache = $this->getContainer($doctrineConfig['metadata_cache']['cache_name'], ['database' => $doctrineConfig['metadata_cache']['database']]);
                }
                $configuration = Setup::createAnnotationMetadataConfiguration([
                    ROOT_PATH . '/entity/Models',
                ], APPLICATION_ENV == 'development', ROOT_PATH . '/entity/Proxies/', $cache, $useSimpleAnnotationReader);
                $entityManager = EntityManager::create($connConfig, $configuration, $this->getContainer("eventManager"));
            }
            $this->container["entityManager-" . $dbName] = $entityManager;
        }
        return $this->container["entityManager-" . $dbName];
    }

    /**
     * 获取指定键的配置文件
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $key
     * @return mixed
     */
    public function getConfig($key)
    {
        if (!$this->getContainer('config')->get($key)) {
            writeLog('获取', [$key . '--不存在!', debug_backtrace()], APP_PATH . '/log/config.log', Logger::ERROR);
            return NULL;
        }
        return $this->getContainer('config')->get($key);
    }

    /**
     * 添加自定义监听器
     *
     * @author macro chen <macro_fengye@163.com>
     * @param array $params
     * @throws \Exception
     * @return EventManager
     */
    public function addEvent(array $params = [])
    {
        $eventManager = $this->getContainer('doctrineEventManager');
        $reflect = null;
        foreach ($params as $key => $value) {
            if (!isset($value['class_name'])) {
                throw new \Exception("class_name必须设置");
            }
            $class_name = $value['class_name'];
            $data = isset($value['data']) ? $value['data'] : [];
            if ($reflect == null) {
                $reflect = new \ReflectionClass(Events::class);
            }
            if ($reflect->getConstant($key)) {
                if (!isset($value['type'])) {
                    throw new \Exception("type必须设置");
                }
                if (!isset($value['dbName'])) {
                    throw new \Exception("dbName必须设置");
                }
                $db_eventManager = $this->getDbInstance($value['dbName'])->getEventManager();
                $db_eventManager->addEventListener($key, new $class_name($data));
                continue;
            }
            $eventManager->addEventListener($key, new $class_name($data));
        }
        return $eventManager;
    }

    /**
     * 添加自定义订阅器
     *
     * @author macro chen <macro_fengye@163.com>
     * @param array $params
     * @throws \Exception
     * @return EventManager
     */
    public function addSubscriber(array $params = [])
    {
        $eventManager = $this->getContainer('doctrineEventManager');
        $reflect = null;
        foreach ($params as $key => $value) {
            if (!isset($value['class_name'])) {
                throw new \Exception("class_name必须设置");
            }
            $className = $value['class_name'];
            $data = $value['data'];
            if ($reflect == null) {
                $reflect = new \ReflectionClass(Events::class);
            }
            if ($reflect->getConstant($key)) {
                if (!isset($value['type'])) {
                    throw new \Exception("type必须设置");
                }
                if (!isset($value['dbName'])) {
                    throw new \Exception("dbName必须设置");
                }
                $dbEventManager = $this->getDbInstance($value['dbName'])->getEventManager();
                $dbEventManager->addEventSubscriber(new $className($data));
                continue;
            }
            $eventManager->addEventSubscriber(new $className($data));
        }
        return $eventManager;
    }

    /**
     * 获取拥有命名明空间的缓存实例
     *
     * @param $cacheType
     * @param array $params
     * @deprecated
     * @throws \Exception
     * @return mixed
     */
    public function getCacheInstanceHaveNamespace($cacheType, array $params = [])
    {
        if (!isset($params['resource_id'])) {
            throw new \Exception('资源ID必须设置', 400);
        }
        $resourceId = $params['resource_id'];
        unset($params['resource_id']);
        $cache = $this->getContainer($cacheType . 'Cache', $params)->getOptions()->getResourceManager()->getResource($resourceId);
        return $cache;
    }

    /**
     * 获取指定组件名字的对象
     *
     * @param $componentName
     * @param array $param
     * @return mixed|null
     */
    public function getContainer($componentName, $param = [])
    {
        if (!$this->container->has($componentName)) {
            if (!defined('SERVICE_NAMESPACE')) define('SERVICE_NAMESPACE', APP_NAME);
            $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $componentName)))));
            if (class_exists(SERVICE_NAMESPACE . '\\Providers\\' . $className . "Service")) {
                $className = SERVICE_NAMESPACE . '\\Providers\\' . $className . "Service";
            } else if (class_exists('Core\\Providers\\' . $className . "Service")) {
                $className = 'Core\\Providers\\' . $className . "Service";
            }
            if ($className) {
                $this->container->register(new $className(), $param);
            } else {
                return null;
            }
        }
        $cacheObj = $this->container->get($componentName);
        if ($componentName === self::REDIS && isset($param['database']) && $param['database']) {
            $cacheObj->select($param['database']);
        }
        return $cacheObj;
    }

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param Application $application
     * @return static
     */
    public static function setInstance($application = null)
    {
        return static::$instance = $application;
    }
}