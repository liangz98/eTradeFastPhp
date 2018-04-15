<?php
class Stat_Controller_Action extends Zend_Controller_Action
{
    private $config;

	public function init()
	{
		$this->checkSystem();
		$this->initView();
		$this->initSetting();
		$this->initUser();
        $this->initConfig();
		$this->view->seed_BaseUrl = $this->_request->getBaseUrl();
		$this->view->cur_pos = $this->_request->getParam('controller');
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
		// 获取参数的TOKEN
		$token = $this->_request->getParam('token');
		// 是否来自API
		$fromapi=false;
		if(empty($token)){
			// 为空则在本地计算机获取保存的Token
			$token = Seed_Cookie::getCookie('seed_Token');
		}else{
			$fromapi=true;
		}
		$this->view->seed_Token = $token;
		if(!empty($token)){
			$my = Seed_Token::decode($token);
			if(!is_array($my)){
				if ($this->_request->isPost())
					exit('关键数据损坏！');
				else
					throw new Exception('关键数据损坏！');
				exit;
			}
			$userM = new Seed_Model_User('system');
			// 只查询激活用户
			$user = $userM->fetchRow(array('user_id'=>$my['user_id'],'token'=>$my['token'],'is_actived'=>'1'));
			if($user['user_id']>0){
				$userTypeM = new Seed_Model_UserType('system');
				$usertype = $userTypeM->fetchRow(array('type_id'=>$user['user_type']));
				if(isset($usertype['type_id'])){
					$user['user_type_name']=$usertype['type_name'];
					$user['user_type_integral']=$usertype['user_integral'];
					$user['main_discount']=$usertype['main_discount'];
					$user['main_discount_type']=$usertype['main_discount_type'];
					$user['accessory_discount']=$usertype['accessory_discount'];
					$user['accessory_discount_type']=$usertype['accessory_discount_type'];
				}else{
					$user['user_type_name']='普通会员';
					$user['user_type_integral']='';
					$user['main_discount']='';
					$user['main_discount_type']='';
					$user['accessory_discount']='';
					$user['accessory_discount_type']='';
				}
			}
		}else{
			$user=array();
		}
		$user['token']=$token;
		if(!isset($user['user_id']))$user['user_id']=0;
		if(!isset($user['user_name']))$user['user_name']="游客";
		if(!isset($user['nick_name']))$user['nick_name']="游客";
		$this->view->seed_User = $user;

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
		if(!isset($setting['website_domain']))$setting['website_domain']="/";
		if(!isset($setting['upload_app_server']))$setting['upload_app_server']="/uploadapi";
		if(!isset($setting['upload_view_server']))$setting['upload_view_server']="/upload_files";
		if(!isset($setting['upload_return_server']))$setting['upload_return_server']="";
		if(!isset($setting['www_app_server']))$setting['www_app_server']="/";
		if(!isset($setting['v_app_server']))$setting['v_app_server']="/";
		if(!isset($setting['wechat_app_server']))$setting['wechat_app_server']="/";
		if(!isset($setting['vuser_app_server']))$setting['vuser_app_server']="/vuser";
		if(!isset($setting['vmall_app_server']))$setting['vmall_app_server']="/vmall";
		if(!isset($setting['vhome_app_server']))$setting['vhome_app_server']="/vhome";
		if(!isset($setting['vservice_app_server']))$setting['vservice_app_server']="/vservice";
		if(!isset($setting['vmarketing_app_server']))$setting['vmarketing_app_server']="/vmarketing";
		$this->view->seed_Setting=$setting;


		/*$seed_vhome_tpl = isset($_GET['tpl'])?trim($_GET['tpl']):'';
		if(!empty($seed_vhome_tpl)){
			define('SEED_MALL_TPL',$seed_vhome_tpl);
		}else{
			if(defined('SEED_HOST_NAME')){
				$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
				$tplfile = SEED_CONF_ROOT."/".strtolower($seed_host_name)."_tpl.php";
				if(file_exists($tplfile)){
					require_once($tplfile);
				}else{
					//define('SEED_MALL_TPL','default');
				}
			}else{
				$tplfile = SEED_CONF_ROOT."/tpl.php";
				if(file_exists($tplfile)){
					require_once($tplfile);
				}else{
					define('SEED_MALL_TPL','default');
				}
			}
		}*/
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
				$insertData['integral_time'] = time();
				$insert_id = $userIntelgralM->insertRow($insertData);
				$affect_row = $userM->updateRow(array('user_integral'=>$user['user_integral']+$integral['integral_value']), array('user_id'=>$user_id));
				if($insert_id && $affect_row) return true;
			}
		}
		return false;
	}

    /**
     * 初始化配置信息
     */
    protected function initConfig()
    {
        if(is_object($this->view) && empty($this->view->stat_config)){
			try {
				$config_file = SEED_CONF_ROOT . '/stat.xml';
				$setting = new Zend_Config_Xml ($config_file);
				$stat_config = $setting->toArray();
                if(!empty($stat_config['spam_words'])) {
                    $stat_config['spam_words'] = explode(',', $stat_config['spam_words']);
                } else {
                    $stat_config['spam_words'] = array();
                }
                if(!empty($stat_config['ignored_ips'])) {
                    $stat_config['ignored_ips'] = explode(',', $stat_config['ignored_ips']);
                } else {
                    $stat_config['ignored_ips'] = array();
                }
                $this->stat_config = $stat_config;
			}catch (Exception $e){
				throw $e;
			}
		}
    }
}
?>