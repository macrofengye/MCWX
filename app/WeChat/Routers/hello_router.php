<?php
//$app = \Polymer\Utils\CoreUtils::getContainer('app');

$app->get("/hello/show", "WeChat\Controller\Hello:show")->setName("hello.show");
$app->get("/hello/daikuan", "WeChat\Controller\Hello:daikuan")->setName("hello.daikuan");
$app->get("/hello/bangbangdai", "WeChat\Controller\Hello:bangbangdai")->setName("hello.bangbangdai");
$app->get("/hello/dealBangbangdai", "WeChat\Controller\Hello:dealBangbangdai")->setName("hello.dealBangbangdai");
$app->get("/hello/dealDaikuan", "WeChat\Controller\Hello:dealDaikuan")->setName("hello.dealDaikuan");
$app->get("/hello/test", "WeChat\Controller\Hello:test")->setName("hello.test");
$app->post("/hello/test1", "WeChat\Controller\Hello:test1")->setName("hello.test1");
$app->map(['GET' , 'POST'],"/hello/account", "WeChat\Controller\Hello:account")->setName("hello.account");
$app->map(['GET' , 'POST'],"/hello/dealAccount", "WeChat\Controller\Hello:dealAccount")->setName("hello.dealAccount");
$app->map(['GET' , 'POST'],"/hello/getWxConfig", "WeChat\Controller\Hello:getWxConfig")->setName("hello.getWxConfig");



