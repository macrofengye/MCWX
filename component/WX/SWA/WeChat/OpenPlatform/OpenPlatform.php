<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform;

use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;
use MComponent\WX\SWA\WeChat\Support\Arr;
use Pimple\Container;

/**
 * Class OpenPlatform.
 *
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\Guard $server
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\Components\PreAuthCode $pre_auth
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\AccessToken $access_token
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\AuthorizerToken $authorizer_token;
 * @property \MComponent\WX\SWA\WeChat\OpenPlatform\Authorization $authorization;
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
     * Container in the scope of the open platform.
     *
     * @var Container
     */
    protected $container;

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
     * Sets the container for use of the platform.
     *
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
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
        if ($instance = $this->container->offsetGet("open_platform.{$name}")) {
            return $instance;
        }
        throw new InvalidArgumentException("Property or component \"$name\" does not exists.");
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return void
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        return $this->$name = $value;
    }

    /**
     * is triggered by calling isset() or empty() on inaccessible members.
     *
     * @param $name string
     * @return bool
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __isset($name)
    {
        if (property_exists($this, $name)) {
            return 1;
        }
        return 0;
    }
}
