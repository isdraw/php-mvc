<?php
class Functions{
	/**
	 * 获取微秒级
	 * @return number
	 */
	public static function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}
	
	/**
	 *
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public static function getNonceStr($length = 32)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}
	
	/**
	 * 将数组转成URL
	 * @param mixed $array	数组
	 * @param string $escape	是否需要编码
	 * @return string
	 */
	public static function encodeURL($array,$escape=TRUE){
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($v != "" && !is_array($v)){
				if($escape){
					$buff .= $k . "=" . urlencode($v) . "&";
				}else{
					$buff .= $k . "=" . $v . "&";
				}
			}
		}
	
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * mysql 时间格式
	 * @return string
	 */
	public static function dateTime_now(){
		return date("Y-m-d H:i:s");
	}
}