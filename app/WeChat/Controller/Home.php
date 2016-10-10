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

class Home extends Controller
{
    public function index(Request $request, Response $response, $args){
        $accessToken = CoreUtils::getContainer('oAuth');
       // print_r($accessToken);
    }
    public function index3(Request $request, Response $response, $args){
        $server = CoreUtils::getContainer('server');
        $user = CoreUtils::getContainer('user');
        print_r($user);
        $server->setMessageHandler(function ($message) use ($user) {
            $fromUser = $user->get($message->FromUserName);
            return $message->Content .' ---- ' . $fromUser->nickname;
        });
        return $server->serve();
    }

    public function index2(Request $request, Response $response, $args){
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
            $postObj = simplexml_load_string( $content );
            $toUser   = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $time     = time();
            $msgType  =  'text';
            $content  = '欢迎关注我们的微信公众账号'.$postObj->FromUserName.'-'.$postObj->ToUserName;
            $template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
            $info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            return $response->write($info);
        }
    }

    public function hello($request, Response $response, $args)
    {
        /*$event = $this->addEvent(self::ENTITY, 'db1', 'event_namespace', 'Events::prePersist');
        $em = $this->getDbInstance(self::ENTITY, 'db1');*/
        // print_r(get_class_methods($event));
        /*$this->render($response, "/home/hello.twig", array(
            'name' => 'Macro',
        ));*/
        $response->getBody()->write('aaaaa');
        return $response->write('llkkk');
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