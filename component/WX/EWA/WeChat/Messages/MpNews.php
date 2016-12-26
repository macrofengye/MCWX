<?php

namespace MComponent\WX\EWA\WeChat\Messages;

use MComponent\WX\EWA\WeChat\Core\Exception;

/**
 * 多图文消息
 *
 * @property string $media_id
 */
class MpNews extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = ['media_id'];

    /**
     * 设置Media_id
     *
     * @param string $mediaId
     *
     * @return MpNews
     */
    public function media($mediaId)
    {
        $this->setAttribute('media_id', $mediaId);
        return $this;
    }

    /**
     * 生成主动消息数组
     *
     * @return array
     */
    public function toStaff()
    {
        return [
            'mpnews' => [
                'media_id' => $this->media_id,
            ],
        ];
    }

    /**
     * 生成回复消息数组
     * @throws
     * @return array
     */
    public function toReply()
    {
        throw new Exception(__CLASS__ . '未实现此方法：toReply()');
        /*return array(
                'Image' => array(
                            'MediaId' => $this->media_id,
                           ),
               );
        */
    }
}
