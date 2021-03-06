<?php
/**
 * @author kubi
 * @link http://www.isdraw.com/mvc/?tag=framework
 * 框架的核心基类,按需求分配为model/controller
 * @copyright Copyright (c) 2015 isdraw.com. All rights reserved.
 */
class FrameWork{
    public $classname;
    public function __construct(){
        $this->classname=get_class($this);
    }

    /**
     * 渲染模版文件,位于application/views/controllername/
     * @param unknown $view 视图名称
     * @param unknown $_param 渲染参数
     * @link http://www.isdraw.com/mvc/?tag=framework-renderer
     */
    public function renderer($view,$_param=array()){
        bootstrap::view($view,$_param,0,array(
            'dirname'=>$this->classname
        ));
    }

    /**
     * /**
     * 数据库PDO操作公共对象
     * @param string $poolname 配置名称
     * @link http://www.isdraw.com/mvc/?tag=framework-pdo
     * @return Drive_PDO
     */
    public function pdo($poolname='default'){
        return bootstrap::pdo($poolname);
    }

    /**
     * 数据模块操作 位于application/models
     * @param unknown $classname 类名称,去掉model_
     * @param unknown $method public的方法名
     * @link http://www.isdraw.com/mvc/?tag=framework-model
     * @return mixed
     */
    public function model($classname, $method){
        $array=func_get_args();
        return call_user_func_array ( array (
            "bootstrap",
            'model'
        ), $array);
    }

    /**
     * 基础路由,可以使用bootstrap::route ,位于application/controllers/
     * @param unknown $classname 路由类名
     * @param unknown $method	路由public方法
     * @link http://www.isdraw.com/mvc/?tag=framework-route
     * @return mixed
     */
    public function route($classname,$method){
        $array=func_get_args();
        return call_user_func_array ( array (
            "bootstrap",
            'route'
        ), $array);
    }
    
    /**
     * 初始化
     */
    public function __init(){}
    /**
     * 方法没找到提示
     */
    public function __error($method=NULL){
    	isdraw_error_tigger("$this->classname::$method() is not found!",404);
    }
}
?>