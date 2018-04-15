<?php
/**
 * 下载素材高级接口
 */
class Wechat_AdvanceAPI_DownloadPI{
    private $_wc_id = 0;
    private $_access_token = '';
    
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
    
    function getDownloadUrl(){
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=";
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        return empty($access_token)?'':$url.$access_token;
    }
    
    
    function download($media_id,$save_path){
        $url = $this->getDownloadUrl();
        if($url){
            $url = $url."&media_id={$media_id}";
            $lea = new Wechat_WechatClient_LeaWeiXinClient();
            $re = $lea->get($url);
            if($re['body']){
                $len = file_put_contents($save_path,$re['body']);
                if($len){
                    return $save_path;
                }
            }
        }
        return false;
    }
    
    
}