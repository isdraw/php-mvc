<?php
	return array(
	    // 主机,端口,数据库,帐号,密码
	    'default' => array(
	        'dsn'=>'mysql:host=localhost;dbname=test',
	        'username'=>'root',
	        'passwd'=>'root',
	        'option'=>array(
	        	PDO::ATTR_PREFETCH=>true
	        ),
	    	'sql'=>'SET NAMES \'utf8\';',
	    ),
	);
?>