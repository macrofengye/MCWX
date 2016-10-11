<?php

namespace WeChat\Utils\WeChat\Support;

/**
 * Class Url.
 */
class Url
{
    /**
     * Get current url.
     *
     * @return string
     */
    public static function current()
    {
        $protocol = (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || (int)$_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
