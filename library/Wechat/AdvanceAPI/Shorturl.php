<?php
//将一条长链接转成短链接。
class Wechat_AdvanceAPI_Shorturl{
    private $_wc_id = 0;
    
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
  
    function post($long_url,$access_token = null,$action = 'long2short'){
        $url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=";
        if($access_token === null){
           $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        }
        if($access_token){
            $url .= $access_token;
            $post_data = array(
              'access_token'=>$access_token,
              'action'=>$action,
              'long_url'=>$long_url
            );
            $userWechatM = new Wechat_Model_Wechat('wechat');
            $re = $userWechatM->doPostData($url, $post_data);
            $res = json_decode($re, true);
            return $res;
        }
        return false;
    }
  
    
}