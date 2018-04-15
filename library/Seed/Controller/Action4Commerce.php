<?php
class Seed_Controller_Action4Commerce extends Zend_Controller_Action
{
	function init()
	{
		if(file_exists(SEED_LICENSE_ROOT."/init.php")){
			require(SEED_LICENSE_ROOT."/init.php");
		}else{
			exit("License File Not Found!");
		}
		
		if(!defined('CURRENT_MODULE_NAME') || !defined('CURRENT_MODULE_TYPE'))throw new Exception("CURRENT_MODULE_NAME or CURRENT_MODULE_TYPE not defined");
		$this->initView();
		$this->view->seed_BaseUrl = $this->_request->getBaseUrl();
		
		$token = $this->_request->getParam('token');
		$fromapi=false;
		if(empty($token)){
			$token = Seed_Auth::getInstance()->getIdentity();
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
			$user = $userM->fetchRow(array('user_id'=>$my['user_id'],'token'=>$my['token'],'is_admin'=>'1','is_actived'=>'1'));
			if($user['user_id']>0){
				$userRoleM = new Seed_Model_UserRole('system');
				$user['roles']=$userRoleM->fetchRows(null,array('user_id'=>$user['user_id']));
			}
			$user['token']=$token;
			$this->view->seed_User = $user;
		}

		$exceptiveArr=array();
		$exceptiveArr[]=CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE.".admin.login";
		$exceptiveArr[]=CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE.".vcode.index";
		
		
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		$controller_name = $this->_request->getControllerName();
		$action_name = $this->_request->getActionName();
		$current = $mod_name.".".$controller_name.".".$action_name;
		if ((!isset($this->view->seed_User['user_id']) || $this->view->seed_User['user_id']<1 || $this->view->seed_User['is_admin']!='1') && !in_array($current,$exceptiveArr)) {

                    if(isset($_SERVER['REQUEST_URI']) && rtrim($_SERVER['REQUEST_URI'],'/') == '/b'){
                        $this->_redirect('/admin/login');
                        exit;
                    }
                    Seed_Browser::redirect('还没有登录？',"/b/admin/login",3000,'top');
                    exit;
    	}
    	
		//获取访问控制列表ACL
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$aclfile = $mod_name."_".strtolower($seed_host_name)."_acls";
		}else{ 
			$aclfile = $mod_name."_acls";
		}
		
		$fileM = new Seed_Model_Cache2File();
		$acls = $fileM->get($aclfile);
		$this->view->seed_Acls = $acls;
		
