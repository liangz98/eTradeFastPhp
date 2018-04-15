<?php

class WechatnotetplController extends Seed_Controller_Action4Admin{
	function indexAction()
	{		
    $noteTpl = new Wechat_Model_Notetpl('wechat');
		$tpls = $noteTpl->fetchRows();
		$this->view->tpls = $tpls;
	}
	
	function addAction()
	{
		if($this->_request->isPost())
		{
			$insertData = array();
			$f1 = new Zend_Filter();
			$f1->addFilter(new Zend_Filter_StripTags())->addFilter(new Seed_Filter_EscapeQuotes());
			$insertData['tpl_id'] = $f1->filter($this->_request->getPost('tpl_id'));
			$insertData['nt_name'] = $f1->filter($this->_request->getPost('nt_name'));
			$insertData['nt_data'] = $f1->filter($this->_request->getPost('nt_data'));
			$insertData['nt_desc'] = $f1->filter($this->_request->getPost('nt_desc'));
			$insertData['send_to'] = $f1->filter($this->_request->getPost('send_to'));
			$insertData['is_actived'] = intval($this->_request->getPost('is_actived'));
			if (empty($insertData['nt_name'])){
				Seed_Browser::tip_show('请输入名称！');
			}else{
        $noteTpl = new Wechat_Model_Notetpl('wechat');
				$nt_id = $noteTpl->insertRow($insertData);
				if($nt_id > 0){
					Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/wechatnotetpl/update?nt_id='.$nt_id);
				}else{
					Seed_Browser::tip_show('添加成功！',$this->view->seed_BaseUrl.'/wechatnotetpl/add');
				}
			}
			exit;
		}
	}
	
	function updateAction()
	{
		if($this->_request->isPost())
		{
			$updateData=array();
			$f1 = new Zend_Filter();
			$f1->addFilter(new Zend_Filter_StripTags())->addFilter(new Seed_Filter_EscapeQuotes());
      $updateData['tpl_id'] = $f1->filter($this->_request->getPost('tpl_id'));
			$updateData['nt_name'] = $f1->filter($this->_request->getPost('nt_name'));
			$updateData['nt_data'] = $f1->filter($this->_request->getPost('nt_data'));
			$updateData['nt_desc'] = $f1->filter($this->_request->getPost('nt_desc'));
			$updateData['send_to'] = $f1->filter($this->_request->getPost('send_to'));
			$updateData['is_actived'] = intval($this->_request->getPost('is_actived'));
			$nt_id = intval($this->_request->getPost('nt_id'));
			if ($nt_id<1){
				Seed_Browser::tip_show('关键数据错误！');
			}else if (empty($updateData['nt_name'])){
				Seed_Browser::tip_show('请输入名称！');
			}else{
			  $noteTpl = new Wechat_Model_Notetpl('wechat');
				$noteTpl->updateRow($updateData,array('nt_id'=>$nt_id));
				Seed_Browser::tip_show('修改成功！',$this->view->seed_BaseUrl.'/wechatnotetpl/update?nt_id='.$nt_id);
			}
			exit;
		}
    $noteTpl = new Wechat_Model_Notetpl('wechat');
		$nt_id = intval($this->_request->getParam('nt_id'));		
		$tpl = $noteTpl->fetchRow(array('nt_id'=>$nt_id));
		$this->view->tpl = $tpl;
	}
	
	function deleteAction() {
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$nt_ids = $this->_request->getPost('nt_id');				
				if(count($nt_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
        $noteTpl = new Wechat_Model_Notetpl('wechat');
				foreach ($nt_ids as $nt_id){
					$nt_id = intval($nt_id);
					if($nt_id>0){
						$noteTpl->deleteRow(array('nt_id' => $nt_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/wechatnotetpl/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
		
		$nt_ids = $this->_request->getParam('nt_ids');
    if(empty($nt_ids))
    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$nt_ids = explode(',',$nt_ids);
		$f3 = new Zend_Filter_Int();
		$mynt_arr = array();
		foreach ($nt_ids as $nt_id)
		{
			$nt_id = intval($nt_id);
			if($nt_id>0)
					$mynt_arr[]=$nt_id;
		}
		if(count($mynt_arr)>0){
      $noteTpl = new Wechat_Model_Notetpl('wechat');
			$tpls = $noteTpl->fetchRowsByIds('nt_id',$mynt_arr);
      $this->view->tpls = $tpls;
		}
	}
}