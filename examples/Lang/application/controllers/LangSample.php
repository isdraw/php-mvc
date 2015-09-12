<?php
class LangSample extends FrameWork{
	public function cn(){
		$lang=$_GET["lang"];
		if(empty($lang)) $lang="cn";
		$lang=bootstrap::lang($lang);
		$this->renderer('/TestLang',array('lang'=>$lang));
	}
}