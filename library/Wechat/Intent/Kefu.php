<?php
class Wechat_Intent_Kefu{

    protected  $_wechat_base_controller = null;
    
    function  __construct() {
        if(null === $this->_wechat_base_controller){
            $this->_wechat_base_controller = Wechat_Base_Controller::getInstance();
        }
    }

    public function Kefu(){
        
        if(null === $this->_wechat_base_controller->_wechat_user)return ;
        $msg_type = $this->_wechat_base_controller->_wechat_base_params->getMsgType();
        $obj_name = 'Wechat_Kefu_'.ucfirst($msg_type);
        $method = 'kefu';
        if($this->_wechat_base_controller->isRunnable($obj_name,$method)){
            $obj = new $obj_name();
            $obj->$method();//根据类型处理相关程序
        }else{
            $wechat_user = $this->_wechat_base_controller->_wechat_user;
            if($wechat_user['ping_fakeid'] > 0){//对话
                $response = new Wechat_Base_Response();
                $response->responseTextMsg('[系统消息]对不起，为了更好的给你提供服务，请用文字消息交流。');
            }else{
                 $userWechatUserM = new Wechat_Model_User('wechat');
                 //清除客服标记
                 $userWechatUserM->updateRow(array('ping_fakeid'=>'0','sign'=>''), array('wu_id'=>$wechat_user['wu_id']));
            }
        }
    }

    
}