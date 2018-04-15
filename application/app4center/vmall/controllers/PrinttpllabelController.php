<?php
class PrinttpllabelController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$ptcl_id = $this->_request->getParam('ptcl_id');
		$ptclM = new Shop_Model_PrintTplLabel('shop');
		$this->view->labels = $ptclM->fetchRows();
		if($ptcl_id>0){
			$this->view->label = $ptclM->fetchRow(array('ptcl_id'=>$ptcl_id));
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
				$insertData['ptcl_desc'] = $f1->filter($this->_request->getPost('ptcl_desc'));				
				$insertData['ptcl_name'] = $f1->filter($this->_request->getPost('ptcl_name'));
				if (empty($insertData['ptcl_name'])){
					Seed_Browser::tip_show('请输入标签名！');
				}else{
					$ptclM = new Shop_Model_PrintTplLabel('shop');
					if($ptclM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/printtpllabel/');
					}else{
						Seed_Browser::tip_show('添加失败！',$this->view->seed_BaseUrl.'/printtpllabel/');
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
				$f1 = new Zend_Filter();
				$f1->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f2 = new Zend_Filter_Int();
				$updateData['ptcl_desc'] = $f1->filter($this->_request->getPost('ptcl_desc'));				
				$updateData['ptcl_name'] = $f1->filter($this->_request->getPost('ptcl_name'));
				$ptcl_id = $f2->filter($this->_request->getPost('ptcl_id'));
				
				if ($ptcl_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}elseif (empty($updateData['ptcl_name'])){
					Seed_Browser::tip_show('请输入标签名！');
				}else{
					$ptclM = new Shop_Model_PrintTplLabel('shop');
					if($ptclM->updateRow($updateData,array('ptcl_id'=>$ptcl_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/printtpllabel/');
					}else{
						Seed_Browser::tip_show('修改失败！',$this->view->seed_BaseUrl.'/printtpllabel/');
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
				$f3 = new Zend_Filter_Alnum();
				$ptcl_id = $f3->filter($this->_request->getPost('ptcl_id'));
				if($ptcl_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}else{
					$ptclM = new Shop_Model_PrintTplLabel('shop');
					if($ptclM->deleteRow(array('ptcl_id'=>$ptcl_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/printtpllabel/');
					}else{
						Seed_Browser::tip_show('删除失败！',$this->view->seed_BaseUrl.'/printtpllabel/');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}