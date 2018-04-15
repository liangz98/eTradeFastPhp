<?php
class Wechat_Base_CheckInnerCode{
   public static function check($str,$is_outurl = false){
        if(is_string($str)){
        $pattern = "/^gzseed:\/\/[a-z_\d]+$/is";
        if(preg_match($pattern, $str)){
            $urlM = new Wechat_Model_Urls('wechat');
            if(!!$urls = $urlM->fetchRow(array('url_code'=>$str))){
                if(!empty($urls['run_code']) && !$is_outurl){
                    try{
                        eval($urls['run_code']);
                        if(isset($run_ok)){
                            return $run_ok;
                        }else{  //出错回答
                            return '';
                        }
                    }catch (Exception $e){
                        return '';
                    }
                }else{
                    $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
                    //返回带微信id的链接
                    ///若含有真实的地址则不做处理，主要是为了不处理商户的URL，by brave 2014-10-31 15:53:51
                    if(!preg_match("/^http:\/\//", $urls['true_url'])){
                    	return Wechat_Advance_AddWcid::add(Wechat_Advance_AdduseridAndtoken::add($view->seed_Setting['vmarketing_app_server'].'/'.trim($urls['true_url'],'/')));
                    }else{
                    	return Wechat_Advance_AddWcid::add(Wechat_Advance_AdduseridAndtoken::add(trim($urls['true_url'],'/')));
                    }
                }
            }
        }
     }
     return $str;
    }
}