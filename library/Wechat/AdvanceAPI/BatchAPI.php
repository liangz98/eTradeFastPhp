<?php
/**
 * 群发高级接口
 */
class Wechat_AdvanceAPI_BatchAPI{
        private $_wc_id = 0;
        
        function __construct($wc_id) {
            $this->_wc_id = intval($wc_id);
        }
        
        /**
         * 获取群发路径
         */
        function getUrl(){
            $url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=";
            $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
            return empty($access_token)?'':$url.$access_token;
        }
        
        /**
         * 获取删除群发路径
         */
        function getDelUrl(){
            $url = "https://api.weixin.qq.com//cgi-bin/message/mass/delete?access_token=";
            $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
            return empty($access_token)?'':$url.$access_token;
        }
        
        
        /**
         * 按组群发
         */
        function batch2Group($group_id,$media_id,$msgtype = 'mpnews'){
            $data = array();
            $data["filter"] = array("group_id"=>$group_id);
            $data[$msgtype] = array('media_id'=>$media_id);
            $data["msgtype"] = $msgtype;
            $url = $this->getUrl();
            if($url){
                 $wechatM = new Wechat_Model_Wechat('wechat');
                 $re =  $wechatM->doPostData($url, json_encode($data), 30);
                 return json_decode($re,1);
            }
            return '';
        }
        
        /*
         * 按用户openID群发
         */
        function batch2User($touser,$media_id,$msgtype = 'mpnews'){
             $data = array();
             $data["touser"] = (array)$touser;
             $data[$msgtype] = array("media_id"=>$media_id);
             $data["msgtype"] = $msgtype;
             $url = $this->getUrl();
             if($url){
                  $wechatM = new Wechat_Model_Wechat('wechat');
                  $re =  $wechatM->doPostData($url, json_encode($data), 30);
                  return json_decode($re,1);
             }
             return '';
        }
        
        /**
         * 删除群发
         * $msgid 发送成功返回的消息id
         */
        function delete($msgid){
            $data = array();
            $data["msgid"] = $msgid;
            $url = $this->getDelUrl();
            if($url){
                  $wechatM = new Wechat_Model_Wechat('wechat');
                  $re =  $wechatM->doPostData($url, json_encode($data), 30);
                   return json_decode($re,1);
            }
            return '';
        }
        
}