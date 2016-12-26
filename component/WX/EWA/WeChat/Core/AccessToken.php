<?php

namespace MComponent\WX\EWA\WeChat\Core;

use Doctrine\Common\Cache\FilesystemCache;

/**
 * 全局通用 AccessToken
 */
class AccessToken
{
    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

    /**
     * 缓存类
     *
     * @var FilesystemCache
     */
    protected $cache;

    /**
     * Query name.
     *
     * @var string
     */
    protected $queryName = 'access_token';

    /**
     * Response Json key name.
     *
     * @var string
     */
    protected $tokenJsonKey = 'access_token';

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $cacheKey = 'macrofengye.wechat.access_token';

    // API
    const API_TOKEN_GET = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret, $cache = null)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->cacheKey = $this->cacheKey . '.' . $appId;
        // $this->cache = new FilesystemCache(ROOT_PATH . '/component/WX/' . WX_TYPE . '/cache');
        $this->cache = $cache;
    }

    /**
     * 设置自定义 token.
     *
     * @param string $token
     * @param int $expires
     *
     * @return $this
     */
    public function setToken($token, $expires = 7200)
    {
        $this->getCache()->save($this->getCacheKey(), $token, $expires - 1500);
        return $this;
    }

    /**
     * 缓存 setter
     *
     * @param Cache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Return the cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * 获取Token
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->fetch($cacheKey);
        logger(__FUNCTION__ , ['cached'=>$cached] , APP_PATH.'/log/accessToken.log');
        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();
            $this->getCache()->save($cacheKey, $token[$this->tokenJsonKey], $token['expires_in'] - 1500);
            logger(__FUNCTION__ , ['token'=>$token] , APP_PATH.'/log/accessToken.log');
            return $token[$this->tokenJsonKey];
        }
        return $cached;
    }

    /**
     * Get the access token from WeChat server
     *
     * @return array|bool
     */
    protected function getTokenFromServer()
    {
        $http = new Http();
        $params = array(
            'corpid' => $this->appId,
            'corpsecret' => $this->appSecret,
        );
        $token = $http->get(self::API_TOKEN_GET, $params);
        return $token;
    }

    /**
     * Set the access token prefix.
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set access token cache key.
     *
     * @param string $cacheKey
     *
     * @return $this
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    /**
     * Get access token cache key.
     *
     * @return string $this->cacheKey
     */
    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix . $this->appId;
        }

        return $this->cacheKey;
    }
}
