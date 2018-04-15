<?php
/*
 * 用户分组管理高级api
 */
class Wechat_AdvanceAPI_UserGroupAPI{
    private $_wc_id = 0;
    
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
    
    /**
     * 查询所有分组
     */
    function getGroup(){
         $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
         if($access_token){
             $url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".$access_token;
             $lea = new Wechat_WechatClient_LeaWeiXinClient();
             $re = $lea->get($url);
             return json_decode(isset($re['body'])?$re['body']:$re,true);
         }
         return false;
    }
    
    /*
     * 查询用户所在分组
     */
    function getId($openid){
         $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
         if($access_token){
             $url = "https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=".$access_token;
             $userWechatM = new Wechat_Model_Wechat('wechat');
             $json_str = json_encode(array('openid'=>$openid));
             $re = $userWechatM->doPostData($url, $json_str);
             return json_decode($re,true);
         }
         return false;
    }
    
    /*
     * 创建分组
     */
    function createGroup($group_name){
         if(empty($group_name))return false;
         $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
         if($access_token){
             $data = array();
             $data['group'] = array('name'=>'[groupname]');
             $json_str = str_replace(array('[groupname]'),array(addslashes($group_name)),json_encode($data));
             $url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token=".$access_token;
             $userWechatM = new Wechat_Model_Wechat('wechat');
             $re = $userWechatM->doPostData($url, $json_str);
             $res = json_decode($re, true);
             return $res;
         }
         return false;
    }
    
    /*
     * 修改分组名
     */
    function renameGroup($id,$name){
         if(empty($rename_group))return false;
         $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
         if($access_token){
             $data = array();
             $data['group'] = array(
                 'id'=>$id
                 ,'name'=>'[groupname]');
             $json_str = str_replace(array('[groupname]'),array(addslashes($group_name)),json_encode($data));
             $url = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token=".$access_token;
             $userWechatM = new Wechat_Model_Wechat('wechat');
             $re = $userWechatM->doPostData($url, $json_str);
             $res = json_decode($re, true);
             return $res;
         }
         return false;
    }
    
    /*
     * 移动用户分组
     */
    function moveUserGroup($openid,$to_groupid){
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        if($access_token){
             $data = array();
             $data['openid'] = $openid;
             $data['to_groupid'] = $to_groupid;
             $json_str = json_encode($data);
             $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=".$access_token;
             $userWechatM = new Wechat_Model_Wechat('wechat');
             $re = $userWechatM->doPostData($url, $json_str);
             $res = json_decode($re, true);
             return $res;
        }
    }
    
}