<?php
class Wechat_Kefu_RecordKefu{
    public static function record($from_user_id,$to_user_id,$content,$new_term = false){
            $wc_controller = Wechat_Base_Controller::getInstance();
            //记录客服对话
            $recordKefuM = new Wechat_Model_RecordKefu('wechat');
            $dataSet = array();
            $dataSet['wc_id'] = intval($wc_controller->_wechat['id']);
            $dataSet['from_user_id'] = $from_user_id;
            $dataSet['to_user_id'] = $to_user_id;
            if((bool)$new_term){
                $dataSet['term'] = 1 + intval($recordKefuM->_db->fetchOne('select max(`term`) from '.$recordKefuM->_prefix.$recordKefuM->_table_name.'
                 where wc_id = "'.$dataSet['wc_id'].'"'));
                $dataSet['kefu_user_id'] = $from_user_id;
            }else{
                $dataSet['term'] = intval($recordKefuM->_db->fetchOne('select max(`term`) from '.$recordKefuM->_prefix.$recordKefuM->_table_name.'
                where wc_id = "'.$dataSet['wc_id'].'" and  ((from_user_id = "'.$from_user_id.'" and to_user_id = "'.$to_user_id.'")
                or (from_user_id = "'.$to_user_id.'" and to_user_id = "'.$from_user_id.'"))'));
            }
            $dataSet['content'] = $content;
            $dataSet['add_time'] = time();

            $msg_type = $wc_controller->_wechat_base_params->getMsgType();
            $dataSet['type'] = Wechat_Advance_Material::getMaterialId($msg_type);//消息类型
            return $recordKefuM->insertRow($dataSet);
    }
    
    /**
     * 清除客服状态
     */
    public static function clearKefuSatus($wuid_or_listarr = 0){
        $dataSet = array('ping_fakeid'=>'0','sign'=>'');
        if(is_array($wuid_or_listarr)){
              if(isset($wuid_or_listarr['wl_id']) && isset($wuid_or_listarr['wc_id']) && isset($wuid_or_listarr['server_user_id']) && isset($wuid_or_listarr['user_id'])){
                    $userWechatUserM = new Wechat_Model_User('wechat');
                    $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
                    //is_over 0：待咨询，1：结束，2：咨询中
                    $waitlistM->updateRow(array('is_over'=>'1'), array('wl_id'=>$wuid_or_listarr['wl_id']));
                    return $userWechatUserM->updateRow($dataSet, array('wc_id = ?'=>$wuid_or_listarr['wc_id'],"user_id = {$wuid_or_listarr['server_user_id']} or user_id = {$wuid_or_listarr['user_id']}"=>null));
              }
        }else{
            $wu_id = intval($wuid_or_listarr);
            if($wu_id < 1){
                $wc_controller = Wechat_Base_Controller::getInstance();
                $wu_id = $wc_controller->_wechat_user['wu_id'];
            }
            //清除客服标记
            $userWechatUserM = new Wechat_Model_User('wechat');
            return $userWechatUserM->updateRow($dataSet, array('wu_id'=>$wu_id)); 
        }
        return false;
    }
    
    
    /**
     *提醒客服有咨询用户
     */
    public static function tipsKefu($pingWechatUser,$pingUser){
        if(is_array($pingWechatUser)){
                $wc_controller = Wechat_Base_Controller::getInstance();
                $kefuParamM = new Wechat_Model_KefuParam('wechat');
                $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
                $condiction = array();
                $condiction['wc_id = ?'] = $pingWechatUser['wc_id'];
                $condiction['server_user_id = ?'] = $pingWechatUser['user_id'];
                $condiction['is_over = ?'] = '2';//对话中
                $list = $waitlistM->fetchRow($condiction);
                if(empty($list)){//客服在对话中，则不提醒
                    $Content = $kefuParamM->getContent('kefu_asking_alert', "您有咨询用户，请回复【?】查看咨询用户列表或者回复【@】自动接入咨询用户。");
                    if($wc_controller->_wechat['advance_auth'] == '1' && $pingWechatUser['last_time'] > (time() - Wechat_Model_User::getSendmsgPeriod())){
                         $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wc_controller->_wechat['id']);
                         $re = $kefuAPI->send_text($pingUser['wc_username'],$Content);
                         if(isset($re['errmsg']) && strtolower($re['errmsg']) == 'ok'){
                             return true;
                         }
                         return false;
                    }elseif($pingWechatUser['fakeid'] > 0){
                         $weixinC = Wechat_Advance_MockLogin::login();
                         $re = $weixinC->send($pingWechatUser['fakeid'],$Content);
                         if(isset($re['base_resp']['err_msg']) && strtolower($re['base_resp']['err_msg']) == 'ok'){
                             return true;
                         }
                         return false;
                   }
              }else{

                      //判断是否超时了
                      if(time() - $pingWechatUser['last_time'] > 60*20){
                           $userWechatUserM = new Wechat_Model_User('wechat');
                           $anotherWechatUser = $userWechatUserM->fetchRow(array('wc_id'=>$list['wc_id'],'user_id'=>$list['user_id'],'is_unfollow'=>'0'));
                           if($anotherWechatUser && (time() - $anotherWechatUser['last_time'] > 60*20)){
                                Wechat_Kefu_RecordKefu::record($list['server_user_id'], $list['user_id'], '[系统]超时对话自动结束');
                                self::clearKefuSatus($list);

                                $kefu_name = $pingWechatUser['service_name'] ? $pingWechatUser['service_name'] : '客服'.$pingWechatUser['service_num'].'号';
                                $another_name = $anotherWechatUser['mark_name'] ? $anotherWechatUser['mark_name'] : ($anotherWechatUser['nick_name'] ? $anotherWechatUser['nick_name'] : '用户'.$anotherWechatUser['user_id'].'号');

                                $respon_str = str_replace("{yonghu}", $another_name, $kefuParamM->getContent('kefu_expire_alert', '[系统消息]用户【{yonghu}】长时间没操作，系统已自动结束对话。'));
                                $send_str = str_replace("{kefu}", $kefu_name, $kefuParamM->getContent("user_expire_alert", '[系统消息]尊敬的用户，您长时间没操作，系统已自动结束您与客服【{kefu}】的对话，感谢您的来访！'));

                                if($wc_controller->_wechat['advance_auth'] == '1' && $pingWechatUser['last_time'] > time() - (Wechat_Model_User::getSendmsgPeriod())){
                                    $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wc_controller->_wechat['id']);
                                    $kefuAPI->send_text($pingUser['wc_username'], $respon_str);
                                }elseif($pingWechatUser['fakeid'] > 0){
                                    $weixinC = Wechat_Advance_MockLogin::login();
                                    $weixinC->send($pingWechatUser['fakeid'],$respon_str);
                                }
                                
                                if($wc_controller->_wechat['advance_auth'] == '1' && $anotherWechatUser['last_time'] > time() - (Wechat_Model_User::getSendmsgPeriod())){
                                    $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wc_controller->_wechat['id']);
                                    $userM = new Seed_Model_User('system');
                                    $anotherUser = $userM->fetchRow(array('user_id'=>$anotherWechatUser['user_id']));
                                    $kefuAPI->send_text($anotherUser['wc_username'], $send_str);
                                }elseif($anotherWechatUser['fakeid'] > 0){
                                    $weixinC = Wechat_Advance_MockLogin::login();
                                    $weixinC->send($anotherWechatUser['fakeid'],$send_str);
                                }
                                
                           }
                           return true;
                       }
                       return false;

              }
        }
        return false;
    }
    
    
    /**
     * 没有建立连接时的退出客服服务
     */
    public static function quitKefu(){
        $wc_controller = Wechat_Base_Controller::getInstance();
        $kefuParamM = new Wechat_Model_KefuParam('wechat');
        self::clearKefuSatus($wc_controller->_wechat_user['wu_id']);
         
        $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
        $condiction = array();
        $condiction['wc_id = ?'] = $wc_controller->_wechat_user['wc_id'];
        $condiction['user_id = ?'] = $wc_controller->_wechat_user['user_id'];
        $condiction['is_over = ?'] = '0';//排队中
        $waitlistM->updateRow(array('is_over'=>'1'), $condiction);
        $Content = $kefuParamM->getContent("user_quit_queun_resp", "您已成功退出客服服务，感谢您的来访，欢迎再次咨询！");
        $wc_controller->_wechat_base_response->responseTextMsg($Content);
    }
    
    /**
     * 咨询者列表
     */
    public static function visitorList() {
         $wc_controller = Wechat_Base_Controller::getInstance();
         $wechat_user = $wc_controller->_wechat_user;
         $content = trim($wc_controller->_wechat_base_params->Content);
         if($wechat_user['is_service'] == '1'){
              $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
              $condiction = array();
              $condiction['wc_id'] = $wechat_user['wc_id'];
              $condiction['server_user_id'] = $wechat_user['user_id'];
              $condiction['is_over'] = "0";
              $list = $waitlistM->fetchRows(array(0,0), $condiction, array('add_time desc'));
              if($list){
                    $wechatUserM = new Wechat_Model_User('wechat');
                    $str = "";
                    foreach ($list as $key => $value) {
                        $wechatuser = $wechatUserM->fetchRow(array('wc_id'=>$value['wc_id'],'user_id'=>$value['user_id']));
                        if($wechatuser && $wechatuser['is_unfollow'] == '0'){
                             $str .= '['.$wechatuser['user_id'].']  '.($wechatuser['mark_name'] ? $wechatuser['mark_name'] : ($wechatuser['nick_name'] ? $wechatuser['nick_name'] : '用户'.$wechatuser['user_id']))."\r\n";
                        }
                    }
                  
                    if($str){
                        $str .= "\r\n".'选择左侧编号接入咨询用户。';
                        $wechatUserM->updateRow(array('sign'=>'cvisitor'),array('wu_id'=>$wc_controller->_wechat_user['wu_id']));
                        $wc_controller->_wechat_base_response->responseTextMsg($str);
                        exit;
                    }
              }else{
                  $kefuParamM = new Wechat_Model_KefuParam('wechat');
                  $Content = $kefuParamM->getContent("kefu_no_asking_resp", "您暂时没有咨询用户！");
                  $wc_controller->_wechat_base_response->responseTextMsg($Content);
              }
         }
        return ;
    }
    
    /**
     * 自动接入下一个咨询者
     */
    public static function autoNextVisitor(){
         $wc_controller = Wechat_Base_Controller::getInstance();
         $wechat_user = $wc_controller->_wechat_user;
         if($wechat_user['is_service'] == '1'){
              $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
              $condiction = array();
              $condiction['wc_id'] = $wechat_user['wc_id'];
              $condiction['server_user_id'] = $wechat_user['user_id'];
              $condiction['is_over'] = "0";
              $next = $waitlistM->fetchRow($condiction, "*", array('add_time asc'));
              if($next){
                  self::connectSucc($next);
                  exit;
              }else{
                  $kefuParamM = new Wechat_Model_KefuParam('wechat');
                  $Content = $kefuParamM->getContent("kefu_no_asking_resp", "您暂时没有咨询用户！");
                  $wc_controller->_wechat_base_response->responseTextMsg($Content);
              }
         }
        
    }
    
    
    /*
     * 成功建立对话连接
     */
    public static function connectSucc($list){
        if($list){//接入咨询用户
            try{
               $wc_controller = Wechat_Base_Controller::getInstance();
               $wechatUserM = new Wechat_Model_User('wechat');
               $userM = new Seed_Model_User('system');
               $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
               $kefuParamM = new Wechat_Model_KefuParam('wechat');
               
               $wechat_user = $wc_controller->_wechat_user;
                       
               $condiction = array('wc_id'=>$list['wc_id'],'user_id'=>$list['user_id']);
               $pingWechatUser = $wechatUserM->fetchRow($condiction);
               $pingUser = $userM->fetchRow(array('user_id'=>$list['user_id']));

               $dataSet = array();
               $dataSet['ping_fakeid'] = $list['wl_id'];
               $dataSet['sign'] = 'kefu';
               $wechatUserM->updateRow($dataSet, array('wu_id'=>$wechat_user['wu_id']));
               $wechatUserM->updateRow($dataSet, array('wu_id'=>$pingWechatUser['wu_id']));
               $waitlistM->updateRow(array('is_over'=>'2','server_time'=>time()), array('wl_id'=>$list['wl_id']));

               $server_name = ($wechat_user['service_name']?$wechat_user['service_name']:'客服'.$wechat_user['service_num'].'号');
               $visitor_name = $pingWechatUser['mark_name'] ? $pingWechatUser['mark_name'] : ($pingWechatUser['nick_name'] ? $pingWechatUser['nick_name'] : '用户'.$pingWechatUser['user_id']);
               $Content = str_replace("{kefu}", $server_name, $kefuParamM->getContent("user_access_alert","您好，感谢您的耐心等待，系统已为您接入客服【{kefu}】，如需退出客服服务请回复数字【00】"));
               if($wc_controller->_wechat['advance_auth'] == '1' && $pingWechatUser['last_time'] > (time() - Wechat_Model_User::getSendmsgPeriod())){
                    $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wechat_user['wc_id']);
                    $re = $kefuAPI->send_text($pingUser['wc_username'], $Content);
               }else{
                    $WeiXinC = Wechat_Advance_MockLogin::login();
                    if(is_object($WeiXinC)){
                       $re = $WeiXinC->send($pingWechatUser['fakeid'],$Content);
                    }
               }
               self::record($wechat_user['user_id'], $pingWechatUser['user_id'],"[系统]对话建立", true);
               $wc_controller->_wechat_base_response->responseTextMsg(str_replace("{yonghu}", $visitor_name,$kefuParamM->getContent("kefu_access_alert","您已成功接入用户【{yonghu}】，退出请回复数字【00】")));
               exit;
            }  catch (Exception $e){
                $response = new Wechat_Base_Response();
                $response->responseTextMsg($e->getLine());
            }
        }
    }

