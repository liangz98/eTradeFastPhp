<?php
class Wechat_Kefu_RecordWebKefu{
    public static function record($wc_id, $from_user_id,$to_user_id,$content,$new_term = false){
        //记录客服对话
        $recordKefuM = new Wechat_Model_RecordKefu('wechat');
        $dataSet = array();
        $dataSet['wc_id'] = intval($wc_id);
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

        $dataSet['type'] = '1';//消息类型
        $recordKefuM->insertRow($dataSet);
        return $dataSet['term'];
    }
    
    public static function connectSucc($list){
        if($list){//接入咨询用户
            try{
               $wechatUserM = new Wechat_Model_User('wechat');
               $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
               $kefuParamM = new Wechat_Model_KefuParam('wechat');
               
               $wechat_user = $wechatUserM->fetchRow(array('wc_id'=>$list['wc_id'],'user_id'=>$list['server_user_id']));
               
               $condiction = array('wc_id'=>$list['wc_id'],'user_id'=>$list['user_id']);
               $pingWechatUser = $wechatUserM->fetchRow($condiction);

               $dataSet = array();
               $dataSet['ping_fakeid'] = $list['wl_id'];
               $dataSet['sign'] = 'kefu';
               $wechatUserM->updateRow($dataSet, array('wu_id'=>$wechat_user['wu_id']));
               $wechatUserM->updateRow($dataSet, array('wu_id'=>$pingWechatUser['wu_id']));
               $waitlistM->updateRow(array('is_over'=>'2','server_time'=>time()), array('wl_id'=>$list['wl_id']));

               $server_name = ($wechat_user['service_name']?$wechat_user['service_name']:'客服'.$wechat_user['service_num'].'号');
               $Content = str_replace("{kefu}", $server_name, $kefuParamM->getContent("user_access_alert","您好，感谢您的耐心等待，系统已为您接入客服【{kefu}】，如需退出客服服务请回复数字【00】"));
               
               $msgM = new Wechat_WechatClient_Message('wechat');
               $msgM->sendText($list['user_id'], $Content);
               return self::record($list['wc_id'], $wechat_user['user_id'], $pingWechatUser['user_id'],"[系统]对话建立", true);
            }  catch (Exception $e){
                $response = new Wechat_Base_Response();
                $response->responseTextMsg($e->getLine());
            }
        }
    }
    
    public static function clearKefuSatus($wuid_or_listarr = 0){
        $dataSet = array('ping_fakeid'=>'0','sign'=>'');
        if(is_array($wuid_or_listarr) && $wuid_or_listarr['is_over'] == '2'){
              if(isset($wuid_or_listarr['wl_id']) && isset($wuid_or_listarr['wc_id']) && isset($wuid_or_listarr['server_user_id']) && isset($wuid_or_listarr['user_id'])){
                    $userWechatUserM = new Wechat_Model_User('wechat');
                    $waitlistM = new Wechat_Model_KefuWaitingList('wechat');
                    //is_over 0：待咨询，1：结束，2：咨询中
                    $waitlistM->updateRow(array('is_over'=>'1'), array('wl_id'=>$wuid_or_listarr['wl_id']));
                    $userWechatUserM->updateRow($dataSet, array('wc_id = ?'=>$wuid_or_listarr['wc_id'],"user_id = {$wuid_or_listarr['server_user_id']} or user_id = {$wuid_or_listarr['user_id']}"=>null));
                    $userM = new Seed_Model_User('system');
                    $kefuAPI = new Wechat_AdvanceAPI_KefuAPI($wuid_or_listarr['wc_id']);
                    $wcUsernamer = $userM->fetchOne(array('user_id'=>$wuid_or_listarr['user_id']), 'wc_username');
                    $kefuAPI->send_text($wcUsernamer, '[系统消息]本次对话已结束,感谢您的来访，谢谢！');
                    self::record($wuid_or_listarr['wc_id'], $wuid_or_listarr['user_id'], $wuid_or_listarr['server_user_id'],"[系统]对话结束");
                    return true;
              }
        }
        return false;
    }
}

