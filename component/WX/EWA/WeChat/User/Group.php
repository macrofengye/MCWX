<?php

namespace MComponent\WX\EWA\WeChat\User;

use MComponent\WX\EWA\WeChat\Core\AccessToken;
use MComponent\WX\EWA\WeChat\Core\Http;

/**
 * 用户组
 */
class Group
{
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/department/list';
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete';

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
    public function __construct()
    {
        $this->http = new Http();
    }

    /**
     * 创建部门
     * @param  string $name 部门名称
     * @param  integer $parentid 父亲部门id
     * @param  integer $order 在父部门中的次序值
     * @param  integer $id 部门id （可以自动生成）
     * @return integer           部门id
     */
    public function create($name, $parentid = 1, $order = null, $id = null)
    {
        $params = array(
            'name' => $name,
            'parentid' => $parentid,
            'order' => $order,
            'id' => $id
        );

        $response = $this->http->jsonPost(self::API_CREATE, $params);

        return $response['id'];
    }

    /**
     * 获取所有部门
     *
     * @return array
     */
    public function lists($id = 0)
    {
        $response = $this->http->get(self::API_GET . '?id=' . $id);

        return $response['department'];
    }

    /**
     * 更新部门
     * @param  string $name 部门名称
     * @param  integer $parentid 父亲部门id
     * @param  integer $order 在父部门中的次序值
     * @param  integer $id 部门id （可以自动生成）
     * @return integer           部门id
     */
    public function update($id, $name, $parentid = 1, $order = null)
    {
        $params = array(
            'name' => $name,
            'parentid' => $parentid,
            'order' => $order,
            'id' => $id
        );

        return $this->http->jsonPost(self::API_UPDATE, $params);
    }

    /**
     * 删除部门
     *
     * @param int $id
     *
     * @return array
     */
    public function delete($id)
    {
        return $this->http->get(self::API_DELETE . '?id=' . $id);
    }

}
