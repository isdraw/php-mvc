<?php
class HelloWorld extends FrameWork{
	public function say(){
		$this->renderer("/sample",array('a'=>true));
	}
}