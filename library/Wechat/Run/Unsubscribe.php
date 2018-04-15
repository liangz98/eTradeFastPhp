<?php
/**
 * 取消关注/取消订阅
 */
class Wechat_Run_Unsubscribe{
    protected $_wechat_controller = null;

    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    
    function run(){
        Wechat_Base_RecordReply::record('取消订阅');
        if(isset($this->_wechat_controller->_wechat_user)){
            $userWechatUserM = new Wechat_Model_User('wechat');
            $userWechatUserM->unfollow($this->_wechat_controller->_wechat_user['wu_id']);
            if(empty($this->_wechat_controller->_wechat_user['nick_name'])){
                $dataSet = array();
                $dataSet['nick_name'] = '微信用户';
                $userWechatUserM->updateRow($dataSet, array('wu_id'=>$this->_wechat_controller->_wechat_user['wu_id']));
                $userM = new Seed_Model_User('system');
                $userM->updateRow($dataSet, array('user_id'=>$this->_wechat_controller->_wechat_user['user_id']));
            }
        }
        exit;
    }
}