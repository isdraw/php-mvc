# PHP mvc 框架 #
****
[http://www.isdraw.com](http://www.isdraw.com)

main Entrance

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

control
	
	<?php
	class HelloWorld extends FrameWork{
		public function welcome(){
			$this->renderer('/welcome',array('notice'=>"你好！开发者，这个是一个演示DEMO"));
		}
		
		public function echo_say(){
			echo "this is a helloWorld text";
		}
	}

view

	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Welcome Demo</title>
	</head>
	<body>
	{$notice} <br />
	<a href="?action=echo_say">echo say</a> <br />
	</body>
	</html>

model

	<?php
	class Model_Sample extends FrameWork{
		/**
		 * 增加数据
		 * @param unknown $obj
		 */
		public function sample_insert($obj){
			$params=array();
			$params['sample_name']=$obj['sample_name'];
			$params['sample_createtime']=date('Y-m-d H:i:s');
			$this->pdo()->insert('sample',$params);
		}
		
		/**
		 * 更新数据
		 * @param unknown $id
		 * @param unknown $obj
		 */
		public function sample_update($id,$obj){
			$this->pdo()->update('sample',$obj,array('sample_id'=>(int)$id));
		}
		
		/**
		 * 删除数据
		 * @param unknown $id
		 */
		public function sample_delete($id){
			$this->pdo()->delete('sample',array('sample_id'=>(int)$id));
		}
		
		/**
		 * 获取单个数据返回object
		 * @param unknown $id
		 * @return Ambigous <boolean, mixed>
		 */
		public function sample_getone($id){
			return $this->pdo()->fetch('select * from `sample` where sample_id=:sample_id',array(':sample_id'=>(int)$id));
		}
		
		/**
		 * 获取所有数据返回array
		 * @return Ambigous <boolean, multitype:unknown, multitype:unknown , multitype:>
		 */
		public function sample_getall(){
			return $this->pdo()->fetchAll('select * from `sample`');
		}
	}

有其他问题请发邮件至: nease@163.com
