<?php
class Home_Controller_Action extends Zend_Controller_Action
{
	public function init()
	{
		if(file_exists(SEED_LICENSE_ROOT."/init.php")){
			require(SEED_LICENSE_ROOT."/init.php");
		}else{
			exit("License File Not Found!");
		}
		
		$this->checkSystem();
		$this->initView();
		$this->initSetting();
		$this->initUser();
		$this->view->seed_BaseUrl = $this->_request->getBaseUrl();
		$this->view->cur_pos = $this->_request->getParam('controller');
		$this->view->addHelperPath(SEED_LIB_ROOT . '/Home/View/Helper');
		$this->initHelperSetting();
        $this->initCommon();//运行公用的一些代码
	}
	
	/**
	 * 检查系统完整性
	 * @throws Exception
	 */
	protected function checkSystem()
	{
		if(!defined('CURRENT_MODULE_NAME') || !defined('CURRENT_MODULE_TYPE'))throw new Exception("CURRENT_MODULE_NAME or CURRENT_MODULE_TYPE not defined");
	}
	
	/**
	 * 初始化用户信息
	 * @throws Exception
	 */
	protected function initUser()
	{
                $token = Seed_Cookie::getCookie('seed_Token');
                $user_id = intval(Seed_Cookie::getCookie('seed_UserId'));
                $my = Seed_Token::decode($token);
                if(is_array($my) && isset($my['user_id']) && $user_id == $my['user_id']){
                    $userM = new Seed_Model_User('system');
                    $this->view->seed_User = $user = $userM->fetchRow(array('user_id'=>$my['user_id'],'token'=>$my['token'],'is_actived'=>'1'));
                    if($user){
                        //设置cookie
                        $expiretime = time() + $this->view->seed_Setting['cookie_expire'];
                        Seed_Cookie::setCookie('seed_Token',$token,$expiretime);
                        Seed_Cookie::setCookie('seed_UserId',$user['user_id'],$expiretime);
                        Seed_Cookie::setCookie('seed_UserName',$user['user_name'],$expiretime);
                        $integralM = new Seed_Model_UserIntegral('system');
                        $integralM->AddWechatSubscribeIntegral($user);
                    }else{
                        if($_COOKIE){
                            foreach ($_COOKIE as $cookieName => $cookieValue) {
                                $cookie_name = Seed_Cookie::parseCookieName($cookieName);
                                if($cookie_name == 'shareuserid'){
                                    continue;
                                }
                                Seed_Cookie::delete($cookie_name);//清除cookie
                            }
                        }
                    }
                }
	}
	
       /**
         * 运行公用的一些代码
         * 注意：如果在Common里添加新文件务必把Autoload.php删掉
         */
        protected function initCommon(){
            $common_dir = SEED_LIB_ROOT.DIRECTORY_SEPARATOR.'Common';
            if(is_dir($common_dir)){
                $Autoload = $common_dir.DIRECTORY_SEPARATOR.'Autoload.php';
                if(file_exists($Autoload)){
                    $load_files = include_once $Autoload;
                    foreach($load_files as $load_file){
                        include_once SEED_LIB_ROOT.$load_file;
                    }
                }else{//没有Autoload.php文件则生成
                    $files = Seed_Folder::read($common_dir,1);
                    $include_files = array();
                    foreach($files as $file){
                         if($file != 'Autoload.php' && substr($file,strrpos($file,'.')) == '.php'){
                             $include_files[$file] = $common_dir.DIRECTORY_SEPARATOR.$file;
                         }
                    }
                    if($include_files){
                        $data_files = array();
                        natsort($include_files);//自然排序
                        foreach ($include_files as $key=>$include_file) {
                            include_once $include_file;
                            $data_files[$key] = str_replace(SEED_LIB_ROOT, '', $include_file);
                        }
                        if($fp = @fopen($Autoload,"w")){//生成文件
                            flock($fp,LOCK_EX);
                            fwrite($fp, "<?php\r\nreturn ".var_export($data_files, true).';');
                            flock($fp,LOCK_UN);
                            fclose($fp);
                            chmod($Autoload,0666);
                        }
                    }
                }
            }
        }


        protected function initSetting()
	{
		// 获取系统设置
		$fileM = new Seed_Model_Cache2File();
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$cachefile = $mod_name."_".strtolower($seed_host_name)."_setting";
		}else{ 
			$cachefile = $mod_name."_setting";
		}
				
