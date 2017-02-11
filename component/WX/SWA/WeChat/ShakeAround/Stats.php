<?php

namespace MComponent\WX\SWA\WeChat\ShakeAround;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;

/**
 * Class Stats.
 */
class Stats extends AbstractAPI
{
    const API_DEVICE = 'https://api.weixin.qq.com/shakearound/statistics/device';
    const API_DEVICE_LIST = 'https://api.weixin.qq.com/shakearound/statistics/devicelist';
    const API_PAGE = 'https://api.weixin.qq.com/shakearound/statistics/page';
    const API_PAGE_LIST = 'https://api.weixin.qq.com/shakearound/statistics/pagelist';

    /**
     * Fetch statistics data by deviceId.
     *
     * @param array $deviceIdentifier
     * @param int   $beginDate        (Unix timestamp)
     * @param int   $endDate          (Unix timestamp)
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function deviceSummary(array $deviceIdentifier, $beginDate, $endDate)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'begin_date' => $beginDate,
            'end_date' => $endDate,
        ];

        return $this->parseJSON('json', [self::API_DEVICE, $params]);
    }

    /**
     * Fetch all devices statistics data by date.
     *
     * @param int $timestamp
     * @param int $pageIndex
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function batchDeviceSummary($timestamp, $pageIndex)
    {
        $params = [
            'date' => $timestamp,
            'page_index' => $pageIndex,
        ];

        return $this->parseJSON('json', [self::API_DEVICE_LIST, $params]);
    }

    /**
     * Fetch statistics data by pageId.
     *
     * @param int $pageId
     * @param int $beginDate (Unix timestamp)
     * @param int $endDate   (Unix timestamp)
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function pageSummary($pageId, $beginDate, $endDate)
    {
        $params = [
            'page_id' => $pageId,
            'begin_date' => $beginDate,
            'end_date' => $endDate,
        ];

        return $this->parseJSON('json', [self::API_PAGE, $params]);
    }

    /**
     * Fetch all pages statistics data by date.
     *
     * @param int $timestamp
     * @param int $pageIndex
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function batchPageSummary($timestamp, $pageIndex)
    {
        $params = [
            'date' => $timestamp,
            'page_index' => $pageIndex,
        ];

        return $this->parseJSON('json', [self::API_PAGE_LIST, $params]);
    }
}
