<?php
/**
 * 语音消息
 */
class Wechat_Run_Voice{
    protected $_wechat_controller = null;
    
    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    function run(){
        
        $Recognition = isset($this->_wechat_controller->_wechat_base_params->Recognition)?$this->_wechat_controller->_wechat_base_params->Recognition:'';
        if($this->_wechat_controller->_wechat['advance_auth'] == '1' && !empty($Recognition)){
                
                //查询报价
                $class_name = 'Wechat_Baojia_Baojia';
                if($this->_wechat_controller->isReadable($class_name)){
                    $obj = new $class_name();
                    $obj->run($Recognition);
                }
            
                Wechat_Base_ParseContent::parse(strip_tags($Recognition)); //解释关键字回复内容
                exit;
        }else{
                $str = '对不起，我还听不懂语音呢！试试发送文字吧！';
                $record_id = Wechat_Base_RecordReply::record($str);
                if($record_id){
                    $stackM = new Wechat_Model_Stack('wechat');
                    $stackM->addGetMaterialStack($record_id);
                }
                $response = new Wechat_Base_Response();
                $response->responseTextMsg($str);
                exit;  
        }
        

    }
}

