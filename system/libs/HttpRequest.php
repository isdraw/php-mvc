<?php
	/**
	 * @copyright Copyright (c) 2015 isdraw.com. All rights reserved.
	 */
	class HttpRequest{
		private $_url;
		private $_options;
		private $_error;
		private $_status;
		private $_info;
		private $_response;
		
		function __construct($url,$optionArray=NULL){
			$this->_url=$url;
			$this->_options=$optionArray;
			if(empty($this->_options))$this->_options=array();
		}
		/**
		 * 执行
		 */
		public function execute(){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch,CURLOPT_URL, $this->_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			foreach ($this->_options as $k=>$v) curl_setopt($ch, intval($k), $v);
			$this->_response = curl_exec($ch);
			$this->_status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$this->_info=curl_getinfo($ch);
			if(curl_errno($ch)){
				$this->_error= curl_error($ch);
			}
			curl_close($ch);
		}
		
		
		/**
		 * 是否需要创建头部
		 * @param mixed $header
		 */
		public function Option_header($header=NULL){
			if(!empty($header))
				$this->_options[CURLOPT_HEADER]=$header;
		}
		
		/**
		 * 创建POST对象
		 * @param mixed $data
		 */
		public function Option_post($data){
			$this->_options[CURLOPT_POST]=TRUE;
			$this->_options[CURLOPT_POSTFIELDS]=$data;
		}
		
		/**
		 * SSL检测
		 * @param string $peer CURLOPT_SSL_VERIFYPEER
		 * @param string $host CURLOPT_SSL_VERIFYHOST
		 */
		public function Option_verify($peer=FALSE,$host=FALSE){
			$this->_options[CURLOPT_SSL_VERIFYPEER]=FALSE;
			$this->_options[CURLOPT_SSL_VERIFYHOST]=FALSE;
		}
		
		/**
		 * 使用SSL证书
		 * @param string $pemfile	证书地址
		 * @param string $type		证书类型
		 * @param string $pwd		证书密码
		 */
		public function Option_sslCert($pemfile,$type="PEM",$pwd=NULL){
			$this->_options[CURLOPT_SSLCERTTYPE]=$type;
			$this->_options[CURLOPT_SSLCERT]=$pemfile;
			if(!empty($pwd))
				$this->_options[CURLOPT_SSLCERTPASSWD]=$pwd;
		}
		
		/**
		 * 使用SSL证书KEY
		 * @param string $pemfile	证书地址
		 * @param string $type		证书类型
		 * @param string $pwd		证书密码
		 */
		public function Option_sslKey($pemfile,$type="PEM",$pwd=NULL){
			$this->_options[CURLOPT_SSLKEYTYPE]=$type;
			$this->_options[CURLOPT_SSLKEY]=$pemfile;
			if(!empty($pwd))
				$this->_options[CURLOPT_SSLKEYPASSWD]=$pwd;
		}
		
		/**
		 * 删除指定的键值
		 * @param unknown $k
		 */
		public function Option_remove($k){
			unset($this->_options[$k]);
		}
		
		/**
		 * 清空配置
		 */
		public function Option_clear(){
			unset($this->_options);
			$this->_options=array();
		}
		
		/**
		 * 使用代理
		 * @param unknown $host	主机
		 * @param unknown $port	端口
		 */
		public function Option_Proxy($host,$port){
			$this->_options[CURLOPT_PROXY]=$host;
			$this->_options[CURLOPT_PROXYPORT]=$port;
		}
		
		/**
		 * 将数组转成URL
		 * @param mixed $array	数组
		 * @param string $escape	是否需要编码
		 * @return string
		 */
		public function encodeURL($array,$escape=TRUE){
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
		
		public function get_options(){return $this->_options;}
		public function get_statusCode(){return $this->_status;}
		public function get_error(){return $this->_error;}
		public function get_response(){return $this->_response;}
		public function get_url(){return $this->_url;}
		public function get_info(){return $this->_info;}
	}
?>