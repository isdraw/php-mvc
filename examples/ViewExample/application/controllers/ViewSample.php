<?php
class ViewSample extends FrameWork{
	public function welcome(){
		$this->renderer('/welcome',array('notice'=>'你好开发者，下面是view的演示'));
	}
	
	/**
	 * 案例展示
	 */
	public function example(){
		$array=array(
			'simpleVar'=>date('Y-m-d H:i:s'),
			'arrayVar'=>array('name'=>'array name'),
			'objectVar'=>json_decode(json_encode(array('name'=>'object name'))),
			'foreachVar'=>array(
				array('name'=>'a'),
				array('name'=>'b'),
				array('name'=>'c'),
			),
			'ifvar'=>true,
		);
		$this->renderer('example',$array);
	}
}