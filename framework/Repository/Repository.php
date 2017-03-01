<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 16-12-12
 * Time: 上午8:55
 */

namespace Polymer\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Util\Inflector;
use Exception;
use Polymer\Exceptions\PresenterException;

class Repository extends EntityRepository
{
    /**
     * View presenter instance
     *
     * @var mixed
     */
    protected $presenterInstance;

    /**
     * 验证查询字段的值
     *
     * @param array $rules
     */
    public function validate(array $rules = [])
    {
    }

    /**
     *  动态调用函数
     *
     * @example 调用方式  $this->getByAge(28) 查询age=28的值
     * @example 调用方式  $this->getByFieldAge(28 , 'name'); 获取name的值,查询条件是age=28
     * @param string $method
     * @param array $arguments
     * @throws Exception
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        try {
            if (0 === stripos($method, 'getBy')) {
                // 根据某个字段获取记录
                $field = $this->parseName(substr($method, 5));
                if ($arguments) {
                    $where[$field] = $arguments[0];
                    return $this->getBy($where);
                } else {
                    throw new \InvalidArgumentException($method . '方法需要传入一个非空参数');
                }
            } elseif (0 === stripos($method, 'getFieldBy')) {
                // 根据某个字段获取记录的某个值
                $field = $this->parseName(substr($method, 10));
                if (2 === count($arguments)) {
                    $where[$field] = $arguments[0];
                    return $this->getBy($where, $arguments[1]);
                } else {
                    throw new \InvalidArgumentException($method . '方法需要传入两个非空参数');
                }
            } else {
                return parent::__call($method, $arguments);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取指定对象的信息
     *
     * @param array $fieldWhere 查询对象的字段条件
     * @param string $fieldTarget 默认为空，若指定则只返回指定字段的值
     * @throws Exception
     * @return mixed
     */
    protected function getBy(array $fieldWhere, $fieldTarget = '')
    {
        $object = $this->findOneBy($fieldWhere);
        if ($object && $fieldTarget) {
            $method = 'get' . lcfirst(Inflector::classify($fieldTarget));
            if (method_exists($object, $method)) {
                return $object->$method();
            } else {
                throw new \InvalidArgumentException('查询字段' . $fieldTarget . '不存在');
            }
        }
        return $object;
    }

    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     *
     * @param string $name 字符串
     * @param integer $type 转换类型
     * @return string
     */
    protected function parseName($name, $type = 0)
    {
        if ($type) {
            return ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $name)))));
        } else {
            return strtolower(trim(preg_replace('/[A-Z]/', '_\\0', $name), '_'));
        }
    }

    /**
     * Prepare a new or cached presenter instance
     *
     * @param $entity
     * @return mixed
     * @throws PresenterException
     */
    public function present($entity)
    {
        if (!$this->presenter || !class_exists($this->presenter)) {
            throw new PresenterException('Please set the $presenter property to your presenter path.');
        }
        if (!$this->presenterInstance) {
            $this->presenterInstance = new $this->presenter($entity);
        }
        return $this->presenterInstance;
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
