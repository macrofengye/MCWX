<?php

namespace MComponent\WX\SWA\WeChat\OpenPlatform\Components;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;

abstract class AbstractComponent extends AbstractAPI
{
    /**
     * Config.
     *
     * @var array
     */
    protected $config;

    /**
     * Request.
     *
     * @var \Slim\Http\Request
     */
    protected $request;

    /**
     * AbstractComponent constructor.
     *
     * @param \MComponent\WX\SWA\WeChat\Core\AccessToken $accessToken
     * @param array $config
     * @param $request
     */
    public function __construct($accessToken, array $config, $request)
    {
        parent::__construct($accessToken);
        $this->config = $config;
        $this->request = $request;
    }
}
