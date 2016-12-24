<?php

namespace MComponent\WX\EWA\WeChat\Messages;

/**
 * 文本消息
 *
 * @property string $content
 */
class Text extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = ['content'];

    /**
     * 生成主动消息数组
     *
     * @return array
     */
    public function toStaff()
    {
        return [
            'text' => [
                'content' => $this->content,
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
            'Content' => $this->content,
        ];
    }
}
