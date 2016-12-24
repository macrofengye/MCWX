<?php

namespace MComponent\WX\EWA\WeChat\User;

use MComponent\WX\EWA\WeChat\Core\AccessToken;
use MComponent\WX\EWA\WeChat\Core\Http;

/**
 * 用户组
 */
class Tag
{
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/tag/get';
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/delete';
    const API_ADD_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers';
    const API_DELETE_USER = 'https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers';
    const API_GET_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/tag/list';

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
     * 创建标签
     * @param  sting $tagName 标签名
     * @param  integer $tagId 标签id
     * @return array
     */
    public function create($tagName, $tagId = null)
    {
        $params = array(
            'tagname' => $tagName,
            'tagid' => $tagId
        );

        return $this->http->jsonPost(self::API_CREATE, $params);
    }

    /**
     * 更新标签
     * @param  integer $tagId
     * @param  string $tagName
     * @return array
     */
    public function update($tagId, $tagName)
    {
        $params = array(
            'tagid' => $tagId,
            'tagname' => $tagName
        );

        return $this->http->jsonPost(self::API_UPDATE, $params);
    }

    /**
     * 删除标签
     *
     * @param int $tagId
     *
     * @return array
     */
    public function delete($tagId)
    {
        return $this->http->get(self::API_DELETE . '?tagid=' . $tagId);
    }

    /**
     * 增加标签成员
     * @param  int $tagId
     * @param  array $userList
     * @param  array $partyList
     * @return array
     */
    public function addUser($tagId, $userList, $partyList)
    {
        $params = array(
            'tagid' => $tagId,
            'userlist' => $userList,
            'partylist' => $partyList
        );

        return $this->http->jsonPost(self::API_ADD_USER, $params);
    }

    /**
     * 获取标签成员
     * @param  int $tagId
     * @return array
     */
    public function getTagUser($tagId)
    {
        return $this->http->get(self::API_GET . '?tagid=' . $tagId);
    }

    /**
     * 删除标签成员
     * @param  int $tagId
     * @param  array $userList
     * @param  array $partyList
     * @return array
     */
    public function deleteTagUser($tagId, $userList, $partyList)
    {
        $params = array(
            'tagid' => $tagId,
            'userlist' => $userList,
            'partylist' => $partyList
        );

        return $this->http->jsonPost(self::API_DELETE_USER, $params);
    }

    /**
     * 获取标签列表
     * @return array
     */
    public function lists()
    {
        return $this->http->get(self::API_GET_LIST);
    }

}
