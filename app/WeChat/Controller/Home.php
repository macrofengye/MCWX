<?php
namespace WeChat\Controller;

use Core\Controller\Controller;
use Core\Utils\CoreUtils;
use Blog\Models\Employee;
use Entity\Models\Eorder;
use Entity\Models\Region;
use Entity\Models\Test;
use Slim\Http\Request;
use Slim\Http\Response;
use EasyWeChat\Foundation\Application;
use WeChat\Utils\Socialite\AccessToken;

class Home extends Controller
{
    public function index(Request $request, Response $response, $args)
    {
        $server = $this->app->component('server');
        $user = $this->app->component('user');
        $user_tag = $this->app->component('user_tag');
        $group = $this->app->component('user_group');

        /*writeLog(__FUNCTION__, [$user->lists()], APP_PATH . '/log/users.log');*/
        //writeLog(__FUNCTION__, [$user_tag->lists()], APP_PATH . '/log/users_tag.log');
        //writeLog(__FUNCTION__, [$group->lists()], APP_PATH . '/log/groups.log');
        $server->setMessageHandler(function ($message) {
            writeLog(__FUNCTION__, [$message], APP_PATH . '/log/debug.log');
            if ($message->MsgType == 'event') {
                switch ($message->Event) {
                    case 'subscript':
                        break;
                    case 'VIEW':
                        return $message;
                        break;
                    case 'CLICK':
                        $tt = ['name' => 'macrochen', 'age' => 23, 'user_id' => 12];
                        $qrcode = $this->app->component('qRCode');
                        $result = $qrcode->temporary('123', 6 * 24 * 3600);
                        $ticket = $result['ticket'];// 或者 $result['ticket']
                        $expireSeconds = $result->expire_seconds; // 有效秒数
                        $url = $result->url; // 二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
                        $url = $qrcode->url($ticket);
                        $content = file_get_contents($url); // 得到二进制图片内容
                        file_put_contents(APP_PATH . '/log/code.jpg', $content); // 写入文件
                        return $url;
                    default:
                        return '欢迎关注我们!';
                        break;
                }
            } elseif
            ($message->MsgType == 'text'
            ) {
                return $message->Content;
            }
        });
        /*$menu = $this->app->component('menu');
        //$menu->destroy();
        $server->setMessageHandler(function ($message) use ($menu) {
            $buttons = [
                [
                    "name" => "帮帮贷",
                    "sub_button" => [
                        [
                            "type" => "view",
                            "name" => "二维码跳转",
                            "url" => "http://wechat.niydiy.com/hello/qrcode"
                        ],
                         [
                            "type" => "click",
                            "name" => "获取二维码",
                            "key" => "qrcode"
                        ],
                        [
                            "type" => "view",
                            "name" => "我的账户",
                            "url" => "http://wechat.niydiy.com/hello/account"
                        ],
                        [
                            "type" => "view",
                            "name" => "加入帮帮贷",
                            "url" => "http://wechat.niydiy.com/hello/bangbangdai"
                        ],
                    ],
                ],
            ];
            $menu->add($buttons);
        });

        return $server->serve();*/
        // print_r($accessToken);
        return $server->serve();
    }

    public function callback(Request $request, Response $response, $args)
    {
        writeLog(__FUNCTION__, [$request], APP_PATH . '/log/callback.txt');
    }

    public function index3(Request $request, Response $response, $args)
    {
        $server = CoreUtils::getContainer('server');
        $user = CoreUtils::getContainer('user');
        print_r($user);
        $server->setMessageHandler(function ($message) use ($user) {
            $fromUser = $user->get($message->FromUserName);
            return $message->Content . ' ---- ' . $fromUser->nickname;
        });
        return $server->serve();
    }

    public function index2(Request $request, Response $response, $args)
    {
        $server = CoreUtils::getContainer('server');
        $server->setMessageHandler(function ($message) {
            return $message->Content;
        });
        return $server->serve();
    }

    public function index1(Request $request, Response $response, $args)
    {
        $nonce = $request->getParam('nonce');
        $token = 'testmcwx';
        $timestamp = $request->getParam('timestamp');
        $echostr = $request->getParam('echostr');
        $signature = $request->getParam('signature');
        $array = array($nonce, $timestamp, $token);
        sort($array);
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            return $response->write($echostr);
        } else {
            $content = file_get_contents('php://input');
            $postObj = simplexml_load_string($content);
            $toUser = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $time = time();
            $msgType = 'text';
            $content = '欢迎关注我们的微信公众账号' . $postObj->FromUserName . '-' . $postObj->ToUserName;
            $template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
            $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            return $response->write($info);
        }
    }

    public function hello(Request $request, Response $response, $args)
    {
        $user = CoreUtils::getContainer('oAuth')->stateless()->user();
        writeLog(__FUNCTION__, [$user->getId(), $user->getNickName(), $user->getName()], APP_PATH . '/log/callback01.txt');
        return $response;
        //return $response->withRedirect('http://m.zdai.com/#/?open_id='.$user->getId());
    }

    public function hello1(Request $request, Response $response, $args)
    {
        /*$event = $this->addEvent(self::ENTITY, 'db1', 'event_namespace', 'Events::prePersist');
        $em = $this->getDbInstance(self::ENTITY, 'db1');*/
        // print_r(get_class_methods($event));
        /*$this->render($response, "/home/hello.twig", array(
            'name' => 'Macro',
        ));*/
        /*$response->getBody()->write('aaaaa');
        return $response->write('llkkk');*/
        /*$server = CoreUtils::getContainer('server');
        $accessToken = CoreUtils::getContainer('access_token');
        $user = CoreUtils::getContainer('oAuth')->user;
        writeLog(__FUNCTION__ , [var_export(CoreUtils::getContainer('oAuth') , true)] , APP_PATH.'/log/callback01.txt');
        return $response->write('aaaa');*/

        $appid = "wx7cba84e8e7343a5c";
        $secret = "67242b76b760ca40442375817304bb29";
        $code = $request->getParam('code');
        writeLog(__FUNCTION__, [$code], APP_PATH . '/log/xxx.log');
//第一步:取得openid
        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $oauth2 = $this->getJson($oauth2Url);
        writeLog(__FUNCTION__, [$oauth2], APP_PATH . '/log/xxx.log');
//第二步:根据全局access_token和openid查询用户信息
        $access_token = $oauth2["access_token"];
        $openid = $oauth2['openid'];
        $get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $userinfo = $this->getJson($get_user_info_url);

//打印用户信息
        print_r($userinfo);


    }

    function getJson($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }


    public function test($request, $response, $args)
    {
        /* @var $eventDispatcher EventDispatcherInterface */
        $eventDispatcher = $this->getContainer('event_dispatcher');
        /* @var $eventEmittingService EventEmittingService */
        $eventEmittingService = $this->getContainer('event_emitting_service');
        $eventEmittingService->emit();

        print_r(get_class_methods($this->getContainer('event_dispatcher')));
    }
}