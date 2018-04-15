<?php
/**
 * 微支付发货通知高级接口
 */
class Wechat_AdvanceAPI_DeliverNotify{
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
    
    function getPostUrl(){
        $url = "https://api.weixin.qq.com/pay/delivernotify?access_token=";
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        return empty($access_token)?'':$url.$access_token;
    }
    
    function post($data){
        $need_arr = array('appid','openid','transid','out_trade_no','deliver_timestamp','deliver_status','deliver_msg','app_signature','sign_method');
        $post_data = array();
        if(is_array($data)){
            foreach($need_arr as $v){
                if(!isset($data[$v])){
                    return false;
                }else{
                    $post_data[$v] = $data[$v];
                }
            }
            $url = $this->getPostUrl();
            if($url){
                $userWechatM = new Wechat_Model_Wechat('wechat');
                $json_str = json_encode($post_data);
                $re = $userWechatM->doPostData($url, $json_str,30);
                $res = json_decode($re, true);
                return $res;
            }
        }

        return false;
    }
    
}