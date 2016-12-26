<?php

namespace MComponent\WX\EWA\WeChat\Broadcast;

use MComponent\WX\EWA\WeChat\Core\AccessToken;
use MComponent\WX\EWA\WeChat\Core\Exception;
use MComponent\WX\EWA\WeChat\Core\Http;
use MComponent\WX\EWA\WeChat\Messages\AbstractMessage;
use MComponent\WX\EWA\WeChat\Server\Message;

class Broadcast
{
    const API_SEND_MESSAGE = 'https://qyapi.weixin.qq.com/cgi-bin/message/send';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 消息
     *
     * @var \MComponent\WX\EWA\WeChat\Messages\AbstractMessage;
     */
    protected $message;

    /**
     * 应用id
     * @var int
     */
    protected $agentid;

    /**
     * constructor
     *
     * @param AccessToken $token
     */
    public function __construct(AccessToken $token)
    {
        $this->http = new Http($token);
    }

    /**
     * 群发来源应用
     * @param  int $agentId
     * @return $this
     */
    public function fromAgentId($agentId)
    {
        $this->agentid = $agentId;
        return $this;
    }

    /**
     * 准备消息
     *
     * @param \MComponent\WX\EWA\WeChat\Messages\AbstractMessage $message
     * @throws \Exception
     * @return Broadcast
     */
    public function send($message)
    {
        is_string($message) && $message = Message::make('text')->with('content', $message);

        if (!$message instanceof AbstractMessage) {
            throw new \Exception("消息必须继承自 'MComponent\\WX\\EWA\\WeChat\\BaseMessage'");
        }

        $this->message = $message;

        return $this;
    }

    /**
     * 消息群发
     * @param  string|array $user 指定用户群
     * @param  string|array $toParty 指定部门
     * @param  string|array $toTag 指定标签
     * @throws Exception
     * @return mixed
     */
    public function to($user = '@all', $toParty = null, $toTag = null)
    {
        if (empty($this->message)) {
            throw new Exception('未设置要发送的消息');
        }

        $this->message->agentid = $this->agentid;

        $this->message->touser = is_array($user) ? implode('|', $user) : $user;

        $this->message->toparty = is_array($toParty) ? implode('|', $toParty) : $toParty;

        $this->message->totag = is_array($toTag) ? implode('|', $toTag) : $toTag;

        return $this->http->jsonPost(self::API_SEND_MESSAGE, $this->message->buildForBroadcast());
    }

    /**
     * 群发应用所见所有人
     *
     * @return mixed
     */
    public function toAll()
    {
        return $this->to();
    }

    /**
     * 按部门发送
     * @param  int|array $toParty array(1, 2, 3)
     * @return mixed
     */
    public function toParty($toParty)
    {
        return $this->to($user = '', $toParty = $toParty, $toTag = '');
    }

    /**
     * 按标签发送
     * @param  int|array $toTag
     * @return mixed
     */
    public function toTag($toTag)
    {
        return $this->to($user = '', $toParty = '', $toTag = $toTag);
    }
}
