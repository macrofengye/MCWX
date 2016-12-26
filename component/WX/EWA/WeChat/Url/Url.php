<?php

namespace MComponent\WX\EWA\WeChat\Url;

use MComponent\WX\EWA\WeChat\Core\AccessToken;
use MComponent\WX\EWA\WeChat\Core\Http;

/**
 * 链接.
 */
class Url
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    const API_SHORT_URL = 'https://api.weixin.qq.com/cgi-bin/shorturl';

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 转短链接.
     *
     * @param string $url
     *
     * @return string
     */
    public function short($url)
    {
        $params = array(
            'action' => 'long2short',
            'long_url' => $url,
        );
        $response = $this->http->jsonPost(self::API_SHORT_URL, $params);
        return $response['short_url'];
    }

    /**
     * 获取当前URL.
     *
     * @return string
     */
    public static function current()
    {
        $request = app()->component('request');
        $protocol = (!empty($request->getServerParam('HTTPS')) && $request->getServerParam('HTTPS') !== 'off' || $request->getServerParam('SERVER_PORT') === 443) ? 'https://' : 'http://';
        if ($request->getServerParam('HTTP_X_FORWARDED_HOST')) {
            $host = $request->getServerParam('HTTP_X_FORWARDED_HOST');
        } else {
            $host = $request->getServerParam('HTTP_HOST');
        }
        return $protocol . $host . $request->getServerParam('REQUEST_URI');
    }
}
