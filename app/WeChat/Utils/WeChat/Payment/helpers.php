<?php
namespace WeChat\Utils\WeChat\Payment;

/**
 * Generate a signature.
 *
 * @param array $attributes
 * @param string $key
 * @param string $encryptMethod
 *
 * @return string
 */
function generate_sign(array $attributes, $key, $encryptMethod = 'md5')
{
    ksort($attributes);

    $attributes['key'] = $key;

    return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($attributes))]));
}

/**
 * Get client ip.
 *
 * @return string
 */
function get_client_ip()
{
    // for php-cli(phpunit etc.)
    if (empty($_SERVER['REMOTE_ADDR'])) {
        return gethostbyname(gethostname());
    }

    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Get current server ip.
 *
 * @return string
 */
function get_server_ip()
{
    // for php-cli(phpunit etc.)
    if (empty($_SERVER['SERVER_ADDR'])) {
        return gethostbyname(gethostname());
    }

    return $_SERVER['SERVER_ADDR'];
}