		$setting = $fileM->get($cachefile);
		if(isset($setting['mobile_send_ecode']) && isset($setting['mobile_send_username']) && isset($setting['mobile_send_password'])){
			if(!defined('MOBILE_SEND_ECODE'))define('MOBILE_SEND_ECODE',$setting['mobile_send_ecode']);
			if(!defined('MOBILE_SEND_USERNAME'))define('MOBILE_SEND_USERNAME',$setting['mobile_send_username']);
			if(!defined('MOBILE_SEND_PASSWORD'))define('MOBILE_SEND_PASSWORD',$setting['mobile_send_password']);
		}
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
		$this->view->seed_Setting=$setting;
		
		$seed_vhome_tpl = trim($_GET['tpl']);
		if(!empty($seed_vhome_tpl)){
			define('SEED_HOME_TPL',$seed_vhome_tpl);
		}else{
			if(defined('SEED_HOST_NAME')){
				$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
				$tplfile = SEED_CONF_ROOT."/".strtolower($seed_host_name)."_tpl.php";
				if(file_exists($tplfile)){
					require_once($tplfile);
				}else{ 
					define('SEED_HOME_TPL','default');
				}
			}else{
				$tplfile = SEED_CONF_ROOT."/tpl.php";
				if(file_exists($tplfile)){
					require_once($tplfile);
				}else{ 
					define('SEED_HOME_TPL','default');
				}
			}
		}
	}
	
	protected function initHelperSetting()
	{
		if(is_object($this->view) && empty($this->view->helper_Setting)){
			try {
				$config_file = SEED_CONF_ROOT . '/helper.xml';
				$setting = new Zend_Config_Xml ($config_file);
				$this->view->helper_Setting = $setting->toArray();
			}catch (Exception $e){
				throw $e;
			}
		}
	}
	
	/**
	 * 获取请求的URI
	 * @return Ambigous <NULL, unknown>
	 */
	protected function getRequestUri()
	{
		$requestUri = null;
		if(isset($_SERVER['HTTP_X_REWRITE_URL']) && !empty($_SERVER['HTTP_X_REWRITE_URL'])){
			$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
		}else{
			$requestUri = $_SERVER["REQUEST_URI"];
		}
		return $requestUri;
	}
	
	/**
	 * 判断当前操作的用户是否已登录
	 * @return boolean
	 */
	protected function isLoggedIn()
	{
		if($this->view->seed_User['user_id']>0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 判断用户行为是否存在，若存在则反正真，否则返回假
	 * @param string $action_mark 行为标识
	 * @param int $user_id 用户ID
	 * @return boolean
	 */
	protected function hasUserAction($action_mark, $user_id)
	{
		$userActionM = new Seed_Model_UserAction('system');
		$userAction = $userActionM->fetchRow(array('user_id'=>$user_id,'action_mark'=>$action_mark));
		if($userAction['action_id'] > 0)return true;
		return false;
	}

	/**
	 * 添加用户行为
	 * @param string $action_mark 行为标识
	 * @param int $user_id 用户ID
	 * @return boolean
	 */
	protected function addUserAction($action_mark, $user_id)
	{
		$userActionM = new Seed_Model_UserAction('system');
		$userActionData = array();
		$userActionData['user_id'] = $user_id;
		$userActionData['action_mark'] = $action_mark;
		$userAction_id = $userActionM->insertRow($userActionData);
		if($userAction_id)return true;
		return false;
	}
	
	/**
	 * 添加用户积分
	 * @param string $integral_name 积分名
	 * @param int $user_id 用户ID
	 * @return boolean 
	 */
	protected function addIntegral($integral_name, $user_id)
	{
		$userM = new Seed_Model_User('system');
		$integralM = new Seed_Model_Integral('system');
		$userIntelgralM = new Seed_Model_UserIntegral('system');
		$user = $userM->fetchRow(array('user_id'=>(int)$user_id));
		if($user['user_id'] > 0){
			$integral = $integralM->fetchRow(array('integral_name'=>$integral_name, 'is_actived'=>'1'));
			if($integral['integral_id'] > 0){
				$insertData = array();
				$insertData['user_id'] = $user_id;
				$insertData['integral_value'] = $integral['integral_value'];
				$insertData['integral_desc'] = $integral['integral_desc'];
				$insertData['add_time'] = time();
				$insert_id = $userIntelgralM->insertRow($insertData);
				$affect_row = $userM->updateRow(array('user_integral'=>$user['user_integral']+$integral['integral_value']), array('user_id'=>$user_id));
				if($insert_id && $affect_row) return true;
			}
		}
		return false;
	}
}
?>