		if(is_array($acls['resources']) && in_array($current,$acls['resources'])){
			$deny=true;
			$roles =$this->view->seed_User['roles'];
			foreach ($roles as $role){
				if($role['mod_name']=="center_admin"){
					if(isset($acls['acls'][$role['role_name']]) && is_array($acls['acls'][$role['role_name']]) && in_array($current,$acls['acls'][$role['role_name']]))$deny=false;
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
		
		if(isset($this->view->seed_User['user_id']) && $this->view->seed_User['user_id']>0){
			$ruleM = new Seed_Model_Rule('system');
			$check = $ruleM->fetchRow(array('mod_name'=>$mod_name,'res_name'=>$controller_name,'priv_name'=>$action_name,'is_log'=>'1'));
			if($check['rule_id']>0){
				$resourceM = new Seed_Model_Resource('system');
				$c_resource = $resourceM->fetchRow(array('mod_name'=>$mod_name,'res_name'=>$controller_name));
				
				$logM = new Seed_Model_Log('system');
				$logData = array();
				$logData['user_id']=$this->view->seed_User['user_id'];
				$logData['user_name']=$this->view->seed_User['user_name'];
				$logData['log_time']=time();
				$logData['log_url']=$current;
				$logData['log_desc']="[".$c_resource['res_desc']."]{".$check['priv_desc']."}";
				$logData['log_ip']=Seed_Browser::get_client_ip();
				$logData['log_data']=serialize(array('get'=>$this->_request->getParams(),'post'=>$_POST));
				$logM->insertRow($logData);
			}
		}
		
		//获取系统设置
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
		if(!isset($setting['static_app_server']))$setting['static_app_server']="http://".$_SERVER['HTTP_HOST']."/static";
		$this->view->seed_Setting=$setting;
        
        if( !empty($this->view->seed_User['user_id'])) {$this->menuList();}
	}
    
    function menuList()
	{
        $menu_id = intval($this->_request->getParam('menu_id'));
        $sub_id  = intval($this->_request->getParam('sub_id'));
        $menu_name = '';
        $sub_name = '';
        $indexBaseUrl = '';
        
        if( !$menu_id) {
            $menu_id = empty($_COOKIE['menu_id']) ? 0 : intval(Seed_Cookie::getCookie('menu_id'));
        } else {
            Seed_Cookie::setCookie('menu_id', $menu_id);
        }
        if( !$sub_id) {
            $sub_id = empty($_COOKIE['sub_id']) ? 0 : intval(Seed_Cookie::getCookie('sub_id'));
        } else {
            Seed_Cookie::setCookie('sub_id', $sub_id);
        }
        
		$fileM = new Seed_Model_Cache2File();
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$menufile = strtolower($seed_host_name)."_menus";
		}else{
			$menufile = "menus";
		}
		$mymenus = $fileM->get($menufile);
		$menus = $submenus = array();
		if(!is_array($mymenus)){
			$menuM = new Seed_Model_Menu('system');	
			$menus = $menuM->fetchRows(null,array('parent'=>0),'order_by ASC');
			$submenus = array();	
			if(!empty($menus)){
				foreach($menus as $key => $menu){
                    if($key == 0 && !$menu_id) {
                        $menu_id = $menu['menu_id'];
                        Seed_Cookie::setCookie('menu_id', $menu_id);
                    }
                    if($menu['menu_id'] == $menu_id) {$menu_name = $menu['menu_name'];}
					// 查询二级菜单
					$seconds=$menuM->fetchRows(null,array('parent'=>$menu["menu_id"]),'order_by ASC');
					if(!empty($seconds)){
						foreach($seconds as $k => $second){
							// 有第三级分类的才算二级菜单
							$thirds=$menuM->fetchRows(null,array('parent'=>$second["menu_id"]),'order_by ASC');
							if(!empty($thirds)){
								foreach($thirds as $tkey => $third){
                                    if($menu['menu_id'] == $menu_id) {
                                        $submenus[]=$third;
                                        if( !$sub_id) {
                                            $sub_id = $third['menu_id'];
                                            Seed_Cookie::setCookie('sub_id', $sub_id);
                                            $indexBaseUrl = $third["link_url"].(strpos($third["link_url"], '?')>0?'&':'?')."menu_id={$menu_id}&sub_id={$sub_id}";
                                        }
                                    }
                                    if($k == 0) {
                                        $menus[$key]['link_url'] = $third['link_url'];
                                    }
                                    if($third['menu_id'] == $sub_id) {$sub_name = $third['menu_name'];}
								}
							}
						}
					}
				}
			}
		}else{
			$roles =$this->view->seed_User['roles'];
			$tempMenus = array();
			foreach ($roles as $role){
				if($role['mod_name']==CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE){
					if(isset($mymenus[$role['role_name']]) && is_array($mymenus[$role['role_name']])){
						$tempMenus = array_merge($tempMenus,$mymenus[$role['role_name']]);
					}
				}
			}
			$menus = Seed_Common::arrayUnique($tempMenus,'menu_id');
            $submenus = array();	
			if(!empty($menus)){
				foreach($menus as $key => $menu){
                    if($key == 0 && !$menu_id) {
                        $menu_id = $menu['menu_id'];
                        Seed_Cookie::setCookie('menu_id', $menu_id);
                    }
                    if($menu['menu_id'] == $menu_id) {$menu_name = $menu['menu_name'];}
					// 查询二级菜单
					$seconds=$menu['second'];
					if(!empty($seconds)){
						foreach($seconds as $k => $second){
							// 有第三级分类的才算二级菜单
							$thirds=$second['third'];
							if(!empty($thirds)){
								foreach($thirds as $tkey => $third){
                                    if($menu['menu_id'] == $menu_id) {
                                        $submenus[]=$third;
                                        if( !$sub_id) {
                                            $sub_id = $third['menu_id'];
                                            Seed_Cookie::setCookie('sub_id', $sub_id);
                                            $indexBaseUrl = $third["link_url"].(strpos($third["link_url"], '?')>0?'&':'?')."menu_id={$menu_id}&sub_id={$sub_id}";
                                        }
                                    }
                                    if($k == 0) {
                                        $menus[$key]['link_url'] = $third['link_url'];
                                    }
                                    if($third['menu_id'] == $sub_id) {$sub_name = $third['menu_name'];}
								}
							}
						}
					}
				}
			}
		}
		$this->view->headmenus = $menus;
		$this->view->submenus = $submenus;
        $this->view->menu_id = $menu_id;
        $this->view->sub_id = $sub_id;
        $this->view->menu_name = $menu_name;
        $this->view->sub_name = $sub_name;
        $this->view->indexBaseUrl = $indexBaseUrl;
	}
}
?>