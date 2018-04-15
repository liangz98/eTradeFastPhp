<?php
class ModuleController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$moduleM=new Seed_Model_Module('system');
		$mod_id = $this->_request->getParam('mod_id');
		$this->view->modules = $moduleM->fetchRows(null,null,'mod_name ASC');
		if($mod_id>0){
			$this->view->module = $moduleM->fetchRow(array('mod_id'=>$mod_id));
		}
	}
	
	function addAction() {		
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$insertData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$insertData['mod_desc'] = $f1->filter($this->_request->getPost('mod_desc'));
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Seed_Filter_Alnum());
				$insertData['mod_name'] = $f3->filter($this->_request->getPost('mod_name'));
				$insertData['mod_type'] = $f3->filter($this->_request->getPost('mod_type'));	
				
				if (empty($insertData['mod_name'])){
					Seed_Browser::tip_show('请输入模块名称！');
				}elseif (empty($insertData['mod_type'])){
					Seed_Browser::tip_show('请输入模块类型！');
				}elseif (empty($insertData['mod_desc'])){
					Seed_Browser::tip_show('请输入模块说明！');
				}else{
					$moduleM=new Seed_Model_Module('system');
					if($mod_id = $moduleM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/module/index');
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
				$updateData=array();
				$f1 = new Zend_Filter( );
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$updateData['mod_desc'] = $f1->filter($this->_request->getPost('mod_desc'));
				
				$f2 = new Zend_Filter_Int();
				$mod_id = $f2->filter($this->_request->getPost('mod_id'));
				
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Seed_Filter_Alnum());
				$updateData['mod_name'] = $f3->filter($this->_request->getPost('mod_name'));
				$updateData['mod_type'] = $f3->filter($this->_request->getPost('mod_type'));
				
				if($mod_id<1){
					Seed_Browser::tip_show('关键参数错误');
				}elseif (empty($updateData['mod_name'])){
					Seed_Browser::tip_show('请输入模块名称！');
				}elseif (empty($updateData['mod_type'])){
					Seed_Browser::tip_show('请输入模块类型！');
				}elseif (empty($updateData['mod_desc'])){
					Seed_Browser::tip_show('请输入模块说明！');
				}else{
					$moduleM=new Seed_Model_Module('system');
					if($moduleM->updateRow($updateData,array('mod_id'=>$mod_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/module/index');
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
				$mod_ids = $this->_request->getPost('mod_id');
				if(count($mod_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				$moduleM=new Seed_Model_Module('system');
				foreach ($mod_ids as $mod_id){
					$mod_id = $f3->filter($mod_id);
					if($mod_id>0){
						$moduleM->deleteRow(array('mod_id'=>$mod_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/module/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$mod_ids = $this->_request->getParam('mod_ids');
	    if(empty($mod_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$mod_ids = explode(',',$mod_ids);
		$f3 = new Zend_Filter_Int();
		$mymods_arr=array();
		foreach ($mod_ids as $mod_id)
		{
			$mod_id = $f3->filter($mod_id);
			if($mod_id>0)
					$mymods_arr[]=$mod_id;
		}
		if(count($mymods_arr)>0){
			$moduleM = new Seed_Model_Module('system');
			$modules = $moduleM->fetchRowsByIds('mod_id',$mymods_arr);
	   		$this->view->modules = $modules;
		}
	}
}