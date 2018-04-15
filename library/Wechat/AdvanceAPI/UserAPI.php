<?php
class Wechat_AdvanceAPI_UserAPI{
    private $_wc_id = 0;
    
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
    
    /*
     * 获取用户信息
     */
    function getUserInfo($open_id){
        if(empty($open_id))return false;
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        if($access_token){
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$open_id}";
            $lea = new Wechat_WechatClient_LeaWeiXinClient();
            $re = $lea->get($url);
            return json_decode(isset($re['body'])?$re['body']:$re,true);
        }
        return false;
    }
    
    /*
     * 下载用户头像
     */
    function getAvatar($headimgurl,$upload_app_server=""){
            if(empty($headimgurl))return '';
    		if(!empty($upload_app_server)){
    			$download_url = $upload_app_server."/image/getavatar?anonymous=1&headimgurl=".$headimgurl;
    			$rs = Seed_Browser::view_page($download_url);
    			return $rs;
    		}/*else{
	            $seed_host_name = '';
	            if(defined('SEED_HOST_NAME')){
	                $seed_host_name = str_replace(".","_",SEED_HOST_NAME)."_";
	            }
	            
	            $imagePath = SEED_WWW_ROOT.DIRECTORY_SEPARATOR.'upload_files'.DIRECTORY_SEPARATOR.'images';
	            if(!is_dir($imagePath)){
	                @mkdir($imagePath,0777);
	            }
	            if(!is_dir($imagePath.DIRECTORY_SEPARATOR.'wechat_avatar')){
	                @mkdir($imagePath.DIRECTORY_SEPARATOR.'wechat_avatar',0777);
	            }
	            $avatarPath = $imagePath.DIRECTORY_SEPARATOR.'wechat_avatar';
	            
	            $lea = new Wechat_WechatClient_LeaWeiXinClient();
	            $re = $lea->get($headimgurl);
	            
	            $imgM = new Seed_Image();
	            $ext = $imgM->getExtByHeader($re['header']);
	            $new_avatar_path =  $avatarPath.'/'.$seed_host_name.date('YmdHis').  rand(100, 999).'.'.$ext;
	              
	            $len = file_put_contents($new_avatar_path,$re['body']);
	            $avtar_img = imagecreatefromjpeg($new_avatar_path);
	            return $new_avatar_path;
    		}*/
    		return;
    }
    
    /*
     * 获取用户列表
     */
    function getUserList($next_openid = ''){
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        if($access_token){
            $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}&next_openid=".$next_openid;
            $lea = new Wechat_WechatClient_LeaWeiXinClient();
            $re = $lea->get($url);
            return json_decode(isset($re['body'])?$re['body']:$re,true);
        }
        return false;
    }
    
}