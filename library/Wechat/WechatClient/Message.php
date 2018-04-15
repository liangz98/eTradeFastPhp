<?php
/*
 * 消息整合类
 */
class Wechat_WechatClient_Message {
    private $_wechatuser = array();
    private $_wechat = array();
    
    function __construct() {
        
    }
    
    function checkValid($user_id){
        if($user_id < 1)return false;
        $wechatUserM = new Wechat_Model_User('wechat');
        $wechatM = new Wechat_Model_Wechat('wechat');
        
        $wechatuser = $wechatUserM->fetchRow(array('user_id'=>$user_id,'is_unfollow'=>'0'));
        if(empty($wechatuser) || $wechatuser['last_time'] < (time() - Wechat_Model_User::getSendmsgPeriod() + 10))return false;//24小时限制
        
        $wechat = $wechatM->fetchRow(array('is_del'=>'0','id'=>$wechatuser['wc_id']));
        if(empty($wechatuser) || (intval($wechatuser['fakeid']) == 0 && $wechat['advance_auth'] == '0'))return false;
        
        $this->_wechatuser = $wechatuser;
        $this->_wechat = $wechat;
        
        return true;
    }
    
    /*
     * 发送文本
     */
    function sendText($user_id,$content){
        $user_id = intval($user_id);
        if($this->checkValid($user_id)){
              if($this->_wechat['advance_auth'] == '1'){
                    $userM = new Seed_Model_User('system');
                    $user = $userM->fetchRow(array('user_id'=>$this->_wechatuser['user_id']));
                    $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($this->_wechat['id']);
                    $re = $kefuAPI->send_text($user['wc_username'], $content);
              }elseif($this->_wechatuser['fakeid'] > 0){
                    $WeiXinC = Wechat_Advance_MockLogin::loginByWcid($this->_wechat['id']);
                    if(is_object($WeiXinC)){
                        $fakeid = $this->_wechatuser['fakeid'];
                        $re = $WeiXinC->send($fakeid, $content);
                        if(isset($re['base_resp']['err_msg']) && strtolower($re['base_resp']['err_msg']) == 'ok'){
                            $re['errmsg'] = 'ok';
                        }
                    }
              }
              if(isset($re['errmsg']) && strtolower($re['errmsg']) == 'ok'){
                   return true;
              }
        }
        return false;
    }
    
    /*
     * 发送图片(注意是单张图片)
     */
    function sendImage($user_id,$media_id,$fileId = 0){
        $user_id = intval($user_id);
        if($this->checkValid($user_id)){
             if($this->_wechat['advance_auth'] == '1' && $media_id){
                    $userM = new Seed_Model_User('system');
                    $user = $userM->fetchRow(array('user_id'=>$this->_wechatuser['user_id']));
                    if($user){
                        $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($this->_wechat['id']);
                        $re = $kefuAPI->send_image($user['wc_username'], $media_id);                      
                    }
              }elseif($fileId > 0 && $this->_wechatuser['fakeid'] > 0){
                    $WeiXinC = Wechat_Advance_MockLogin::loginByWcid($this->_wechat['id']);
                    if(is_object($WeiXinC)){
                        $fakeid = $this->_wechatuser['fakeid'];
                        $re = $WeiXinC->sendImage($fakeid, $fileId);
                        if(isset($re['base_resp']['err_msg']) && strtolower($re['base_resp']['err_msg']) == 'ok'){
                            $re['errmsg'] = 'ok';
                        }                            
                    }
              }
              if(isset($re['errmsg']) && strtolower($re['errmsg']) == 'ok'){
                   return true;
              }
        }
        return false;
    }
    
