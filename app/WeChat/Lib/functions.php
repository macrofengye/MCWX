<?php
function haha()
{
    echo "hahahahhasssss";
}
/**
 * 验证用户是否已经登录
 * @author macro chen <macro_fengye@163.com>
 */
function checkLogin($request, $response, $next){
    $sessionContainer = app()->component("session");
    $path = $request->getUri()->getPath();
    preg_match("#login$#" , $path , $matches);
    if(empty($sessionContainer->username) && !count($matches)){
        return $response->withRedirect('/home/login');
    }
    $response = $next($request, $response);
    return $response;
}



/**
 * 检测用户是否有权访问
 * @author macro chen <macro_fengye@163.com>
 */
function checkPermission($request, $response, $next){
    $response = $next($request, $response);
    return $response;
}