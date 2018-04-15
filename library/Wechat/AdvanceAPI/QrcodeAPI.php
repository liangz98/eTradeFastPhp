<?php
/**
 * 二维码高级接口
 */
class Wechat_AdvanceAPI_QrcodeAPI{
        private $_wc_id = 0;
        
        function __construct($wc_id) {
            $this->_wc_id = intval($wc_id);
        }
        
        /*
         * 二维码图片ticket获取url
         */
        function getQrTicketUrl(){
            $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=";
            $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
            return empty($access_token)?'':$url.$access_token;
        }
        
        /*
         * 取得图片的ticket
         */
        function getQrTicket($scene_id,$expire_seconds = 0){
    //{"ticket":"gQG28DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0FuWC1DNmZuVEhvMVp4NDNMRnNRAAIEesLvUQMECAcAAA==","expire_seconds":1800}
    //{"errcode":40013,"errmsg":"invalid appid"}
            $scene_id = intval($scene_id);
            if($scene_id < 1)return '';
            $ticket = '';
            if($expire_seconds > 0){
                $arr = $this->get_Linshi_QrTicket($scene_id,$expire_seconds);
            }else{
                $arr = $this->get_Yongjiu_QrTicket($scene_id);
            }
            if(isset($arr['ticket'])){
                $ticket = $arr['ticket'];
            }
            return $ticket;
        }
        
        
        /*
         * 临时二维码图片ticket
         */
        function get_Linshi_QrTicket($scene_id,$expire_seconds = 1800){
            $url = $this->getQrTicketUrl();
            if(empty($url))return array();
            
            $data = array();
            $expire_seconds = abs(intval($expire_seconds));
            if($expire_seconds < 1 || $expire_seconds > 1800){
                $expire_seconds = 1800;//最大不超过1800秒。
            }
            $scene_id = intval($scene_id);
            $data['expire_seconds'] = $expire_seconds;
            $data['action_name'] = "QR_SCENE";
            $data['action_info'] = array("scene"=>array("scene_id"=>$scene_id));
            $wechatM = new Wechat_Model_Wechat('wechat');
            $re = $wechatM->doPostData($url, Zend_Json::encode($data));
            return Zend_Json::decode($re,1);
        }
        
        /*
         * 永久二维码图片ticket
         */
        function get_Yongjiu_QrTicket($scene_id){
           $url = $this->getQrTicketUrl();
           if(empty($url))return array();
           
           $scene_id = intval($scene_id);
           if($scene_id < 1)return array();
           $data = array();
           $data["action_name"] = "QR_LIMIT_SCENE";
           $data["action_info"] = array("scene"=>array("scene_id"=>$scene_id));
           $wechatM = new Wechat_Model_Wechat('wechat');
           $re = $wechatM->doPostData($url, Zend_Json::encode($data));
           return Zend_Json::decode($re,1);
        }
        

        /*
         * 获取二维码图片url
         */
        function getQRImageUrl($scene_id,$expire_seconds = 0 ,$ticket = ''){
            if(empty($ticket))$ticket = $this->getQrTicket($scene_id, $expire_seconds);
            $imgurl = "";
            if(!empty($ticket)){
                $imgurl =  "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
            }
            return $imgurl;
        }
        
}