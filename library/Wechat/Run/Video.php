<?php
/**
 * 文本消息
 */
class Wechat_Run_Video{
    function run(){
        $str = '谢谢您上传视频，我会敦促我主人尽快看视频的了！';
        $record_id = Wechat_Base_RecordReply::record($str);
        if($record_id){
            $stackM = new Wechat_Model_Stack('wechat');
            $stackM->addGetMaterialStack($record_id);
        }
        $response = new Wechat_Base_Response();
        $response->responseTextMsg($str);
        exit;
    }
}
