<?php
/**
 * 链接消息
 */
class Wechat_Run_Link{
    protected $_wechat_controller = null;

    function  __construct() {
//        if(null === $this->_wechat_controller){
//            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
//        }
    }
    
    function run(){
        Wechat_Base_RecordReply::record('谢谢您分享了一个链接');
        $response = new Wechat_Base_Response();
        $response->responseTextMsg('谢谢您分享了一个链接');
        exit;
    }
}
