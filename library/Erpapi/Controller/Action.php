<?php
//安卓api
class Erpapi_Controller_Action extends Zend_Controller_Action
{	
	function init()
	{
		if(!defined('CURRENT_MODULE_NAME'))throw new Exception("CURRENT_MODULE_NAME not defined");
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		
		$this->initView();
		$this->view->seed_BaseUrl = $this->_request->getBaseUrl();
                
		//获取访问控制列表ACL
		$fileM = new Seed_Model_Cache2File();
		$setting = $fileM->get($mod_name."_setting");
		
		///这里设置默认的微信参数，若后台没设置的话，就使用以下默认的 2013-11-23 15:21:20
		if(isset($setting['mobile_send_ecode']) && isset($setting['mobile_send_username']) && isset($setting['mobile_send_password'])){
			define('MOBILE_SEND_ECODE',$setting['mobile_send_ecode']);
			define('MOBILE_SEND_USERNAME',$setting['mobile_send_username']);
			define('MOBILE_SEND_PASSWORD',$setting['mobile_send_password']);
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
				if ($this->_request->isPost()){
//					exit('权限不够，访问受限！');
                                        $this->outputData(1,'权限不够，访问受限！', $this->view->data_format);
                                }else{
					throw new Exception('权限不够，访问受限！');
                                }
				exit;
			}
		}
                
        //全局参数
        $this->view->df = $this->getRequest()->getParam('df');//数据格式

        //验证身份
        $erpapi = trim(strip_tags($this->getRequest()->getParam('erpapi')));
        $erpapi_key = isset($this->view->seed_Setting['erpapi_key']) ? $this->view->seed_Setting['erpapi_key'] : '';
        if(empty($erpapi) || empty($erpapi_key) || $erpapi != $erpapi_key){
            exit('params error');
        }
        $perpage = intval($this->getRequest()->getParam('perpage'));
        $this->view->perpage = $perpage > 0 ? $perpage : 20;
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
        
        
        /*
         * 输入数据
         */
        protected function outputData($error = 1,$res = array(),$data_format = '') {
            header("Content-Type:text/html;charset=utf-8;");
            $data_format = $data_format ? $data_format : $this->view->df ? $this->view->df : 'json';
            $result = array();
            $result['err'] = intval($error);
            $result['res'] = $res;
            switch ($data_format) {
                case 'json':
                case '':
                        echo Zend_Json::encode($result);
                    break;
                case 'serialize':
                        echo serialize($result);
                    break;
                case 'debug':
                default:
                        var_dump($result);
                    break;
            }
            exit;
        }
        
        /*
         * 输入数据
         */
        protected function directOutputData($res = array(),$data_format = '') {
            header("Content-Type:text/html;charset=utf-8;");
            $data_format = $data_format ? $data_format : $this->view->df ? $this->view->df : 'json';
            switch ($data_format) {
                case 'json':
                case '':
                        echo Zend_Json::encode($res);
                    break;
                case 'serialize':
                        echo serialize($res);
                    break;
                case 'debug':
                default:
                        var_dump($res);
                    break;
            }
            exit;
        }
        
        protected function timestamp2date($format = 'YmdHis',$timestamp = null){
            $timestamp = is_numeric($timestamp) ? $timestamp : time();
            if($timestamp > 0){
                return date($format,$timestamp);
            }
            return '';
        }
}
?>