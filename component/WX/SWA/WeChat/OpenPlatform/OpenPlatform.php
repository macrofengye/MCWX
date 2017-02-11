<?php

namespace MComponent\WX\SWA\WeChat\OpenPlatform;

use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;
use MComponent\WX\SWA\WeChat\Support\Arr;

/**
 * Class OpenPlatform.
 *
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\Guard $server
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\Components\PreAuthCode $pre_auth
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\AccessToken $access_token
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\Components\Authorizer $authorizer
 */
class OpenPlatform
{
    /**
     * Server guard.
     *
     * @var Guard
     */
    protected $server;

    /**
     * OpenPlatform component access token.
     *
     * @var AccessToken
     */
    protected $access_token;

    /**
     * OpenPlatform config.
     *
     * @var array
     */
    protected $config;

    /**
     * Components.
     *
     * @var array
     */
    private $components = [
        'pre_auth' => Components\PreAuthCode::class,
        'authorizer' => Components\Authorizer::class,
    ];

    /**
     * OpenPlatform constructor.
     *
     * @param Guard $server
     * @param $access_token
     * @param array $config
     */
    public function __construct(Guard $server, $access_token, $config)
    {
        $this->server = $server;
        $this->access_token = $access_token;
        $this->config = $config;
    }

    /**
     * Magic get access.
     *
     * @param $name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if ($class = Arr::get($this->components, $name)) {
            return new $class($this->access_token, $this->config);
        }

        throw new InvalidArgumentException("Property or component \"$name\" does not exists.");
    }
}
