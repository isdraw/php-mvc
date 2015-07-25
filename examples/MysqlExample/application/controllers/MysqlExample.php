<?php
class MysqlExample extends FrameWork {
	public function test() {
		$act = $_REQUEST ['act'];
		if (empty ( $act ))
			$act = "list";
		if($act=="do_add"){
			$this->model('sample', 'sample_insert',$_POST);
		}
		$this->renderer ( '/demo', array (
						'act' => 'list',
						'list' => $this->model ( 'sample', 'sample_getall' ) 
				) );
	}
}