    /*
     * 发送语音
     */
    function sendVoice($user_id,$media_id,$fileId = 0){
        $user_id = intval($user_id);
        if($this->checkValid($user_id)){
              if($this->_wechat['advance_auth'] == '1' && $media_id){
                    $userM = new Seed_Model_User('system');
                    $user = $userM->fetchRow(array('user_id'=>$this->_wechatuser['user_id']));
                    if($user){
                        $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($this->_wechat['id']);
                        $re = $kefuAPI->send_voice($user['wc_username'], $media_id);                    
                    }
              }elseif($fileId > 0 && $this->_wechatuser['fakeid'] > 0){
                    $WeiXinC = Wechat_Advance_MockLogin::loginByWcid($this->_wechat['id']);
                    if(is_object($WeiXinC)){
                        $fakeid = $this->_wechatuser['fakeid'];
                        $re = $WeiXinC->sendVoice($fakeid, $fileId);
                        if(isset($re['base_resp']['err_msg']) && strtolower($re['base_resp']['err_msg']) == 'ok'){
                            $re['errmsg'] = 'ok';
                        }                            
                    }
              }
              if(isset($re['errmsg']) && strtolower($re['errmsg']) == 'ok'){
                   return true;
              }
        }
        return false;
    }
    
    /*
     * 发送视频
     */
    function sendVideo($user_id,$media_id,$thumb_media_id,$fileId = 0){
        $user_id = intval($user_id);
        if($this->checkValid($user_id)){
             if($this->_wechat['advance_auth'] == '1' && $media_id){
                    $userM = new Seed_Model_User('system');
                    $user = $userM->fetchRow(array('user_id'=>$this->_wechatuser['user_id']));
                    if($user){
                        $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($this->_wechat['id']);
                        $re = $kefuAPI->send_video($user['wc_username'], $media_id, $thumb_media_id);                 
                    }
              }elseif($fileId > 0 && $this->_wechatuser['fakeid'] > 0){
                    $WeiXinC = Wechat_Advance_MockLogin::loginByWcid($this->_wechat['id']);
                    if(is_object($WeiXinC)){
                        $fakeid = $this->_wechatuser['fakeid'];
                        $re = $WeiXinC->sendVoice($fakeid, $fileId);
                        if(isset($re['base_resp']['err_msg']) && strtolower($re['base_resp']['err_msg']) == 'ok'){
                            $re['errmsg'] = 'ok';
                        }                            
                    }
              }
              if(isset($re['errmsg']) && strtolower($re['errmsg']) == 'ok'){
                   return true;
              }
        }
        return false;
    }
    
    /*
     *发送音乐 
     */
    function sendMusic($user_id, $title, $description, $musicurl, $hqmusicurl, $thumb_media_id){
        $user_id = intval($user_id);
        if($this->checkValid($user_id)){
             if($this->_wechat['advance_auth'] == '1'){
                    $userM = new Seed_Model_User('system');
                    $user = $userM->fetchRow(array('user_id'=>$this->_wechatuser['user_id']));
                    if($user){
                        $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($this->_wechat['id']);
                        $re = $kefuAPI->send_music($user['wc_username'], $title, $description, $musicurl, $hqmusicurl, $thumb_media_id);            
                    }
              }
              if(isset($re['errmsg']) && strtolower($re['errmsg']) == 'ok'){
                   return true;
              }
        }
        return false;       
    }
    
    /*
     * 发送图文
     */
    function sendNews($user_id,$articles,$fileId = 0){
        $user_id = intval($user_id);
        if($this->checkValid($user_id)){
              if($this->_wechat['advance_auth'] == '1' && $articles){
                    $userM = new Seed_Model_User('system');
                    $user = $userM->fetchRow(array('user_id'=>$this->_wechatuser['user_id']));
                    if($user){
                        $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($this->_wechat['id']);
                        $re = $kefuAPI->send_news($user['wc_username'], $articles);               
                    }
              }elseif($fileId > 0 && $this->_wechatuser['fakeid'] > 0){
                    $WeiXinC = Wechat_Advance_MockLogin::loginByWcid($this->_wechat['id']);
                    if(is_object($WeiXinC)){
                        $fakeid = $this->_wechatuser['fakeid'];
                        $re = $WeiXinC->sendNews($fakeid, $fileId);
                        if(isset($re['base_resp']['err_msg']) && strtolower($re['base_resp']['err_msg']) == 'ok'){
                            $re['errmsg'] = 'ok';
                        }                            
                    }
              }
              if(isset($re['errmsg']) && strtolower($re['errmsg']) == 'ok'){
                   return true;
              }           
        }
        return false;       
    }
    
}