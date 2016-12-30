<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/9/21
 * Time: 18:02
 */

namespace Polymer\Boot;

use Doctrine\ORM\ORMException;
use Polymer\Providers\InitAppProvider;
use Polymer\Utils\Constants;
use Doctrine\Common\Cache\ArrayCache;
use Slim\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Monolog\Logger;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Tools\Setup;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

final class Application
{
    /**
     * 整个应用的实例
     *
     * @var $this
     */
    protected static $instance;

    /**
     * 应用的服务容器
     *
     * @var Container
     */
    private $container;

    /**
     * 启动WEB应用
     *
     * @author macro chen <macro_fengye@163.com>
     * @return boolean
     */
    public function start()
    {
        try {
            $this->initEnvironment();
            $this->component('routerFile');
            $this->component('app')->run();
        } catch (\Exception $e) {
            echo \GuzzleHttp\json_encode(['code' => 1000, 'msg' => $e->getMessage(), 'data' => []]);
            return false;
        }
        if ($this->config('customer.show_use_memory')) {
            echo '分配内存量 : ' . convert(memory_get_usage(true));
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '内存的峰值 : ' . convert(memory_get_peak_usage(true));
        }
        return true;
    }

    /**
     * 启动控制台，包括单元测试及其他的控制台程序(定时任务等...)
     *
     * @author macro chen <macro_fengye@163.com>
     * @return boolean
     */
    public function startConsole()
    {
        try {
            $this->initEnvironment();
        } catch (\Exception $e) {
            echo \GuzzleHttp\json_encode(['code' => 1000, 'msg' => $e->getMessage(), 'data' => []]);
            return false;
        }
        if ($this->config('customer.show_use_memory')) {
            echo '分配内存量 : ' . convert(memory_get_usage(true));
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '内存的峰值 : ' . convert(memory_get_peak_usage(true));
        }
        return true;
    }


    /**
     * 初始化应用的环境
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private function initEnvironment()
    {
        if (APPLICATION_ENV === 'production') {
            ini_set('display_errors', 'off');
            error_reporting(0);
        } else {
            ini_set('display_errors', 'on');
            error_reporting(E_ALL);
        }
        set_error_handler('handleError');
        set_exception_handler('handleException');
        register_shutdown_function('handleShutdown');
        $this->container = new Container();
        $this->container->register(new InitAppProvider());
        $this->component('config');
        $this->container['application'] = $this;
        static::setInstance($this);
    }

    /**
     * 实例化数据库链接对象
     *
     * @param string $dbName
     * @param string $folder 实体文件夹的名字
     * @throws \Doctrine\ORM\ORMException | \InvalidArgumentException
     * @return EntityManager
     */
    public function db($dbName, $folder = 'Models')
    {
        if (!$this->component('entityManager-' . $dbName)) {
            $dbConfig = $this->config('db.' . APPLICATION_ENV);
            if (isset($dbConfig[$dbName]) && $dbConfig[$dbName]) {
                $connConfig = $dbConfig[$dbName] ?: [];
                $useSimpleAnnotationReader = $connConfig['useSimpleAnnotationReader'];
                unset($connConfig['useSimpleAnnotationReader']);
                if (APPLICATION_ENV === 'development') {
                    $cache = new ArrayCache();
                } else {
                    $cacheName = $this->config('doctrine.metadata_cache.cache_name');
                    $database = $this->config('doctrine.metadata_cache.database');
                    $cache = $this->component($cacheName, ['database' => $database]);
                }
                $configuration = Setup::createAnnotationMetadataConfiguration([
                    ROOT_PATH . '/entity/' . $folder,
                ], APPLICATION_ENV === 'development', ROOT_PATH . '/entity/Proxies/', $cache, $useSimpleAnnotationReader);
                try {
                    $entityManager = EntityManager::create($connConfig, $configuration, $this->component('eventManager'));
                    $this->container['database_name'] = $dbName;
                    $this->container['entityManager-' . $dbName] = $entityManager;
                } catch (\InvalidArgumentException $e) {
                    return null;
                }
            }
        }
        return $this->container['entityManager-' . $dbName];
    }

    /**
     * 获取指定键的配置文件
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $key
     * @return mixed
     */
    public function config($key)
    {
        if (!$this->component('config')->get($key)) {
            logger('获取', [$key . '--不存在!'], APP_PATH . '/log/config.log', Logger::ERROR);
            return NULL;
        }
        return $this->component('config')->get($key);
    }

