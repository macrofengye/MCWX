<?php

namespace MComponent\WX\EWA\WeChat\Messages;

use MComponent\WX\EWA\WeChat\Exception;

/**
 * 坐标消息
 *
 * @property string $content
 */
class Location extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = [
        'lat',
        'lon',
        'scale',
        'label',
    ];

    /**
     * 生成主动消息数组
     */
    public function toStaff()
    {
        throw new Exception('暂时不支持发送链接消息');
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        throw new Exception('暂时不支持回复链接消息');
    }
}
