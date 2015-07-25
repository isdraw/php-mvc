<?php
require '../../system/bootstrap.php';
bootstrap::$debug=true;
bootstrap::start(false);
//请在config/config.php 中修改数据库账号密码默认是test,root,'root',直接用的phpstudy测试则无需改密码
bootstrap::route('MysqlExample', 'test');