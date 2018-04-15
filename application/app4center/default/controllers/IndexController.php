<?php
class IndexController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
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
			$mod_name = CURRENT_MODULE_NAME;
			$menuM = new Seed_Model_Menu('system');	
			if(is_array($this->view->seed_User['roles']) && count($this->view->seed_User['roles'])>0){
				foreach ($this->view->seed_User['roles'] as $r){
					$rows = $menuM->fetchMenuList(null,array('parent'=>0, 'role_name' => $r['role_name']));
					$menus = array_merge($menus,$rows);
				}
			}
			$menus = Seed_Common::arrayUnique($menus,'menu_id');
			$submenus = array();	
			if(!empty($menus)){
				foreach($menus as $menu){
					// 查询二级菜单
					$seconds=$menuM->fetchRows(null,array('parent'=>$menu["menu_id"]),'order_by ASC');
					if(!empty($seconds)){
						foreach($seconds as $second){
							// 有第三级分类的才算二级菜单
							$thirds=$menuM->fetchRows(null,array('parent'=>$second["menu_id"]),'order_by ASC');
							if(!empty($thirds)){
								foreach($thirds as $third){
									$second["third"][]=$third;
								}
								$menu["second"][]=$second;
							}
						}
					}
					$submenus[]=$menu;
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
			$menus = $submenus = Seed_Common::arrayUnique($tempMenus,'menu_id');
		}
		$this->view->menus = $menus;
		$this->view->submenus = $submenus;

        //增加版号的显示
        $versionfile = SEED_CONF_ROOT."/version.php";
        if(file_exists($versionfile)){
            require_once($versionfile);
        }
	}
	
	function welcomeAction()
	{
        //增加版号的显示
        $versionfile = SEED_CONF_ROOT."/version.php";
        if(file_exists($versionfile)){
            require_once($versionfile);
        }
	}
}