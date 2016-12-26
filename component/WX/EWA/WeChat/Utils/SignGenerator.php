<?php

namespace MComponent\WX\EWA\WeChat\Utils;

/**
 * 签名生成器（专门用于生成微信各种签名）
 */
class SignGenerator extends Attributes
{
    /**
     * 加密类型.
     */
    protected $hashType = 'md5';

    /**
     * 是否转为大写.
     */
    protected $isUpper = true;

    /**
     * 排序回调函数.
     */
    protected $sortAfterCallback;

    public function __construct(array $params = array())
    {
        $this->attributes = $params;
    }

    /**
     * 移除一项.
     *
     * @param $key
     *
     * @return $this
     */
    public function removeParams($key)
    {
        unset($this->attributes[$key]);

        return $this;
    }

    /**
     * 设置加密类型.
     *
     * @param $hashType
     *
     * @throws \Exception
     */
    public function setHashType($hashType)
    {
        $type = strtolower($hashType);
        if (!in_array($type, array('md5', 'sha1'), true)) {
            throw new \Exception(sprintf('Invalid Hash Type %s', $hashType));
        }
        $this->hashType = $type;
    }

    /**
     * 是否转为大写.
     *
     * @param $value
     *
     * @return bool
     */
    public function setUpper($value)
    {
        return $this->isUpper = (bool)$value;
    }

    /**
     * 将全部项目排序.
     */
    public function sortable()
    {
        ksort($this->attributes);
        if (is_callable($this->sortAfterCallback)) {
            call_user_func($this->sortAfterCallback, $this);
        }
    }

    /**
     * 排序之后调用（事件）.
     *
     * @param callable $callback
     */
    public function onSortAfter($callback)
    {
        $this->sortAfterCallback = $callback;
    }

    /**
     * 获取签结果.
     *
     * @return string
     */
    public function getResult()
    {
        $this->sortable();
        $query = http_build_query($this->attributes);
        $query = urldecode($query);
        $result = call_user_func($this->hashType, $query);

        return $this->isUpper ? strtoupper($result) : $result;
    }
}
