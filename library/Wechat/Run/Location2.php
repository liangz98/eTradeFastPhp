<?php
/**
 * 上报位置消息
 */
class Wechat_Run_Location2{
    protected $_wechat_controller = null;

    function  __construct() {
//        if(null === $this->_wechat_controller){
//            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
//        }
    }
    function run(){
        
//            $Latitude =  $this->_wechat_controller->_wechat_base_params->Latitude;
//            $Longitude = $this->_wechat_controller->_wechat_base_params->Longitude;
//            $Precision = $this->_wechat_controller->_wechat_base_params->Precision;
        
            Wechat_Base_RecordReply::record('');
            
//            $response = new Wechat_Base_Response();
//            $response->responseTextMsg('欢迎上传图片！');
            
            exit;
        
        
    }
}