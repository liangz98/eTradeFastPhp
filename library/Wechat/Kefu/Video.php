<?php
class Wechat_Kefu_Video{
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
                 if($wechat_user['ping_fakeid'] > 0){//对话
 $response->responseTextMsg('[系统消息]对不起，发送视频失败！');
                     $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
                     $list = $waitlistM->fetchRow(array('wl_id'=>$wechat_user['ping_fakeid'],'wc_id'=>$wechat_user['wc_id']));
                     if($list){
                         $WeiXinC = Wechat_Advance_MockLogin::login();
                         if(is_object($WeiXinC)){
                             $latestMsg = $WeiXinC->getLatestMsgs();
                             $type = 4;//视频
                              if(is_array($latestMsg)){
                                      foreach ($latestMsg as $value) {
                                         //检测数据
                                         if(!isset($value['id']) || !isset($value['fakeid']) || !isset($value['type']) || !($value['type'] == $type) || !($value['fakeid'] == $wechat_user['fakeid'])){
                                             continue;
                                         }
                                         $filename = 'wx_'.date('YmdHis').'_'.rand(100, 999);
                                         $res = $WeiXinC->saveMaterial($value['id'],$filename);
                                         if(isset($res['msg']) && strtolower($res['msg']) == 'ok'){
                                             $materials = $WeiXinC->getMaterial($type);
                                             if(is_array($materials)){
                                                 foreach($materials as $mVal){
                                                     if(isset($mVal['file_id']) && $mVal['file_id'] > 0 && isset($mVal['name']) && $mVal['name'] == $filename){

                                                         $wechatUserM = new Wechat_Model_User('wechat');
                                                         $ping_userid = $wechat_user['user_id'] == $list['server_user_id'] ? $list['user_id'] : $list['server_user_id'];
                                                         $pingWechatUser = $wechatUserM->fetchRow(array('wc_id'=>$wechat_user['wc_id'],'user_id'=>$ping_userid,'is_unfollow'=>'0'));
                                                         if($pingWechatUser){
                                                              $re = $WeiXinC->sendVideo($pingWechatUser['fakeid'], $mVal['file_id']);//根据素材的id发送消息
                                                              if(isset($re['base_resp']['err_msg'])){
                                                                  if(strtolower($re['base_resp']['err_msg']) == 'ok'){
                                                                   //发送成功
                                                                     $rk_id = Wechat_Kefu_RecordKefu::record($wechat_user['user_id'], $pingWechatUser['user_id'],"视频消息");
                                                                     $stackM = new Wechat_Model_Stack('wechat');
                                                                     if($rk_id)$stackM->addGetMaterialStack($rk_id,'kefu');
                                                                     $WeiXinC->delMaterial($mVal['file_id']);
                                                                     exit;
                                                                 }else{
                                                                     Wechat_Kefu_RecordKefu::record($wechat_user['user_id'], $pingWechatUser['user_id'],"[系统记录]发送失败，错误信息为:{$re['base_resp']['err_msg']}");
                                                                     $WeiXinC->delMaterial($mVal['file_id']);
                                                                 }
                                                             }
                                                         }
                                                         break;
                                                     }
                                                 }
                                             }
                                         }
                                         break;
                                     }
                              }
                             
                         }

                     }

                     //默认发送失败
                     $response->responseTextMsg('[系统消息]对不起，发送视频失败！');

                    }else{
                         //清除客服标记
                         Wechat_Kefu_RecordKefu::clearKefuSatus($wechat_user['wu_id']);
                    }
                

    }
}
