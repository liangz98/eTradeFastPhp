<?php
class IwdController extends Shop_Controller_Action
{
	function indexAction()
	{
		
	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/iwd/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>