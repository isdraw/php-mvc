<?php
//引用框架启动文件
require '../../system/bootstrap.php';
//开启调试模式
bootstrap::$debug=true;
//启动应用
bootstrap::start(false);
//自定义路由
$action=$_GET['action'];
if(empty($action)) $action="welcome"; 
//路由跳转
bootstrap::route('HelloWorld', $action);
bootstrap::log('server start');