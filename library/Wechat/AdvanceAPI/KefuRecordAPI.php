<?php
/**
 * 获取官方客服聊天记录接口
 */
class Wechat_AdvanceAPI_KefuRecordAPI{
    private $_wc_id = 0;
    
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
    
    function getPostUrl(){
        $url = "https://api.weixin.qq.com/cgi-bin/customservice/getrecord?access_token=";
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        return empty($access_token)?'':$url.$access_token;
    }
    
    
    /**
     * 获取记录
     * $starttime 查询开始时间，UNIX时间戳
     * $endtime   查询结束时间，UNIX时间戳，每次查询不能跨日查询
     * $openid    普通用户的标识，对当前公众号唯一
     * $pageindex 查询第几页，从1开始
     * $pagesize  每页大小，每页最多拉取1000条
     */
    function getrecord($starttime,$endtime,$openid,$pageindex = 1,$pagesize = 1000){
        $data = array();
        $starttime = intval($starttime);
        $endtime = intval($endtime);
        
        if($starttime > 0 && $endtime == 0){
           $endtime = mktime(23, 59, 59, date('m',$starttime), date('d',$starttime), date('Y',$starttime));
        }elseif($starttime == 0 && $endtime > 0){
          $starttime = mktime(0, 0, 0, date('m',$endtime),date('d',$endtime), date('Y',$endtime));
        }
        if(date('Ymd',$starttime) != date('Ymd',$endtime))return false;//不能跨日查询
        $data["starttime"] = $starttime;
        $data["endtime"] = $endtime;
        $data["openid"] = $openid;
        $data["pagesize"] = $pagesize;
        $data["pageindex"] = $pageindex;
        $json_str = json_encode($data);
        return $this->post($json_str);
    }
    
    function post($json_str){
        $url = $this->getPostUrl();
        if($url){
            $userWechatM = new Wechat_Model_Wechat('wechat');
            $re = $userWechatM->doPostData($url, $json_str);
            $res = json_decode($re, true);
            return $res;
        }
        return false;
    }
    
    /**
     * 操作ID的中文说明
     * $opercode 操作ID(会化状态）
     */
    function opercode2Chinese($opercode){
        $oper_arr = array(
             '1000'=>'创建未接入会话'
            ,'1001'=>'接入会话'
            ,'1002'=>'主动发起会话'
            ,'1004'=>'关闭会话'
            ,'1005'=>'抢接会话'
            ,'2001'=>'公众号收到消息'
            ,'2002'=>'客服发送消息'
            ,'2003'=>'客服收到消息'
        );
        return isset($oper_arr[$opercode]) ? $oper_arr[$opercode] : '';
    }
    
}
    