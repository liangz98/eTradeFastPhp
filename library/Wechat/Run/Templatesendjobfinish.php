<?php
/**
 * 模版消息返回处理
 */
class Wechat_Run_Masssendjobfinish{
    protected $_wechat_controller = null;

    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    function run(){
//        $ToUserName = $this->_wechat_controller->_wechat_base_params->ToUserName;
//        $FromUserName = $this->_wechat_controller->_wechat_base_params->FromUserName;
//        $CreateTime = $this->_wechat_controller->_wechat_base_params->CreateTime;
//        $MsgType = $this->_wechat_controller->_wechat_base_params->MsgType;
//        $Event = $this->_wechat_controller->_wechat_base_params->Event;
//        $MsgID = $this->_wechat_controller->_wechat_base_params->MsgID;
//        $Status = $this->_wechat_controller->_wechat_base_params->Status;
        $str = '';
        $record_id = Wechat_Base_RecordReply::record($str);
        exit();
    }
}
