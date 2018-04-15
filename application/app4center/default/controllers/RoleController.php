<?php
class RoleController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		$roleM=new Seed_Model_Role('system');
		$role_id = $this->_request->getParam('role_id');
		$this->view->roles = $roleM->fetchRows(null,array('mod_name'=>$mod_name),array('order_by asc'));
		if($role_id>0){
			$this->view->role = $roleM->fetchRow(array('role_id'=>$role_id));
		}
	}
	
	function menuAction()
	{
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1= new Zend_Filter_Int();
				$role_id = $f1->filter($this->_request->getPost('role_id'));
				if($role_id<1){
					Seed_Browser::tip_show('关键数据为空！');
				}
				$insertData = array();
				$insertData['mod_name']=$mod_name;
				$insertData['role_id']=$role_id;
				$menu_ids = $this->_request->getPost('menu_ids');
				$roleMenuM=new Seed_Model_RoleMenu('system');
				$roleMenuM->deleteRow(array('role_id'=>$role_id));
				if(count($menu_ids)>0){
					foreach ($menu_ids as $menu_id){
						if($menu_id=$f1->filter($menu_id)){
							$insertData['menu_id']=$menu_id;
							$roleMenuM->insertRow($insertData);
						}
					}
				}
				
				// 更新父类菜单
				$menuM = new Seed_Model_Menu('system');
				$menus = $menuM->fetchRows(null,array('parent'=>0));
				foreach ($menus as $k=>$menu){
					$tempMenus = $menuM->fetchRows(null,array('parent'=>$menu['menu_id']));
					if(is_array($tempMenus) && !empty($tempMenus)){
						foreach($tempMenus as $tempMenu){
							$check = $roleMenuM->checkChildRow($role_id,$tempMenu['menu_id']);
							if($check['menu_id']>0){
								$tempRoleMenu = $roleMenuM->fetchRow(array('role_id'=>$role_id,'menu_id'=>$tempMenu['menu_id']));
								if(!($tempRoleMenu['menu_id']>0)){
									$insertData = array();
									$insertData['mod_name']=$mod_name;
									$insertData['role_id']=$role_id;
									$insertData['menu_id']=$tempMenu['menu_id'];
									$roleMenuM->insertRow($insertData);
								}
							}
						}
					}
					$check = $roleMenuM->checkChildRow($role_id,$menu['menu_id']);
					if($check['menu_id']>0){
						$tempRoleMenu = $roleMenuM->fetchRow(array('role_id'=>$role_id,'menu_id'=>$menu['menu_id']));
						if($tempRoleMenu['menu_id'] < 1){
							$insertData = array();
							$insertData['mod_name']=$mod_name;
							$insertData['role_id']=$role_id;
							$insertData['menu_id']=$menu['menu_id'];
							$roleMenuM->insertRow($insertData);
						}
					}
				}
				
				//生成缓存
				$rolemenus = $roleMenuM->fetchJoinRows(null,null,array('t3.parent ASC','t3.order_by ASC'),array('*',array('role_name'),array('menu_name','link_url','parent')));
				
				$mymenus=array();
				$submenus=array();
				foreach ($rolemenus as $k=>$rolemenu){
					if($rolemenu['parent']==0){
						// 检索二级
						$secondMenus = array();
						foreach ($rolemenus as $menu){
							if($rolemenu['menu_id'] == $menu['parent'] && $rolemenu['role_id'] == $menu['role_id']){
								// 检索三级
								foreach ($rolemenus as $menu2){
									if($menu['menu_id'] == $menu2['parent'] && $menu['role_id'] == $menu2['role_id']){
										$menu['third'][] = $menu2;
									}
								}
								$secondMenus[]=$menu;
							}
						}
						$mymenus[$rolemenu['role_name']][]=array('menu_id'=>$rolemenu['menu_id'],'menu_name'=>$rolemenu['menu_name'],'link_url'=>$rolemenu['link_url'],'parent'=>$rolemenu['parent'],'second'=>$secondMenus);
					}
				}
				$cacheM = new Seed_Model_Cache2File();
				if(defined('SEED_HOST_NAME')){
					$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
					$menufile = strtolower($seed_host_name)."_menus";
				}else{ 
					$menufile = "menus";
				}
		
				$cacheM->save($menufile,$mymenus);
				Seed_Browser::tip_show('角色菜单更新成功！',$this->view->seed_BaseUrl.'/role/menu?role_id='.$role_id);
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}	
			exit;
		}
		$role_id = $this->_request->getParam('role_id');
		$this->view->role_id = $role_id;
		$roleM=new Seed_Model_Role('system');
		$this->view->roles = $roleM->fetchRows(null,array('mod_name'=>$mod_name),'order_by ASC');
		
		if($role_id>0){
			$roleMenuM=new Seed_Model_RoleMenu('system');
			$rolemenus = $roleMenuM->fetchRows(null,array('role_id'=>$role_id));
			$mymenus =array();
			if(is_array($rolemenus)){
				foreach ($rolemenus as $rolemenu){
					$mymenus[]=$rolemenu['role_id']."-".$rolemenu['menu_id'];
				}
			}
		}
		
		$menuM = new Seed_Model_Menu('system');	
		$menus = $menuM->fetchRows(null,array('parent'=>0),'order_by ASC');

		foreach ($menus as $k=>$menu){
			$submenus = $menuM->fetchRows(null,array('parent'=>$menu['menu_id']),array('order_by ASC'));

			// 若存在子分类
			if(count($submenus)>0){
				foreach ($submenus as $n=>$submenu){
					$permit=(in_array($role_id."-".$submenu['menu_id'],$mymenus))?'1':'0';
					$submenus[$n]['permit']=$permit;
					// 三级菜单
					$thirdMenus = $menuM->fetchRows(null,array('parent'=>$submenu['menu_id']),array('order_by ASC'));
					if(count($thirdMenus)>0){
						foreach ($thirdMenus as $m=>$thirdMenu){
							$permit=(in_array($role_id."-".$thirdMenu['menu_id'],$mymenus))?'1':'0';
							$thirdMenus[$m]['permit']=$permit;
							// 四级菜单
							$fourMenus = $menuM->fetchRows(null,array('parent'=>$thirdMenu['menu_id']),array('order_by ASC'));
							if(count($fourMenus)>0){
								foreach ($fourMenus as $mm=>$fourMenu){
									$permit=(in_array($role_id."-".$fourMenu['menu_id'],$mymenus))?'1':'0';
									$fourMenus[$mm]['permit']=$permit;
								}
							}
							$thirdMenus[$m]['fourmenus'] = $fourMenus;
						}
					}
					$submenus[$n]['thirdmenus'] = $thirdMenus;
				}
			}
			
			$permit=(in_array($role_id."-".$menu['menu_id'],$mymenus))?'1':'0';
			$menus[$k]['permit']=$permit;
			$menus[$k]['submenus'] = $submenus;
		}
		$this->view->menus = $menus;
	}
	
	function ajaxAction()
	{
		$mod_name = $this->_request->getParam('mod_name');
		$roleM=new Seed_Model_Role('system');
		$roles = $roleM->fetchRows(null,array('mod_name'=>$mod_name));
		$json_string = json_encode($roles);
		echo $json_string;
		exit;
	}
	
	function addAction() {		
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['role_desc'] = $f1->filter($this->_request->getPost('role_desc'));
				
				$f2 = new Zend_Filter_Int();
				$insertData['order_by'] = $f2->filter($this->_request->getPost('order_by'));	
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());
				$insertData['mod_name'] = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;	
				$insertData['role_name'] = $f3->filter($this->_request->getPost('role_name'));	
				
				if (empty($insertData['mod_name'])){
					Seed_Browser::tip_show('请输入模块名称！');
				}elseif (empty($insertData['role_name'])){
					Seed_Browser::tip_show('请输入角色名称！');
				}elseif (empty($insertData['role_desc'])){
					Seed_Browser::tip_show('请输入模块说明！');
				}else{
					$roleM=new Seed_Model_Role('system');
					if($role_id = $roleM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/role/index');
					}else{
						Seed_Browser::tip_show('添加失败！');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
	
	function updateAction() {		
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$updateData['role_desc'] = $f1->filter($this->_request->getPost('role_desc'));
				
				$f2 = new Zend_Filter_Int();
				$updateData['order_by'] = $f2->filter($this->_request->getPost('order_by'));
				$role_id = $f2->filter($this->_request->getPost('role_id'));	
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());	
				$updateData['role_name'] = $f3->filter($this->_request->getPost('role_name'));	
				$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;	
				
				if ($role_id<1){
					Seed_Browser::tip_show('关键数据为空！');
				}elseif (empty($updateData['role_name'])){
					Seed_Browser::tip_show('请输入角色名称！');
				}elseif (empty($updateData['role_desc'])){
					Seed_Browser::tip_show('请输入模块说明！');
				}else{
					$roleM=new Seed_Model_Role('system');
					if($roleM->updateRow($updateData,array('role_id'=>$role_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/role/index');
					}else{
						Seed_Browser::tip_show('修改失败！');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$role_ids = $this->_request->getPost('role_id');				
				if(count($role_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$f2 = new Zend_Filter( );
				$f2->addFilter(new Zend_Filter_Alnum());	
				$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;	
				$roleM=new Seed_Model_Role('system');
				$aclM = new Seed_Model_Acl('system');
				$roleMenuM=new Seed_Model_RoleMenu('system');
				$userRoleM=new Seed_Model_UserRole('system');
				foreach ($role_ids as $role_id){
					$role_id = $f3->filter($role_id);
					if($role_id>0){
						$oldRoleDetail=$roleM->fetchRow(array('role_id'=>$role_id));
						if($roleM->deleteRow(array('role_id'=>$role_id))){							
							$aclM->deleteRow(array('role_id'=>$role_id));							
							$roleMenuM->deleteRow(array('role_id'=>$role_id));							
							$userRoleM->deleteRow(array('mod_name'=>$mod_name,'role_name'=>$oldRoleDetail['role_name']));
						}
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/role/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$role_ids = $this->_request->getParam('role_ids');
	    if(empty($role_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$role_ids = explode(',',$role_ids);
		$f3 = new Zend_Filter_Int();
		$myroles_arr=array();
		foreach ($role_ids as $role_id)
		{
			$role_id = $f3->filter($role_id);
			if($role_id>0)
					$myroles_arr[]=$role_id;
		}
		if(count($myroles_arr)>0){
			$roleM = new Seed_Model_Role('system');
			$roles = $roleM->fetchRowsByIds('role_id',$myroles_arr);
	   		$this->view->roles = $roles;
		}
	}
        
        function detailAction(){
            $role_id = $this->_request->getParam('role_id');
            if(empty($role_id)){
                Seed_Browser::error('找不到相关的数据!');
            }
            $userM = new Seed_Model_User('system');
            $userRoleM = new Seed_Model_UserRole('system');
            $roleM = new Seed_Model_Role('system');
            $conditions = array();
            $conditions['role_id'] = $role_id;
            $role = $roleM->fetchRow($conditions);
            if($role){
                $perpage=15;
                $page=intval($this->_request->getParam('page'));
                $total = $userRoleM->fetchRowsCount(array('mod_name'=>$role['mod_name'],'role_name'=>$role['role_name']));
                $pageObj = new Seed_Page($this->_request,$total,$perpage);
                $this->view->page = $pageObj->getPageArray();
                        if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
                if($page<1)$page=1;
               $userRoles = $userRoleM->fetchRows(array(($page-1)*$perpage,$perpage),array('mod_name'=>$role['mod_name'],'role_name'=>$role['role_name']));               	
               foreach($userRoles as $k=>$userRole){
                   $user = $userM->fetchRow(array('user_id'=>$userRole['user_id']));
                   $userRoles[$k]['nick_name'] = $user['nick_name'];
                   $userRoles[$k]['user_name'] = $user['user_name'];
                   $userRoles[$k]['is_admin'] = $user['is_admin'];
                   $userRoles[$k]['is_actived'] = $user['is_actived'];
                   $userRoles[$k]['user_email'] = $user['user_email'];
               }
               $this->view->userRoles = $userRoles;
            }
            
            
        }
}