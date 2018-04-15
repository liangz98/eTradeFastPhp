<?php
class ContactController extends Kyapi_Controller_Action
{
	public function preDispatch()
	{
		$this->view->cur_pos = 'info';
		if(empty($this->view->userID)&&($this->view->visitor)){
			Mobile_Browser::redirect('请先登录系统！',$this->view->seed_Setting['user_app_server']."/login");
		}
	}
	public function indexAction()
	{
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/contact/index.phtml");
			echo $content;
			exit;
		}
	}
	public function addAction()
	{
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/contact/index.phtml");
			echo $content;
			exit;
		}
	}
	public function editAction()
	{
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/contact/edit.phtml");
			echo $content;
			exit;
		}
	}
	public function viewAction()
	{
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/contact/view.phtml");
			echo $content;
			exit;
		}
	}
}