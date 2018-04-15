<?php
class LogController extends Seed_Controller_Action4Admin
{
	function preDispatch()
	{
	}

	function indexAction()
	{
		$logM = new Seed_Model_Log('system');

		$conditions = array();
		//查询条件
		if(trim($this->_request->getParam('user_name'))!='')
			$conditions['user_name']=trim($this->_request->getParam('user_name'));
		if(trim($this->_request->getParam('user_id'))!='')
			$conditions['user_name']=trim($this->_request->getParam('user_id'));

		$perpage=15;
    	$page=intval($this->_request->getParam('page'));
    	$total = $logM->fetchRowsCount($conditions);
    	$pageObj = new Seed_Page($this->_request,$total,$perpage);
    	$this->view->page = $pageObj->getPageArray();
	   	if($page>$this->view->page['totalpage'])$page=$this->view->page['totalpage'];
    	if($page<1)$page=1;
		$logs = $logM->fetchRows(array(($page-1)*$perpage,$perpage),$conditions,"log_id DESC");
		$this->view->logs = $logs;
		$this->view->conditions = $conditions;
	}
	
	
	function viewAction(){
		$log_id = $this->_request->getParam('log_id');
		if($log_id<1)throw new Exception('参数错误');
		$logM = new Seed_Model_Log('system');
		$log = $logM->fetchRow(array('log_id'=>$log_id));
		if($log['log_id']<1)throw new Exception('没有找到相关数据');
		$log['log_data']=unserialize($log['log_data']);
		$this->view->log = $log;
	}
}