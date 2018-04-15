<?php
class BranchregionController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$branchRegionM = new Home_Model_BranchRegion('home');
		
		$br_id = $this->_request->getParam('br_id');
		$this->view->regions = $branchRegionM->fetchRows(null,null,array('order_by ASC'));
		$this->view->regionoptions = $branchRegionM->getParentOption(0);
		if($br_id>0){
			$this->view->region = $branchRegionM->fetchRow(array('br_id'=>$br_id));
		}
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) { 
			try{
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				
				$insertData = array();
				$insertData['br_name']=$f3->filter($this->_request->getPost('br_name'));
				$insertData['order_by']=intval($this->_request->getPost('order_by'));
				$insertData['parent']=intval($this->_request->getPost('parent'));
				
				if(empty($insertData['br_name'])){
					Seed_Browser::tip_show('分区名称不能为空！');
				}else{
					$branchRegionM = new Home_Model_BranchRegion('home');
					$check = $branchRegionM->fetchRow(array('br_name'=>$insertData['br_name']));
					if($check['br_id']>0){
						Seed_Browser::tip_show('分区名称已经存在！');
					}
					if($br_id = $branchRegionM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/branchregion/index");
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
				$branchRegionM = new Home_Model_BranchRegion('home');
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$br_id=intval($this->_request->getPost('br_id'));
				$branch_region = $branchRegionM->fetchRow(array('br_id'=>$br_id));
				if($branch_region['br_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}
				
				$updateData = array();
				$updateData['br_name']=$f3->filter($this->_request->getPost('br_name'));
				$updateData['order_by']=intval($this->_request->getPost('order_by'));
				$updateData['parent']=intval($this->_request->getPost('parent'));
				
				if(empty($updateData['br_name'])){
					Seed_Browser::tip_show('分区名称不能为空！');
				}elseif($br_id==$updateData['parent']){
					Seed_Browser::tip_show('所属上层不能为自身！');
				}else{
					$check = $branchRegionM->fetchRow(array('br_name'=>$updateData['br_name']));
					if($check['br_id']>0 && $check['br_id']!=$br_id){
						Seed_Browser::tip_show('分区名称已经存在！');
					}
					if($branchRegionM->updateRow($updateData,array('br_id'=>$br_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/branchregion/index");
		    		}else{
		    			Seed_Browser::tip_show('修改失败！');
		    		}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	}
	
	function deleteAction() {
		if ($this->_request->isPost()){
			try{
				$br_id = intval($this->_request->getPost('br_id'));
				if($br_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}elseif($br_id==1){
					Seed_Browser::tip_show('该数据不能删除！');
				}else{
					$branchRegionM = new Home_Model_BranchRegion('home');
					if($branchRegionM->deleteRow(array('br_id'=>$br_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/branchregion/index');
					}else{
						Seed_Browser::tip_show('删除失败！');
					}
				}
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
}
	