<?php
class GdyfcpyController extends Shop_Controller_Action
{
	function indexAction()
	{
		
	if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/gdyfcpy/index.phtml");
			echo $content;
			exit;
		}
	}
}
?>