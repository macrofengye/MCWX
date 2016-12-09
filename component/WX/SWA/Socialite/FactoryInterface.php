<?php
namespace MComponent\WX\SWA\Socialite;

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
     * @return \MComponent\WX\SWA\Socialite\
     */
    public function driver($driver = null);
}
