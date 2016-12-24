<?php

namespace MComponent\WX\EWA\WeChat\Agent;

/**
 * 应用
 */
class Agent
{
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get';
    const API_SET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/set';
    const API_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/agent/list';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 获取企业号应用
     * @param  string $agentid 应用id
     * @return array
     */
    public function get($agentid)
    {
        $response = $this->http->get(self::API_GET . '?agentid=' . $agentid);

        return $response;
    }

    /**
     * 设置企业号应用
     *
     * @param  array $agentInfo 应用信息 具体数据参考微信接口
     * @return array
     */
    public function set(array $agentInfo)
    {
        return $this->http->jsonPost(self::API_SET, $agentInfo);
    }

    /**
     * 获取应用概况列表
     *
     * @param void
     * @return array
     */
    public function lists()
    {
        $response = $this->http->get(self::API_LIST);

        return $response['agentlist'];
    }
}
