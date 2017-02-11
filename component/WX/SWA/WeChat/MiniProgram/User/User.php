<?php

namespace MComponent\WX\SWA\WeChat\MiniProgram\User;

use MComponent\WX\SWA\WeChat\MiniProgram\Core\AbstractMiniProgram;

class User extends AbstractMiniProgram
{
    /**
     * Api.
     */
    const JSCODE_TO_SESSION = 'https://api.weixin.qq.com/sns/jscode2session';

    /**
     * JsCode 2 session key.
     *
     * @param string $jsCode
     *
     * @return \MComponent\WX\SWA\WeChat\Support\Collection
     */
    public function getSessionKey($jsCode)
    {
        $params = [
            'appid' => $this->config('app_id'),
            'secret' => $this->config('secret'),
            'js_code' => $jsCode,
            'grant_type' => 'authorization_code',
        ];

        return $this->parseJSON('GET', [self::JSCODE_TO_SESSION, $params]);
    }
}
