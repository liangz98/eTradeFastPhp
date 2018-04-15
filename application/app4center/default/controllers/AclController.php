<?php
class AclController extends Seed_Controller_Action4Admin 
{
	function indexAction()
	{
		$mod_type="admin";
		$mod_name=$this->_request->getParam('mod_name');
		if(empty($mod_name))$mod_name=CURRENT_MODULE_NAME;

		$moduleM = new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$mod_name,'mod_type'=>$mod_type));
		if($module['mod_id']<1){
			Seed_Browser::error("请先添加“".$mod_name."_".$mod_type."”模块！");
		}
		$modules = $moduleM->fetchRows(null,array('mod_type'=>'admin'));
		$this->view->modules = $modules;
		$this->view->mod_type = $mod_type;
		$this->view->mod_name = $mod_name;
		$this->view->module = $module;

		$f1 = new Zend_Filter_Int();
		$role_id = $f1->filter($this->_request->getParam('role_id'));
		$roleM=new Seed_Model_Role('system');
		$role = $roleM->fetchRow(array('role_id'=>$role_id,'mod_name'=>CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE));
		if($role['role_id']<1){
			Seed_Browser::error("角色参数错误！");
		}
		$this->view->role = $role;

		$this->view->role_id = $role_id;
		$acls=array();
		if($role_id>0){
			$aclM=new Seed_Model_Acl('system');
			$acls = $aclM->fetchRows(null,array('role_id'=>$role_id));
			$this->view->acls = $acls;
		}
		$myacls =array();
		if(is_array($acls)){
			foreach ($acls as $acl){
				$myacls[]=$acl['role_id']."-".$acl['rule_id'];
			}
		}

		$resourceM=new Seed_Model_Resource('system');
		$this->view->resources = $resourceM->fetchRows(null,array('mod_name'=>$mod_name."_".$mod_type));
		$ruleM = new Seed_Model_Rule('system');
		$rules = $ruleM->fetchJoinRows(null,array('mod_name'=>$mod_name."_".$mod_type));

		$myrules = array();
		if (is_array($rules)) {
			foreach ($rules as $rule){
				if(!isset($myrules[$rule['res_name']])){
					$myrules[$rule['res_name']]=array();
					$myrules[$rule['res_name']]['res_name']=$rule['res_name'];
					$myrules[$rule['res_name']]['res_desc']=$rule['res_desc'];
				}
				$permit=(in_array($role_id."-".$rule['rule_id'],$myacls))?'1':'0';
				$myrules[$rule['res_name']]['privileges'][]=array('priv_name'=>$rule['priv_name'],'priv_desc'=>$rule['priv_desc'],'rule_id'=>$rule['rule_id'],'permit'=>$permit);
			}
		}

		$this->view->rules = $myrules;
	}


	function updateAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$mod_type="admin";
				$mod_name=$this->_request->getPost('mod_name');
				if(empty($mod_name)){
					Seed_Browser::tip_show("参数错误！");
				}
				$moduleM = new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$mod_name,'mod_type'=>$mod_type));
				if($module['mod_id']<1){
					Seed_Browser::tip_show("请先添加“".$mod_name."_".$mod_type."”模块！");
				}
				
				$addData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				
				$f2=new Zend_Filter_Int();
				$role_id = $f2->filter($this->_request->getPost('role_id'));
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());
				
				if ($role_id<1){
					Seed_Browser::tip_show('关键数据为空！');
				}
				$aclM = new Seed_Model_Acl('system');
				$aclM->deleteRow(array('role_id'=>$role_id,'mod_name'=>$mod_name."_".$mod_type));
				
				$rule_ids = $this->_request->getPost('rule_ids');
				if(is_array($rule_ids)){
					$ruleM = new Seed_Model_Rule('system');
					$roleM = new Seed_Model_Role('system');
					$role = $roleM->fetchRow(array('role_id'=>$role_id));
					foreach ($rule_ids as $rule_id){
						$rule=$ruleM->fetchRow(array('rule_id'=>$rule_id));
						$addData=array();
						$addData['role_id']=$role_id;
						$addData['rule_id']=$rule['rule_id'];
						$addData['role_name']=$role['role_name'];
						$addData['mod_name']=$rule['mod_name'];
						$addData['res_name']=$rule['res_name'];
						$addData['priv_name']=$rule['priv_name'];
						$aclM->insertRow($addData);
					}
				}
				
				$modules = $moduleM->fetchRows(null,array('mod_type'=>'admin'));
				foreach ($modules as $module){
					$mod_name = $module['mod_name']."_".$module['mod_type'];
					$aclM = new Seed_Model_Acl('system');
					$acls = $aclM->fetchRows(null,array('mod_name'=>$mod_name));
					$resources =array();
					$myacls = array();
					if(is_array($acls)){
						foreach ($acls as $acl){
							if(!in_array($acl['mod_name'].".".$acl['res_name'].".".$acl['priv_name'],$resources))$resources[]=$acl['mod_name'].".".$acl['res_name'].".".$acl['priv_name'];
							if(!isset($myacls[$acl['role_name']]))$myacls[$acl['role_name']]=array();
							$myacls[$acl['role_name']][]=$acl['mod_name'].".".$acl['res_name'].".".$acl['priv_name'];
						}
					}
					$data=array('resources'=>$resources,'acls'=>$myacls);
					
					if(defined('SEED_HOST_NAME')){
						$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
						$aclfile = $mod_name."_".strtolower($seed_host_name)."_acls";
					}else{ 
						$aclfile = $mod_name."_acls";
					}
				
					$cacheM = new Seed_Model_Cache2File();
					$cacheM->save($aclfile,$data);
				}
				
				Seed_Browser::tip_show('更新成功！',$this->view->seed_BaseUrl.'/acl/index?role_id='.$role_id);
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}