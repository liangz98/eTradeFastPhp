<?php
/**
 * 点击菜单
 */
class Wechat_Run_Click{
    protected $_wechat_controller = null;

    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    function run(){

        $EventKey = $this->_wechat_controller->_wechat_base_params->EventKey;//点击菜单
        if($this->_wechat_controller->_wechat['bottom_menu']){
            $menu = @unserialize($this->_wechat_controller->_wechat['bottom_menu']);
            if($menu){
                foreach($menu as $k=>$v){
                    if($k == $EventKey){
                        $userWechatReplyM = new Wechat_Model_Reply('wechat');
                        $reply = $userWechatReplyM->fetchRow(array('r_id'=>$v,'wc_id'=>$this->_wechat_controller->_wechat['id']));
                        if($reply){
                            Wechat_Base_ParseReply::parse($reply);
                        }
                        break;
                    }
                }
            }
        }
        $response = new Wechat_Base_Response();
        $response->responseTextMsg('菜单功能调试中...');
        exit;
    }
}
