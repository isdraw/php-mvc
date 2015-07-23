<?php
class HelloWorld extends FrameWork{
	public function say(){
		$this->pdo()->insert('sample',array('sample_text'=>date('Y-m-d H:i:s')));
	}
}