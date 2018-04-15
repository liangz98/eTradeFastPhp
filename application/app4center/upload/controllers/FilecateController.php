<?php
class FilecateController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$categoryM=new Seed_Model_FileCategory('system');
		$cate_id = $this->_request->getParam('cate_id');
		$this->view->categories = $categoryM->fetchRows();
		if($cate_id>0){
			$this->view->category = $categoryM->fetchRow(array('cate_id'=>$cate_id));
		}
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
				$insertData['cate_name'] = ($this->_request->getPost('cate_name'));
				$insertData['cate_desc'] = $f1->filter($this->_request->getPost('cate_desc'));
				$insertData['cate_path'] = ($this->_request->getPost('cate_path'));
				$insertData['cate_ext'] = $f1->filter($this->_request->getPost('cate_ext'));
				$insertData['is_pub'] = $f2->filter($this->_request->getPost('is_pub'));

				if (empty($insertData['cate_name'])){
					Seed_Browser::tip_show('请输入分类名称！');
				}elseif (empty($insertData['cate_desc'])){
					Seed_Browser::tip_show('请输入分类说明！');
				}elseif (empty($insertData['cate_path'])){
					Seed_Browser::tip_show('请输入保存路径！');
				}elseif (empty($insertData['cate_ext'])){
					Seed_Browser::tip_show('请输入扩展名！');
				}else{
					$categoryM = new Seed_Model_FileCategory('system');
					if($cate_id = $categoryM->insertRow($insertData)){
						Seed_Browser::tip_show('添加分类成功！',$this->view->seed_BaseUrl.'/filecate/index/cate_id/'.$cate_id);
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
				$updateData['cate_name'] = $f3->filter($this->_request->getPost('cate_name'));
				$updateData['cate_desc'] = $f1->filter($this->_request->getPost('cate_desc'));
				$updateData['cate_path'] = $f3->filter($this->_request->getPost('cate_path'));
				$updateData['cate_ext'] = $f1->filter($this->_request->getPost('cate_ext'));
				$updateData['is_pub'] = $f2->filter($this->_request->getPost('is_pub'));
			
				$cate_id = $f2->filter($this->_request->getPost('cate_id'));
				
				if ($cate_id<1){
					Seed_Browser::tip_show('关键数据出错！');
				}elseif (empty($updateData['cate_name'])){
					Seed_Browser::tip_show('请输入分类名称！');
				}elseif (empty($updateData['cate_desc'])){
					Seed_Browser::tip_show('请输入分类说明！');
				}elseif (empty($updateData['cate_path'])){
					Seed_Browser::tip_show('请输入保存路径！');
				}elseif (empty($updateData['cate_ext'])){
					Seed_Browser::tip_show('请输入扩展名！');
				}else{
					$categoryM = new Seed_Model_FileCategory('system');
					
					if($categoryM->updateRow($updateData,array('cate_id'=>$cate_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/filecate/index/cate_id/'.$cate_id);
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
					$categoryM = new Seed_Model_FileCategory('system');
					$my = $categoryM->fetchRow(array('cate_id'=>$cate_id));
					if($categoryM->deleteRow(array('cate_id'=>$cate_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/filecate/index');
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
	
	function ajaxAction()
	{
		$fileCateM = new Seed_Model_FileCategory('system');
		$fileCate = $fileCateM->fetchRows(null, null, null, array('cate_name', 'cate_desc'));
		$json_string = json_encode($fileCate);
		echo $json_string;
		exit;
	}
}