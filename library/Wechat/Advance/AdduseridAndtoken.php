<?php
class Wechat_Advance_AdduseridAndtoken{
    public static function add($url){
        if(empty($url))return '';
//        if(strpos($url, substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'],'.')+1)) == false){
//            return $url;//外链则不作处理
//        }
        $w_controller = Wechat_Base_Controller::getInstance();
 	$user_id = $w_controller->_wechat_user['user_id'];
        if(intval($user_id) < 1)return $url;//原样返回
        $time = time();
        $url_token = Seed_Token::encode($user_id,$time,'gzseed');
 	if(strpos($url,"?")!=false){
 		$myurl = str_replace("?","?uid=".$user_id.'&ut='.$url_token."&",$url);
 	}else{
 		$myurl = $url."?uid=".$user_id.'&ut='.$url_token;
 	}
 	return $myurl;
    }
}