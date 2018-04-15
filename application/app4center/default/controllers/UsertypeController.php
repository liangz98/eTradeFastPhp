<?php
class UsertypeController extends Seed_Controller_Action4Admin 
{
	function indexAction()
	{
		$userTypeM = new Seed_Model_UserType('system');
		$userTypes = $userTypeM->fetchRows(null,array(),'order_by ASC');
		$this->view->usertypes = $userTypes;
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter();
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Zend_Filter();
				$f4->addFilter(new Seed_Filter_Float());
				$insertData = array();
				$insertData['type_name']=$f3->filter($this->_request->getPost('type_name'));
				$insertData['user_integral']=intval($this->_request->getPost('user_integral'));
				$insertData['main_discount']=$f4->filter($this->_request->getPost('main_discount'));
				$insertData['main_discount_type']=$f2->filter($this->_request->getPost('main_discount_type'));
				$insertData['order_by']=$f2->filter($this->_request->getPost('order_by'));
				if(empty($insertData['type_name'])){
					Seed_Browser::tip_show('类型名称不能为空！');
				}else{
					$userTypeM = new Seed_Model_UserType('system');
					if($userType_id = $userTypeM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/usertype/index");
		    		}else{
		    			Seed_Browser::tip_show('添加失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	}
	
	function updateAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter();
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Zend_Filter();
				$f4->addFilter(new Seed_Filter_Float());
				$type_id = (int)$this->_request->getPost('type_id');
				$updateData = array();
				$updateData['type_name']=$f3->filter($this->_request->getPost('type_name'));
				$updateData['user_integral']=intval($this->_request->getPost('user_integral'));
				$updateData['main_discount']=$f4->filter($this->_request->getPost('main_discount'));
				$updateData['main_discount_type']=$f2->filter($this->_request->getPost('main_discount_type'));
				$updateData['order_by']=$f2->filter($this->_request->getPost('order_by'));
				if(empty($updateData['type_name'])){
					Seed_Browser::tip_show('类型名称不能为空！');
				}else{
					$userTypeM = new Seed_Model_UserType('system');
					if($userType_id = $userTypeM->updateRow($updateData, array('type_id'=>$type_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/usertype/index");
		    		}else{
		    			Seed_Browser::tip_show('修改失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
		$type_id = (int)$this->_request->getParam('type_id');
		if($type_id==0){
			Seed_Browser::tip_show('找不到相关的数据!');
		}
		$userTypeM = new Seed_Model_UserType('system');
		$userType = $userTypeM->fetchRow(array('type_id'=>$type_id));
		$this->view->usertype = $userType;
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$type_ids = $this->_request->getPost('type_id');				
				if(count($type_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$userTypeM = new Seed_Model_UserType('system');
				foreach ($type_ids as $type_id){
					$type_id = (int)$type_id;
					if($type_id>0){
						$userTypeM->deleteRow(array('type_id'=>$type_id));
					}
				}
				
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/usertype/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$type_ids = $this->_request->getParam('type_ids');
	    if(empty($type_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$type_ids = explode(',',$type_ids);
		$mytypes_arr=array();
		foreach ($type_ids as $type_id)
		{
			$type_id = (int)$type_id;
			if($type_id>0)
					$mytypes_arr[]=$type_id;
		}
		if(count($mytypes_arr)>0){
			$userTypeM = new Seed_Model_UserType('system');
			$userTypes = $userTypeM->fetchRowsByIds('type_id',$mytypes_arr);
	   		$this->view->usertypes = $userTypes;
		}
	}
	
	function orderAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$f3 = new Zend_Filter_Alnum();
				$type_ids = $this->_request->getPost('type_ids');
				$order_bys = $this->_request->getPost('order_bys');
				$updateData=array();
				$userTypeM = new Seed_Model_UserType('system');
				if(is_array($type_ids)){
					foreach ($type_ids as $k=>$type_id){
						$type_id = $f3->filter($type_id);
						$updateData['order_by'] = $f3->filter($order_bys[$k]);
						$userTypeM->updateRow($updateData,array('type_id'=>$type_id));
					}
				}
				Seed_Browser::tip_show('排序成功！',$this->view->seed_BaseUrl.'/usertype/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}
	