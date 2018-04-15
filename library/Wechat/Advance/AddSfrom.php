<?php
/**
 * 自动添加来源
 */
class Wechat_Advance_AddSfrom{
    public static function add($url,$sfrom){
        if(empty($url))return '';
//        if(strpos($url, substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'],'.')+1)) == false){
//            return $url;//外链则不作处理
//        }
        if(strpos($url,"?")!=false){
 		$myurl = str_replace("?","?sfrom=".$sfrom."&",$url);
 	}else{
 		$myurl = $url."?sfrom=".$sfrom;
 	}
 	return  $myurl;
    }
    
}
