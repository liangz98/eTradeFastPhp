<?php
 /**
  * 自动在链接里增加TOKEN参数
  * @param <string> $str 字符
  */
class Wechat_Advance_AddTokenCode{

    public static function add($url){
        if(empty($url))return '';

        $w_controller = Wechat_Base_Controller::getInstance();
        $username = $w_controller->_wechat_base_params->FromUserName;
        if($w_controller->login_token){
            $phpsessid = $w_controller->login_token;
        }else{
            $userWechatUserM = new Wechat_Model_User('wechat');
            $phpsessid = session_id();
            $userWechatUserM->updateRow(array('wc_phpsessid'=>$phpsessid),array('wc_username = BINARY ?'=>$username));
            $w_controller->login_token = $phpsessid;
        }
        //加入参数，默认需要登录
        if(strpos($url,"?")!=false){
	 		$myurl = str_replace("?","?sfrom=bm&",$url);
	 	}else{
	 		$myurl = $url."?sfrom=bm";
	 	}
	 	
	 	
	 	if(strpos($url,"?")!=false){
	 		$token = Seed_Token::encode($username,$phpsessid);
	 		$myurl = str_replace("?","?token=".$token."&",$url);
	 	}else{
	 		$myurl = $url."?token=".Seed_Token::encode($username,$phpsessid);
	 	}
	 	return  $myurl;
    }
    
}