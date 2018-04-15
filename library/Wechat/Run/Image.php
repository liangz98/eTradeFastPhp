<?php
/**
 * 图片消息
 */
class Wechat_Run_Image{
    protected $_wechat_controller = null;
    
    function  __construct() {
//        if(null === $this->_wechat_controller){
//            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
//        }
    }

    function run(){
        Wechat_Base_RecordReply::record('欢迎上传图片！');
        $response = new Wechat_Base_Response();
        $response->responseTextMsg('欢迎上传图片！');
        exit;
    }
}
