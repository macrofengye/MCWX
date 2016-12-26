<?php

namespace MComponent\WX\EWA\WeChat\Chat;

use MComponent\WX\EWA\WeChat\Core\Http;

/**
 * 会话
 */
class Chat
{
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/chat/get';
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/update';
    const API_QUIT = 'https://qyapi.weixin.qq.com/cgi-bin/chat/quit';
    const API_CLEARNOTIFY = 'https://qyapi.weixin.qq.com/cgi-bin/chat/clearnotify';
    const API_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/chat/send';
    const API_SETMUTE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/setmute';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->http = new Http();
    }

    /**
     * 创建会话
     * @param  string $chatid 会话id
     * @param  string $name 会话标题
     * @param  string $owner 管理员userid
     * @param  array $userlist 会话成员列表
     * @return integer           部门id
     */
    public function create($chatid, $name, $owner, $userlist)
    {
        $params = array(
            'chatid' => $chatid,
            'name' => $name,
            'owner' => $owner,
            'userlist' => $userlist
        );
        $response = $this->http->jsonPost(self::API_CREATE, $params);
        return $response;
    }

    /**
     * 获取会话
     * @param  string $chatId 会话id
     * @return array
     */
    public function get($chatId)
    {
        $response = $this->http->get(self::API_GET . '?chatid=' . $chatId);
        return $response['chat_info'];
    }

    /**
     * 修改会话信息
     * @param  string $chatid 会话id
     * @param  string $op_user 操作人userid
     * @param  string $name 会话标题
     * @param  string $owner 管理员userid
     * @return array
     */
    public function update($chatid, $op_user, $name = null, $owner = null, $add_user_list = array(), $del_user_list = array())
    {
        $params = array(
            'chatid' => $chatid,
            'op_user' => $op_user,
            'name' => $name,
            'owner' => $owner,
            'add_user_list' => $add_user_list,
            'del_user_list' => $del_user_list
        );

        return $this->http->jsonPost(self::API_UPDATE, $params);
    }

    /**
     * 退出会话
     *
     * @param int $chatId
     *
     * @return array
     */
    public function quit($chatId, $op_user)
    {
        $params = array(
            'chatid' => $chatId,
            'op_user' => $op_user
        );

        return $this->http->jsonPost(self::API_QUIT, $params);
    }

    /**
     * 清除会话未读状态
     *
     * @param  int $chatId 会话所有者的userid
     * @param  array $chat 会话
     * @return array
     */
    public function clearnotify($chatId, $chat)
    {
        $params = array(
            'chatid' => $chatId,
            'chat' => $chat
        );

        return $this->http->jsonPost(self::API_CLEARNOTIFY, $params);
    }

    /**
     * 发消息
     *
     * @param  array $type 接收人类型：single|group，分别表示：群聊|单聊
     * @param  array $id 接收人的值，为userid|chatid，分别表示：成员id|会话id
     * @param  string $sender 发送人
     * @param  string $msgtype 消息类型
     * @param  array $content 消息内容
     * @return array
     */
    public function send($type, $id, $sender, $msgtype, $content)
    {
        $params = array(
            'receiver' => array(
                'type' => $type,
                'id' => $id
            ),
            'sender' => $sender,
            'msgtype' => $msgtype,
            $msgtype => $content
        );

        return $this->http->jsonPost(self::API_SEND, $params);
    }

    /**
     * 设置成员新消息免打扰
     *
     * @param  array $user_mute_list 成员新消息免打扰参数，数组，最大支持10000个成员
     * @return array
     */
    public function setMute($user_mute_list)
    {
        $params = array(
            'user_mute_list' => $user_mute_list,
        );
        return $this->http->jsonPost(self::API_SETMUTE, $params);
    }
}
