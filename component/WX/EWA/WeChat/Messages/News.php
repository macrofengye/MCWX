<?php

namespace MComponent\WX\EWA\WeChat\Messages;

use Closure;

/**
 * 图文消息
 */
class News extends AbstractMessage
{
    /**
     * 属性
     *
     * @var array
     */
    protected $items = [];

    /**
     * 添加图文消息内容
     *
     * @param NewsItem $item
     *
     * @return News
     */
    public function item(NewsItem $item)
    {
        array_push($this->items, $item);

        return $this;
    }

    /**
     * 添加多条图文消息
     *
     * @param array|Closure $items
     *
     * @return News
     */
    public function items($items)
    {
        if ($items instanceof Closure) {
            $items = $items();
        }

        array_map(array($this, 'item'), (array)$items);

        return $this;
    }

    /**
     * 生成主动消息数组
     */
    public function toStaff()
    {
        $articles = array();

        foreach ($this->items as $item) {
            $articles[] = [
                'title' => $item->title,
                'description' => $item->description,
                'url' => $item->url,
                'picurl' => $item->pic_url,
            ];
        }

        return array('news' => array('articles' => $articles));
    }

    /**
     * 生成回复消息数组
     */
    public function toReply()
    {
        $articles = [];

        foreach ($this->items as $item) {
            $articles[] = [
                'Title' => $item->title,
                'Description' => $item->description,
                'Url' => $item->url,
                'PicUrl' => $item->pic_url,
            ];
        }

        return [
            'ArticleCount' => count($articles),
            'Articles' => $articles,
        ];
    }
}
