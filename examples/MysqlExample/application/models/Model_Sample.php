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