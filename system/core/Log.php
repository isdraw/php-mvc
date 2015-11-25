<?php
/**
 * 日志处理模块
 * @author kubi
 * @link http://www.isdraw.com/mvc/?tag=log
 */
class Log
{
    private $handle = null;
    private static $instance = null;
    private $levelString=array(
        LOG_INFO=>"INFO",
        LOG_WARNING=>"WARN",
        LOG_DEBUG=>"DEBUG",
        LOG_ERR=>"ERROR",
    );

    public function __construct($file = '')
    {
        if(!file_exists(dirname($file))){
            mkdir ( dirname($file), 0777 );
        }
        $this->handle = fopen($file, 'a');
    }

    /**
     * 写入日志信息
     * @param int $level 参考常量LOG_
     * @param string $msg 日志内容
     * @link http://www.isdraw.com/mvc/?tag=log-write
     */
    public function write($level,$msg)
    {
        $msg = '[' . date('H:i:s') . '][' . $this->levelString[$level] . '] ' . $msg . "\r\n";
        fwrite($this->handle, $msg, 4096);
    }

    public function __destruct()
    {
        fclose($this->handle);
    }

    /**
     * 初始化
     * @param string $file
     * @link http://www.isdraw.com/mvc/?tag=log-init
     * @return Log
     */
    public static function Init($file='')
    {
        if (! self::$instance) {
            self::$instance = new self($file);
        }
        return self::$instance;
    }

    /**
     * INFO日志
     * @param string $msg
     * @link http://www.isdraw.com/mvc/?tag=log-info
     */
    public static function INFO($msg)
    {
        self::$instance->write(LOG_INFO, $msg);
    }

    /**
     * 警告日志
     * @param string $msg
     * @link http://www.isdraw.com/mvc/?tag=log-warn
     */
    public static function WARN($msg)
    {
        self::$instance->write(LOG_WARNING, $msg);
    }

    /**
     * 调试日志
     * @param string $msg
     * @link http://www.isdraw.com/mvc/?tag=log-debug
     */
    public static function DEBUG($msg)
    {
        self::$instance->write(LOG_DEBUG, $msg);
    }

    /**
     * 错误日志
     * @param string $msg
     * @link http://www.isdraw.com/mvc/?tag=log-error
     */
    public static function ERROR($msg)
    {
        $stack=var_export(debug_backtrace(),true);
        self::$instance->write(LOG_ERR, $stack . $msg);
    }
}