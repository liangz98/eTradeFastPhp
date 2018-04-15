<?php
class Wechat_Kefu_Text{
    protected  $_wechat_base_controller = null;
    protected  $_kefuParamM = null;
                function  __construct() {
        if(null === $this->_wechat_base_controller){
            $this->_wechat_base_controller = Wechat_Base_Controller::getInstance();
        }
        if(null === $this->_kefuParamM){
            $this->_kefuParamM = new Wechat_Model_KefuParam('wechat');
        }
    }

    function kefu(){
            $wechat_user = $this->_wechat_base_controller->_wechat_user;
            $response = new Wechat_Base_Response();
            $content = trim($this->_wechat_base_controller->_wechat_base_params->Content);
            $advance_auth = $this->_wechat_base_controller->_wechat['advance_auth'];
            $wechatUserM = new Wechat_Model_User('wechat');
            $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
            $userM = new Seed_Model_User('system');
            
            if($wechat_user['ping_fakeid'] > 0){//对话,ping_fakeid意义已改
                $list = $waitlistM->fetchRow(array('wl_id'=>$wechat_user['ping_fakeid'],'is_over'=>'2')); //对话中
                $pingWechatUser = array();
                if($list){
                    if($list['user_id'] == $list['server_user_id']){
                        Wechat_Kefu_RecordKefu::clearKefuSatus($list);
                        $content_str = $this->_kefuParamM->getContent('cannot_ask_self_resp','对不起，您不能自己咨询自己！');
                        $response->responseTextMsg($content_str);
                        exit;
                    }
                    $ping_userid = $wechat_user['user_id'] == $list['server_user_id'] ? $list['user_id'] : $list['server_user_id'];
                    $pingWechatUser = $wechatUserM->fetchRow(array('user_id'=>$ping_userid,'wc_id'=>$wechat_user['wc_id']));
                }
                if($list && $pingWechatUser){
                    $user = $userM->fetchRow(array('user_id'=>$wechat_user['user_id']));
                    $pingUser = $userM->fetchRow(array('user_id'=>$pingWechatUser['user_id']));
                    if($content == '00'){//退出
                        $respon_str = $this->_kefuParamM->getContent('user_conversation_over_resp','[系统消息]本次对话已结束,感谢您的来访，谢谢！');
                        $send_str = $this->_kefuParamM->getContent('kefu_conversation_over_resp', '[系统消息]本次对话已结束！');
                        
                        //查看是否有排队咨询用户，有刚提醒客服
                        $condiction = array();
                        $condiction['wc_id'] = $wechat_user['wc_id'];
                        $condiction['server_user_id'] = $list['server_user_id'];
                        $condiction['is_over'] = '0';
                        $turnlist = $waitlistM->fetchRow($condiction);
                        if($turnlist){
                            $send_str .= $this->_kefuParamM->getContent('kefu_list_alert', "您还有排队咨询用户，回复【?】查看咨询用户列表或者回复【@】自动接入咨询用户");
                        }
                        
                        if($advance_auth == '1' && $pingWechatUser['last_time'] > (time() - Wechat_Model_User::getSendmsgPeriod())){//通过高级认证
                               $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wechat_user['wc_id']);
                               $kefuAPI->send_text($pingUser['wc_username'], $list['server_user_id'] == $pingUser['user_id'] ? $send_str : $respon_str);
                        }else{
                               $WeiXinC = Wechat_Advance_MockLogin::login(); 
                               if(is_object($WeiXinC)){
                                   $WeiXinC->send($pingWechatUser['fakeid'], $list['server_user_id'] == $pingWechatUser['user_id'] ? $send_str : $respon_str);
                               }
                        }
                        //记录客服对话
                        Wechat_Kefu_RecordKefu::record($wechat_user['user_id'], $pingUser['user_id'], '[系统]对话结束');                  
                        Wechat_Kefu_RecordKefu::clearKefuSatus($list);
                        $response->responseTextMsg($list['server_user_id'] == $wechat_user['user_id'] ? $send_str : $respon_str);
                        exit;
                    
                    }else{//处理对话内容。
                            $my_name = $wechat_user['is_service'] == '1'?($wechat_user['service_name']?$wechat_user['service_name']:'客服'.$wechat_user['service_num'].'号'):$wechat_user['nick_name'];
                            $ping_name = $pingWechatUser['is_service'] == '1'?($pingWechatUser['service_name']?$pingWechatUser['service_name']:'客服'.$pingWechatUser['service_num'].'号'):$pingWechatUser['nick_name'];
                           
                            if(time() - $wechat_user['last_time'] > 60*20 && time() - $pingWechatUser['last_time'] > 60*20){//超过20分钟没操作断开连接
                                if($list['server_user_id'] == $wechat_user['user_id']){
                                    $respon_str = str_replace("{yonghu}", $ping_name, $this->_kefuParamM->getContent('kefu_expire_alert', "[系统消息]用户【{yonghu}】长时间没操作，系统已自动结束对话。"));
                                    $send_str = str_replace("{kefu}", $my_name, $this->_kefuParamM->getContent('user_expire_alert', "[系统消息]尊敬的用户，您长时间没操作，系统已自动结束您与客服【{kefu}】的对话，感谢您的来访！"));
                                }else{
                                    $send_str = str_replace("{yonghu}", $my_name,  $this->_kefuParamM->getContent('kefu_expire_alert', "[系统消息]用户【{yonghu}】长时间没操作，系统已自动结束对话。"));
                                    $respon_str = str_replace("{kefu}", $ping_name, $this->_kefuParamM->getContent('user_expire_alert', "[系统消息]尊敬的用户，您长时间没操作，系统已自动结束您与客服【{kefu}】的对话，感谢您的来访！"));
                                }
                                $wechatUserM->updateRow(array('last_service_time'=>time()), array('wu_id'=>$wechat_user['wu_id']));
                                if($advance_auth == '1' && $pingWechatUser['last_time'] > (time() - Wechat_Model_User::getSendmsgPeriod())){//通过高级认证
                                       $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wechat_user['wc_id']);
                                       $kefuAPI->send_text($pingUser['wc_username'],$send_str);
                                }else{
                                       $WeiXinC = Wechat_Advance_MockLogin::login(); 
                                       if(is_object($WeiXinC)){
                                           $WeiXinC->send($pingWechatUser['fakeid'],$send_str);
                                       }
                                }
                                Wechat_Kefu_RecordKefu::record($wechat_user['user_id'], $pingUser['user_id'], '[系统]对话结束'); 
                                Wechat_Kefu_RecordKefu::clearKefuSatus($list);
                                $response->responseTextMsg($respon_str);
                                exit;
                            }

                            
                            $wechatUserM->updateRow(array('last_service_time'=>time()), array('wu_id'=>$wechat_user['wu_id']));
                            //记录客服对话
                            Wechat_Kefu_RecordKefu::record($wechat_user['user_id'], $pingWechatUser['user_id'], $content);

                            if($advance_auth == '1' && $pingWechatUser['last_time'] > (time() - Wechat_Model_User::getSendmsgPeriod())){//通过高级认证
                                 $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wechat_user['wc_id']);
                                 $kefuAPI->send_text($pingUser['wc_username'],$my_name.' 说：'.$content);
                            }else{
                                 $WeiXinC = Wechat_Advance_MockLogin::login();
                                 if(is_object($WeiXinC))$WeiXinC->send($pingWechatUser['fakeid'],$my_name.' 说：'.$content);
                            }
                            exit;
                            
                    }
                    
               }else{//数据有误的处理
                   Wechat_Kefu_RecordKefu::clearKefuSatus();
               }
                 
            }else{//未建立对话
                 
                 if(preg_match('/^\d+$/s',$content) && $content > 0){//回复数字
                      if($content == '00'){//点击了服务后再退出客服服务
                          Wechat_Kefu_RecordKefu::quitKefu();//没有建立连接时的退出客服服务
                          exit;
                      }
                      $content = intval($content);
                      $userWechatUserM = new Wechat_Model_User('wechat');
                      $pingWechatUser = $userWechatUserM->fetchRow(array('service_num'=>$content,'wc_id'=>intval($this->_wechat_base_controller->_wechat['id']),'is_service'=>'1','service_type'=>'0','is_unfollow'=>'0'));
                      if($pingWechatUser){
                          if($pingWechatUser['wu_id'] == $wechat_user['wu_id']){
                              $Content = $this->_kefuParamM->getContent('cannot_ask_self_resp','对不起，您不能自己咨询自己！');
                              $response->responseTextMsg($Content);
                              exit;
                          }
                          
                          Wechat_Kefu_RecordKefu::checkServerTime();//检查是否在服务时间

                          $pingUser = $userM->fetchRow(array('user_id'=>$pingWechatUser['user_id']));

                          $condiction = array();
                          $condiction["wc_id"] = $wechat_user['wc_id'];
                          $condiction["user_id"] = $wechat_user['user_id'];
                          $condiction["is_over"] = "0";
                          $list = $waitlistM->fetchRow($condiction);
                          if($list){//已在队列排队中
                                if($pingWechatUser['user_id'] == $list['server_user_id']){//排队后再次点击该客服
                                    $condiction = array();
                                    $condiction["wc_id = ?"] = $wechat_user['wc_id'];
                                    $condiction["is_over in (?)"] = array('0','2');//排除已结束的对话
                                    $condiction['server_user_id = ?'] = $pingWechatUser['user_id'];
                                    $condiction['add_time < ?'] = $list['add_time'];
                                    $pre_count = $waitlistM->fetchRowsCount($condiction);
                                    
                                    Wechat_Kefu_RecordKefu::tipsKefu($pingWechatUser, $pingUser);//提醒客服有咨询用户
                                    
                                    if($pre_count > 0){
                                        $pre_count_str = $pre_count > 100 ? (intval($pre_count/100) * 100)."+" : $pre_count;
                                        $Content = str_replace('{ren}',$pre_count_str,$this->_kefuParamM->getContent('user_queue_alert',"尊敬的用户，您前面还有{ren}人在排队中，请耐心等候，您也可以继续选择编号更换客服或者回复数字【00】退出客服服务。"));
                                        $response->responseTextMsg($Content);
                                        exit;
                                    }else{
                                        $Content =  $this->_kefuParamM->getContent('user_accessing_alert',"正在为您接入该客服，请稍候...");
                                        $response->responseTextMsg($Content);
                                        exit;
                                    }
                                }else{//切换客服
                                    $condiction = array();
                                    $condiction['wc_id'] = $wechat_user['wc_id'];
                                    $condiction['user_id'] = $wechat_user['user_id'];
                                    $waitlistM->updateRow(array('is_over'=>'1'), $condiction);//清空原本的队列
                                }
                         }
                         
                        //加入排队
                        $dataSet = array();
                        $dataSet['wc_id']= $wechat_user['wc_id'];
                        $dataSet['server_user_id']= $pingWechatUser['user_id'];
                        $dataSet['user_id']= $wechat_user['user_id'];
                        $dataSet['add_time']= time();
                        $dataSet['is_over']= "0";
                        $wl_id = $waitlistM->insertRow($dataSet);//加入排队队列

                        $condiction = array();
                        $condiction['wc_id = ?'] = $wechat_user['wc_id'];
                        $condiction['server_user_id = ?'] = $pingWechatUser['user_id'];
                        $condiction['is_over in(?)'] = array('0','2');
                        $count = $waitlistM->fetchRowsCount($condiction);

                        Wechat_Kefu_RecordKefu::tipsKefu($pingWechatUser, $pingUser);//提醒客服有咨询用户
                        
                        if($count > 1){
                            $Content = str_replace("{ren}", ($count - 1), $this->_kefuParamM->getContent('user_queue_alert',"尊敬的用户，您前面还有{ren}人在排队中，请耐心等候，您也可以继续选择编号更换客服或者回复数字【00】退出客服服务。"));
                        }elseif($count == 1){
                            $Content = $this->_kefuParamM->getContent('user_accessing_alert',"正在为您接入该客服，请稍候...");
                        }else{
                            $Content = $this->_kefuParamM->getContent('user_queue_failure_alert',"对不起，排队失败，请继续选择编号更换客服。");
                        }
                        $response->responseTextMsg($Content);
                        exit;
                          
                      }
                     
                 }
                
                 //不连接则清除客服标记后接着处理后面内容
                 Wechat_Kefu_RecordKefu::clearKefuSatus();
                 
            }
                
                
    }
    
 

    
}
