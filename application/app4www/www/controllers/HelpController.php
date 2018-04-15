<?php
class HelpController extends Shop_Controller_Action
{
	function preDispatch()
	{
		$this->view->cur_pos = $this->_request->getParam('controller');
		$cururl = $_SERVER['REQUEST_URI'];
		preg_match('/([\w]+)\.html/',$cururl,$arr);
		if(isset($arr[1]) && !empty($arr[1]))
		{
			preg_match('/^\/help\/([\w]+).html/isU',$cururl,$arr);
			if(isset($arr[1]) && !empty($arr[1])){
				$this->_request->setParam('help_mark',$arr[1]);
				$this->indexAction();
				exit;
			}
			Shop_Browser::redirect('没有找到相关信息！',$this->view->seed_BaseUrl."/");
		}
	}
	
	function indexAction(){
		$help_mark = $this->_request->getParam('help_mark');
		if(empty($help_mark))$help_mark="about";
		$this->view->help_mark = $help_mark;
		$helpM = new Home_Model_Help('home');
		$help = $helpM->fetchRow(array('help_mark'=>$help_mark));
		if($help['help_id']<1){
			$help=$helpM->fetchRow();
			if($help['help_id']<1)
			Shop_Browser::error("没有找到相关数据！");
		}
		$this->view->help = $help;

		$helps = $helpM->fetchRows(null,array('parent'=>0,'is_actived'=>'1'),'order_by ASC');
		foreach ($helps as $k=>$help){
			$helps[$k]['sub_helps']=$helpM->fetchRows(null,array('parent'=>$help['help_id'],'is_actived'=>'1'),'order_by ASC');
		}
		$this->view->helps = $helps;
		
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL.'/help/index.phtml');
			echo $content;
			exit;
		}
	}
}