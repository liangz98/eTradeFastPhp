<?php
/**
 * jsapi签名类，为微信2015-01-09开放的针对微信客户端6.0.2版本的jsapi接口
 * http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
 *
 * @category   Wechat
 * @package    Wechat_Advance_Ticket
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 * @author	   Brave(cnbrave@qq.com)
 */
class Wechat_Advance_Ticket{
    
    /**
     * 微信公众号获取JsApiTicket，获得jsapi_ticket之后，就可以生成JS-SDK权限验证的签名了，2015-01-09微信更新的新接口和功能
     * 
     * @param string $wc_id 
     * @return array
     * 
     */
    protected  static function getJsApiTicket(){
    	$userWechatM = new Wechat_Model_Wechat('wechat');
		$condition=array();
	    $condition['wechat_type']='0';
	    $condition['is_actived']='1';
	    $condition['is_abort']='0';
	    $condition['is_del']='0';
	    $wechat = $userWechatM->fetchRow($condition);
	    if($wechat['id']<1) return false;
	    $wc_id = $wechat['id'];
    	$accessToken = Wechat_Advance_AccessToken::getAccessTokenByWcid($wc_id);
    	if(empty($accessToken))return;
    	
    	$userwechat = $wechat;
		$ticket = $userwechat['ticket'];
		$ticker_expire_time = intval($userwechat['ticker_expire_time']);
		if($ticket && $ticker_expire_time > time()){
			return $ticket;
		}else{
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$accessToken}&type=jsapi";
	        $msg = @file_get_contents($url);
	        $msg = @json_decode($msg,TRUE);
			if(isset($msg['ticket'])){
				$dataSet = array();
				$dataSet['ticket'] = $msg['ticket'];
				$dataSet['ticker_expire_time'] = isset($msg['expires_in']) ? $msg['expires_in'] + time() : time() + 300;
				$userWechatM->updateRow($dataSet, array('id'=>$userwechat['id']));
				return $msg['ticket'];
			}
		}
    }
    
    
    /**
     * 获取jsapi签名包数据，返回包含签名的数组信息
     * @return boolean|multitype:string number unknown 
     */
    public static function getSignPackage(){
    	$userWechatM = new Wechat_Model_Wechat('wechat');
		$condition=array();
	    $condition['wechat_type']='0';
	    $condition['is_actived']='1';
	    $condition['is_abort']='0';
	    $condition['is_del']='0';
	    $wechatDetail = $userWechatM->fetchRow($condition);
	    if($wechatDetail['id']<1) return false;
    	
		$appId = $wechatDetail['appid'];
		$jsApiTicket = self::getJsApiTicket();
		$timestamp = time();
		$nonceStr = mt_rand(10000,99999).Seed_Common::genRandomStringBy(17);
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$str = "jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s";
		$enstr = sprintf($str,$jsApiTicket,$nonceStr,$timestamp,$url);
		$signature = sha1($enstr);
    	$signPackage = array();
    	$signPackage['appId'] = $appId;
    	$signPackage['timestamp'] = $timestamp;
    	$signPackage['nonceStr'] = $nonceStr;
    	$signPackage['signature'] = $signature;
    	$signPackage['rawString'] = $enstr; //rawString作用是用于开发调试，非微信接口要求所属参数
    	return $signPackage;
    }
}