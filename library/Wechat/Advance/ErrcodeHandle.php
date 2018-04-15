<?php
/*
 * 接口错误处理类
 */
class Wechat_Advance_ErrcodeHandle {
    protected $_wechat_controller = null;
    /*
     * 自动访问用
     */
    public function handleErr($errcode){
        if(null === $this->_wechat_controller){
            $this->_wechat_controller = Wechat_Base_Controller::getInstance();
        }

        $errcode_arr = $this->access_token_ErrArr();
        if(in_array($errcode, $errcode_arr)){
            $wc_id = $this->_wechat_controller->_wechat['id'];
            return $this->handle_access_token($wc_id);
        }
    }

    /*
     * 自主调用用
     */
    public function handleErrByWcid($wcid,$errcode){
        $errcode_arr = $this->access_token_ErrArr();
        if(in_array($errcode, $errcode_arr)){
            return $this->handle_access_token($wcid);
        }
    }


    /*
     * 获取access_token错误码
     */
    public function access_token_ErrArr(){
        $errcode_arr = array('40001','40014','41001','42001');//access_token错误
        return $errcode_arr;
    }

    /*
     * 处理access_token错误
     */
    public function handle_access_token($wc_id){
        $wechatM = new Wechat_Model_Wechat('wechat');
        return $wechatM->updateRow(array('access_token'=>'','access_token_expire'=>'0'), array('id'=>$wc_id));
    }

}