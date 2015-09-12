<?php
class Lang{
	public static function getlang($filename){
		$_data=array();
		if(file_exists($filename)){
			$string=file_get_contents($filename);
			//解析$text
			$_list=preg_split("/\\n/", $string);
			foreach ($_list as $item){
				$poz=stripos($item, '=');
				if($poz>0){
					$key=self::filtrate(substr($item, 0,$poz));
					$value=(substr($item, $poz+1));
					$_data[$key]=$value;
				}
			}
		}
		return $_data;
	}
	
	/**
	 * 函数过滤器
	 * @param unknown $string
	 * @return mixed
	 */
	private static function filtrate($string){
		$string=trim($string,'\r\s\n');
		$string=str_replace('\\r', '\r', $string);
		$string=str_replace('\\n', '\n', $string);
		return $string;
	}
}
?>