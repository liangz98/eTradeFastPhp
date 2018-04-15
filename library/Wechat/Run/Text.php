<?php
/**
 * 文本消息
 */
class Wechat_Run_Text{
    protected $_wechat_controller = null;
    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    function run(){
        $Content = str_replace(array('"'),array(''),trim($this->_wechat_controller->_wechat_base_params->Content));
        //$CreateTime = isset($this->_wechat_controller->_wechat_base_params->CreateTime)?$this->_wechat_controller->_wechat_base_params->CreateTime:time();
        //模拟登陆获取用户fakeid等
        //$view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        //Mobile_Browser::view_page($view->seed_Setting['wechat_app_server'].'/wechat/wechat-interface/getuserinfo?uid='.$this->_wechat_controller->_wechat_user['user_id'].'&wcid='.$this->_wechat_controller->_wechat['id'].'&c='.$Content.'&t='.$CreateTime.'&token='.md5($this->_wechat_controller->_wechat_user['user_id'].'_'.$this->_wechat_controller->_wechat['id'].md5($CreateTime)));
        
       // if($this->_wechat_controller->_wechat_user['confirm_fakeid'] == '0'){
         //   $stackM = new Wechat_Model_Stack('wechat');
           // $stackM->addStack('1');
       // }
        
        Wechat_Kefu_RecordKefu::kefuOper();//处理客服操作
        //
          //查询报价
        $class_name = 'Wechat_Baojia_Baojia';
        if($this->_wechat_controller->isReadable($class_name)){
            $obj = new $class_name();
            $obj->run($Content);
        }
              
        Wechat_Base_ParseContent::parse($Content); //解释关键字回复内容
        exit;
    }
}
