<?php

namespace MComponent\WX\EWA\WeChat\Messages;

/**
 * 图文项
 */
class NewsItem extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'pic_url',
        'url',
    ];
}
