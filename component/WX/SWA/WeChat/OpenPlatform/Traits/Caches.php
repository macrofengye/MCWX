<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform\Traits;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;

trait Caches
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Sets cache store.
     *
     * @param Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Gets cache store. Defaults to file system in system temp folder.
     * @throws \Exception
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * Gets the cached data.
     *
     * @param string $key
     * @param mixed $default A default value or a callable to return the value.
     * @throws \Exception
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($cached = $this->getCache()->fetch($key)) {
            return $cached;
        }
        if (is_callable($default)) {
            return $default();
        }
        return $default;
    }

    /**
     * Sets the cached data.
     *
     * @param string $key
     * @param mixed $value
     * @param int $life Cache life time in seconds.
     * @throws \Exception
     * @return bool
     */
    public function set($key, $value, $life = 0)
    {
        return $this->getCache()->save($key, $value, $life);
    }

    /**
     * Removes the cached data.
     *
     * @param string $key
     * @throws \Exception
     * @return bool
     */
    public function remove($key)
    {
        return $this->getCache()->delete($key);
    }
}