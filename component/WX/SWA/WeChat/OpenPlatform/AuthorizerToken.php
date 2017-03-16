<?php
namespace MComponent\WX\SWA\WeChat\OpenPlatform;

use MComponent\WX\SWA\WeChat\Core\AccessToken as BaseAccessToken;

/**
 * Class AuthorizerToken
 *
 * AuthorizerToken is responsible for the access token of the authorizer,
 * the complexity is that this access token also requires the refresh token
 * of the authorizer which is acquired by the open platform authorization
 * process.
 *
 * This completely overrides the original AccessToken.
 *
 * @package EasyWeChat\OpenPlatform
 */
class AuthorizerToken extends BaseAccessToken
{
    /**
     * Handles authorization.
     *
     * @var Authorization
     */
    protected $authorization;

    /**
     * AuthorizerAccessToken constructor.
     *
     * @param string $appId
     * @param Authorization $authorization
     */
    public function __construct($appId, Authorization $authorization)
    {
        parent::__construct($appId, null);
        $this->authorization = $authorization;
    }

    /**
     * Get token from WeChat API.
     *
     * @param bool $forceRefresh
     * @throws \Exception
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cached = $this->authorization->getAuthorizerAccessToken();
        if ($forceRefresh || empty($cached)) {
            return $this->authorization->handleAuthorizerAccessToken();
        }
        return $cached;
    }
}
