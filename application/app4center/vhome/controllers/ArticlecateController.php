<?php
class ArticlecateController extends Commerce_Controller_Action4Commerce
{
	function indexAction()
	{
		$cateM=new Home_Model_NewsCate('home');
		$cate_id = $this->_request->getParam('cate_id');
		$this->view->cates = $cateM->fetchRows(null,null,array('order_by ASC'));
		$this->view->cateoptions = $cateM->getParentOption(0);
		if($cate_id>0){
			$this->view->cate = $cateM->fetchRow(array('cate_id'=>$cate_id));
		}
	}
	
	function addAction() {
		//AJAX POST
		if ($this->_request->isPost()) { 
			try{
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_Text();
				
				$insertData = array();
				$insertData['cate_name']=$f3->filter($this->_request->getPost('cate_name'));
				$insertData['cate_mark']=strtolower($f1->filter($this->_request->getPost('cate_mark')));
				$insertData['cate_m_image']=$f3->filter($this->_request->getPost('cate_m_image'));
				$insertData['cate_image']=$insertData['cate_m_image'];
				$insertData['parent']=intval($this->_request->getPost('parent'));
				
				if(empty($insertData['cate_name'])){
					Seed_Browser::tip_show('分类名称不能为空！');
				}elseif(empty($insertData['cate_mark'])){
					Seed_Browser::tip_show('保存文件名不能为空！');
				}else{
					$cateM=new Home_Model_NewsCate('home');
					$check = $cateM->fetchRow(array('cate_name'=>$insertData['cate_name']));
					if($check['cate_id']>0){
						Seed_Browser::tip_show('分类名称已经存在！');
					}
					$check = $cateM->fetchRow(array('cate_mark'=>$insertData['cate_mark']));
					if($check['cate_id']>0){
						Seed_Browser::tip_show('保存文件名已经存在！');
					}
					
					if($cate_id = $cateM->insertRow($insertData)){
						Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl."/articlecate/index");
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
	
	function updateAction() {
		//AJAX POST
		if ($this->_request->isPost()) {
			try{
				$cateM=new Home_Model_NewsCate('home');
				$f1=new Seed_Filter_Alnum();
				$f2=new Zend_Filter_Int();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				$f4 = new Seed_Filter_Text();
				$cate_id=$f2->filter($this->_request->getPost('cate_id'));
				$cateDetail = $cateM->fetchRow(array('cate_id'=>$cate_id));
				if($cateDetail['cate_id']<1){
					Seed_Browser::tip_show("关键数据错误！");
				}
				
				$updateData = array();
				$updateData['cate_name']=$f3->filter($this->_request->getPost('cate_name'));
				$updateData['cate_mark']=strtolower($f1->filter($this->_request->getPost('cate_mark')));
				$updateData['cate_m_image']=$f3->filter($this->_request->getPost('cate_m_image'));
				if(isset($this->view->seed_Setting['pc_open']) && $this->view->seed_Setting['pc_open']=='1'){
					$updateData['cate_image']=$f3->filter($this->_request->getPost('cate_image'));
				}
				
				$updateData['parent']=intval($this->_request->getPost('parent'));
				
				if($updateData['parent']==$cate_id)$updateData['parent']=0;
				
				if($cate_id<1){
					Seed_Browser::tip_show('关键参数错误！');
				}elseif(empty($updateData['cate_name'])){
					Seed_Browser::tip_show('分类名称不能为空！');
				}elseif(empty($updateData['cate_mark'])){
					Seed_Browser::tip_show('保存文件名不能为空！');
				}else{
					$check = $cateM->fetchRow(array('cate_name'=>$updateData['cate_name']));
					if($check['cate_id']>0 && $check['cate_id']!=$cate_id){
						Seed_Browser::tip_show('分类名称已经存在！');
					}
					$check = $cateM->fetchRow(array('cate_mark'=>$updateData['cate_mark']));
					if($check['cate_id']>0 && $check['cate_id']!=$cate_id){
						Seed_Browser::tip_show('保存名称已经存在！');
					}
					if($cateM->updateRow($updateData,array('cate_id'=>$cate_id))){
						Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl."/articlecate/index?cate_id=".$cate_id);
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
				$f3 = new Zend_Filter_Int();
				$cate_id = intval($this->_request->getPost('cate_id'));
				if($cate_id<1){
					Seed_Browser::tip_show('参数数据错误！');
				}else{
					$cateM=new Home_Model_NewsCate('home');
					if($cateM->deleteRow(array('cate_id'=>$cate_id))){
						Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/articlecate/index');
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