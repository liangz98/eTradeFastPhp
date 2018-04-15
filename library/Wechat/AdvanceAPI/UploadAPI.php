<?php
/**
 * 上传素材高级接口
 */
class Wechat_AdvanceAPI_UploadAPI{
    private $_wc_id = 0;
    
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
    
     function getUploadUrl($type){
       
        $type_arr = array('image','voice','video','thumb');
        if(!in_array($type,$type_arr)){
            return '';
        }
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?type={$type}&access_token=";
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        return empty($access_token)?'':$url.$access_token;
        
    }
    
    
    function upload($type,$media_name){
        
        $upload_url = $this->getUploadUrl($type);
        if($upload_url){
            if(is_uploaded_file($media_name)){//文件流
                $userWechatM = new Wechat_Model_Wechat('wehcat');
                $postfile = array('media'=>$media_name);
                $res = $userWechatM->doPostData($upload_url,http_build_query($postfile));
                clearstatcache();
            }else{
                $send_snoopy = new Wechat_WechatClient_Snoopy();
//                $send_snoopy->set_SourceData(true);
                $send_snoopy->agent = $_SERVER['HTTP_USER_AGENT'];
                $send_snoopy->referer = "http://mp.weixin.qq.com/";
                $post = array();
                $postfile = array('media'=>$media_name);
                $send_snoopy->set_submit_multipart();
                $submit = $upload_url;
                $send_snoopy->submit($submit,$post,$postfile);
                $res = $send_snoopy->results;
            }
           return $res;
        }
        return false;
        
    }
    
    /**
     * 图文消息上传url
     */
    function getUploadNewsUrl(){
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=";
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        return empty($access_token)?'':$url.$access_token;
    }
    
    /**
     * 上传图文
     */
    function uploadnews($news){
            $data = array();
            $data["articles"] = array();
            foreach($news as $k=>$v){
                $arr = array();
                $arr['thumb_media_id'] = isset($v['thumb_media_id']) ? $v['thumb_media_id'] : '';
                $arr['author'] = isset($v['author']) ? $v['author'] : '';
                $arr['title'] = isset($v['title']) ? $v['title'] : '';
                $arr['content_source_url'] = isset($v['content_source_url']) ? $v['content_source_url'] : '';
                $arr['content'] = isset($v['content']) ? $v['content'] : '';
                $arr['digest'] = isset($v['digest']) ? $v['digest'] : '';
                $data["articles"][] = $arr;
            }
            $post_url = $this->getUploadNewsUrl();
            $wechatM = new Wechat_Model_Wechat('wechat');
            $res = $wechatM->doPostData($post_url, json_encode($data));
            return $res;
        }
    
}