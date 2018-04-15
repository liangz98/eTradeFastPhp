<?php
/**
 * 选择咨询用户
 */
class Wechat_Intent_Cvisitor{

    protected  $_wechat_base_controller = null;
    
    function  __construct() {
        if(null === $this->_wechat_base_controller){
            $this->_wechat_base_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    function Cvisitor(){
          $msg_type = $this->_wechat_base_controller->_wechat_base_params->getMsgType();
          if($msg_type == "text"){
                $content = trim($this->_wechat_base_controller->_wechat_base_params->Content);
                if(preg_match('/^\d+$/s',$content) && $content > 0){//回复数字
                        $wechat_user = $this->_wechat_base_controller->_wechat_user;
                        if($wechat_user['is_service'] == '1'){
                                $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
                                $condiction = array();
                                $condiction['wc_id'] = $wechat_user['wc_id'];
                                $condiction['server_user_id'] = $wechat_user['user_id'];
                                $condiction['user_id'] = $content;
                                $condiction['is_over'] = "0";
                                $list = $waitlistM->fetchRow($condiction);
                                if($list){
                                    Wechat_Kefu_RecordKefu::connectSucc($list);
                                    exit;
                                }
                        }
                    
                }
          }
          
          //清除标记
          Wechat_Kefu_RecordKefu::clearKefuSatus($this->_wechat_base_controller->_wechat_user['wu_id']);
          
    }
    
    
}