    public static function checkServerTime(){
         $kefuParamM = new Wechat_Model_KefuParam('wechat');
         $restDay = trim($kefuParamM->getContent('rest_dayof_week'),',');
         if(preg_match("/^[1-7](,[1-7])*$/i", $restDay)){
             if(in_array(date('w'), explode(',', $restDay))){
                 self::clearKefuSatus();
                 $out_of_server_alert = $kefuParamM->getContent("out_of_server_alert", "对不起，今天是休息天，客服暂时不能给您提供咨询服务。");
                 $response = new Wechat_Base_Response();
                 $response->responseTextMsg($out_of_server_alert);
                 exit;
             }
         }

         $server_start_time = $kefuParamM->getContent('server_start_time');
         if(preg_match("/\d{1,2}\:\d{1,2}/i", $server_start_time)){
              if(time() < strtotime(date('Y-m-d ').$server_start_time.':00')){ //客服还没上班
                 $out_of_server_start_time_resp = $kefuParamM->getContent("out_of_server_start_time_resp", "对不起，客服还没上班呢，请稍候再来咨询哦。");
                 $response = new Wechat_Base_Response();
                 $response->responseTextMsg($out_of_server_start_time_resp);
                 exit;
              }
         }

         $server_over_time = $kefuParamM->getContent('server_over_time');
         if(preg_match("/\d{1,2}\:\d{1,2}/i", $server_over_time)){
             if(time() > strtotime(date('Y-m-d ').$server_over_time.':00')){//客服已下班
                 $out_of_server_over_time_resp = $kefuParamM->getContent("out_of_server_over_time_resp", "对不起，客服已经下班了，请在工作日再来咨询哦。");
                 $response = new Wechat_Base_Response();
                 $response->responseTextMsg($out_of_server_over_time_resp);
                 exit;
             }
         }
    }
    
    
    public static function kefuOper() {
         $wc_controller = Wechat_Base_Controller::getInstance();
         $content = trim($wc_controller->_wechat_base_params->Content);
         
         $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
                 '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9','？' => '?','＠'=>'@');
         $content =  strtr($content,$arr);
         switch ($content) {
             case '00':
                    $wechat_user = $wc_controller->_wechat_user;
                    $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
                    $condiction = array();
                    $condiction['wc_id = ?'] = $wechat_user['wc_id'];
                    $condiction['user_id = ?'] = $wechat_user['user_id'];
                    $condiction['is_over in (?)'] = array('0','2');
                    $list = $waitlistM->fetchRow($condiction);
                    if($list){
                        self::quitKefu();
                    }else{
                        $kefuParamM = new Wechat_Model_KefuParam('wechat');
                        $wc_controller->_wechat_base_response->responseTextMsg($kefuParamM->getContent("user_no_server_resp","对不起，您没有在咨询列表！"));
                    }
                 break;
             case '?':
                    if($wc_controller->_wechat_user['is_service'] == '1'){
                        self::visitorList();
                    }
                 break;
             case '@':
                    if($wc_controller->_wechat_user['is_service'] == '1'){
                        self::autoNextVisitor();
                    }
                 break;
             default:
                 break;
         }
    }
    
    
    
}