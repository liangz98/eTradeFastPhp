<?php
class Wechat_Base_UnknowReply{
   public static function reply(){
       $w_controller = Wechat_Base_Controller::getInstance();
       
       if(isset($w_controller->_wechat['unknow_reply']) && $w_controller->_wechat['unknow_reply'] > 0){
            $wechatReplyM = new Wechat_Model_Reply('wechat');
            $reply = $wechatReplyM->fetchRow(array('r_id'=>$w_controller->_wechat['unknow_reply'],'wc_id'=>$w_controller->_wechat['id']));
            if($reply)Wechat_Base_ParseReply::parse($reply,true);
        }
        
        $responseM = new Wechat_Base_Response();
        if(method_exists($responseM, "responseKeFu")){
            $responseM->responseKeFu();
        	exit;
        }
            
//        $response_msg = '';
//        Wechat_Base_RecordReply::record($response_msg,'text');
//        $response = new Wechat_Base_Response();
//        $response->responseTextMsg($response_msg);
        exit;
    }
}