<?php
/**
 * 微信客服类
 */
class Wechat_Kefu{
    protected $_wechat_controller = null;

    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }

    function kefulist($wc_id,$fakeid){
        $kefuParamM = new Wechat_Model_KefuParam('wechat');
        $default_str = $kefuParamM->getContent('kefu_busy_alert', "对不起，客服忙碌中,请稍候再咨询...");
        if($fakeid < 1 && $this->_wechat_controller->_wechat['advance_auth'] == '0'){
            return $default_str;
        }
        try{
            $wechatUserM = new Wechat_Model_User('wechat');
            $services = $wechatUserM->fetchRows(null, array('wc_id = ?'=>$wc_id,'is_service = ?'=>'1','service_type = ?'=>'0','is_unfollow = ?'=>'0','service_num > ?'=>'0','last_time > ?'=>(time() - Wechat_Model_User::getSendmsgPeriod())),array('service_num asc'));
            $str = '';
            $str .= $kefuParamM->getContent('user_choose_resp', "选择左侧编号进入在线客服咨询。")."\r\n";
            $flag = false;
            foreach($services as $k=>$v){
                $str .= '[ '.$v['service_num'].' ]  '.($v['service_name']?$v['service_name']:'客服'.$v['service_num'].'号')."\r\n";
                $flag = true;
            }
            if($flag){
                $wechatUserM->updateRow(array('sign'=>'kefu'),array('wu_id'=>$this->_wechat_controller->_wechat_user['wu_id']));
                return $str;
            }
        }catch(Exception $e){}
        return $default_str;
    }
}