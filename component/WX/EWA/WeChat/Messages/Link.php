<?php

namespace MComponent\WX\EWA\WeChat\Messages;

/**
 * 链接消息
 *
 * @property string $content
 */
class Link extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'url',
    ];
}
