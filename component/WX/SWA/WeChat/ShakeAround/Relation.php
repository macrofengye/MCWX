<?php

namespace MComponent\WX\SWA\WeChat\ShakeAround;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;

/**
 * Class Relation.
 */
class Relation extends AbstractAPI
{
    const API_DEVICE_BINDPAGE = 'https://api.weixin.qq.com/shakearound/device/bindpage';
    const API_RELATION_SEARCH = 'https://api.weixin.qq.com/shakearound/relation/search';

    /**
     * Bind pages for device.
     *
     * @param array $deviceIdentifier
     * @param array $pageIds
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function bindPage(array $deviceIdentifier, array $pageIds)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'page_ids' => $pageIds,
        ];

        return $this->parseJSON('json', [self::API_DEVICE_BINDPAGE, $params]);
    }

    /**
     * Get pageIds by deviceId.
     *
     * @param array $deviceIdentifier
     * @param bool  $raw
     *
     * @return array|\MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function getPageByDeviceId(array $deviceIdentifier, $raw = false)
    {
        $params = [
            'type' => 1,
            'device_identifier' => $deviceIdentifier,
        ];

        $result = $this->parseJSON('json', [self::API_RELATION_SEARCH, $params]);

        if ($raw === true) {
            return $result;
        }
        $page_ids = array();
        if (!empty($result->data['relations'])) {
            foreach ($result->data['relations'] as $item) {
                $page_ids[] = $item['page_id'];
            }
        }

        return $page_ids;
    }

    /**
     * Get devices by pageId.
     *
     * @param int $pageId
     * @param int $begin
     * @param int $count
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function getDeviceByPageId($pageId, $begin, $count)
    {
        $params = [
            'type' => 2,
            'page_id' => intval($pageId),
            'begin' => intval($begin),
            'count' => intval($count),
        ];

        return $this->parseJSON('json', [self::API_RELATION_SEARCH, $params]);
    }
}
