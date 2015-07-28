<?php
require '../../system/bootstrap.php';
bootstrap::$debug = true;
bootstrap::start ();
//---------------------------------------
bootstrap::import('test/TestClass');
$tc=new TestClass();
bootstrap::view ( '/singleSample', array (
		'time' => date ( "Y-m-d H:i:s" ) ,
		'name'=>bootstrap::model('sample', "getname"),
		'msg'=>$tc->doTest(),
) );
bootstrap::log('服务器启动了');