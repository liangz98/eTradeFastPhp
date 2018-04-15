<?php
class PrinttplController extends Seed_Controller_Action4Admin
{
	function indexAction()
	{		
		$printTpl = new Shop_Model_PrintTpl('shop');
		$tpls = $printTpl->fetchRows();
		$this->view->tpls = $tpls;
	}
	
	function addAction()
	{
		if($this->_request->isPost())
		{
			$insertData=array();
			$f1 = new Zend_Filter();
			$f1->addFilter(new Zend_Filter_StripTags())->addFilter(new Seed_Filter_EscapeQuotes());
			$insertData['pt_name'] = $f1->filter($this->_request->getPost('pt_name'));

			$f2 = new Seed_Filter_Float();
			$insertData['pt_height'] = $f2->filter($this->_request->getPost('pt_height'));
			$insertData['pt_width'] = $f2->filter($this->_request->getPost('pt_width'));
			
			$insertData['pt_bg'] = $this->_request->getPost('pt_bg');
			$insertData['is_actived'] = intval($this->_request->getPost('is_actived'));
			
			if (empty($insertData['pt_name'])){
				Seed_Browser::tip_show('请输入名称！');
			}else if (empty($insertData['pt_height'])){
				Seed_Browser::tip_show('请输入高度！');
			}else if (empty($insertData['pt_width'])){
				Seed_Browser::tip_show('请输入宽度！');
			}else{
				$printTplM = new Shop_Model_PrintTpl('shop');
				$pt_id = $printTplM->insertRow($insertData);
				if($pt_id>0){
					Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/printtpl/set?pt_id='.$pt_id);
				}else{
					Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/printtpl/add');
				}
			}
			exit;
		}
		$printTplLabelM = new Shop_Model_PrintTplLabel('shop');
		$labels = $printTplLabelM->fetchRows();
		$this->view->labels = $labels;
	}
	
	function updateAction()
	{
		if($this->_request->isPost())
		{
			$updateData=array();
			$f1 = new Zend_Filter();
			$f1->addFilter(new Zend_Filter_StripTags())->addFilter(new Seed_Filter_EscapeQuotes());
			$updateData['pt_name'] = $f1->filter($this->_request->getPost('pt_name'));

			$f2 = new Seed_Filter_Float();
			$updateData['pt_height'] = $f2->filter($this->_request->getPost('pt_height'));
			$updateData['pt_width'] = $f2->filter($this->_request->getPost('pt_width'));
			
			$updateData['pt_bg'] = $this->_request->getPost('pt_bg');
			$updateData['is_actived'] = intval($this->_request->getPost('is_actived'));
			
			$pt_id = intval($this->_request->getPost('pt_id'));
			
			if ($pt_id<1){
				Seed_Browser::tip_show('关键数据错误！');
			}else if (empty($updateData['pt_name'])){
				Seed_Browser::tip_show('请输入名称！');
			}else if (empty($updateData['pt_height'])){
				Seed_Browser::tip_show('请输入高度！');
			}else if (empty($updateData['pt_width'])){
				Seed_Browser::tip_show('请输入宽度！');
			}else{
				$printTplM = new Shop_Model_PrintTpl('shop');
				$printTplM->updateRow($updateData,array('pt_id'=>$pt_id));
				Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/printtpl/update?pt_id='.$pt_id);
			}
			exit;
		}
		
		$printTpl = new Shop_Model_PrintTpl('shop');		
		
		$pt_id = intval($this->_request->getParam('pt_id'));		
		$tpl = $printTpl->fetchRow(array('pt_id'=>$pt_id));
		$this->view->tpl = $tpl;
	}
	
	function setAction()
	{
		if($this->_request->isPost())
		{
			$updateData=array();
			$updateData['pt_data'] = $this->_request->getPost('prt_tmpl_data');
			$updateData['pt_data'] = str_replace("\r\n","",$updateData['pt_data']);
			
			$pt_id = intval($this->_request->getPost('pt_id'));
			
			if ($pt_id<1){
				Seed_Browser::tip_show('关键数据错误！');
			}else{
				$printTplM = new Shop_Model_PrintTpl('shop');
				$printTplM->updateRow($updateData,array('pt_id'=>$pt_id));
				Seed_Browser::redirect('设置成功！',$this->view->seed_BaseUrl.'/printtpl/set?pt_id='.$pt_id);
			}
			exit;
		}
		
		$printTpl = new Shop_Model_PrintTpl('shop');		
		
		$pt_id = intval($this->_request->getParam('pt_id'));		
		$tpl = $printTpl->fetchRow(array('pt_id'=>$pt_id));
		$this->view->tpl = $tpl;
		
		$printTplLabel = new Shop_Model_PrintTplLabel('shop');
		$labels = $printTplLabel->fetchRows(null,null,'ptcl_id ASC');
		$this->view->labels = $labels;
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$pt_ids = $this->_request->getPost('pt_id');				
				if(count($pt_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$printTpl = new Shop_Model_PrintTpl('shop');	
				foreach ($pt_ids as $pt_id){
					$pt_id = intval($pt_id);
					if($pt_id>0){
						$printTpl->deleteRow(array('pt_id' => $pt_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/printtpl/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$pt_ids = $this->_request->getParam('pt_ids');
	    if(empty($pt_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$pt_ids = explode(',',$pt_ids);
		$f3 = new Zend_Filter_Int();
		$mypt_arr=array();
		foreach ($pt_ids as $pt_id)
		{
			$pt_id = intval($pt_id);
			if($pt_id>0)
					$mypt_arr[]=$pt_id;
		}
		if(count($mypt_arr)>0){
			$printTpl = new Shop_Model_PrintTpl('shop');		
			$tpls = $printTpl->fetchRowsByIds('pt_id',$mypt_arr);
	   		$this->view->tpls = $tpls;
		}
	}
}