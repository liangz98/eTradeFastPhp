<?php
/**
 * 记录回复内容
 */
class Wechat_Base_RecordReply{
   public static  function record($response_msg,$msg_type = null){

        $w_controller = Wechat_Base_Controller::getInstance();
        if(empty($msg_type))$msg_type = $w_controller->_wechat_base_params->getMsgType();

        $msg_type = strtolower($msg_type);
        
        $arr = array();
        switch ($msg_type) {
            case 'image'://图片
                isset($w_controller->_wechat_base_params->PicUrl) ? $arr['picurl'] = $w_controller->_wechat_base_params->PicUrl : '';
                break;
            case 'location'://位置
                isset($w_controller->_wechat_base_params->Location_X) ? $arr['location_x'] = $w_controller->_wechat_base_params->Location_X : '';
                isset($w_controller->_wechat_base_params->Location_Y) ? $arr['location_y'] = $w_controller->_wechat_base_params->Location_Y : '';
                isset($w_controller->_wechat_base_params->Scale) ? $arr['scale'] = $w_controller->_wechat_base_params->Scale : '';
                isset($w_controller->_wechat_base_params->Label) ? $arr['label'] = $w_controller->_wechat_base_params->Label : '';
                break;
            case 'location2'://授权位置
                isset($w_controller->_wechat_base_params->Event) ? $arr['event'] = $w_controller->_wechat_base_params->Event : '';
                isset($w_controller->_wechat_base_params->Latitude) ? $arr['location_x'] = $w_controller->_wechat_base_params->Latitude : '';
                isset($w_controller->_wechat_base_params->Longitude) ? $arr['location_y'] = $w_controller->_wechat_base_params->Longitude : '';
                isset($w_controller->_wechat_base_params->Precision) ? $arr['precision'] = $w_controller->_wechat_base_params->Precision : '';
                break;
            case 'link'://链接
                isset($w_controller->_wechat_base_params->Title) ? $arr['title'] = $w_controller->_wechat_base_params->Title : '';
                isset($w_controller->_wechat_base_params->Description) ? $arr['description'] = $w_controller->_wechat_base_params->Description : '';
                isset($w_controller->_wechat_base_params->Url) ? $arr['url'] = $w_controller->_wechat_base_params->Url : '';
                break;
            case 'subscribe'://关注或扫描二维码关注
            case 'scan'://扫描
            case 'view'://点击直接跳转菜单
                isset($w_controller->_wechat_base_params->EventKey) ? $arr['eventkey'] = $w_controller->_wechat_base_params->EventKey : '';
                isset($w_controller->_wechat_base_params->Ticket) ? $arr['ticket'] = $w_controller->_wechat_base_params->Ticket : '';
                break;
            case 'unsubscribe'://取消关注

                break;
            case 'click'://点击菜单
                isset($w_controller->_wechat_base_params->Event) ? $arr['event'] = $w_controller->_wechat_base_params->Event : '';
                $bottomMenuM = new Wechat_Model_BottomMenu('wechat');
                $name = $bottomMenuM->_db->fetchOne("select `name` from ".$bottomMenuM->_prefix.$bottomMenuM->_table_name."
                    where wc_id = '{$w_controller->_wechat['id']}' and `key` = '{$w_controller->_wechat_base_params->EventKey}'");
                $arr['content'] = $name;
                break;
            case 'voice'://语音
                isset($w_controller->_wechat_base_params->Recognition) ? $arr['recognition'] = $w_controller->_wechat_base_params->Recognition : '';
                isset($w_controller->_wechat_base_params->MediaId) ? $arr['media_id'] = $w_controller->_wechat_base_params->MediaId : '';
                isset($w_controller->_wechat_base_params->Format) ? $arr['format'] = $w_controller->_wechat_base_params->Format : '';
                break;
            case 'video'://视频
                isset($w_controller->_wechat_base_params->MediaId) ? $arr['media_id'] = $w_controller->_wechat_base_params->MediaId : '';
                isset($w_controller->_wechat_base_params->ThumbMediaId) ? $arr['thumb_mediaid'] = $w_controller->_wechat_base_params->ThumbMediaId : '';
                break;
            case 'masssendjobfinish'://群发消息结束报告
                $Status = isset($this->_wechat_controller->_wechat_base_params->Status) ? $this->_wechat_controller->_wechat_base_params->Status : '';
                $TotalCount = isset($this->_wechat_controller->_wechat_base_params->TotalCount) ? $this->_wechat_controller->_wechat_base_params->TotalCount : '';
                $FilterCount = isset($this->_wechat_controller->_wechat_base_params->FilterCount) ? $this->_wechat_controller->_wechat_base_params->FilterCount : '';
                $SentCount = isset($this->_wechat_controller->_wechat_base_params->SentCount ) ? $this->_wechat_controller->_wechat_base_params->SentCount : '';
                $ErrorCount = isset($this->_wechat_controller->_wechat_base_params->ErrorCount) ? $this->_wechat_controller->_wechat_base_params->ErrorCount : '';
                $arr['content'] = '状态：'.$Status.';'.'发送粉丝：'.$TotalCount.';'.'过滤粉丝：'.$FilterCount.';'.'成功数：'.$SentCount.';'.'失败数：'.$ErrorCount;
                break;
            case 'templatesendjobfinish'://模板消息发送报告
                $Status = isset($this->_wechat_controller->_wechat_base_params->Status) ? $this->_wechat_controller->_wechat_base_params->Status : '';
                if($Status == 'success'){//送达成功
                     $arr['content'] = '模板消息发送成功';
                }elseif(is_string($Status) && substr($Status, 0, 7) == 'failed:'){
                     $Status_arr = explode(':', $Status);
                     switch ($Status_arr[1]) {
                       case 'user block'://送达由于用户拒收
                         $arr['content'] = '模板消息发送失败,用户拒绝接收';
                         break;
                       case 'system failed'://送达由于其他原因失败
                         $arr['content'] = '模板消息发送失败';
                         break;
                       default:
                         break;
                     }
                }
                break;
            case 'scancodepush':
            case 'scancodewaitmsg':
            case 'picsysphoto':
            case 'picphotooralbum':
            case 'picweixin':
            case 'locationselect':
              
              break;
            case 'text'://文本
            default:
                $msg_type = 'text';
                isset($w_controller->_wechat_base_params->Content) ? $arr['content'] = $w_controller->_wechat_base_params->Content : '';
                break;
        }
        $arr['wc_id'] = $w_controller->_wechat['id'];
        $arr['msg_type'] = $msg_type;
        $arr['response_msg'] = $response_msg;
        $arr['from_user_name'] = $w_controller->_wechat_base_params->FromUserName;
        $arr['add_time'] = time();
        $arr['msgid'] = isset($w_controller->_wechat_base_params->MsgId)?$w_controller->_wechat_base_params->MsgId:'';
        $userWechatRecordM = new Wechat_Model_Record('wechat');
        return $userWechatRecordM->insertRow($arr);
    }

}