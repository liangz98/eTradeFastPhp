<?php
class Wechat_Base_SecondInterface{
    public static function ping(){
        $w_controller = Wechat_Base_Controller::getInstance();
        if(!isset($w_controller->_wechat['second_interface']) || empty($w_controller->_wechat['second_interface']) || !isset($GLOBALS["HTTP_RAW_POST_DATA"])){
            return false;
        }
        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $userWechatM = new Wechat_Model_Wechat('wechat');
        $data = $userWechatM->doPostData($w_controller->_wechat['second_interface'], $postStr);
        if(preg_match('/<xml>.*<\/xml>/is', $data)){//判断数据
            Wechat_Base_RecordReply::record('外部接口消息');//记录消息
            echo $data;//原样输出
            exit;
        }
        return false;
    }
}