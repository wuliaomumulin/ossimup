<?php
//echo 111;die;
header('content-type:text/html;charset=utf-8');
//header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods:GET, POST');
header('Access-Control-Max-Age:86400');
define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
session_start();
$app  = new Yaf\Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();
?>