    /**
     * 添加自定义监听器
     *
     * @author macro chen <macro_fengye@163.com>
     * @param array $params
     * @throws \InvalidArgumentException | ORMException
     * @return EventManager
     */
    public function addEvent(array $params = [])
    {
        try {
            return $this->addEventOrSubscribe($params, 1);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }
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
        try {
            return $this->addEventOrSubscribe($params, 0);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * 添加事件监听器或者订阅器
     *
     * @param array $params
     * @param int $listener 0 添加事件订阅器 1 添加事件监听器
     * @return mixed|null
     * @throws ORMException | \InvalidArgumentException
     */
    private function addEventOrSubscribe(array $params, $listener)
    {
        $method = $listener ? 'addEventListener' : 'addEventSubscriber';
        $reflect = null;
        foreach ($params as $key => $value) {
            if (!isset($value['class_name'])) {
                throw new \InvalidArgumentException('class_name必须设置');
            }
            $class_name = $value['class_name'];
            $data = isset($value['data']) ? $value['data'] : [];
            if ($reflect === null) {
                $reflect = new \ReflectionClass(Events::class);
            }
            if ($reflect->getConstant($key)) {
                if (!isset($value['type'])) {
                    throw new \InvalidArgumentException('type必须设置');
                }
                if (!isset($value['db_name'])) {
                    throw new \InvalidArgumentException('db_name必须设置');
                }
                try {
                    $listener === 1 ? $this->db($value['db_name'])->getEventManager()->$method($key, new $class_name($data)) : $this->db($value['db_name'])->getEventManager()->$method(new $class_name($data));
                } catch (ORMException $e) {
                    throw $e;
                }
            } else {
                $listener === 1 ? $this->component('eventManager')->{$method}($key, new $class_name($data)) : $this->component('eventManager')->{$method}(new $class_name($data));
            }
        }
        return $this->component('eventManager');
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
            throw new \InvalidArgumentException('资源ID必须设置', 400);
        }
        $resourceId = $params['resource_id'];
        unset($params['resource_id']);
        $cache = $this->component($cacheType . 'Cache', $params)->getOptions()->getResourceManager()->getResource($resourceId);
        return $cache;
    }

    /**
     * 获取指定组件名字的对象
     *
     * @param $componentName
     * @param array $param
     * @return mixed|null
     */
    public function component($componentName, array $param = [])
    {
        if (!$this->container->has($componentName)) {
            !defined('PROVIDERS_NAMESPACE') && define('PROVIDERS_NAMESPACE', APP_NAME);
            $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $componentName)))));
            if (class_exists(PROVIDERS_NAMESPACE . '\\Providers\\' . $className . 'Provider')) {
                $className = PROVIDERS_NAMESPACE . '\\Providers\\' . $className . 'Provider';
            } else if (class_exists('Polymer\\Providers\\' . $className . 'Provider')) {
                $className = 'Polymer\\Providers\\' . $className . 'Provider';
            }
            if (class_exists($className)) {
                $this->container->register(new $className(), $param);
            } else {
                return null;
            }
        }
        try {
            $cacheObj = $this->container->get($componentName);
            if ($componentName === Constants::REDIS) {
                $database = (isset($param['database']) && $param['database']) ? $param['database'] : 0;
                $cacheObj->select($database);
            }
            return $cacheObj;
        } catch (ContainerValueNotFoundException $e) {
            return null;
        } catch (ContainerException $e) {
            return null;
        }
    }

    /**
     * 获取全局可用的应用实例
     *
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * 设置全局可用的应用实例
     *
     * @param Application $application
     * @return static
     */
    public static function setInstance($application = null)
    {
        return static::$instance = $application;
    }


    /**
     * 获取业务模型实例
     * @param string $modelName 模型的名字
     * @param array $parameters 实例化时需要的参数
     * @param string $path 附加路径
     * @return mixed
     */
    public function model($modelName, array $parameters = [], $path = '')
    {
        !defined('BUSINESS_MODEL_NAMESPACE') && define('BUSINESS_MODEL_NAMESPACE', APP_NAME);
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $modelName)))));
        $className = BUSINESS_MODEL_NAMESPACE . '\\Models\\' . ($path ? ucfirst($path) . '\\' : '') . ucfirst($className) . 'Model';
        if (class_exists($className)) {
            return new $className($parameters);
        }
        return null;
    }

    /**
     * 获取实体模型实例
     * @param $tableName
     * @return bool
     */
    public function entity($tableName)
    {
        !defined('ENTITY_NAMESPACE') && define('ENTITY_NAMESPACE', 'Entity\\Models');
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $tableName)))));
        $className = ENTITY_NAMESPACE . '\\' . ucfirst($className);
        if (class_exists($className)) {
            return new $className;
        }
        return null;
    }

    /**
     *
     * 获取EntityRepository
     * @param $entityName
     * @param $db
     * @return \Doctrine\ORM\EntityRepository | null
     */
    public function repository($entityName, $db)
    {
        !defined('REPOSITORIES_NAMESPACE') && define('REPOSITORIES_NAMESPACE', 'Entity\\Repositories');
        !defined('ENTITY_NAMESPACE') && define('ENTITY_NAMESPACE', 'Entity\\Models');
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $entityName)))));
        $repositoryClassName = REPOSITORIES_NAMESPACE . '\\' . ucfirst($className) . 'Repository';
        if (class_exists($repositoryClassName)) {
            try {
                return $this->db($db)->getRepository(ENTITY_NAMESPACE . '\\' . ucfirst($className));
            } catch (ORMException $e) {
                return null;
            } catch (\InvalidArgumentException $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * 获取服务组件
     *
     * @param string $serviceName
     * @param array|null $params
     * @return null | Object
     */
    public function service($serviceName, array $params = null)
    {
        !defined('SERVICES_NAMESPACE') && define('SERVICES_NAMESPACE', APP_NAME . '\\Services');
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $serviceName)))));
        $className = SERVICES_NAMESPACE . '\\' . ucfirst($className) . 'Service';
        if (class_exists($className)) {
            return new $className($params);
        }
        return null;
    }
}