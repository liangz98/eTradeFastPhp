<?php
 /**
  * @param <string> $url 自动在链接里增加微信id参数
  */
class Wechat_Advance_AddWcid{

    public static function add($url){
        if(empty($url))return '';
//        if(strpos($url, substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'],'.')+1)) == false){
//            return $url;//外链则不作处理
//        }
        $w_controller = Wechat_Base_Controller::getInstance();
 	$wc_id = $w_controller->_wechat['id'];
 	if(strpos($url,"?")!=false){
 		$myurl = str_replace("?","?wc=".$wc_id."&",$url);
 	}else{
 		$myurl = $url."?wc=".$wc_id;
 	}
 	return $myurl;
    }
    
}