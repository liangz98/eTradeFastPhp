<?php
class MailoutboxController extends Seed_Controller_Action4Admin
{
	function indexAction(){
		$mailOutboxM=new Seed_Model_MailOutbox('system');
		$conditions = array();
		//查询条件
		if(trim($this->_request->getParam('send_mobile'))!='')
			$conditions['send_mobile']=trim($this->_request->getParam('send_mobile'));
			
		$perpage=10;
    	$page=intval($this->_request->getParam('page'));
    	$total = $mailOutboxM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$this->view->mails = $mailOutboxM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions,'send_id DESC');
		$this->view->conditions = $conditions;
	}
	
	function addAction(){
		if ($this->_request->isPost()) {
			try{
				$f1 = new Seed_Filter_Email();
				$f2 = new Seed_Filter_Text();
				$f3 = new Zend_Filter( );
				$f3->addFilter(new Zend_Filter_StripTags())
					->addFilter(new Seed_Filter_EscapeQuotes());
				
				$send_email = $f1->filter($this->_request->getPost('send_email'));
				$send_title = $f3->filter($this->_request->getPost('send_title'));
				$send_content = $this->_request->getPost('send_content');
				
				if(empty($send_email)){
					Seed_Browser::tip_show('收件邮箱错误！');
				}elseif(empty($send_title)){
					Seed_Browser::tip_show('邮件标题不能为空！');
				}elseif(empty($send_content)){
					Seed_Browser::tip_show('邮件内容不能为空！');
				}else{
					$mailOutboxM=new Seed_Model_MailOutbox('system');
					$insertData = array();
					$insertData['send_email']=$send_email;
					$insertData['send_title']=$send_title;
					$insertData['send_content']=$send_content;
					$insertData['add_time']=time();
					$insertData['user_id']=0;
					$insertData['send_user_id']=$this->view->seed_User['user_id'];
					$send_id =$mailOutboxM->insertRow($insertData);
					if($send_id>0){
						$mailOutboxM->initConfig($this->view->seed_Setting);
						$mailOutboxM->sendMail($send_id);
						Seed_Browser::tip_show('添加到队列！',$this->view->seed_BaseUrl.'/mailoutbox/index');
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
	
	function deleteAction(){
		//AJAX POST
		if ($this->_request->isPost()){
			try{
				$f3 = new Zend_Filter_Int();
				$send_ids = $this->_request->getPost('send_id');				
				if(count($send_ids)==0){
					Seed_Browser::tip_show('找不到相关的数据!');
				}
				
				$mailOutboxM=new Seed_Model_MailOutbox('system');
				foreach ($send_ids as $send_id){
					$send_id = $f3->filter($send_id);
					if($send_id>0){
						$mailOutboxM->deleteRow(array('send_id'=>$send_id));
					}
				}
				Seed_Browser::tip_show('删除成功！',$this->view->seed_BaseUrl.'/mailoutbox/index');
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
			$mailOutboxM = new Seed_Model_MailOutbox('system');
			$mails = $mailOutboxM->fetchRowsByIds('send_id',$mysmss_arr);
	   		$this->view->mails = $mails;
		}
	}
	
	function resendAction(){
		$send_id = intval($this->_request->getParam('send_id'));
		$mailOutboxM = new Seed_Model_MailOutbox('system');
		$mailOutboxM->updateRow(array('is_sended'=>'0'),array('send_id'=>$send_id));
		$mailOutboxM->initConfig($this->view->seed_Setting);
		$mailOutboxM->sendMail($send_id);
		Seed_Browser::redirect('设置重发成功!',$this->view->seed_BaseUrl.'/mailoutbox/index');
		exit;
	}
	
}