<?php
/**
 * 微信参数
 */
class Wechat_Base_Params
{
    /**
     * 实例容器
     *
     * @var string
     */
    protected static $_instance = null;
    
    /**
     * 构造方法
     * 
     */
    public function  __construct() {
    }

    /**
     * 获取实例
     *
     * @return	object
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 魔术方法
     *
     * @return	
     */
    public function __set($key, $val)
    {
        if ('_' != substr($key, 0, 1)) {
            $this->$key = $val;
            return;
        }
        require_once 'Zend/Exception.php';
        throw new Zend_Exception(get_class($this).' set variable '.$key.' error!');
    }

    /**
     * 魔术方法
     *
     * @return	
     */
    public function __get($key)
    {
        return null;
    }

    /**
     * 魔术方法
     *
     * @return	
     */
    function  __call($name,  $arguments) {
        
    }

    /**
     * 设置参数
     *
     * @return	
     */
    public function set($key, $val){
        if ('_' != substr($key, 0, 1)) {
            $this->$key = $val;
            return;
        }
    }

    /**
     * 获取参数
     *
     * @return	mixed
     */
    public function get($key){
        if(isset($this->$key)){
            return $this->$key;
        }
        return null;
    }

    /**
     * 获取微信消息类型
     *
     * @return	string
     */
    public function getMsgType(){
        if(isset($this->MsgType)){
            switch ($this->MsgType) {
                case 'event':
                    if(isset($this->Event)){
                        switch ($this->Event){
                            case 'subscribe':
                            case 'unsubscribe':
                            case 'CLICK':
                            case 'SCAN':
                            case 'VIEW':
                            case 'MASSSENDJOBFINISH':
                            case 'TEMPLATESENDJOBFINISH':
                                $msg_type = $this->Event;
                                break;
                            case 'LOCATION';
                                $msg_type = 'LOCATION2';
                                 break;
                            case 'scancode_push':
                            case 'scancode_waitmsg':
                            case 'pic_sysphoto':
                            case 'pic_photo_or_album':
                            case 'pic_weixin':
                            case 'location_select':
                              $msg_type = str_replace('_', '', $this->Event);
                                break;
                            default:
                                break;
                        }
                    }
                    break;
                default:
                    $msg_type = $this->MsgType;
                    break;
            }
        }
        if(!isset($msg_type))$msg_type = 'text';
        return strtolower($msg_type);
    }
}