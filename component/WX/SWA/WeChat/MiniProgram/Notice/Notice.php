<?php

namespace MComponent\WX\SWA\WeChat\MiniProgram\Notice;

use MComponent\WX\SWA\WeChat\Notice\Notice as BaseNotice;

class Notice extends BaseNotice
{
    /**
     * {@inheritdoc}.
     */
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'page' => '',
        'form_id' => '',
        'data' => [],
        'emphasis_keyword' => '',
    ];

    /**
     * {@inheritdoc}.
     */
    protected $defaults = [
        'touser' => '',
        'template_id' => '',
        'form_id' => '',
        'data' => [],
    ];

    /**
     * {@inheritdoc}.
     */
    protected $required = ['touser', 'template_id', 'form_id'];

    public function __construct()
    {
        $accessToken = func_get_args()[0];

        parent::__construct($accessToken);
    }

    /**
     * Send notice message.
     */
    const API_SEND_NOTICE = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send';
}
