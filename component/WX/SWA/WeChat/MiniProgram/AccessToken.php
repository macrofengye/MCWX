<?php

namespace MComponent\WX\SWA\WeChat\MiniProgram;

use MComponent\WX\SWA\WeChat\Core\AccessToken as CoreAccessToken;

/**
 * Class AccessToken.
 */
class AccessToken extends CoreAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'macrowechat.common.mini.program.access_token.';
}
