<?php

namespace WeChat\Utils\Socialite;

/**
 * Interface AccessTokenInterface.
 */
interface AccessTokenInterface
{
    /**
     * Return the access token string.
     *
     * @return string
     */
    public function getToken();
}
