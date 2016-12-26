<?php

namespace MComponent\WX\EWA\WeChat\Messages;

/**
 * 群发视频消息
 *
 * @property string $media_id
 */
class MpVideo extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = [
        'media_id',
    ];

    /**
     * 设置视频消息
     *
     * @param string $mediaId
     *
     * @return $this
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
            'mpvideo' => [
                'media_id' => $this->media_id,
            ],
        ];
    }
}
