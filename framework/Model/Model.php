<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/10/26
 * Time: 19:42
 */

namespace Polymer\Model;

use Doctrine\DBAL\Sharding\PoolingShardManager;
use Doctrine\ORM\EntityManager;
use Polymer\Boot\Application;
use Polymer\Exceptions\EntityValidateErrorException;
use Polymer\Utils\Constants;
use Symfony\Component\Validator\Exception\NoSuchMetadataException;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class Model
{
    /**
     * 应用APP
     *
     * @var Application
     */
    protected $app = null;

    /**
     * 验证组件
     *
     * @var RecursiveValidator
     */
    protected $validator = null;

    /**
     * 要验证的实体对象
     *
     * @var null
     */
    private $EntityObject = null;

    /**
     * 验证器的规则
     *
     * @var array
     */
    protected $validateRules = [];

    /**
     * EntityManager实例
     *
     * @var EntityManager
     */
    protected $em = null;

    /**
     * 模型构造函数
     *
     * Model constructor.
     */
    public function __construct()
    {
        $this->app = app();
        $this->validator = $this->app->component('validator');
    }

    /**
     * 生成数据库表的实体对象
     *
     * @param int $target
     * @param array $data
     * @param null $entityFolder
     * @param array $validateRules
     *
     * @throws \Exception
     * @return Object
     */
    protected function make($target = Constants::MODEL_FIELD, array $data = [], $entityFolder = null, array $validateRules = [])
    {
        try {
            if (Constants::MODEL_OBJECT) {
                if (null === $entityFolder) {
                    $entityFolder = property_exists($this, 'entityFolder') ? $this->entityFolder : null;
                }
                if (!$this->EntityObject) {
                    $this->EntityObject = $this->app->entity($this->table, $entityFolder);
                }
            }
            $this->validate($target, $data, $validateRules);
            if ($this->app->component('error_collection')->get($this->table)) {
                throw new EntityValidateErrorException('实体验证错误!');
            }
            return $this->EntityObject;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据类名获取类的全名
     *
     * @param string $cls
     * @return string
     */
    private function getConstraintClass($cls = '')
    {
        $class = '';
        if (class_exists('\\Symfony\\Component\\Validator\\Constraints\\' . $cls)) {
            $class = '\\Symfony\\Component\\Validator\\Constraints\\' . $cls;
            return $class;
        } elseif (class_exists(APP_NAME . '\\Constraints\\' . $cls)) {
            $class = APP_NAME . '\\Constraints\\' . $cls;
            return $class;
        } elseif (class_exists('Polymer\\Constraints\\' . $cls)) {
            $class = 'Polymer\\Constraints\\' . $cls;
            return $class;
        }
        return $class;
    }

    /**
     * 合并请求参数数据与自定义参数数据
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function mergeParams(array $data = [])
    {
        $data = array_merge($this->app->component('request')->getParams(), $data);
        if (property_exists($this, 'mappingField')) {
            $combineData = [];
            foreach ($data as $key => $value) {
                isset($this->mappingField[$key]) ? $combineData[$this->mappingField[$key]] = $value : $combineData[$key] = $value;
            }
            return $combineData;
        }
        return $data;
    }

    /**
     * 验证对象,可通过参数指定要验证的目标
     *
     * @param int $target
     * @param array $data
     * @param array $validateRules
     *
     * @throws \Exception
     */
    private function validate($target = Constants::MODEL_FIELD, array $data = [], array $validateRules = [])
    {
        $data = $this->mergeParams($data);
        $target === Constants::MODEL_FIELD ? $this->validateFields($data, $validateRules) : $this->validateObject($data, $validateRules);
    }

    /**
     * 根据自定义的规则验证数据字段
     *
     * @param array $data
     * @param array $rules
     * @return array
     */
    private function validateFields(array $data = [], array $rules = [])
    {
        $returnData = [];
        $this->validateRules = empty($rules) ? $this->rules : $rules;
        if (!empty($this->validateRules)) {
            foreach ($data as $property => $val) {
                if (isset($this->validateRules[$property])) {
                    $constraints = $this->propertyConstraints($property);
                    $errors = $this->validator->validate($val, $constraints);
                    if (count($errors)) {
                        foreach ($errors as $error) {
                            $returnData[$property] = $error->getMessage();
                        }
                    }
                }
            }
        }
        $returnData === [] ?: $this->app->component('error_collection')->set($this->table, $returnData);
    }

    /**
     * 给对象赋值并且验证对象的值是否合法
     *
     * @param array $data
     * @param array $rules
     * @throws NoSuchMetadataException
     * @return array
     */
    private function validateObject(array $data = [], array $rules = [])
    {
        $this->validateRules = empty($rules) ? $this->rules : $rules;
        foreach ($data as $k => $v) {
            $setMethod = 'set' . ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $k)))));
            if (method_exists($this->EntityObject, $setMethod)) {
                $this->EntityObject->$setMethod($v);
            }
        }
        try {
            $classMetadata = $this->validator->getMetadataFor($this->EntityObject);
            foreach ($classMetadata->getReflectionClass()->getProperties() as $val) {
                if (!isset($data[$val->getName()])) {
                    $data[$val->getName()] = '';
                }
            }
            if (!empty($this->validateRules)) {
                foreach ($data as $property => $val) {
                    if (isset($this->validateRules[$property])) {
                        $constraints = $this->propertyConstraints($property);
                        $classMetadata->addPropertyConstraints($property, $constraints);
                    }
                }
            }
            $errors = $this->validator->validate($this->EntityObject);
            if (count($errors)) {
                foreach ($errors as $error) {
                    $returnData[$error->getPropertyPath()] = $error->getMessage();
                }
                $this->app->component('error_collection')->set($this->table, $returnData);
            }
        } catch (NoSuchMetadataException $e) {
            throw $e;
        }
    }

    /**
     * 实例化指定属性的验证器类
     *
     * @param string $property
     * @return array
     */
    private function propertyConstraints($property)
    {
        $constraints = [];
        foreach ($this->validateRules[$property] as $cls => $params) {
            if (is_numeric($cls)) {
                $cls = $params;
                $params = null;
            }
            $class = $this->getConstraintClass($cls);
            if (!empty(trim($class))) {
                $constraints[] = new $class($params);
            }
        }
        return $constraints;
    }

    /**
     * 切换数据库的链接
     *
     * @param int $shardId
     * @return boolean
     * @throws \Exception
     */
    protected function switchConnect($shardId)
    {
        try {
            return $this->em->getConnection()->connect((int)$shardId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取PoolingShardManager实例，用于全局查询
     *
     * @return PoolingShardManager|null
     */
    protected function sharedManager()
    {
        if ($this->em) {
            return new PoolingShardManager($this->em->getConnection());
        }
        return null;
    }

    /**
     * 给对象新增属性
     *
     * @param $propertyName
     * @param $value
     * @return $this
     */
    protected function setProperty($propertyName, $value)
    {
        if (!isset($this->$propertyName)) {
            $this->$propertyName = $value;
        }
        return $this;
    }

    /**
     * 获取对象属性
     *
     * @param $propertyName
     * @return mixed
     */
    protected function getProperty($propertyName)
    {
        if (!isset($this->$propertyName)) {
            return $this->$propertyName;
        }
        return null;
    }

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return $this
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * is triggered by calling isset() or empty() on inaccessible members.
     *
     * @param $name string
     * @return bool
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }
}