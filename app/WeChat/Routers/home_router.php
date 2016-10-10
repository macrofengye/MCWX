<?php
$app = \Core\Utils\CoreUtils::getContainer('app');
$app->map(['GET' , 'POST'] , "/", APP_NAME."\\Controller\\Home:index")->setName("blog.home.index");
$app->map(['GET' , 'POST'] , "/home/index", APP_NAME."\\Controller\\Home:index")->setName("blog.home.index");
$app->map(['GET' , 'POST'] , "/home/hello", APP_NAME."\\Controller\\Home:hello")->setName("blog.home.hello");