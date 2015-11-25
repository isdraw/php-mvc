<?php
/**
 * @copyright Copyright (c) 2015 isdraw.com. All rights reserved.
 */
error_reporting (E_ALL);
date_default_timezone_set ( 'PRC' );
define ( "ALLOW_ACCESS", true );
define ( "RENDERER_DEFAULT", 0 );
define ( "RENDERER_DEFAULT_PATH", 1 );
define ( "RENDERER_HEAD", '<?php if(!defined("ALLOW_ACCESS"))exit("not access");?>' );

if (! function_exists ( 'isdraw_autoloader' )) {
	function isdraw_autoloader($classname) {
		$class = strtolower ( $classname );
        $dirs=array('core');
        foreach ($dirs as $item){
    		$classFile = __DIR__."/$item/" . str_replace ( '_', '/', $classname ) . ".php";
    		if (file_exists ( $classFile ) && ! class_exists ( $class )) {
    			require  $classFile;
    			break;
    		}
        }
	}
	spl_autoload_register ( 'isdraw_autoloader' );
}

if (! function_exists ( "isdraw_error_handler" )) {
	function isdraw_error_handler($error_level, $error_message, $file, $line) {
		switch ($error_level) {
			case E_NOTICE :
			case E_USER_NOTICE :
			case E_WARNING :
			case E_USER_WARNING :
				break;
			case E_ERROR :
			case E_USER_ERROR :
				isdraw_error_tigger( $error_message,500);
				break;
			default :
				isdraw_error_tigger( $error_message,500);
				break;
		}
	}
	set_error_handler ( 'isdraw_error_handler' );
}

if (! function_exists ( "isdraw_error_shutdown" )) {
	function isdraw_error_shutdown() {
		$exception = error_get_last ();
		isdraw_trace_error($exception["file"],$exception['line'], $exception['message']);
	}
	register_shutdown_function ( "isdraw_error_shutdown" );
}

if (! function_exists ( "isdraw_error_tigger" )) {
	function isdraw_error_tigger($name,$state=500) {
			$trace = debug_backtrace ();
			// 查找对应的非系统内容
			for($i = 0; $i < count ( $trace ); $i ++) {
			    $m=$trace[$i];
			    $r=isdraw_trace_error($m['file'],$m['line'], $name,$state);
                if($r===true){
                    break;
                }
			}
	}
}

/**
 * 输出日志
 * @param unknown $file
 * @param unknown $line
 * @param unknown $tigger
 */
function isdraw_trace_error($file,$line,$name,$state){
    if(strpos($file, __DIR__)===false && !empty($line)){
        if(is_callable('__handle')){
            call_user_func('__handle',$state,array('file'=>$file,'line'=>$line,'name'=>$name));
        }else{
            if(!bootstrap::$debug) return ;
            $text= $file ." at line ".$line;
            $text.=" <span style='color:red;'>$name</span>";
            echo $text."<br />";
        }
        return true;
    }
    return false;
}

/**
 * 当前相对路径
 * @return string
 */
function base_url(){
    $uri= $_SERVER['REQUEST_URI'];
    $poz= strripos($uri, '/');
    if($poz>=0){
        $uri=substr($uri,0, $poz);
    }
    return trim($uri,'/').'/';
}

/**
 * 当前绝对路径
 * @return string 返回以http开头的路径
 */
function website_url(){
    $protocal=strtolower($_SERVER['SERVER_PROTOCOL']);
    if(strpos($protocal, 'http/')===0){
        $protocal="http://";
    }else{
        $protocal="https://";
    }
    $protocal.=$_SERVER['SERVER_NAME'].'/'.base_url();
    return $protocal;
}

/**
 * 获取目录中的子文件夹
 * @param string $dir
 */
