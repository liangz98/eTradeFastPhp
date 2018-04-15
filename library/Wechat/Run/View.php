<?php
/**
 * 点击直接跳转的菜单
 */
class Wechat_Run_View{
    protected $_wechat_controller = null;
    
    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    
     function run(){
         
         
         $EventKey = $this->_wechat_controller->_wechat_base_params->EventKey;//点击菜单
         if($EventKey){
             Wechat_Base_RecordReply::record($EventKey); 
         }
         
//         
//         $Autoload = SEED_TEMP_ROOT.DIRECTORY_SEPARATOR.'aaaa.txt';
//            if($fp = @fopen($Autoload,"w")){//生成文件
//                flock($fp,LOCK_EX);
//                fwrite($fp, $this->_wechat_controller->_wechat_base_params->EventKey);
//                flock($fp,LOCK_UN);
//                fclose($fp);
//            }
            exit;
     }
}

