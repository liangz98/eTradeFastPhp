<?php
class Seed_Controller_Action4User extends Zend_Controller_Action
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
			$token = Seed_Cookie::getCookie('seed_Token');
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
		
		//获取访问控制列表ACL
		$fileM = new Seed_Model_Cache2File();
		
		//获取系统设置
		$setting = $fileM->get($mod_name."_setting");
		if(isset($setting['is_close']) && $setting['is_close']=='1'){
			$close_reason = (isset($setting['close_reason']))?$setting['close_reason']:"网站关闭！";
			if ($this->_request->isPost())
				exit($close_reason);
			else
				throw new Exception($close_reason);
			exit;
		}
		
		//禁止代理访问
		if(isset($setting['forbidden_proxys']) && !empty($setting['forbidden_proxys'])){
			if(isset($_SERVER['HTTP_PROXY_CONNECTION']) || isset($_SERVER['HTTP_VIA']) || isset($_SERVER['HTTP_USER_AGENT_VIA']) || isset($_SERVER['HTTP_X_FORWARDED_FOR']) || isset($_SERVER['HTTP_PROXY_CONNECTION'])) {
			    $deny_msg=(isset($setting['forbidden_proxys_reason']) && !empty($setting['forbidden_proxys_reason']))?$setting['forbidden_proxys_reason']:"禁止代理访问，访问受限！";
				if ($this->_request->isPost())
					exit($deny_msg);
				else
					throw new Exception($deny_msg);
				exit;
			}
		}
		
		//IP限制
		if(isset($setting['forbidden_ips']) && !empty($setting['forbidden_ips'])){
			$deny=false;
			$ipforbiddens = explode('<br />',nl2br($setting['forbidden_ips']));
			if(is_array($ipforbiddens)){
				foreach ($ipforbiddens as $k=>$v) { 
					$ipaddres = trim($v); 
					$ip = str_ireplace(".","\.",$ipaddres); 
					$ip = str_replace("*","[0-9]{1,3}",$ip); 
					$ipaddres = "/".$ip."/"; 
					$iptables[] = $ipaddres; 
				}
			}
			
			$ip=Seed_Browser::get_client_ip();
			$ip = trim($ip);
			if (is_array($iptables)) { 
				foreach($iptables as $value) {
					if (preg_match($value,$ip)) { 
						$deny = true; 
						break; 
					} 
				} 
			} 
			if($deny){
				$deny_msg=(isset($setting['forbidden_ips_reason']) && !empty($setting['forbidden_ips_reason']))?$setting['forbidden_ips_reason']:"IP限制，访问受限！";
				if ($this->_request->isPost())
					exit($deny_msg);
				else
					throw new Exception($deny_msg);
				exit;
			}
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
		if(!isset($setting['static_app_server']))$setting['static_app_server']="http://".$_SERVER['HTTP_HOST']."/static";
		///END
		
		$this->view->seed_Setting = $setting;
		
		$acls = $fileM->get($mod_name."_acls");
		
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		$controller_name = $this->_request->getControllerName();
		$action_name = $this->_request->getActionName();
		$current = $mod_name.".".$controller_name.".".$action_name;
		if(is_array($acls['resources']) && in_array($current,$acls['resources'])){
			$deny=true;
			$roles =$user['roles'];
			if(is_array($roles) && count($roles)>0){
				foreach ($roles as $role){
					if($role['mod_name']==$mod_name){
						if(isset($acls['acls'][$role['role_name']]) && is_array($acls['acls'][$role['role_name']]) && in_array($current,$acls['acls'][$role['role_name']]))$deny=false;
					}
				}
			}
			if($deny){
				if ($this->_request->isPost())
					exit('权限不够，访问受限！');
				else
					throw new Exception('权限不够，访问受限！');
				exit;
			}
		}
	}
}
?>