function list_files($dir){
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

/**
 * 自定义迷你PHP框架,框架包括库导入,路由,模块,视图等基础功能
 * 并可以灵活扩展,使用简单,除了数据库需要pdo环境之外,无其他配置.
 * @author kubi
 * @link http://www.isdraw.com/mvc/?tag=bootstrap
 */
class bootstrap {
	private static $_config = array ();
	public static $debug = false;
	private static $name;
    private static $_map;
    private static $_tpl;
    private static $_pdo;
    private static $appname;

    /**
     * 引擎的启动文件(必须)
     * @param string $need_create_dir 是否需要创建基础目录
     * @param string $appname	当前的应用名称,默认为application
     * @link http://www.isdraw.com/mvc/?tag=bootstrap-start
     */
	public static function start($need_create_dir=false,$appname=NULL) {
		session_start();
		self::$_pdo=array();
	    self::$_map=array();
	    self::$_tpl=new Template();
        if(empty($appname)) $appname='application';
        self::$appname=$appname;
		if($need_create_dir){
		    require __DIR__.'/core/Build.php';
            Build::mkdir(array ('','config','controllers','models','views','libraries','logs','langs'));
            Build::create_config();
		}
	}

	/**
	 * 当前的配置选项由application/config/config.php 定义
	 * @param string $name 配置节点名称
	 * @return boolean|multitype:
	 * @link http://www.isdraw.com/mvc/?tag=bootstrap-config
	 */
	public static function config($name) {
	    if(empty(self::$_config)){
	        if(file_exists(self::appath ( "config/config.php" ))){
	           self::$_config = require_once self::appath ( "config/config.php" );
	        }
	    }
		if (! isset ( self::$_config [$name] )) {
			return false;
		}
		return self::$_config [$name];
	}

	/**
	 * /**
	 * 当前数据库连接对象
	 * @param string $name	由application/config/config.php 中定义的数据连接方式定义
	 * @link http://www.isdraw.com/mvc/?tag=bootstrap-pdo
	 * @return unknown|Drive_PDO
	 */
	public static function pdo($name='default'){
		global $isdraw_pdo_pool;
		if(isset($isdraw_pdo_pool[$name])){
			return $isdraw_pdo_pool[$name];
		}else{
			$_c=self::config($name);
			if($_c){
			    require_once 'drive/Drive_PDO.php';
			    $dsn=$_c["dsn"];
			    $username=$_c["username"];
			    $passwd=$_c["passwd"];
			    $option=$_c["option"];
			    $sql=$_c["sql"];
			    if(empty($option) && !is_array($option)) $option=array();
			    $_db=new Drive_PDO($dsn, $username, $passwd, $option,$sql);
				return $_db;
			}
		}
		isdraw_error_tigger("key $name not found in config.php",404);
		exit();
	}

	/**
	 * 视图全局控制方式,位于application/views/
	 * @param string $view		视图名称,不以html结尾
	 * @param unknown $_param		视图渲染参数
	 * @param number $level	渲染方式RENDERER_常量定义
	 * @param string $option	渲染参数,由Framework定义工作路径 array('dirname'=>)
	 * @link http://www.isdraw.com/mvc/?tag=bootstrap-view
	 * @return boolean|string 返回正确路径或false
	 */
	public static function view($view, $_param = array(), $level = 0,$option=NULL) {
		$view=str_replace('\\', '/', $view);
		if(strpos($view, "/")!==0){
			if($option && isset($option["dirname"])){
				$view=$option["dirname"].'/'.$view;
			}else{
				$dirname=dirname($view);
				$option=array('dirname'=>$dirname=='.'?'':$dirname);
			}
		}
		$view=trim($view,"/");
		$templatefile = self::appath ( "views/$view.html" );
		if (! file_exists ( $templatefile )) {
			isdraw_error_tigger ( "view $view is not exists!",404);
			return false;
		}
		$cachefile=self::appath ( "cache/$view.php");
		if (! file_exists ( $cachefile ) || filemtime ( $cachefile ) < filemtime ( $templatefile )) {
			$cachedir = dirname ( $cachefile );
			if (! file_exists ( $cachedir )) {
				mkdir ( $cachedir, 0777, true );
			}
			file_put_contents( $cachefile, RENDERER_HEAD . self::$_tpl->Compiling ( file_get_contents ( $templatefile ) ));
		}

		if (file_exists ( $cachefile )) {
			switch ($level) {
				case RENDERER_DEFAULT :
				default :
					if($option!=null){
						$_param["bootstrap_option"]=$option;
					}
					extract ( $_param, EXTR_PREFIX_SAME, "isdraw_prefix" );
					require_once $cachefile;
					break;
				case RENDERER_DEFAULT_PATH :
					return $cachefile;
					break;
			}
		}
	}

	/**
	 * 删除cache
	 * @link http://www.isdraw.com/mvc/?tag=bootstrap-remove_cache
	 */
	public static function remove_cache(){
        return self::remove_cache_protected(self::appath('cache'));
	}

	/**
	 * 内部删除缓存的保护方法
	 * @param unknown $path 删除的路径
	 * @return boolean
	 */
	private static function remove_cache_protected($path){
	    $pattern="/^\\.{1,2}$/";
	    $dh=opendir($path);
	    while ($file=readdir($path)) {
	        if(preg_match($pattern, $file)) {
	            $fullpath=$path."/".$file;
	            if(!is_dir($fullpath)) {
	                unlink($fullpath);
	            } else {
	                self::remove_cache_protected($fullpath);
	            }
	        }
	    }
	    closedir($dh);
	    //删除当前文件夹：
	    if(rmdir($path)) {
	        return true;
	    } else {
	        return false;
	    }
	}

	/**
	 * 全局性路由器,位于application/controllers/目录下
	 * @param unknown $controller 控制器类名称
	 * @param unknown $action	控制器的public方法
	 * @link http://www.isdraw.com/mvc/?tag=route
	 * @return mixed
	 */
	public static function route($controller, $action) {
		$array=func_get_args();
		array_unshift($array, "");
		array_unshift($array, "");
		array_unshift($array, "controllers");
		return call_user_func_array ( array (
				__CLASS__,
				'delegate'
		), $array);
	}

	/**
	 * 模块调用位于application/model/
	 * @param unknown $model	类名
	 * @param unknown $method 方法名public
	 * @link http://www.isdraw.com/mvc/?tag=model
	 * @return mixed
	 */
	public static function model($model, $method) {
		$array=func_get_args();
		array_unshift($array, "");
		array_unshift($array, "Model_");
		array_unshift($array, "models");
		return call_user_func_array ( array (
				__CLASS__,
				'delegate'
		), $array);
	}

	/**
	 * 内部调用model,controller核心类
	 * @param unknown $type	类型
	 * @param unknown $pre 前缀
	 * @param unknown $mpre
	 * @param unknown $classname 类名
	 * @param unknown $method 方法名public
	 * @return mixed
	 */
	private static function delegate($type,$pre,$mpre,$classname,$method){
		$abs_class="$pre$classname";
		$file_path = self::appath ( "$type/$abs_class.php" );
		if (! isset ( self::$_map [$abs_class] )) {
			if (file_exists ( $file_path )) {
				require_once $file_path;
				if (class_exists ( $abs_class )) {
					$_instance = new $abs_class ();
					if (method_exists ( $_instance, "__init" )) {
						$_instance->__init ();
					}
					self::$_map [$abs_class] = $_instance;
				}
			}
		}

		if (isset ( self::$_map [$abs_class] )) {
			$ctrl_instance = self::$_map [$abs_class];
			$method="$mpre$method";
			if(method_exists($ctrl_instance, '_'.$method)){
                $method='_'.$method;
			}
			if (method_exists ( $ctrl_instance, $method ) && $method!="__init") {
				$params = func_get_args();
				array_splice($params, 0,5);
				return call_user_func_array ( array (
						$ctrl_instance,
						$method
				), $params );
			}else{
				isdraw_error_tigger("$type $classname::$method() is not found!",404);
			}
		} else {
		     isdraw_error_tigger("$type $classname is not found!",404);
		}
	}

	 /**
	  * 类库或文件导入功能
	  * @param unknown $path 目录相对application/libraries
	  * @link http://www.isdraw.com/mvc/?tag=import
	  */
	public static function import($path){
		if(strpos($path,'.php')===false){
			$path=$path.'.php';
		}
		$lib_file=self::appath('libraries/'.$path);
		if(file_exists($lib_file)){
			 require_once $lib_file;
		}else{
		      isdraw_error_tigger($lib_file." is not found!",404);
		}
	}

	/**
	 * 当前应用程序跟目录本地路径
	 * @param string $filename	相对路径名称
	 * @link http://www.isdraw.com/mvc/?tag=appath
	 * @return string
	 */
	public static function appath($filename = "") {
		if(empty(self::$name)){
		    self::$name=dirname ( $_SERVER ["SCRIPT_FILENAME"] ).'/'.self::$appname;
		}
		return self::$name . '/' . $filename;
	}

	/**
	 * URL跳转功能
	 * @param unknown $path 跳转的路径
	 * @param string $array 参数key=>value
	 * @link http://www.isdraw.com/mvc/?tag=redirect
	 */
	public static function redirect($path,$array=NULL){
        $str="";
        if($array!=null){
            foreach ($array as $key=>$value){
                $str.=$key."=".$value."&";
            }
            $str=trim($str,'&');
            $path=$path."?".$str;
        }
        header("Location: $path");
    }

	/**
	 * 全局日志输出位于application/logs
	 * @param unknown $msg	输出的日志可以为object/array/string,如果不是string则输出json格式
	 * @param number $level 日志类型 LOG_ 常量
	 * @link http://www.isdraw.com/mvc/?tag=bootstrap-log
	 */
	public static function log($msg,$level=LOG_INFO){
	    global $isdraw_trace_log;
	    if($isdraw_trace_log==null){
	        $logHandler= Log::Init(self::appath("/logs/".date('Y-m-d').'.log'));
	        $isdraw_trace_log = Log::Init($logHandler, 15);
	    }
	    switch (gettype($msg)){
	        case "array":
	        case "object":
	            $msg=json_encode($msg);
	            break;
	        default:
	            $msg=(string)$msg;
	            break;
	    }
        switch ($level){
            case LOG_DEBUG:
                Log::DEBUG($msg);
                break;
            case LOG_INFO:
                Log::INFO($msg);
                break;
            case LOG_ERR:
                Log::ERROR($msg);
                break;
            case LOG_WARNING:
                Log::WARN($msg);
                break;
        }
	}
	
	/**
	 * 创建默认控制器
	 * @param unknown $ctrl_name 控制器名称
	 */
	public static function create_ctrl($ctrl_name){
		$filename=self::appath('controllers');
		if(file_exists($filename)){
			$filename=self::appath('controllers/'.$ctrl_name.'.php');
			if(!file_exists($filename)){
				$data=sprintf("<?php\r\nclass %s extends FrameWork{\r\n\tpublic function index(){\r\n\t}\r\n}",$ctrl_name);
				file_put_contents($filename, $data);
			}
		}else{
			exit('ctrl folder is not exists');
		}
	}
	
	/**
	 * 创建默认模块
	 * @param unknown $model_name 模块名称
	 */
	public static function create_model($model_name){
		$filename=self::appath('models');
		if(file_exists($filename)){
			$filename=self::appath('models/Model_'.$model_name.'.php');
			if(!file_exists($filename)){
				$data=sprintf("<?php\r\nclass Model_%s extends FrameWork{\r\n\tpublic function index(){\r\n\t}\r\n}",$model_name);
				file_put_contents($filename, $data);
			}
		}
	}
	
	/**
	 * 语言扩展包
	 * @param string $name
	 * @return multitype:
	 */
	public static function lang($name){
		return Lang::getlang(self::appath('langs/'.$name.'.lang'));
	}
}
?>