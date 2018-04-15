<?php
class Wechat_Advance_AccessToken{
    
    public static function getAccessTokenByWcid($wc_id){
        $wc_id = intval($wc_id);
        $userWechatM = new Wechat_Model_Wechat('wechat');
        $userwechat = $userWechatM->fetchRow(array('id'=>$wc_id,'is_actived'=>'1','is_abort'=>'0','is_del'=>'0','advance_auth'=>'1'));
        if($userwechat){
            $access_token = $userwechat['access_token'];
            $access_token_expire = intval($userwechat['access_token_expire']);
            if($access_token && $access_token_expire > time()){
                return $access_token;
            }else{
                $appid =  strip_tags($userwechat['appid']);
                $appsecret =  strip_tags($userwechat['appsecret']);
                if($appid && $appsecret){
                    $userWechatM = new Wechat_Model_Wechat('wechat');
                    $msg = $userWechatM->getAccessToken($appid, $appsecret);
                    if(isset($msg['access_token'])){
                        $dataSet = array();
                        $dataSet['access_token'] = $msg['access_token'];
                        $dataSet['access_token_expire'] = isset($msg['expires_in']) ? $msg['expires_in'] + time() : time() + 300;
                        $userWechatM->updateRow($dataSet, array('id'=>$userwechat['id']));
                        return $msg['access_token'];
                    }
                }
            }
        }
        return '';
    }
    
    
}