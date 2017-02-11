<?php

namespace MComponent\WX\SWA\WeChat\ShakeAround;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;

/**
 * Class ShakeAround.
 */
class ShakeAround extends AbstractAPI
{
    const API_ACCOUNT_REGISTER = 'https://api.weixin.qq.com/shakearound/account/register';
    const API_ACCOUNT_AUDIT_STATUS = 'https://api.weixin.qq.com/shakearound/account/auditstatus';
    const API_GET_SHAKE_INFO = 'https://api.weixin.qq.com/shakearound/user/getshakeinfo';

    /**
     * Device instance.
     *
     * @var \MComponent\WX\SWA\WeChat\ShakeAround\Device
     */
    protected $device = null;

    /**
     * Group instance.
     *
     * @var \MComponent\WX\SWA\WeChat\ShakeAround\Group
     */
    protected $group = null;

    /**
     * Page instance.
     *
     * @var \MComponent\WX\SWA\WeChat\ShakeAround\Page
     */
    protected $page = null;

    /**
     * Material instance.
     *
     * @var \MComponent\WX\SWA\WeChat\ShakeAround\Material
     */
    protected $material = null;

    /**
     * Relation instance.
     *
     * @var \MComponent\WX\SWA\WeChat\ShakeAround\Relation
     */
    protected $relation = null;

    /**
     * Stats instance.
     *
     * @var \MComponent\WX\SWA\WeChat\ShakeAround\Stats
     */
    protected $stats = null;

    /**
     * Register shake around.
     *
     * @param string $name
     * @param string $tel
     * @param string $email
     * @param string $industryId
     * @param array  $certUrls
     * @param string $reason
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function register($name, $tel, $email, $industryId, array $certUrls, $reason = '')
    {
        $params = [
            'name' => $name,
            'phone_number' => strval($tel),
            'email' => $email,
            'industry_id' => $industryId,
            'qualification_cert_urls' => $certUrls,
        ];

        if ($reason !== '') {
            $params['apply_reason'] = $reason;
        }

        return $this->parseJSON('json', [self::API_ACCOUNT_REGISTER, $params]);
    }

    /**
     * Get audit status.
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function getStatus()
    {
        return $this->parseJSON('get', [self::API_ACCOUNT_AUDIT_STATUS]);
    }

    /**
     * Get shake info.
     *
     * @param string $ticket
     * @param int    $needPoi
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function getShakeInfo($ticket, $needPoi = null)
    {
        $params = [
            'ticket' => $ticket,
        ];

        if ($needPoi !== null) {
            $params['need_poi'] = intval($needPoi);
        }

        return $this->parseJSON('json', [self::API_GET_SHAKE_INFO, $params]);
    }

    /**
     * Return the device instance.
     *
     * @return \MComponent\WX\SWA\WeChat\ShakeAround\Device
     */
    public function device()
    {
        if (is_null($this->device)) {
            $this->device = new Device($this->accessToken);
        }

        return $this->device;
    }

    /**
     * Return the group instance.
     *
     * @return \MComponent\WX\SWA\WeChat\ShakeAround\Group
     */
    public function group()
    {
        if (is_null($this->group)) {
            $this->group = new Group($this->accessToken);
        }

        return $this->group;
    }

    /**
     * Return the page instance.
     *
     * @return \MComponent\WX\SWA\WeChat\ShakeAround\Page
     */
    public function page()
    {
        if (is_null($this->page)) {
            $this->page = new Page($this->accessToken);
        }

        return $this->page;
    }

    /**
     * Return the material instance.
     *
     * @return \MComponent\WX\SWA\WeChat\ShakeAround\Material
     */
    public function material()
    {
        if (is_null($this->material)) {
            $this->material = new Material($this->accessToken);
        }

        return $this->material;
    }

    /**
     * Return the relation instance.
     *
     * @return \MComponent\WX\SWA\WeChat\ShakeAround\Relation
     */
    public function relation()
    {
        if (is_null($this->relation)) {
            $this->relation = new Relation($this->accessToken);
        }

        return $this->relation;
    }

    /**
     * Return the stats instance.
     *
     * @return \MComponent\WX\SWA\WeChat\ShakeAround\Stats
     */
    public function stats()
    {
        if (is_null($this->stats)) {
            $this->stats = new Stats($this->accessToken);
        }

        return $this->stats;
    }
}
