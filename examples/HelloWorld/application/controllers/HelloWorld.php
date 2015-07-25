<?php
class HelloWorld extends FrameWork{
	public function welcome(){
		$this->renderer('/welcome',array('notice'=>"你好！开发者，这个是一个演示DEMO"));
	}
	
	public function echo_say(){
		echo "this is a helloWorld text";
	}
}