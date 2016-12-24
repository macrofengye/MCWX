<?php

namespace MComponent\WX\EWA\WeChat\Messages;


/**
 * 声音消息
 *
 * @property string $media_id
 */
class Voice extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = ['media_id'];

    /**
     * 媒体
     *
     * @var \MComponent\WX\EWA\WeChat\Media
     */
    protected $media;

    /**
     * 设置语音
     *
     * @param string $mediaId
     *
     * @return Voice
     */
    public function media($mediaid)
    {
        $this->setAttribute('media_id', $mediaid);

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
            'voice' => [
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
            'Voice' => [
                'MediaId' => $this->media_id,
            ],
        ];
    }
}
