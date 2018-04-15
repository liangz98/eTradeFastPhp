<?php
class Seed_Controller_Action4Agent extends Zend_Controller_Action
{
	function init()
	{
		if(file_exists(SEED_LICENSE_ROOT."/init.php")){
			require(SEED_LICENSE_ROOT."/init.php");
		}else{
			exit("License File Not Found!");
		}
		
		if(!defined('CURRENT_MODULE_NAME'))throw new Exception("CURRENT_MODULE_NAME not defined");
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		
		$this->initView();
		$this->view->seed_BaseUrl = $this->_request->getBaseUrl();
		
		$token = $this->_request->getParam('token');
		$fromapi=false;
		if(empty($token)){
			$token = Seed_Cookie::getCookie('seed_AgentToken');
		}else{
			$fromapi=true;
		}
		$this->view->seed_Token = $token;
		
		$userM = new Seed_Model_User('system');
		if(!empty($token)){
			$my = Seed_Token::decode($token);
			if(!is_array($my)){
				if ($this->_request->isPost())
					exit('关键数据损坏！');
				else
					throw new Exception('关键数据损坏！');
				exit;
			}
			$user = $userM->fetchRow(array('user_id'=>$my['user_id'],'token'=>$my['token'],'is_actived'=>'1'));
		}else{
			$user=array();
		}
		$user['token']=$token;
		if(!isset($user['user_id']))$user['user_id']=0;		
		if(!isset($user['user_name']))$user['user_name']="游客";
		$this->view->seed_User = $user;
		
		if($user['user_id']>0){
			$shopM = new Shop_Model_Shop('shop');
			$shop = $shopM->fetchRow(array("user_id"=>$user['user_id']));
			$this->view->shop = $shop;
		}
		
		//获取访问控制列表ACL
		$fileM = new Seed_Model_Cache2File();
		
		//获取系统设置
		$setting = $fileM->get($mod_name."_setting");
		
		///发送短信的定义参数代码
		if(isset($setting['mobile_send_ecode']) && isset($setting['mobile_send_username']) && isset($setting['mobile_send_password'])){
			if(!defined('MOBILE_SEND_ECODE'))define('MOBILE_SEND_ECODE',$setting['mobile_send_ecode']);
			if(!defined('MOBILE_SEND_USERNAME'))define('MOBILE_SEND_USERNAME',$setting['mobile_send_username']);
			if(!defined('MOBILE_SEND_PASSWORD'))define('MOBILE_SEND_PASSWORD',$setting['mobile_send_password']);
		}
		
		///这里设置默认的微信参数，若后台没设置的话，就使用以下默认的 2013-11-23 15:21:20
		if(!isset($setting['website_domain']))$setting['website_domain']=$_SERVER['HTTP_HOST'];
		if(!isset($setting['upload_app_server']))$setting['upload_app_server']="http://".$_SERVER['HTTP_HOST']."/uploadapi";
		if(!isset($setting['upload_view_server']))$setting['upload_view_server']="http://".$_SERVER['HTTP_HOST']."/upload_files";
		if(!isset($setting['upload_return_server']))$setting['upload_return_server']="";
		if(!isset($setting['www_app_server']))$setting['www_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['v_app_server']))$setting['v_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['union_app_server']))$setting['union_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['wechat_app_server']))$setting['wechat_app_server']="http://".$_SERVER['HTTP_HOST'];
		if(!isset($setting['vuser_app_server']))$setting['vuser_app_server']="http://".$_SERVER['HTTP_HOST']."/vuser";
		if(!isset($setting['vmall_app_server']))$setting['vmall_app_server']="http://".$_SERVER['HTTP_HOST']."/vmall";
		if(!isset($setting['vhome_app_server']))$setting['vhome_app_server']="http://".$_SERVER['HTTP_HOST']."/vhome";
		if(!isset($setting['vservice_app_server']))$setting['vservice_app_server']="http://".$_SERVER['HTTP_HOST']."/vservice";
		if(!isset($setting['vmarketing_app_server']))$setting['vmarketing_app_server']="http://".$_SERVER['HTTP_HOST']."/vmarketing";
		if(!isset($setting['vplug_app_server']))$setting['vplug_app_server']="http://".$_SERVER['HTTP_HOST']."/vplug";
		if(!isset($setting['mobunion_app_server']))$setting['mobunion_app_server']="http://".$_SERVER['HTTP_HOST']."/mobunion";
		if(!isset($setting['agent_app_server']))$setting['agent_app_server']="http://".$_SERVER['HTTP_HOST']."/agent";
		if(!isset($setting['static_app_server']))$setting['static_app_server']="http://".$_SERVER['HTTP_HOST']."/static";
		///END
		
		$this->view->seed_Setting = $setting;
		
        $this->view->user_pos = $this->_request->getParam('controller');
        
        ///读取商家/分销商绑定的公众帐号
        if($user['user_id']>0){
	        $wechatM = new Wechat_Model_Wechat('wechat');
	        $this->view->wechat = $wechatM->fetchRow(array('user_id'=>$user['user_id'],'wechat_type'=>'1','is_actived'=>'1','is_abort'=>'0','is_del'=>'0'));
        }
	}
	
	/**
     * 输出JSON数据
     *
     * @param  $code  状态码
     * @param  $msg   消息
     * @param  $data  返回数据
     */
    protected function output($code = 0, $msg = '', $data = array())
    {
        exit(Zend_Json::encode(array('code' => $code, 'msg' => $msg, 'data' => $data)));
    }
}
?>