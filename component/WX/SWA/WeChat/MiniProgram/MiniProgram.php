<?php

namespace MComponent\WX\SWA\WeChat\MiniProgram;

use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;
use MComponent\WX\SWA\WeChat\MiniProgram\Material\Temporary;
use MComponent\WX\SWA\WeChat\MiniProgram\Notice\Notice;
use MComponent\WX\SWA\WeChat\MiniProgram\QRCode\QRCode;
use MComponent\WX\SWA\WeChat\MiniProgram\Staff\Staff;
use MComponent\WX\SWA\WeChat\MiniProgram\User\User;
use MComponent\WX\SWA\WeChat\Support\Arr;

/**
 * Class MiniProgram.
 *
 * @property \MComponent\WX\SWA\WeChat\MiniProgram\Server\Guard $server
 * @property \MComponent\WX\SWA\WeChat\MiniProgram\User\User $user
 * @property \MComponent\WX\SWA\WeChat\MiniProgram\Notice\Notice $notice
 * @property \MComponent\WX\SWA\WeChat\MiniProgram\Staff\Staff $staff
 * @property \MComponent\WX\SWA\WeChat\MiniProgram\QRCode\QRCode $qrcode
 * @property \MComponent\WX\SWA\WeChat\MiniProgram\Material\Temporary $material_temporary
 */
class MiniProgram
{
    /**
     * Access Token.
     *
     * @var \MComponent\WX\SWA\WeChat\MiniProgram\AccessToken
     */
    protected $accessToken;

    /**
     * Mini program config.
     *
     * @var array
     */
    protected $config;

    /**
     * Guard.
     *
     * @var \MComponent\WX\SWA\WeChat\MiniProgram\Server\Guard
     */
    protected $server;

    /**
     * Components.
     *
     * @var array
     */
    protected $components = [
        'user' => User::class,
        'notice' => Notice::class,
        'staff' => Staff::class,
        'qrcode' => QRCode::class,
        'material_temporary' => Temporary::class,
    ];

    /**
     * MiniProgram constructor.
     *
     * @param \MComponent\WX\SWA\WeChat\MiniProgram\Server\Guard $server
     * @param \MComponent\WX\SWA\WeChat\MiniProgram\AccessToken $accessToken
     * @param array $config
     */
    public function __construct($server, $accessToken, $config)
    {
        $this->server = $server;

        $this->accessToken = $accessToken;

        $this->config = $config;
    }

    /**
     * Magic get access.
     *
     * @param $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if ($class = Arr::get($this->components, $name)) {
            return new $class($this->accessToken, $this->config);
        }

        throw new InvalidArgumentException("Property or component \"$name\" does not exists.");
    }
}
