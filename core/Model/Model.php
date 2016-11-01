<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26
 * Time: 19:42
 */

namespace Core\Model;

use Core\Utils\Constants;

class Model
{
    /**
     * 数据库表名
     *
     * @var string
     */
    protected $table = '';

    /**
     * 验证规则
     *
     * @var array
     */
    protected $rules = [];

    /**
     * 应用APP
     *
     * @var null
     */
    protected $app = null;

    /**
     * 验证组件
     *
     * @var null
     */
    protected $validator = null;

    /**
     * 要验证的对象
     *
     * @var null
     */
    protected $validateObj = null;

    /**
     * 表单字段映射
     *
     * @var array
     */
    protected $mappingField = [];

    /**
     * 表的主键
     *
     * @var string
     */
    protected $primaryKey = '';

    /**
     * 验证器的分组
     *
     * @var array
     */
    protected $validateGroups = [];

    /**
     * 模型构造函数
     *
     * Model constructor.
     */
    public function __construct()
    {
        $this->app = app();
        $this->validator = $this->app->component('validator');
        $this->validateObj = $this->app->entity($this->table);
    }

    /**
     * 生成数据库表的实体对象
     *
     * @param int $target
     * @param array $data
     *
     * @throws \Exception
     * @return array
     */
    protected function make($target = Constants::MODEL_FIELD, array $data = [])
    {
        try {
            return $this->validate($target, $data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $cls
     *
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
        } elseif (class_exists('Core\\Constraints\\' . $cls)) {
            $class = 'Core\\Constraints\\' . $cls;
            return $class;
        }
        return $class;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \Exception
     */
    private function mergeParams(array $data = [])
    {
        $data ?: $data = $this->app->component('request')->getParams();
        if ($this->mappingField) {
            $combineData = [];
            foreach ($data as $key => $value) {
                isset($this->mappingField[$key]) ? $combineData[$this->mappingField[$key]] = $value : $combineData[$key] = $value;
            }
            return $combineData;
        }
        return $data;
    }

    /**
     * 给实体验证对象设置值
     *
     * @param int $target
     * @param array $data
     *
     * @throws \Exception
     * @return array
     */
    private function validate($target = Constants::MODEL_FIELD, array $data = [])
    {
        $data = $this->mergeParams($data);
        $returnData = [];
        switch ($target) {
            case Constants::MODEL_FIELD:
                $returnData = $this->validateFields($data);
                break;
            case Constants::MODEL_OBJECT:
                $returnData = $this->validateObject($data);
                break;
            default:
                break;
        }

        return $returnData;
    }

    /**
     * 验证数据字段
     *
     * @param array $data
     *
     * @return array
     */
    private function validateFields(array $data = [])
    {
        $returnData = [];
        foreach ($data as $key => $val) {
            if (isset($this->rules[$key])) {
                $constraints = [];
                foreach ($this->rules[$key] as $cls => $params) {
                    if (is_numeric($cls)) {
                        $cls = $params;
                        $params = null;
                    }
                    $class = $this->getConstraintClass($cls);
                    if (!empty(trim($class))) {
                        $constraints[] = new $class($params);
                    }
                }
                $error = $this->validator->validate($val, $constraints);
                if (count($error)) {
                    foreach ($error as $obj) {
                        $returnData[$key] = $obj->getMessage();
                    }
                }
            }
        }

        return $returnData;
    }

    /**
     * 给对象赋值并且验证对象的值是否合法
     *
     * @param array $data
     *
     * @return array
     */
    private function validateObject(array $data = [])
    {
        $returnData = [];
        foreach ($data as $k => $v) {
            $setMethod = 'set' . ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $k)))));
            if (method_exists($this->validateObj, $setMethod)) {
                $this->validateObj->$setMethod($v);
            }
        }
        $classMetadata = $this->validator->getMetadataFor($this->validateObj);
        if (!empty($this->rules)) {
            foreach ($this->rules as $property => $constraint) {
                $constraints = [];
                foreach ($constraint as $cls => $params) {
                    if (is_numeric($cls)) {
                        $cls = $params;
                        $params = null;
                    }
                    $class = $this->getConstraintClass($cls);
                    if (!empty(trim($class))) {
                        $constraints[] = new $class($params);
                    }
                }
                $classMetadata->addPropertyConstraints($property, $constraints);
            }
        }
        $errors = $this->validator->validate($this->validateObj);
        if (count($errors)) {
            foreach ($errors as $obj) {
                $returnData[$obj->getPropertyPath()] = $obj->getMessage();
            }
        }

        return $returnData;
    }
}