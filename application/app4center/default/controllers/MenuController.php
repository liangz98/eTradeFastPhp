<?php
class MenuController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$menuM=new Seed_Model_Menu('system');
		$menu_id = $this->_request->getParam('menu_id');
		$this->view->menus = $menuM->fetchRows(null,null,array('order_by ASC'));
		$this->view->menuoptions = $menuM->getParentOption(0);
		if($menu_id>0){
			$this->view->menu = $menuM->fetchRow(array('menu_id'=>$menu_id));
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
				$insertData['menu_name'] = $f1->filter($this->_request->getPost('menu_name'));
				$insertData['menu_lang'] = $f1->filter($this->_request->getPost('menu_lang'));
				$insertData['menu_blank'] = $f1->filter($this->_request->getPost('menu_blank'));
				$insertData['link_url'] = $f1->filter($this->_request->getPost('link_url'));
				
				$f3 = new Zend_Filter_Int();
				$insertData['parent'] = $f3->filter($this->_request->getPost('parent'));				
				$insertData['order_by'] = $f3->filter($this->_request->getPost('order_by'));
				
				if (empty($insertData['menu_name'])){
					Seed_Browser::tip_show('请输入菜单名称！');
				}elseif (empty($insertData['link_url'])){
					Seed_Browser::tip_show('请输入链接地址！');
				}else{
					$menuM=new Seed_Model_Menu('system');
					if($menu_id = $menuM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/menu/index?menu_id='.$menu_id);
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
                $updateData['menu_name'] = $f1->filter($this->_request->getPost('menu_name'));
                $updateData['menu_lang'] = $f1->filter($this->_request->getPost('menu_lang'));
                $updateData['menu_blank'] = $f1->filter($this->_request->getPost('menu_blank'));
                $updateData['link_url'] = $f1->filter($this->_request->getPost('link_url'));
				
				$f3 = new Zend_Filter_Int();
				$updateData['parent'] = $f3->filter($this->_request->getPost('parent'));				
				$updateData['order_by'] = $f3->filter($this->_request->getPost('order_by'));
				$menu_id = $f3->filter($this->_request->getPost('menu_id'));
				
				if($menu_id<1){
					Seed_Browser::tip_show('关键参数错误');
				}elseif (empty($updateData['menu_name'])){
					Seed_Browser::tip_show('请输入菜单名称！');
				}elseif (empty($updateData['link_url'])){
					Seed_Browser::tip_show('请输入链接地址！');
				}elseif($menu_id==$updateData['parent']){
					Seed_Browser::tip_show('所属上层不能为自身！');
				}else{
					$menuM=new Seed_Model_Menu('system');
					if($menuM->updateRow($updateData,array('menu_id'=>$menu_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/menu/index?menu_id='.$menu_id);
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
				$menu_id = $f3->filter($this->_request->getPost('menu_id'));
				if($menu_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}else{
					$menuM=new Seed_Model_Menu('system');
					if($menuM->deleteRow(array('menu_id'=>$menu_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/menu/index');
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