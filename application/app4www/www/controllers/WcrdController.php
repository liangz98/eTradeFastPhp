<?php
class WcrdController extends Shop_Controller_Action
{
	function indexAction()
	{
		
	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/wcrd/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>