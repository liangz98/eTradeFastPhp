<?php
class Commerce_Controller_Action4CMUpload extends Zend_Controller_Action
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
				$return = array(
                    'errorCode' => 'seed.user.nologin',
                    'success' => false
                );
                exit(Zend_Json::encode($return));
			}
			$user = $userM->fetchRow(array('user_id'=>$my['user_id'],'token'=>$my['token'],'is_actived'=>'1'));
			if($user['user_id']>0){
				$userRoleM = new Seed_Model_UserRole('system');
				$user['roles']=$userRoleM->fetchRows(null,array('user_id'=>$user['user_id']));
			}
			$user['token']=$token;
			$this->view->seed_User = $user;
		} else {
            $return = array(
                'errorCode' => 'seed.user.nologin',
                'success' => false
            );
            exit(Zend_Json::encode($return));
        }
		
		//获取访问控制列表ACL
		$fileM = new Seed_Model_Cache2File();
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$cachefile = $mod_name."_".strtolower($seed_host_name)."_setting";
		}else{ 
			$cachefile = $mod_name."_setting";
		}
				
		$setting = $fileM->get($cachefile);
		$this->view->seed_Setting = $setting;
	}
}
?>