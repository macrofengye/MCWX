<?php
namespace WeChat\Utils\Socialite;

/**
 * Interface FactoryInterface.
 */
interface FactoryInterface
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param string $driver
     *
     * @return \WeChat\Utils\Socialite\
     */
    public function driver($driver = null);
}
