<?php
namespace WeChat\Utils\WeChat\Semantic;

use WeChat\Utils\WeChat\Core\AbstractAPI;

/**
 * Class Semantic.
 */
class Semantic extends AbstractAPI
{
    const API_SEARCH = 'https://api.weixin.qq.com/semantic/semproxy/search';

    /**
     * Get the semantic content of giving string.
     *
     * @param string $keyword
     * @param array|string $categories
     * @param array $other
     *
     * @return array
     */
    public function query($keyword, $categories, array $other = [])
    {
        $params = [
            'query' => $keyword,
            'category' => implode(',', (array)$categories),
            'appid' => $this->getAccessToken()->getAppId(),
        ];

        return $this->parseJSON('json', [self::API_SEARCH, array_merge($params, $other)]);
    }
}
