<?php
class MobileinboxController extends Seed_Controller_Action4Admin
{
	function indexAction(){
		$mobileInboxM=new Seed_Model_MobileInbox('system');
		$conditions = array();
		//查询条件
		if(trim($this->_request->getParam('send_mobile'))!='')
			$conditions['send_mobile']=trim($this->_request->getParam('send_mobile'));
			
		$perpage=10;
    	$page=intval($this->_request->getParam('page'));
    	$total = $mobileInboxM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$this->view->messages = $mobileInboxM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions,'send_id DESC');
		$this->view->conditions = $conditions;
	}
	
	function deleteAction(){
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$send_ids = $this->_request->getPost('send_id');				
				if(count($send_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$mobileInboxM=new Seed_Model_MobileInbox('system');
				foreach ($send_ids as $send_id){
					$send_id = $f3->filter($send_id);
					if($send_id>0){
						$mobileInboxM->deleteRow(array('send_id'=>$send_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/mobileinbox/index');
				exit;
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	
		
		$send_ids = $this->_request->getParam('send_ids');
	    if(empty($send_ids))
	    {
			Seed_Browser::error('找不到相关的数据!');
		}
		$send_ids = explode(',',$send_ids);
		$f3 = new Zend_Filter_Int();
		$mysmss_arr=array();
		foreach ($send_ids as $send_id)
		{
			$send_id = $f3->filter($send_id);
			if($send_id>0)
					$mysmss_arr[]=$send_id;
		}
		if(count($mysmss_arr)>0){
			$mobileInboxM = new Seed_Model_MobileInbox('system');
			$messages = $mobileInboxM->fetchRowsByIds('send_id',$mysmss_arr);
	   		$this->view->messages = $messages;
		}
	}
}