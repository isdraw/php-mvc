<?php if(!defined("ALLOW_ACCESS"))exit("not access");?><?php  include bootstrap::view('header',null,RENDERER_DEFAULT_PATH,$bootstrap_option);  ?>
<!-- 简单变量 -->
<div>简单变量演示</div>
<?php  echo $simpleVar;  ?> <br />
<!-- 数组变量 -->
<?php  echo $arrayVar['name'];  ?> <br />
<!-- 对象变量 -->
<?php  echo $objectVar->name;  ?> <br />
<!-- 条件判断 -->
<?php if (ifvar===true) { ?>
ifVar 变量为true <br />
<?php }else{  ?>
ifVar 变量为false <br />
<?php } ?>
<!-- 循环变量 -->
<?php  foreach ( $foreachVar as $item) { ?> 
<?php  echo $item["name"];  ?> <br />
<?php  }   ?>
<!-- 原始代码 -->
<?php 
echo "helloWorld sample <br />";
 ?>
//模板代码参考修改自zblog在此特别感谢
<?php  include bootstrap::view('footer',null,RENDERER_DEFAULT_PATH,$bootstrap_option);  ?>