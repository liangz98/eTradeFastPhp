<?php
/**
 * 手机信息表模型 (snsys_mobile_outboxes)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_MobileOutbox extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'mobile_outboxes';

    /**
     * 获取最新一条短信
     *
     * @return	array
     *
     */
    function getLastToSend(){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("is_sended = ?", '0');
            $select->order('send_id DESC');
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 发送短信
     *
     * @param	string	$mobile	手机号码
     * @param	string	$content	短信内容
     * @param	int	$time	发送时间
     * @return
     *
     */
    function mobileSend($mobile,$content,$time){
		$this->insertRow(array('send_content'=>$content,'send_vcode'=>'','send_mobile'=>$mobile,'add_time'=>$time));

		$ecode = (defined('MOBILE_SEND_ECODE'))?MOBILE_SEND_ECODE:"";
		$username = (defined('MOBILE_SEND_USERNAME'))?MOBILE_SEND_USERNAME:"";
		$password = (defined('MOBILE_SEND_PASSWORD'))?MOBILE_SEND_PASSWORD:"";

		//短信接口
		$url="http://utf8.sms.webchinese.cn/?Uid=".$username."&Key=".$password."&smsMob=".$mobile."&smsText=".urlencode($content)."";
		Seed_Browser::view_page($url);
	}

    /**
     * 发送手机验证码
     *
     * @param	string	$mobile	手机号码
     * @param	string	$content	发送内容
     * @param	string	$vcode	验证码
     * @param	int	$time	发送时间
     * @return
     *
     */
	function mobileSendVcode($mobile,$content,$vcode,$time){
		$this->insertRow(array('send_content'=>$content,'send_vcode'=>$vcode,'send_mobile'=>$mobile,'add_time'=>$time));

		$ecode = (defined('MOBILE_SEND_ECODE'))?MOBILE_SEND_ECODE:"";
		$username = (defined('MOBILE_SEND_USERNAME'))?MOBILE_SEND_USERNAME:"";
		$password = (defined('MOBILE_SEND_PASSWORD'))?MOBILE_SEND_PASSWORD:"";

        //短信接口
        $url="http://utf8.sms.webchinese.cn/?Uid=".$username."&Key=".$password."&smsMob=".$mobile."&smsText=".urlencode($content)."";
        Seed_Browser::view_page($url);
	}
}
