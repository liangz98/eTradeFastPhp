<?php
class ImagecatepathController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$catepathM=new Seed_Model_ImageCatePath('system');
		
		//查询条件
		$conditions = array();
		if($this->_request->getParam('cate_name') != '')
		$conditions['cate_name'] = trim($this->_request->getParam('cate_name'));
		
		//调整分页
		$perpage = 15;
		$page = intval($this->_request->getParam('page'));
		$total = $catepathM->fetchRowsCount($conditions);
		$pageObj = new Seed_Page($this->_request, $total, $perpage);
		$this->view->page = $pageObj->getPageArray();
		if($page>$this->view->page['totalpage']) $page=$this->view->page['totalpage'];
		if($page<1)$page=1;
		
		$this->view->paths = $catepathM->fetchRows(array(($page-1)*$perpage,$perpage), $conditions, array('cate_name asc','path_name ASC'));
		$this->view->conditions = $conditions;
		
		$imageCateM = new Seed_Model_ImageCategory('system');
		$this->view->cates = $imageCateM->fetchRows();
	}
	
	function addAction()
	{
		if ($this->_request->isPost()) {
			try{
				$f1= new zend_filter();
		        $f1->addFilter(new Zend_Filter_StripTags());
				$f2= new Zend_Filter_Int();
				$f3 = new Zend_Filter_Alnum();
				
				$insertData = array();
				$insertData['cate_name'] = $f3->filter($this->_request->getPost('cate_name'));
				$insertData['path_name'] = $f3->filter($this->_request->getPost('path_name'));
				$insertData['path_desc'] = $f1->filter($this->_request->getPost('path_desc'));

				if (empty($insertData['cate_name'])){
					Seed_Browser::tip_show('分类选择错误！');
				}elseif (empty($insertData['path_name'])){
					Seed_Browser::tip_show('请输入目录名称！');
				}elseif (empty($insertData['path_desc'])){
					Seed_Browser::tip_show('请输入目录说明！');
				}else{
					$catepathM = new Seed_Model_ImageCatePath('system');
					if($path_id = $catepathM->insertRow($insertData)){
						Seed_Browser::tip_show('添加目录成功！',$this->view->seed_BaseUrl.'/imagecatepath/index?cate_name='.$insertData['cate_name']);
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
				$f1= new zend_filter();
		        $f1->addFilter(new Zend_Filter_StripTags());
				$f2= new Zend_Filter_Int();
				$f3 = new Zend_Filter_Alnum();
				
				$updateData = array();
				$updateData['path_name'] = $f3->filter($this->_request->getPost('path_name'));
				$updateData['path_desc'] = $f1->filter($this->_request->getPost('path_desc'));
			
				$path_id = $f2->filter($this->_request->getPost('path_id'));
				
				if ($path_id<1){
					Seed_Browser::tip_show('关键数据出错！');
				}elseif (empty($updateData['path_name'])){
					Seed_Browser::tip_show('请输入分类名称！');
				}elseif (empty($updateData['path_desc'])){
					Seed_Browser::tip_show('请输入分类说明！');
				}else{
					$catepathM = new Seed_Model_ImageCatePath('system');
					
					if($catepathM->updateRow($updateData,array('path_id'=>$path_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/imagecatepath/index');
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
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f1 = new Zend_Filter_Int();
				$cate_id = $f1->filter($this->_request->getPost('cate_id'));	
				
				if($cate_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}else{
					$catepathM = new Seed_Model_ImageCatePath('system');
					$my = $catepathM->fetchRow(array('cate_id'=>$cate_id));
					if($catepathM->deleteRow(array('cate_id'=>$cate_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/imagecatepath/index');
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