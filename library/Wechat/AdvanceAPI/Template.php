<?php
/**
 * 模板高级接口
 */
class Wechat_AdvanceAPI_Template{
        private $_wc_id = 0;
        private $_data = array();
        
        function __construct($wc_id) {
            $this->_wc_id = intval($wc_id);
        }
        
        //获取请求地址
        function getPostUrl(){
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=";
            $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
            return empty($access_token)?'':$url.$access_token;
        }
        
        //发送模板消息
        function send($touser,$template_id,$url,$topcolor = '#FF0000',$json_str = null){
            if($json_str === null){
               $json_data = array(
                 'touser'=>$touser,
                 'template_id'=>$template_id,
                 'url'=>$url,
                 'topcolor'=>$topcolor,
                 'data'=>$this->_data
               );
               $json_str = Seed_Json::json_encode_unescaped_unicode($json_data);//排除中文的json转义
            }
            $posturl = $this->getPostUrl();
            if($posturl){
              $userWechatM = new Wechat_Model_Wechat('wechat');
              $re = $userWechatM->doPostData($posturl, $json_str);
              $res = json_decode($re, true);
              return $res;
            }
            return false;
        }
        
        //组合data
        function combineData($key,$value,$color = '#173177'){
            $this->_data[$key] = array(
              'value'=>$value ,
              'color'=>$color
             );
        }
        
}