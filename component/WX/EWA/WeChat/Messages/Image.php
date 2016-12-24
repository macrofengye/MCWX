<?php

namespace MComponent\WX\EWA\WeChat\Messages;

/**
 * 图片消息
 *
 * @property string $media_id
 */
class Image extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array('media_id');

    /**
     * 设置音乐消息封面图
     *
     * @param string $mediaId
     *
     * @return Image
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
            'image' => [
                'media_id' => $this->media_id,
            ],
        ];
    }

    /**
     * 生成回复消息数组
     *
     * @return array
     */
    public function toReply()
    {
        return [
            'Image' => [
                'MediaId' => $this->media_id,
            ],
        ];
    }
}
