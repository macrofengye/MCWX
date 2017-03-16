<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform;

use Doctrine\Common\Cache\Cache;
use MComponent\WX\SWA\WeChat\Core\Exceptions\RuntimeException;
use MComponent\WX\SWA\WeChat\OpenPlatform\Traits\Caches;
use MComponent\WX\SWA\WeChat\Support\Collection;

class VerifyTicket
{
    use Caches;

    /**
     * App Id.
     *
     * @var string
     */
    protected $appId;

    /**
     * Cache Key.
     *
     * @var string
     */
    private $cacheKey;

    /**
     * Component verify ticket xml structure name.
     *
     * @var string
     */
    protected $ticketXmlName = 'ComponentVerifyTicket';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'wechat.open_platform.component_verify_ticket.';

    /**
     * VerifyTicket constructor.
     *
     * @param string $appId
     * @param Cache $cache
     */
    public function __construct($appId, Cache $cache = null)
    {
        $this->appId = $appId;
        $this->setCache($cache);
    }

    /**
     * Save component verify ticket.
     *
     * @param Collection $message
     *
     * @return bool
     */
    public function cache(Collection $message)
    {
        return $this->set(
            $this->getCacheKey(),
            $message->get($this->ticketXmlName)
        );
    }

    /**
     * Get component verify ticket.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getTicket()
    {
        if ($cached = $this->get($this->getCacheKey())) {
            return $cached;
        }
        throw new RuntimeException('Component verify ticket does not exists.');
    }

    /**
     * Set component verify ticket cache key.
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
     * Get component verify ticket cache key.
     *
     * @return string $this->cacheKey
     */
    public function getCacheKey()
    {
        if (null === $this->cacheKey) {
            return $this->prefix . $this->appId;
        }
        return $this->cacheKey;
    }
}
