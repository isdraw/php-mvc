<?php if(!defined("ALLOW_ACCESS"))exit("not access");?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
<div><?php  echo $lang["TITLE"];  ?></div>
<div><?php  echo $lang["CONTENT"];  ?></div>
<?php if ($_GET["lang"]=="cn") { ?>
<a href="?lang=en">english</a>
<?php }else{  ?>
<a href="?lang=cn">中文</a>
<?php } ?>
</body>
</html>