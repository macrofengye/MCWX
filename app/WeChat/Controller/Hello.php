<?php
namespace WeChat\Controller;


use Polymer\Controller\Controller;
use Polymer\Utils\CoreUtils;
use Slim\Http\Request;
use Slim\Http\Response;

class Hello extends Controller
{
    public function account(Request $request, Response $response, $args)
    {
        $url = 'http://wechat.niydiy.com/hello/dealAccount';
        $response = CoreUtils::getContainer('oAuth')->scopes(['snsapi_userinfo'])->redirect($url);
        return $response;
    }

    public function daikuan(Request $request, Response $response, $args)
    {
        $url = 'http://wechat.niydiy.com/hello/dealDaikuan';
        logger(__CLASS__ . '::' . __FUNCTION__, [$url], APP_PATH . '/log/debug.log');
        $response = CoreUtils::getContainer('oAuth')->scopes(['snsapi_userinfo'])->redirect($url);
        return $response;
    }

    public function bangbangdai(Request $request, Response $response, $args)
    {
        $url = 'http://wechat.niydiy.com/hello/dealBangbangdai';
        logger(__CLASS__ . '::' . __FUNCTION__, [$url], APP_PATH . '/log/debug.log');
        $response = CoreUtils::getContainer('oAuth')->scopes(['snsapi_userinfo'])->redirect($url);
        return $response;
    }


    public function dealBangbangdai(Request $request, Response $response, $args)
    {
        $user = CoreUtils::getContainer('oAuth')->user();
        logger(__FUNCTION__, [$user->getId(), $user->getNickName(), $user->getName()], APP_PATH . '/log/callback01.txt');
        return $response->withRedirect('http://wechat.edai.com/#/?openid=' . $user->getId() . '&name=' . $user->getName());
    }

    public function dealDaikuan(Request $request, Response $response, $args)
    {
        $user = CoreUtils::getContainer('oAuth')->user();
        logger(__FUNCTION__, ['http://m.niydiy.com/#/?openid=' . $user->getId() . '&name=' . $user->getName(), $user->getId(), $user->getNickName(), $user->getName()], APP_PATH . '/log/callback01.txt');
        return $response->withRedirect('http://m.niydiy.com/#/?openid=' . $user->getId() . '&name=' . $user->getName());
    }

    public function dealAccount(Request $request, Response $response, $args)
    {
        $user = CoreUtils::getContainer('oAuth')->user();
        logger(__FUNCTION__, ['http://m.niydiy.com/#/?openid=' . $user->getId() . '&name=' . $user->getName(), $user->getId(), $user->getNickName(), $user->getName()], APP_PATH . '/log/callback01.txt');
        return $response->withRedirect('http://wechat.edai.com/#/ucenter?openid=' . $user->getId() . '&name=' . $user->getName());
    }

    public function show($request, $response, $args)
    {
        $server = CoreUtils::getContainer('server');
        $js = CoreUtils::getContainer('js');
        print_r($js->config(array('onMenuShareQQ', 'onMenuShareWeibo'), true));
        return $response;
    }

    public function show11($request, $response, $args)
    {
        $appid = 'wx7cba84e8e7343a5c';
        $redirect_uri = urlencode('http://wechat.niydiy.com/home/hello');
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
        return $response->withRedirect($url);
        /*$response = CoreUtils::getContainer('oAuth')->scopes(['snsapi_userinfo'])->redirect();
        return $response;*/
    }

    public function show1($request, $response, $args)
    {
        $view = $this->app->getContainer()->get('view');
        $view->render($response, 'profile.twig', [
            'name' => "aaaaaa"
        ]);
        echo "aaaa";
    }

    public function test(Request $request, Response $response, $args)
    {
        return $response->withRedirect('http://127.0.0.1/test/index.html');
    }

    public function test1(Request $request, Response $response, $args)
    {
        return $response->withJson(['aaa' => CoreUtils::getContainer('session')->get('name'), $request->getCookieParams()]);
    }


    public function getWxConfig(Request $request, Response $response, $args)
    {
        $server = CoreUtils::getContainer('server');
        $js = CoreUtils::getContainer('js');
        $js->setUrl('http://wechat.edai.com/');
        $config = $js->config(['onMenuShareTimeline', 'onMenuShareAppMessage'], false, false, false);
        unset($config['beta']);
        return $response->withJson(['code' => 0, 'msg' => '成功', 'data' => $config]);
    }
}