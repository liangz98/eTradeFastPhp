<?php
/**
 * 微信控制器
 */
class Wechat_Base_Controller{
    
    public  static $_instance = null;
    
    public  $_wechat_base_params = null;
    public  $_wechat_base_response = null;
    public  $_wechat = null;
    public  $_wechat_user = null;
    public  $_record_reply = true;
    public  $_select_priority = '0';

    function  __construct() {
        if (null === $this->_wechat_base_params) {
           $this->_wechat_base_params = Wechat_Base_Params::getInstance();
        }
        if (null === $this->_wechat_base_response) {
            $this->_wechat_base_response = new Wechat_Base_Response();
        }
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __set($key, $val)
    {
        if ('_' != substr($key, 0, 1)) {
            $this->$key = $val;
            return;
        }
        require_once 'Zend/Exception.php';
        throw new Zend_Exception(get_class($this).' set variable '.$key.' error!');
    }
    
    public function __get($key)
    {
        return null;
    }

    public  function isReadable($class_name,$path = null){
        if(class_exists($class_name)){
            return true;
        }
        if(!$path && !defined('SEED_LIB_ROOT')){
            require_once 'Zend/Exception.php';
            throw new Zend_Exception(get_class($this).' path has not given and SEED_LIB_ROOT has not defined!');
        }elseif(!$path){
            $path = SEED_LIB_ROOT;
        }
        $file_name = $class_name;
        if(strpos($class_name,'_') != false){
            $file_name = str_replace('_','/', $class_name);
        }
        $file_name = rtrim($path,'/').'/'.$file_name.'.php';
        return Zend_Loader::isReadable($file_name);
    }

    public function isRunnable($class_name,$method = 'run',$path = null){
        if($this->isReadable($class_name, $path)){
            $obj = new $class_name();
            if(method_exists($obj,$method)){
                return $obj;
            }
            return false;
        }
    }


}