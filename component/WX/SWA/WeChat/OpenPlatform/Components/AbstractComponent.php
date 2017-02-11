<?php

namespace MComponent\WX\SWA\WeChat\OpenPlatform\Components;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;
use Symfony\Component\HttpFoundation\Request;

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
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * AbstractComponent constructor.
     *
     * @param \EasyWeChat\Core\AccessToken $accessToken
     * @param array                        $config
     * @param $request
     */
    public function __construct($accessToken, array $config, $request = null)
    {
        parent::__construct($accessToken);
        $this->config = $config;
        $this->request = $request ?: Request::createFromGlobals();
    }
}
