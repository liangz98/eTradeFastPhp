<?php
class Wechat_Intent_Webkefu{
    protected  $_wechat_base_controller = null;
    
    function  __construct() {
        if(null === $this->_wechat_base_controller){
            $this->_wechat_base_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    function Webkefu(){
        
           $msg_type = $this->_wechat_base_controller->_wechat_base_params->getMsgType();
           if($msg_type == 'text'){
                $recrod_webkefuM = new Wechat_Model_RecordWebKefu('wechat');  
                $web_conversation = $recrod_webkefuM->fetchRow(array('rk_id'=>$this->_wechat_base_controller->_wechat_user['ping_fakeid']));
                if($web_conversation){
                    $content = strip_tags(trim($this->_wechat_base_controller->_wechat_base_params->Content));
                    $arr = array('０' => '0');
                    $content =  strtr($content,$arr);
                    if($content == '00'){
                        Wechat_Kefu_RecordKefu::clearKefuSatus($this->_wechat_base_controller->_wechat_user['wu_id']);
                        $this->_wechat_base_controller->_wechat_base_response->responseTextMsg('结束本次网页咨询成功！');
                        exit;
                    }
                    
                    $dataSet = array();
                    $dataSet['wc_id'] = $this->_wechat_base_controller->_wechat_user['wc_id'];
                    $dataSet['kefu_user_id'] = $this->_wechat_base_controller->_wechat_user['user_id'];
                    $dataSet['resp_name'] = $this->_wechat_base_controller->_wechat_user['service_name'] ? $this->_wechat_base_controller->_wechat_user['service_name'] : '客服'.$this->_wechat_base_controller->_wechat_user['service_num'].'号';
                    $dataSet['from_user_id'] = $this->_wechat_base_controller->_wechat_user['user_id'];
                    $dataSet['from_user_token'] = $web_conversation['from_user_token'];
                    $dataSet['to_user_id'] = $web_conversation['from_user_id'];
                    $dataSet['type'] = 1;
                    $dataSet['content'] = $content;
                    $dataSet['local_url'] = "";
                    $dataSet['is_kefu_resp'] = "1";
                    $dataSet['add_time'] = time();
                    $dataSet['need_output'] = '1';
                    $recrod_webkefuM->insertRow($dataSet);
                    exit;
//                    $this->_wechat_base_controller->_wechat_base_response->responseTextMsg('回复成功！');
                }
           }else{
               $this->_wechat_base_controller->_wechat_base_response->responseTextMsg('对不起，网页咨询回复只支持文本！');
           }
        
    }
    
}