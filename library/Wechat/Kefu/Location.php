<?php
class Wechat_Kefu_Location{
    protected  $_wechat_base_controller = null;

    function  __construct() {
        if(null === $this->_wechat_base_controller){
            $this->_wechat_base_controller = Wechat_Base_Controller::getInstance();
        }
          //更新时间
        $userWechatUserM = new Wechat_Model_User('wechat');
        $userWechatUserM->updateRow(array('last_service_time'=>time()), array('wu_id'=>$this->_wechat_base_controller->_wechat_user['wu_id']));
    }

    function kefu(){
         $wechat_user = $this->_wechat_base_controller->_wechat_user;
         $response = new Wechat_Base_Response();
         $Label = strip_tags($this->_wechat_base_controller->_wechat_base_params->Label);
         if($wechat_user['ping_fakeid'] > 0){//对话
             
               $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
               $list = $waitlistM->fetchRow(array('wl_id'=>$wechat_user['ping_fakeid'],'wc_id'=>$wechat_user['wc_id']));
               if($list){
                   //处理对话内容。
                     $userWechatUserM = new Wechat_Model_User('wechat');
                     $ping_userid = $wechat_user['user_id'] == $list['server_user_id'] ? $list['user_id'] : $list['server_user_id'];
                     $pingWechatUser = $userWechatUserM->fetchRow(array('wc_id'=>$wechat_user['wc_id'],'user_id'=>$ping_userid,'is_unfollow'=>'0'));

                     if($pingWechatUser){
                            $send_name = $wechat_user['is_service'] == '1'?($wechat_user['service_name'] ? $wechat_user['service_name'] : '客服'.$wechat_user['wu_id'].'号'):($wechat_user['mark_name'] ? $wechat_user['mark_name'] : $wechat_user['nick_name']);
                               //记录客服对话
                            $content = '['.$send_name.'] 发送位置：'."\r\n".$Label;
                            if($this->_wechat_base_controller->_wechat['advance_auth'] == '1' && $pingWechatUser['last_time'] > (time() - Wechat_Model_User::getSendmsgPeriod())){
                                $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wechat_user['wc_id']);
                                $userM = new Seed_Model_User('system');
                                $pingUser = $userM->fetchRow(array('user_id'=>$ping_userid));
                                $re = $kefuAPI->send_text($pingUser['wc_username'], $content);
                            }else{
                                $WeiXinC = Wechat_Advance_MockLogin::login();
                                if(is_object($WeiXinC)){
                                     $re = $WeiXinC->send($pingWechatUser['fakeid'],$content);
                                     if(isset($re['base_resp']['err_msg'])){
                                         $re['errmsg'] = $re['base_resp']['err_msg'];
                                     }
                                }
                            }

                            if(isset($re['errmsg'])){
                                if(strtolower($re['errmsg']) == 'ok'){
                                    Wechat_Kefu_RecordKefu::record($wechat_user['user_id'], $pingWechatUser['user_id'], $Label);
                                    exit;
                                }else{
                                    Wechat_Kefu_RecordKefu::record($wechat_user['user_id'], $pingWechatUser['user_id'],"[系统记录]发送失败，错误信息为:{$re['errmsg']}");
                                }
                            }
                         
                     }
                   

               }

               $response->responseTextMsg('[系统消息]对不起，发送位置失败！');

         }else{
              //清除客服标记
             Wechat_Kefu_RecordKefu::clearKefuSatus($wechat_user['wu_id']);
         }

        
    }
}