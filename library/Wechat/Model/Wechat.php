<?php
/**
 * 微信设置表模型 (snwc_wechats)
 *
 * @category   Wechat
 * @package    Wechat_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Wechat_Model_Wechat extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'wechats';
    
    /**
     * 密钥
     *
     * @var string
     */
    public $_account_key = 'gzseed_wechat';
    
    /**
     * 检测微信账号
     *
     * @return	array
     *
     */
    function checkWechatAccount(){
    		///增加wechat_type的过滤用于识别是商户还是平台的公众帐号，0为平台，1为商户  by brave 2014-10-14 16:31:21
            $wechats = $this->fetchRows(null, array('is_del'=>'0','wechat_type'=>'0'), array('id asc'));
            return $wechats;
    }

    /**
     * 加密接口密钥
     * 
     * @param integer $wc_id	公众号ID
     * @param string $key	本地密钥
     * @return string
     * 
     */
    function accountCode($wc_id,$key = ''){
       $key = $key?$key:$this->_account_key;
       return md5(md5($wc_id).$key);
    }

    /**
     * 微信接口入口验证
     * 
     * @param integer $wc_id  微信公众号id
     * @param string $a_code 密钥
     * @param string $key    本地密钥
     * @return array
     * 
     */
    function interfaceCheckWechatAccount($wc_id,$a_code){
       $wc_id = intval($wc_id);
       if($wc_id < 1)return false;
       $key = $this->fetchOne(array('id'=>$wc_id), 'token');
       $key = $key?$key:$this->_account_key;
       $a_code = str_replace(array('"',"'"), array('',''), $a_code);
       if($wc_id < 1 || empty($a_code))return false;
       $row = $this->_db->fetchRow("select * from ".$this->_prefix.$this->_table_name." where id = '".$wc_id."' and md5(concat(md5(id),'{$key}')) = '".$a_code."' and is_abort = '0' and is_del = '0'");
       if($row)return $row;
       return false;
    }

    /**
     * 微信公众号获取access_token
     * 
     * @param string $appid	应用ID
     * @param string $appsecret	应用加密串
     * @return array
     * 
     */
    function getAccessToken($appid,$appsecret){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
        $msg = @file_get_contents($url);
        $msg = @json_decode($msg,TRUE);
        return $msg;
    }

    /**
     * 微信公众号生成菜单
     * 
     * @param string $access_token 微信access_token
     * @param array $button   菜单数组
     * @return array 返回消息
     * 
     */
    function createMenu($access_token,$button){
        $btn_str = '{
             "button":[';
        foreach($button as $k=>$v){
            if(isset($v['sub_button']) && !empty($v['sub_button'])){
                $btn_str .= '{
                     "name":"'.$v['name'].'",
                     "sub_button":[';
                foreach($v['sub_button'] as $sk=>$sv){
                    $btn_str .= '{
                       "type":"'.$sv['type'].'",
                       "name":"'.$sv['name'].'",
                       '.($sv['type'] == 'click'?'"key"':'"url"').':"'.$sv['key'].'"
                    }'.(isset($v['sub_button'][$sk+1])?',':'');
                }
                $btn_str .= ']}'.(isset($button[$k+1])?',':'');
            }else{
                $btn_str .= '{
                  "type":"'.$v['type'].'",
                  "name":"'.$v['name'].'",
                  '.($v['type'] == 'click'?'"key"':'"url"').':"'.$v['key'].'"
              }'.(isset($button[$k+1])?',':'');
            }
        }
        $btn_str .= ']
         }';
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $msg = $this->doPostData($url, $btn_str,30);
        $msg = @json_decode($msg,true);
        return $msg;
    }

    /**
     * 微信公众号删除菜单
     * 
     * @param string $access_token	令牌
     * @return array
     */
    function deleteMenu($access_token){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token;
        $msg = @file_get_contents($url);
        $msg = @json_decode($msg,TRUE);
        return $msg;
    }

    /**
     * 微信公众号删除菜单
     * 
     * @param string $access_token	令牌
     * @return array
     */
    public  function doPostData($url,$str,$timeout = 5){
        $resultStr = '';
        if(function_exists('curl_init')){
            $resultStr = $this->vcurl($url,$str,'','','',$timeout);
        }elseif(function_exists('file_get_contents')){
            $context = array();
            $context['http'] = array ('method' => 'POST','content' =>$str);
            $resultStr = file_get_contents($url, false, stream_context_create($context));
        }elseif(function_exists('fsockopen')){
            $resultStr = $this->HTTP_Post($url,$str);
        }
        return $resultStr;
    }
    
    /**
     * 发送数据
     *
     * @param	string	$URL	地址
     * @param	string	$data	数据
     * @param	string	$cookie	COOKIE
     * @param	string	$referrer	来源
     * @return	string
     */
    function HTTP_Post($URL,$data,$cookie="", $referrer="")//有些服务器可能需要通过此方式
    {
            $URL_Info=parse_url($URL);
            if(!isset($URL_Info["port"])){
                    $URL_Info["port"]=80;
            }
            $request="";
            $request.="POST ".$URL_Info["path"]." HTTP/1.0\r\n";
            $request.="Host: ".$URL_Info["host"]."\r\n";
            if($referrer)
            $request.="Referer: ".$referrer."\r\n";
            $request.="Content-type: application/x-www-form-urlencoded\r\n";
            $request.="Content-length: ".strlen($data)."\r\n";
            $request.="Connection: close\r\n\r\n";
            $request.=$data."\r\n\r\n";
            if($cookie)
            $request.="Cookie:   $cookie\r\n";
            $fp = fsockopen($URL_Info["host"],$URL_Info["port"]);
            fwrite($fp, $request);
            $result="";
            while(!feof($fp)) {
                    $result .= fgets($fp, 1024);
            }
            fclose($fp);
            $result = preg_replace (array ("'HTTP/1.[\w\W]*<xml>'i"),array ( "<xml>"),$result);
            return trim($result);
    }
    
    /**
     * 发送数据
     *
     * @param	string	$url	地址
     * @param	string	$post	数据
     * @param	string	$cookie	COOKIE
     * @param	string	$cookiejar	COOKIEJAR
     * @param	string	$referer	来源
     * @param	integer	$timeout	超时
     * @return	string
     */
    function vcurl($url, $post = '', $cookie = '', $cookiejar = '', $referer = '',$timeout = 5){
            $tmpInfo = '';
            $cookiepath = getcwd().'./'.$cookiejar;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
            if($referer) {
            curl_setopt($curl, CURLOPT_REFERER, $referer);
            } else {
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            }
            if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            }
            if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
            }
            if($cookiejar) {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiepath);
            }
            //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT,$timeout);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

            $tmpInfo = curl_exec($curl);
            if (curl_errno($curl)) {
             return curl_error($curl);
            }
            curl_close($curl);
            return $tmpInfo;
        }

    /**
     * 发送数据
     *
     * @param	string	$URL	地址
     * @param	string	$data	数据
     * @param	string	$cookie	COOKIE
     * @param	string	$referer	来源
     * @return	
     */
    public static function threadPost($URL,$data,$cookie = '',$referer = ''){
       $URL_Info=parse_url($URL);
       if(!isset($URL_Info["port"])){
            $URL_Info["port"]=80;
       }
       $request.="POST ".$URL_Info["path"]." HTTP/1.0\n";
       $request.="Host: ".$URL_Info["host"]."\n";
       $request.="Referer: $referer\n";
       $request.="Content-type: application/x-www-form-urlencoded\n";
       $request.="Content-length: ".strlen($data)."\n";
       $request.="Connection: close\n";
       $request.="Cookie:   $cookie\n";
       $request.="\n";
       $request.=$data."\n";
       $fp = fsockopen($URL_Info["host"],$URL_Info["port"]);
       if($fp){
             fputs($fp, $request);
             fclose($fp);
             return true;
       }
       return false;
   }

    /**
     * userAgent检测是否微信浏览器
     * 
     * @return boolean
     */
    public static function  is_wechatbrowser(){
         // MicroMessenger
        // Mozilla/5.0 (Linux; U; Android 2.3.6; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1 MicroMessenger/4.5.255
        $userAgent = strtolower(isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'');
        if(strpos($userAgent,'micromessenger') != false){
            return true;
        }
//            if(preg_match("/Windows\s*Phone/is", $userAgent)){
//               return true; 
//            }
        return false;
    }

    /**
     * userAgent检测是否微信浏览器
     * 
     * @return boolean
     */
    public static function wechatbrowser_version(){
        $userAgent = strtolower(isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'');
        $pattern = '/micromessenger\/([\d|\.]+)/is';
        preg_match($pattern, $userAgent, $matches);
        if(isset($matches[1]))return $matches[1];
        return false;
    }
}