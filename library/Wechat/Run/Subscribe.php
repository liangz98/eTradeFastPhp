<?php
/**
 * 关注/订阅
 */
class Wechat_Run_Subscribe{
    protected $_wechat_controller = null;

    function  __construct() {
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }
    }
    function run(){

    	//$response = new Wechat_Base_Response();
       // $response->responseTextMsg("test");
                    
        if(isset($this->_wechat_controller->_wechat_user) && $this->_wechat_controller->_wechat_user){
            $userWechatUserM = new Wechat_Model_User('wechat');
            $userWechatUserM->follow($this->_wechat_controller->_wechat_user['wu_id']);
        }
        
         $integral_value = Seed_Model_UserIntegralsParam::getValue('first_follow',0);
         $integral_desc = '关注';
         $eventKey = 0;
         if(isset($this->_wechat_controller->_wechat_base_params->EventKey)){
             $eventKey = str_replace("qrscene_", "", $this->_wechat_controller->_wechat_base_params->EventKey);
         }
         
         if($eventKey > 0){//扫描二维吗
                $qrM = new Wechat_Model_QrcodeImg("wechat");
                $qr = $qrM->fetchRow(array('scene_id'=>$eventKey));
                if($qr){
                      if($qr['scene_id'] == 1){
                          $integral_value = Seed_Model_UserIntegralsParam::getValue('scan_product_qrcode',0);
                          $integral_desc = '扫描产品二维码关注';
                      }else{
                          $integral_desc = '扫描二维码关注获';
                      }
                      $userWechatReplyM = new Wechat_Model_Reply('wechat');
                      $reply = $userWechatReplyM->fetchRow(array('r_id'=>$qr['qr_reply']));
                      if($reply){
                          Wechat_Base_ParseReply::parse($reply);
                      }
                }
         }
         
         
         if(isset($this->_wechat_controller->_wechat_user['user_id'])){
            $subscribe = 'subscribe';
            $integralM = new Seed_Model_UserIntegral('system');
            $check = $integralM->fetchRow(array('user_id'=>$this->_wechat_controller->_wechat_user['user_id'],'source'=>$subscribe));
            if(empty($check)){
                $integralM->addIntegral($this->_wechat_controller->_wechat_user['user_id'], $integral_value, $integral_desc, $subscribe, '关注');
            }
         }
         
        
        switch ($this->_wechat_controller->_wechat['wc_wel_type']) {
            case 2:
                $news_content = json_decode( $this->_wechat_controller->_wechat['news_content'], true);
                $news = array();
                $record_title = '[图文]';
                foreach($news_content as $k=>$v){
                    $key = explode('_', $k);
                    if(count($key) != 2)continue;
                    if($key[0] == 'url'){
                        $v = Wechat_Base_CheckInnerCode::check($v,true);
                    }
                    if($key[0] == 'title'){
                         $record_title .= ($record_title ? "\r\n" : "").$v;
                    }
                    $news[$key[1]][$key[0]] = $v;
                }
                Wechat_Base_RecordReply::record($record_title);
                $response = new Wechat_Base_Response();
                $response->responseNewsMsg($news);
                break;
            case 3:
                $music_content = json_decode($this->_wechat_controller->_wechat['music_content'],true);
                $Title = isset($music_content['music_title']) ? $music_content['music_title'] :'音乐消息';
                $Description = isset($music_content['music_description']) ? $music_content['music_description']:'';
                $MusicUrl = isset($music_content['music_url']) ? $music_content['music_url'] : '';
                $HQMusicUrl = isset($music_content['hq_music_url']) ? $music_content['hq_music_url'] : '';
                Wechat_Base_RecordReply::record('音乐消息');
                $response = new Wechat_Base_Response();
                $response->responseMusicMsg($Title, $Description, $MusicUrl, $HQMusicUrl);
                break;
            case 1:
            default:
                $content = json_decode($this->_wechat_controller->_wechat['text_content'],true);
                if(isset($content['content'])){
                    $content['content'] = Wechat_Base_CheckInnerCode::check($content['content']);
                    Wechat_Base_RecordReply::record($content['content']);
                    $response = new Wechat_Base_Response();
                    $response->responseTextMsg($content['content']);
                }
                break;
        }
    }
}
