<?php
class MobileoutboxController extends Seed_Controller_Action4Admin
{
	function indexAction(){
		$mobileOutboxM=new Seed_Model_MobileOutbox('system');
		$conditions = array();
		//查询条件
		if(trim($this->_request->getParam('send_mobile'))!='')
			$conditions['send_mobile']=trim($this->_request->getParam('send_mobile'));
			
		$perpage=10;
    	$page=intval($this->_request->getParam('page'));
    	$total = $mobileOutboxM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$this->view->messages = $mobileOutboxM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions,'send_id DESC');
		$this->view->conditions = $conditions;
	}
	
	function addAction(){
		if ($this->_request->isPost()) {
			try{
				$f1 = new Seed_Filter_Mobile();
				$f2 = new Zend_Filter_StripTags();
				
				$send_mobile = $f1->filter($this->_request->getPost('send_mobile'));
				$send_port = intval($this->_request->getPost('send_port'));
				$send_content = $f2->filter($this->_request->getPost('send_content'));
				
				if(empty($send_mobile)){
					Seed_Browser::tip_show('手机号码错误！');
				}elseif(empty($send_content)){
					Seed_Browser::tip_show('手机信息不能为空！');
				}else{
					$mobileOutboxM=new Seed_Model_MobileOutbox('system');
					$mobileOutboxM->mobileSend($send_mobile,$send_content,time());
					Seed_Browser::tip_show('添加到队列！',$this->view->seed_BaseUrl.'/mobileoutbox/index');
				}
				
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
	}
	
	function importAction(){
		if ($this->_request->isPost()) {
			try{
				$f1 = new Seed_Filter_Mobile();
				$f2 = new Zend_Filter_StripTags();
				
				$send_mobiles = $this->_request->getPost('send_mobiles');
				$send_mobiles = explode('<br />',nl2br($send_mobiles));
				$send_content = $f2->filter($this->_request->getPost('send_content'));
				
				if(empty($send_content)){
					Seed_Browser::tip_show('手机信息不能为空！');
				}
				
				if(is_array($send_mobiles) && count($send_mobiles)){
					$mobileOutboxM=new Seed_Model_MobileOutbox('system');
					foreach ($send_mobiles as $k=>$send_mobile){
						$send_mobile = $f1->filter(trim($send_mobile));
						if(!empty($send_mobile)){
							$mobileOutboxM->mobileSend($send_mobile,$send_content,time());
						}
					}
				}
				Seed_Browser::tip_show('添加到队列！',$this->view->seed_BaseUrl.'/mobileoutbox/index');
				
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
			exit;
		}
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
				
				$mobileOutboxM=new Seed_Model_MobileOutbox('system');
				foreach ($send_ids as $send_id){
					$send_id = $f3->filter($send_id);
					if($send_id>0){
						$mobileOutboxM->deleteRow(array('send_id'=>$send_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/mobileoutbox/index');
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
			$mobileOutboxM = new Seed_Model_MobileOutbox('system');
			$messages = $mobileOutboxM->fetchRowsByIds('send_id',$mysmss_arr);
	   		$this->view->messages = $messages;
		}
	}
	
	function resendAction(){
		$send_id = intval($this->_request->getParam('send_id'));
		$mobileOutboxM = new Seed_Model_MobileOutbox('system');
		$outbox = $mobileOutboxM->fetchRow(array('send_id'=>$send_id));
		$mobileOutboxM->mobileSend($outbox['send_mobile'],$outbox['send_content'],time());
		Seed_Browser::redirect('设置重发成功!',$this->view->seed_BaseUrl.'/mobileoutbox/index');
		exit;
	}
	
}