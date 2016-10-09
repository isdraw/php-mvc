<?php
class Native{
	 /*
	 * 获取目录中的子文件夹
	 * @param string $dir
	 */
	public static function list_files($dir){
		$list=array('files'=>array(),'dirs'=>array());
		if ( $handle = opendir($dir)) {
			while ( ($file = readdir($handle)) !== false )
			{
				if(!preg_match("/(^\\.+$)|(^\\.htaccess$)/", $file)){
					if(is_dir($dir.'/'.$file)){
						$list['files'][]=$file;
					}else{
						$list['dirs'][]=$file;
					}
				}
			}
		}
		return $list;
	}
}