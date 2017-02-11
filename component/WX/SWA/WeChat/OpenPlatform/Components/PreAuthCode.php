<?php

namespace MComponent\WX\SWA\WeChat\OpenPlatform\Components;

use MComponent\WX\SWA\WeChat\Core\Exceptions\InvalidArgumentException;
use MComponent\WX\SWA\WeChat\Core\Exceptions\RuntimeException;
use MComponent\WX\SWA\WeChat\Support\Url;

class PreAuthCode extends AbstractComponent
{
    /**
     * Create pre auth code url.
     */
    const CREATE_PRE_AUTH_CODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';

    /**
     * Pre auth link.
     */
    const PRE_AUTH_LINK = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';

    /**
     * AppId.
     *
     * @var string
     */
    private $appId;

    /**
     * Redirect Uri.
     *
     * @var string
     */
    protected $redirectUri;

    /**
     * Get AppId.
     *
     * @return string
     */
    public function getAppId()
    {
        if (is_null($this->appId)) {
            $this->appId = $this->config['app_id'];
        }

        return $this->appId;
    }

    /**
     * Set AppId.
     *
     * @param string $appId
     *
     * @return $this
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Get pre auth code.
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function getCode()
    {
        $data = [
            'component_appid' => $this->getAppId(),
        ];

        $result = $this->parseJSON('json', [self::CREATE_PRE_AUTH_CODE, $data]);

        if (empty($result['pre_auth_code'])) {
            throw new InvalidArgumentException('Invalid response.');
        }

        return $result['pre_auth_code'];
    }

    /**
     * Get Redirect url.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getRedirectUri()
    {
        if (!$this->redirectUri) {
            throw new RuntimeException('You need to provided a redirect uri.');
        }

        return $this->redirectUri;
    }

    /**
     * Set redirect uri.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;

        return $this;
    }

    /**
     * Get auth page link.
     *
     * @return string
     */
    public function getAuthLink()
    {
        return sprintf(self::PRE_AUTH_LINK,
            $this->config['app_id'],
            $this->getCode(),
            Url::encode($this->getRedirectUri())
        );
    }
}
