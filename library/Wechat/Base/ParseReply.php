<?php
/**
 * 解析回复内容
 */
class Wechat_Base_ParseReply{
   public static function parse($reply_arr,$save_record = true){
        $save_record = (bool)$save_record;
        if(!isset($reply_arr['reply_type']))return;
        switch ($reply_arr['reply_type']) {
            case 2:
                $text = preg_split('/\{\|\}/is', $reply_arr['r_text'], null,PREG_SPLIT_NO_EMPTY);
                $index = rand(0,count($text)-1);
                $text[$index] = Wechat_Base_CheckInnerCode::check($text[$index]);//检查是否有内部运行码
                if($save_record){
                    Wechat_Base_RecordReply::record($text[$index]);
                }
                $response = new Wechat_Base_Response();
                $response->responseTextMsg($text[$index]);
                break;
            case 3:
                $userWechatReplyDetailM = new Wechat_Model_ReplyDetail('wechat');
                $news = $userWechatReplyDetailM->fetchRows(array(0,10), array('CONCAT(",",r_id,",") like ?'=>'%,'.$reply_arr['r_id'].',%'),array('order_by asc','d_id asc'));
                $news_arr = array();
                $record_title = '[图文]';
                foreach($news as $k=>$v){
                    $res = json_decode($v['content'], true);
                    $res['url'] = Wechat_Base_CheckInnerCode::check($res['link'],true);
                    unset($res['link']);
                    $news_arr[] = $res;
                    $record_title .= ($record_title ? "\r\n" : "").$res['title'];
                }
                if($save_record){
                      Wechat_Base_RecordReply::record($record_title);
                }
                $response = new Wechat_Base_Response();
                $response->responseNewsMsg($news_arr);
                break;
            case 4: ///增加语音回复， by brave 2014-10-17 16:07:41
                $userWechatReplyDetailM = new Wechat_Model_ReplyDetail('wechat');
                $v = $userWechatReplyDetailM->fetchRow(array('r_id'=>$reply_arr['r_id']));
                $voice = json_decode($v['content'], true);
                $record_title = '[语音]';
                if($save_record){
                      Wechat_Base_RecordReply::record($record_title);
                }
                
                ///获取微信公众帐号ID
                $w_controller = Wechat_Base_Controller::getInstance();
                $wc_id = $w_controller->_wechat['id']; 
                $type = 'voice';
                $file_path = SEED_FILE_ROOT;
                $file_path = str_replace('/files', '', $file_path);
                $media_name = $file_path.$voice['voice_path'];
                
                $wechatM = new Wechat_Model_Wechat('wechat');
                $wechat = $wechatM->fetchRow(array('id'=>$wc_id,'is_actived'=>'1','is_abort'=>'0','is_del'=>'0'));
                $token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
                $token_url = sprintf($token_url,$wechat['appid'],$wechat['appsecret']);
                
                /*$response = new Wechat_Base_Response();
		        $response->responseTextMsg('i:'.$token_url);*/
                
        		$res = file_get_contents($token_url);
        		$access_token = null;
				if (!empty($res)) {
					$data = json_decode($res, true);
					if(isset($data['access_token'])){
						$access_token = trim($data['access_token']);
						$upload_url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type=voice';
						$res = $wechatM->vcurl($upload_url, array('media' => '@' . $media_name));
		                
		                $data = json_decode($res, true);
						if (isset($data['media_id'])){
							$response = new Wechat_Base_Response();
							$response->responseVoiceMsg($data['media_id']);
						}
					} 
				}
                break;
            case 1:
            default:
                $reply_arr['r_text'] = Wechat_Base_CheckInnerCode::check($reply_arr['r_text']);//检查是否有内部运行码
                if($save_record){
                      Wechat_Base_RecordReply::record($reply_arr['r_text']);
                }
                $response = new Wechat_Base_Response();
                $response->responseTextMsg($reply_arr['r_text']);
                break;
        }
    }
}