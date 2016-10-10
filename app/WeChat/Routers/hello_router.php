<?php
//$app = \Core\Utils\CoreUtils::getContainer('app');

$app->get("/hello/show", "Blog\Controller\Hello:show")->setName("hello.show");
