<?php
//引用框架启动文件
require '../../system/bootstrap.php';
bootstrap::$debug=true;
bootstrap::start(false);
$action=$_REQUEST['action'];
if(empty($action))$action='welcome';
bootstrap::route('viewsample', $action);