<?php
    /**
     * 默认文件预创建模块,调用方式 bootstrap::start(true);
     * @author kubi
     */
    class Build{
        /**
         * 创建默认文件夹
         * @param unknown $dirs
         */
        public static function mkdir($dirs){
            foreach ( $dirs as $item ) {
                $path = bootstrap::appath ( $item );
                if (! file_exists ( $path )) {
                    mkdir ( $path, 0777 );
                    echo $path."<br />";
                }
                //htaccess
                //order allow,denydeny from all
                if(!file_exists($path.'/.htaccess')){
                    file_put_contents($path.'/.htaccess', "order allow,denydeny from all");
                }
                if(!file_exists($path.'/index.html')){
                    file_put_contents($path.'/index.html', '');
                }
            }
        }

        /**
         * 创建默认的config文件
         */
        public static function create_config(){
            $conf=bootstrap::appath('config/config.php');
            if(!file_exists($conf)){
                $text="<?php
	return array(
	    // 主机,端口,数据库,帐号,密码
	    'default' => array(
	        'dsn'=>'mysql:host=localhost;dbname=test',
	        'username'=>'root',
	        'passwd'=>'',
	        'option'=>array(
	        	PDO::ATTR_PREFETCH=>true
	        ),
	    	'sql'=>'SET NAMES \\'utf8\\';',
	    ),
	);
?>";
                file_put_contents($conf, $text);
            }
        }
    }
?>