<?php
/**
 * 微信回复类
 */
class Wechat_Base_Response{

protected $_wechat_base_params = null;
protected $_token_code = false;

public function  __construct() {
    if (null === $this->_wechat_base_params) {
       $this->_wechat_base_params = Wechat_Base_Params::getInstance();
    }
}

public  function responseTextMsg($Content){ //文本消息
        $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>";

        $urlFilter = new Seed_Filter_Url();
        if($urlFilter->filter($Content)){//检查是否url
            $Content = Wechat_Advance_AddTokenCode::add($Content);
        }else{
            $emotionC = new Wechat_Emotion_WechatEmotion();
            $Content = $emotionC->outputEmotion($Content);//处理表情符号
        }
        if(empty($Content))exit;
        $resultStr = sprintf($textTpl,$this->_wechat_base_params->FromUserName, $this->_wechat_base_params->ToUserName, time(),'text', $this->format($Content));
        $w_controller = Wechat_Base_Controller::getInstance();
        if($w_controller->isReadable('Wechat_Advance_SendBatchMsg')){
             Wechat_Advance_SendBatchMsg::send();
        }
        echo $resultStr;
        exit;
    }

  public function responseNewsMsg($news){ //图文消息
        if(empty($news))exit;
        $textTpl = " <xml>
         <ToUserName><![CDATA[%s]]></ToUserName>
         <FromUserName><![CDATA[%s]]></FromUserName>
         <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[%s]]></MsgType>
         <ArticleCount>%s</ArticleCount>
         <Articles>";
        $ArticleCount = count($news);
        if($ArticleCount > 10){
            $news = array_slice($news,0, 10);//最多10个item
            $ArticleCount = 10;
        }
        
        $w_controller = Wechat_Base_Controller::getInstance();
        if($w_controller->isReadable('Wechat_Advance_AddTokenCode')){
             $this->_token_code = true;
        }
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        foreach($news as $v){
            if($this->_token_code){
               $v['url'] = Wechat_Advance_AddTokenCode::add($v['url']);
            }
        $textTpl .= "<item>
         <Title><![CDATA[".$this->format($v['title'])."]]></Title>
         <Description><![CDATA[".$this->format($v['description'])."]]></Description>
         <PicUrl><![CDATA[".$this->format($view->WechatShowImage($v['picurl']))."]]></PicUrl>
         <Url><![CDATA[".$this->format($v['url'])."]]></Url>
         </item>";
        }
        $textTpl .= "</Articles>
         </xml> ";
        $resultStr = sprintf($textTpl, $this->_wechat_base_params->FromUserName, $this->_wechat_base_params->ToUserName, time(),'news', $ArticleCount);
        if($w_controller->isReadable('Wechat_Advance_SendBatchMsg')){
             Wechat_Advance_SendBatchMsg::send();
        }
        echo $resultStr;
        exit;
    }

  public function responseMusicMsg($Title,$Description,$MusicUrl,$HQMusicUrl){ //音乐消息
        $textTpl = "<xml>
         <ToUserName><![CDATA[%s]]></ToUserName>
         <FromUserName><![CDATA[%s]]></FromUserName>
         <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[%s]]></MsgType>
         <Music>
         <Title><![CDATA[%s]]></Title>
         <Description><![CDATA[%s]]></Description>
         <MusicUrl><![CDATA[%s]]></MusicUrl>
         <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
         </Music>
         </xml>";
         $resultStr = sprintf($textTpl, $this->_wechat_base_params->FromUserName, $this->_wechat_base_params->ToUserName, time(),'music',  $this->format($Title),  $this->format($Description),$this->format($MusicUrl),$this->format($HQMusicUrl));
         $w_controller = Wechat_Base_Controller::getInstance();
         if($w_controller->isReadable('Wechat_Advance_SendBatchMsg')){
             Wechat_Advance_SendBatchMsg::send();
         }
         echo $resultStr;
         exit;
    }
    
	public function responseVoiceMsg($mediaId){ //音乐消息
        $textTpl = "<xml>
         <ToUserName><![CDATA[%s]]></ToUserName>
         <FromUserName><![CDATA[%s]]></FromUserName>
         <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[%s]]></MsgType>
         <Voice>
            <MediaId><![CDATA[%s]]></MediaId>
            </Voice>
         </xml>";
         $resultStr = sprintf($textTpl, $this->_wechat_base_params->FromUserName, $this->_wechat_base_params->ToUserName, time(),'voice',  $this->format($mediaId));
         echo $resultStr; 
         exit;
    }
    
    public function responseKeFu(){
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        </xml>";
        $resultStr = sprintf($textTpl, $this->_wechat_base_params->FromUserName, $this->_wechat_base_params->ToUserName, time(),'transfer_customer_service');
        echo $resultStr;
        exit;
    }
    
    function format($str){
        return str_replace('%','%%', $str);
    }
}