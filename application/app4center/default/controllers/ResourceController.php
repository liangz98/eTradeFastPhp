<?php
class ResourceController extends Seed_Controller_Action4Admin
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
				$myrules[$rule['res_name']]['privileges'][]=array('priv_name'=>$rule['priv_name'],'priv_desc'=>$rule['priv_desc'],'rule_id'=>$rule['rule_id']);
			}
		}
		$this->view->rules = $myrules;
	}
	
	function addAction() {		
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
				
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['res_desc'] = $f1->filter($this->_request->getPost('res_desc'));
				
				$f2 = new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());
				$insertData['res_name'] = $f3->filter($this->_request->getPost('res_name'));
				$insertData['mod_name'] = $mod_name."_".$mod_type;	
				$is_log = $f2->filter($this->_request->getPost('is_log'));
				
				if (empty($insertData['mod_name'])){
					Seed_Browser::tip_show('关键数据为空！');
				}elseif (empty($insertData['res_name'])){
					Seed_Browser::tip_show('请输入资源名称！');
				}elseif (empty($insertData['res_desc'])){
					Seed_Browser::tip_show('请输入资源说明！');
				}else{
					$resourceM=new Seed_Model_Resource('system');
					$resourceM->deleteRow(array('mod_name'=>$insertData['mod_name'],'res_name'=>$insertData['res_name']));
					if(!$res_id = $resourceM->insertRow($insertData)){
						Seed_Browser::tip_show('添加失败！');
					}
					$ruleM=new Seed_Model_Rule('system');
					$ruleM->deleteRow(array('mod_name'=>$insertData['mod_name'],'res_name'=>$insertData['res_name']));
					$priv_names = $this->_request->getPost('priv_names');
					$priv_descs = $this->_request->getPost('priv_descs');
					$is_logs = $this->_request->getPost('is_logs');
					$is_secondary_logins = $this->_request->getPost('is_secondary_logins');
					if (is_array($priv_names)) {
						foreach ($priv_names as $k=>$priv_name){
							$priv_name = $f3->filter($priv_name);
							$priv_desc=$priv_descs[$k];
							$is_log=(isset($is_logs[$k]))?intval($is_logs[$k]):0;
							$is_secondary_login=(isset($is_secondary_logins[$k]))?intval($is_secondary_logins[$k]):0;
							
							if(!empty($priv_name) && !empty($priv_desc)){
								$ruleData=array();
								$ruleData['mod_name'] = $insertData['mod_name'];
								$ruleData['res_name'] = $insertData['res_name'];
								$ruleData['priv_name'] = $priv_name;
								$ruleData['priv_desc'] = $priv_desc;
								$ruleData['is_log'] = $is_log;
								$ruleData['is_secondary_login'] = $is_secondary_login;
								$ruleM->insertRow($ruleData);
							}
						}
					}
					Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/resource/index?mod_name='.$mod_name);
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		//GET VIEW
		$mod_type="admin";
		$mod_name=$this->_request->getParam('mod_name');
		if(empty($mod_name))$mod_name=CURRENT_MODULE_NAME;
		
		$moduleM = new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$mod_name,'mod_type'=>$mod_type));
		if($module['mod_id']<1){
			Seed_Browser::error("请先添加“".$mod_name."_".$mod_type."”模块！");
		}
		$this->view->mod_type = $mod_type;
		$this->view->mod_name = $mod_name;
		$this->view->module = $module;
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
				
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['res_desc'] = $f1->filter($this->_request->getPost('res_desc'));
				
				$f2 = new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());
				$insertData['res_name'] = $f3->filter($this->_request->getPost('res_name'));
				$insertData['mod_name'] = $mod_name."_".$mod_type;	
				$is_log = $f2->filter($this->_request->getPost('is_log'));
				
				if (empty($insertData['mod_name'])){
					Seed_Browser::tip_show('关键数据为空！');
				}elseif (empty($insertData['res_name'])){
					Seed_Browser::tip_show('请输入资源名称！');
				}elseif (empty($insertData['res_desc'])){
					Seed_Browser::tip_show('请输入资源说明！');
				}else{
					$resourceM=new Seed_Model_Resource('system');
					$resourceM->deleteRow(array('mod_name'=>$insertData['mod_name'],'res_name'=>$insertData['res_name']));
					if(!$res_id = $resourceM->insertRow($insertData)){
						Seed_Browser::tip_show('添加失败！');
					}
					$ruleM=new Seed_Model_Rule('system');
					$ruleM->deleteRow(array('mod_name'=>$insertData['mod_name'],'res_name'=>$insertData['res_name']));
					$priv_names = $this->_request->getPost('priv_names');
					$priv_descs = $this->_request->getPost('priv_descs');
					$is_logs = $this->_request->getPost('is_logs');
					$is_secondary_logins = $this->_request->getPost('is_secondary_logins');
					if (is_array($priv_names)) {
						foreach ($priv_names as $k=>$priv_name){
							$priv_name = $f3->filter($priv_name);
							$priv_desc=$priv_descs[$k];
							$is_log=(isset($is_logs[$k]))?intval($is_logs[$k]):0;
							$is_secondary_login=(isset($is_secondary_logins[$k]))?intval($is_secondary_logins[$k]):0;
							
							if(!empty($priv_name) && !empty($priv_desc)){
								$ruleData=array();
								$ruleData['mod_name'] = $insertData['mod_name'];
								$ruleData['res_name'] = $insertData['res_name'];
								$ruleData['priv_name'] = $priv_name;
								$ruleData['priv_desc'] = $priv_desc;
								$ruleData['is_log'] = $is_log;
								$ruleData['is_secondary_login'] = $is_secondary_login;
								$ruleM->insertRow($ruleData);
							}
						}
					}
					Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/resource/index?mod_name='.$mod_name);
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		//GET VIEW
		$mod_type="admin";
		$mod_name=$this->_request->getParam('mod_name');
		if(empty($mod_name))$mod_name=CURRENT_MODULE_NAME;
		$res_name=$this->_request->getParam('res_name');
		
		$moduleM = new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$mod_name,'mod_type'=>$mod_type));
		if($module['mod_id']<1){
			Seed_Browser::error("请先添加“".$mod_name."_".$mod_type."”模块！");
		}
		$this->view->mod_type = $mod_type;
		$this->view->mod_name = $mod_name;
		$this->view->module = $module;
		
		$resourceM=new Seed_Model_Resource('system');
		$resource = $resourceM->fetchRow(array('res_name'=>$res_name,'mod_name'=>$mod_name."_".$mod_type));
		if($resource['res_id']<1)Seed_Browser::error("参数错误！");
		$this->view->resource = $resource;
		
		$ruleM=new Seed_Model_Rule('system');
		$this->view->rules = $ruleM->fetchRows(null,array('res_name'=>$res_name,'mod_name'=>$mod_name."_".$mod_type));
	}

	function recruitAction() {		
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
				
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f2 = new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_Alnum());
				$insertData['res_name'] = $f3->filter($this->_request->getPost('res_name'));
				$insertData['is_log'] = intval($this->_request->getPost('is_log'));
				$insertData['is_secondary_login'] = intval($this->_request->getPost('is_secondary_login'));
				$insertData['mod_name'] = $mod_name."_".$mod_type;
				$insertData['priv_name'] = $f3->filter($this->_request->getPost('priv_name'));
				$insertData['priv_desc'] = $f1->filter($this->_request->getPost('priv_desc'));
				if (empty($insertData['mod_name'])){
					Seed_Browser::tip_show('请输入模块名称！');
				}elseif (empty($insertData['res_name'])){
					Seed_Browser::tip_show('请输入资源名称！');
				}elseif (empty($insertData['priv_name'])){
					Seed_Browser::tip_show('请输入权限名称！');
				}else{
					$ruleM=new Seed_Model_Rule('system');
					if($ruleM->insertRow($insertData)){
						Seed_Browser::tip_show('补充成功！',$this->view->seed_BaseUrl.'/resource/index?mod_name='.$mod_name);
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
				
				$f2 = new Zend_Filter( );
				$f2->addFilter(new Zend_Filter_Alnum());
				$f3 = new Zend_Filter_Int();
				$rule_ids = $this->_request->getPost('rule_ids');
				if(count($rule_ids)<1){
					Seed_Browser::tip_show('没有任何选择！');
				}else{
					$ruleM=new Seed_Model_Rule('system');
					foreach ($rule_ids as $rule_id){
						if($rule_id=$f3->filter($rule_id)){
							$ruleM->deleteRow(array('rule_id'=>$rule_id));
						}
					}
					$resourceM= new Seed_Model_Resource('system');
					$resources = $resourceM->fetchRows(null,array('mod_name'=>$mod_name."_".$mod_type));
					foreach ($resources as $resource){
						if($ruleM->fetchRowsCount(array('mod_name'=>$mod_name."_".$mod_type,'res_name'=>$resource['res_name']))==0)
							$resourceM->deleteRow(array('res_id'=>$resource['res_id']));
					}
					Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/resource/index?mod_name='.$mod_name);
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}