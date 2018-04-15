<?php
class ModuleapiController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$mod_name = $this->_request->getParam('mod_name');
		$mod_name_a = explode("_",$mod_name);
		$api_id = $this->_request->getParam('api_id');
		if(empty($mod_name))Seed_Browser::error('请先选择模块！');
		$moduleM=new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$mod_name_a[0],'mod_type'=>$mod_name_a[1]));
		if($module['mod_id']<1)Seed_Browser::error('没有找到相关模块！');
		$this->view->module = $module;
		$moduleapiM=new Seed_Model_ModuleApi('system');
		$this->view->apis = $moduleapiM->fetchRows(null,array('mod_name'=>$mod_name_a[0],'mod_type'=>$mod_name_a[1]),'api_name ASC');
		if($api_id>0){
			$this->view->api = $moduleapiM->fetchRow(array('api_id'=>$api_id));
		}
	}
	
	function addAction() {		
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$mod_name = $this->_request->getPost('mod_name');
				$mod_name_a = explode("_",$mod_name);
				$moduleM=new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$mod_name_a[0],'mod_type'=>$mod_name_a[1]));
				if($module['mod_id']<1)Seed_Browser::tip_show('没有找到相关模块！');
				
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['api_desc'] = $f1->filter($this->_request->getPost('api_desc'));				
				$insertData['api_url'] = $f1->filter($this->_request->getPost('api_url'));
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Seed_Filter_Alnum());
				$insertData['mod_name'] = $module['mod_name'];
				$insertData['mod_type'] = $module['mod_type'];
				$insertData['api_name'] = $f3->filter($this->_request->getPost('api_name'));
				
				
				if (empty($insertData['mod_name'])){
					Seed_Browser::tip_show('请输入模块名称！');
				}elseif (empty($insertData['api_name'])){
					Seed_Browser::tip_show('请输入接口名称！');
				}elseif (empty($insertData['api_desc'])){
					Seed_Browser::tip_show('请输入接口说明！');
				}elseif (empty($insertData['api_url'])){
					Seed_Browser::tip_show('请输入接口路径！');
				}else{
					$moduleapiM=new Seed_Model_ModuleApi('system');
					if($api_id = $moduleapiM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/moduleapi/index?mod_name='.$mod_name);
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
				$mod_name = $this->_request->getPost('mod_name');
				$mod_name_a = explode("_",$mod_name);
				$moduleM=new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$mod_name_a[0],'mod_type'=>$mod_name_a[1]));
				if($module['mod_id']<1)Seed_Browser::tip_show('没有找到相关模块！');
				
				$updateData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				
				$updateData['api_desc'] = $f1->filter($this->_request->getPost('api_desc'));				
				$updateData['api_url'] = $f1->filter($this->_request->getPost('api_url'));
				
				$f2 = new Zend_Filter_Int();
				$api_id = $f2->filter($this->_request->getPost('api_id'));
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Seed_Filter_Alnum());
				$updateData['api_name'] = $f3->filter($this->_request->getPost('api_name'));
				
				
				if($api_id<1){
					Seed_Browser::tip_show('关键参数错误');
				}elseif (empty($updateData['api_name'])){
					Seed_Browser::tip_show('请输入接口名称！');
				}elseif (empty($updateData['api_desc'])){
					Seed_Browser::tip_show('请输入接口说明！');
				}elseif (empty($updateData['api_url'])){
					Seed_Browser::tip_show('请输入接口路径！');
				}else{
					$moduleapiM=new Seed_Model_ModuleApi('system');
					if($moduleapiM->updateRow($updateData,array('api_id'=>$api_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/moduleapi/index?mod_name='.$mod_name);
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
				$f2 = new Zend_Filter( );
				$f2->addFilter(new Seed_Filter_Alnum());
				$mod_name = $f2->filter($this->_request->getPost('mod_name'));
				$mod_name_a = explode("_",$mod_name);
				$moduleM=new Seed_Model_Module('system');
				$module = $moduleM->fetchRow(array('mod_name'=>$mod_name_a[0],'mod_type'=>$mod_name_a[1]));
				if($module['mod_id']<1)Seed_Browser::tip_show('没有找到相关模块！');
				
				$f3 = new Zend_Filter_Int();
				$api_ids = $this->_request->getPost('api_id');
				if(count($api_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				$moduleapiM=new Seed_Model_ModuleApi('system');
				foreach ($api_ids as $api_id){
					$api_id = $f3->filter($api_id);
					if($api_id>0){
						$moduleapiM->deleteRow(array('api_id'=>$api_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/moduleapi/index?mod_name='.$mod_name);
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$mod_name = $this->_request->getParam('mod_name');
		$mod_name_a = explode("_",$mod_name);
		if(empty($mod_name))Seed_Browser::error('请先选择模块！');
		$moduleM=new Seed_Model_Module('system');
		$module = $moduleM->fetchRow(array('mod_name'=>$mod_name_a[0],'mod_type'=>$mod_name_a[1]));
		if($module['mod_id']<1)Seed_Browser::error('没有找到相关模块！');
		$this->view->module = $module;
		
		$api_ids = $this->_request->getParam('api_ids');
	    if(empty($api_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$api_ids = explode(',',$api_ids);
		$f3 = new Zend_Filter_Int();
		$myapis_arr=array();
		foreach ($api_ids as $api_id)
		{
			$api_id = $f3->filter($api_id);
			if($api_id>0)
					$myapis_arr[]=$api_id;
		}
		if(count($myapis_arr)>0){
			$moduleapiM = new Seed_Model_ModuleApi('system');
			$apis = $moduleapiM->fetchRowsByIds('api_id',$myapis_arr);
	   		$this->view->apis = $apis;
		}
	}
}