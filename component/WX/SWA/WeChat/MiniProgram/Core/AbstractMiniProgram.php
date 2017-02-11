<?php

namespace MComponent\WX\SWA\WeChat\MiniProgram\Core;

use MComponent\WX\SWA\WeChat\Core\AbstractAPI;

class AbstractMiniProgram extends AbstractAPI
{
    /**
     * Mini program config.
     *
     * @var array
     */
    protected $config;

    /**
     * AbstractMiniProgram constructor.
     *
     * @param \MComponent\WX\SWA\WeChat\MiniProgram\AccessToken $accessToken
     * @param array                               $config
     */
    public function __construct($accessToken, $config)
    {
        parent::__construct($accessToken);

        $this->config = $config;
    }
}
