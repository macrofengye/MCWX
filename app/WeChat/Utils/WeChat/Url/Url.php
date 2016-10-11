<?php
namespace WeChat\Utils\WeChat\Url;

use WeChat\Utils\WeChat\Core\AbstractAPI;

/**
 * Class Url.
 */
class Url extends AbstractAPI
{
    const API_SHORTEN_URL = 'https://api.weixin.qq.com/cgi-bin/shorturl';

    /**
     * Shorten the url.
     *
     * @param string $url
     *
     * @return string
     */
    public function shorten($url)
    {
        $params = [
            'action' => 'long2short',
            'long_url' => $url,
        ];

        return $this->parseJSON('json', [self::API_SHORTEN_URL, $params]);
    }
}
