<?php
/**
 * User: Macro Chen <macro_fengye@163.com>
 * Date: 2016/12/23
 * Time: 17:58
 */

namespace MComponent\WX\EWA\Providers;

use MComponent\WX\EWA\WeChat\Server\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['server'] = function ($pimple) {
            $cfg = $pimple['config']['wechat'];
            $options = array(
                'token' => $cfg['token'],   //填写应用接口的Token
                'aes_key' => $cfg['aes_key'],//填写加密用的EncodingAESKey
                'app_id' => $cfg['app_id'],  //填写高级调用功能的app_id
                'app_secret' => $cfg['app_secret'], //填写高级调用功能的密钥
                'agent_id' => $cfg['agent_id'] //应用的id
            );
            $server = new Server($options);
            return $server;
        };
    }
}