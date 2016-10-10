<?php 
$app = $container['application']->getContainer('app')->add($container['application']->getContainer("sessionMiddleware"));
$app->get("/hello/show", "Blog\Controller\Hello:show")->setName("hello.show");
$app->map(['GET' , 'POST'] , "/", APP_NAME."\\Controller\\Home:index")->setName("blog.home.index");
$app->map(['GET' , 'POST'] , "/home/index", APP_NAME."\\Controller\\Home:index")->setName("blog.home.index");
$app->map(['GET' , 'POST'] , "/home/hello", APP_NAME."\\Controller\\Home:hello")->setName("blog.home.hello");
