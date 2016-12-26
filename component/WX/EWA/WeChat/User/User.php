<?php

namespace MComponent\WX\EWA\WeChat\User;

use MComponent\WX\EWA\WeChat\Core\AccessToken;
use MComponent\WX\EWA\WeChat\Core\Http;
use MComponent\WX\EWA\WeChat\Utils\Bag;

/**
 * 用户
 */
class User
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete';
    const API_BATCH_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete';
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/get';
    const API_SIMPLE_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist';
    const API_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/list';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct()
    {
        $this->http = new Http();
    }

    /**
     * 创建成员
     * @param  array $userInfo 用户信息 具体数据参考微信接口
     * @return array
     */
    public function create(array $userInfo)
    {
        return $this->http->jsonPost(self::API_CREATE, $userInfo);
    }

    /**
     * 更新成员
     * @param  array $userInfo 用户信息 具体数据参考微信接口
     * @return mixed
     */
    public function update(array $userInfo)
    {
        return $this->http->jsonPost(self::API_UPDATE, $userInfo);
    }

    /**
     * 删除成员
     * @param string $userId 成员UserID
     * @return array
     */

    public function delete($userId)
    {
        return $response = $this->http->get(self::API_DELETE . '?userid=' . $userId);
    }


    /**
     * 批量删除成员
     *
     * @param array $userID
     *
     * @return bool
     */
    public function batchDelete(array $userID)
    {
        $params = array(
            'useridlist' => $userID,
        );
        return $this->http->jsonPost(self::API_BATCH_DELETE, $params);
    }


    /**
     * 读取成员信息
     *
     * @param string $userId
     * @return Bag
     */
    public function get($userId)
    {
        $param = array('userid' => $userId);
        return new Bag($this->http->get(self::API_GET, $param));
    }

    /**
     * 获取部门成员
     * @param  integer $departmentId 部门id
     * @param  integer $fetchChild 是否递归获取子部门下面的成员
     * @param  integer $status 成员类型 可叠加
     * @return array
     */
    public function simpleList($departmentId, $fetchChild = 1, $status = 1)
    {
        $params = array(
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild,
            'status' => $status,
        );

        $response = $this->http->get(self::API_SIMPLE_LIST, $params);

        return $response['userlist'];
    }

    /**
     * 获取部门成员(详情)
     * @param  integer $departmentId 部门id
     * @param  integer $fetchChild 是否递归获取子部门下面的成员
     * @param  integer $status 成员类型 可叠加
     * @return Bag
     */
    public function lists($departmentId, $fetchChild = 1, $status = 1)
    {
        $params = array(
            'department_id' => $departmentId,
            'fetch_child' => $fetchChild,
            'status' => $status,
        );

        return new Bag($this->http->get(self::API_LIST, $params));
    }

}
