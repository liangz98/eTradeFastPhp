<?php
class ImagecateController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{
		$categoryM=new Seed_Model_ImageCategory('system');
		$cate_id = $this->_request->getParam('cate_id');
		$this->view->categories = $categoryM->fetchRows(null,null,'cate_name ASC');
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
				$f3 = new Seed_Filter_Alnum();
				
				$insertData = array();
				$insertData['cate_name'] = $f3->filter($this->_request->getPost('cate_name'));
				$insertData['cate_desc'] = $f1->filter($this->_request->getPost('cate_desc'));
				$insertData['cate_path'] = $f3->filter($this->_request->getPost('cate_path'));
				$insertData['cate_ext'] = $f1->filter($this->_request->getPost('cate_ext'));
				$insertData['is_pub'] = intval($this->_request->getPost('is_pub'));
				$insertData['water_mark'] = intval($this->_request->getPost('water_mark'));
				$insertData['keep_orgname'] = intval($this->_request->getPost('keep_orgname'));
				$insertData['normal_width'] = intval($this->_request->getPost('normal_width'));
				$insertData['normal_height'] = intval($this->_request->getPost('normal_height'));
				$insertData['normal_type'] = intval($this->_request->getPost('normal_type'));
				$insertData['thumb_width'] = intval($this->_request->getPost('thumb_width'));
				$insertData['thumb_height'] = intval($this->_request->getPost('thumb_height'));
				$insertData['thumb_type'] = intval($this->_request->getPost('thumb_type'));				
				$insertData['square_width'] = intval($this->_request->getPost('square_width'));
				$insertData['square_height'] = intval($this->_request->getPost('square_height'));
				$insertData['square_type'] = intval($this->_request->getPost('square_type'));				
				$insertData['wap_width'] = intval($this->_request->getPost('wap_width'));
				$insertData['wap_height'] = intval($this->_request->getPost('wap_height'));
				$insertData['wap_type'] = intval($this->_request->getPost('wap_type'));
                $insertData['width_limit'] = intval($this->_request->getPost('width_limit'));
                $insertData['height_limit'] = intval($this->_request->getPost('height_limit'));
                $insertData['size_limit'] = intval($this->_request->getPost('size_limit'));
                $insertData['file_limit'] = intval($this->_request->getPost('file_limit'));

				if (empty($insertData['cate_name'])){
					Seed_Browser::tip_show('请输入分类名称！');
				}elseif (empty($insertData['cate_desc'])){
					Seed_Browser::tip_show('请输入分类说明！');
				}elseif (empty($insertData['cate_path'])){
					Seed_Browser::tip_show('请输入保存路径！');
				}elseif (empty($insertData['cate_ext'])){
					Seed_Browser::tip_show('请输入扩展名！');
				}else{
					$categoryM = new Seed_Model_ImageCategory('system');
					if($cate_id = $categoryM->insertRow($insertData)){
						Seed_Browser::tip_show('添加分类成功！',$this->view->seed_BaseUrl.'/imagecate/index/cate_id/'.$cate_id);
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
				$f3 = new Seed_Filter_Alnum();
				
				$updateData = array();
				$updateData['cate_name'] = $f3->filter($this->_request->getPost('cate_name'));
				$updateData['cate_desc'] = $f1->filter($this->_request->getPost('cate_desc'));
				$updateData['cate_path'] = $f3->filter($this->_request->getPost('cate_path'));
				$updateData['cate_ext'] = $f1->filter($this->_request->getPost('cate_ext'));
				$updateData['is_pub'] = intval($this->_request->getPost('is_pub'));
				$updateData['water_mark'] = intval($this->_request->getPost('water_mark'));
				$updateData['keep_orgname'] = intval($this->_request->getPost('keep_orgname'));
				$updateData['normal_width'] = intval($this->_request->getPost('normal_width'));
				$updateData['normal_height'] = intval($this->_request->getPost('normal_height'));
				$updateData['normal_type'] = intval($this->_request->getPost('normal_type'));
				$updateData['thumb_width'] = intval($this->_request->getPost('thumb_width'));
				$updateData['thumb_height'] = intval($this->_request->getPost('thumb_height'));
				$updateData['thumb_type'] = intval($this->_request->getPost('thumb_type'));			
				$updateData['square_width'] = intval($this->_request->getPost('square_width'));
				$updateData['square_height'] = intval($this->_request->getPost('square_height'));
				$updateData['square_type'] = intval($this->_request->getPost('square_type'));				
				$updateData['wap_width'] = intval($this->_request->getPost('wap_width'));
				$updateData['wap_height'] = intval($this->_request->getPost('wap_height'));
				$updateData['wap_type'] = intval($this->_request->getPost('wap_type'));
                $updateData['width_limit'] = intval($this->_request->getPost('width_limit'));
                $updateData['height_limit'] = intval($this->_request->getPost('height_limit'));
                $updateData['size_limit'] = intval($this->_request->getPost('size_limit'));
                $updateData['file_limit'] = intval($this->_request->getPost('file_limit'));
			
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
					$categoryM = new Seed_Model_ImageCategory('system');
					
					if($categoryM->updateRow($updateData,array('cate_id'=>$cate_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/imagecate/index/cate_id/'.$cate_id);
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
					$categoryM = new Seed_Model_ImageCategory('system');
					$my = $categoryM->fetchRow(array('cate_id'=>$cate_id));
					if($categoryM->deleteRow(array('cate_id'=>$cate_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/imagecate/index');
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
		$imageCateM = new Seed_Model_ImageCategory('system');
		$imageCate = $imageCateM->fetchRows(null, null, null, array('cate_name', 'cate_desc'));
		$json_string = json_encode($imageCate);
		echo $json_string;
		exit;
	}
}