<?php
/**
 * 客服主动发消息高级接口
 */
class Wechat_AdvanceAPI_KefuAPI{
    private $_wc_id = 0;
    
    function __construct($wc_id) {
        $this->_wc_id = intval($wc_id);
    }
    
    function getPostUrl(){
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=";
        $access_token = Wechat_Advance_AccessToken::getAccessTokenByWcid($this->_wc_id);
        return empty($access_token)?'':$url.$access_token;
    }
    
    function send_text($OPENID,$Content){
        $data = array();
        $data["touser"] = $OPENID;
        $data["msgtype"] = "text";
        $data["text"] = array(
            "content"=>"[content]"
        );
        $json_str = str_replace(array('[content]'),array(addslashes($Content)),json_encode($data));
        return $this->post($json_str);
    }
    
    function send_notice($OPENID,$Content){
        $data = array();
        $data["touser"] = $OPENID;
        $data["msgtype"] = "text";
        $data["text"] = array(
            "content"=>"[content]"
        );
        $json_str = str_replace(array('[content]'),array($Content),json_encode($data));
        return $this->post($json_str);
    }
    
    function send_image($OPENID,$media_id){
        $data = array();
        $data["touser"] = $OPENID;
        $data["msgtype"] = "image";
        $data["image"] = array(
            "media_id"=>$media_id
        );
        $json_str = json_encode($data);
        return $this->post($json_str);
    }
    
    function send_voice($OPENID,$media_id){
        $data = array();
        $data["touser"] = $OPENID;
        $data["msgtype"] = "voice";
        $data["voice"] = array(
            "media_id"=>$media_id
        );
        $json_str = json_encode($data);
        return $this->post($json_str);
    }
    
    function send_video($OPENID,$media_id,$thumb_media_id){
        $data = array();
        $data["touser"] = $OPENID;
        $data["msgtype"] = "video";
        $data["video"] = array(
            "media_id"=>$media_id,
            "thumb_media_id"=>$thumb_media_id
        );
        $json_str = json_encode($data);
        return $this->post($json_str);
        
    }
    
    function send_music($OPENID,$title,$description,$musicurl,$hqmusicurl,$thumb_media_id){
        $data = array();
        $data["touser"] = $OPENID;
        $data["msgtype"] = "music";
        $data["music"] = array(
            "title"=>"[title]",
            "description"=>"[description]",
            "musicurl"=>$musicurl,
            "hqmusicurl"=>$hqmusicurl,
            "thumb_media_id"=>$thumb_media_id
        );
        $json_str = str_replace(array('[title]','[description]'),array(addslashes($title),  addslashes($description)),json_encode($data));
        return $this->post($json_str);   
    }
    
    function send_news($OPENID,$articles){
        $artis = array();
        $articles = (array)$articles;
        $i = 0;
        $replace_key = $replace_val = array();
        foreach($articles as $key=>$val){
            $replace_val[] = isset($val['title'])?addslashes($val['title']):'';
            $replace_val[] = isset($val['description'])?addslashes($val['description']):'';
            $replace_val[] = isset($val['url'])?$val['url']:'';
            $replace_val[] = isset($val['picurl'])?$val['picurl']:'';
            
            $artis[$i]["title"] = $replace_key[] = "[title_{$i}]";
            $artis[$i]["description"] = $replace_key[] =  "[description_{$i}]";
            $artis[$i]["url"] = $replace_key[] =  "[url_{$i}]";
            $artis[$i]["picurl"] = $replace_key[] = "[picurl_{$i}]";
            
            ++$i;
        }
        $data = array();
        $data["touser"] = $OPENID;
        $data["msgtype"] = "news";
        $data["news"] = array(
            "articles"=>$artis
        );
        
        $json_str = str_replace($replace_key,$replace_val,json_encode($data));
        return $this->post($json_str);
        
    }
    
    function post($json_str){
        $url = $this->getPostUrl();
        if($url){
            $userWechatM = new Wechat_Model_Wechat('wechat');
            $re = $userWechatM->doPostData($url, $json_str);
            $res = json_decode($re, true);
            return $res;
        }
        return false;
    }
    
}
