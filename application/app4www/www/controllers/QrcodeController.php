<?php
class QrcodeController extends Zend_Controller_Action
{
	function preDispatch()
	{
		$this->view->cur_pos = $this->_request->getParam('controller'); 
	}
	
	public function indexAction()
	{
		$url = $this->_request->getParam('url');
		if($url=="")$url="http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
		else $url = urldecode($url);
		$level = "M";
		$size = 8;
		include SEED_LIB_ROOT."/Plugin/Phpqrcode.php";
		QRcode::png($url , false , $level, $size, 2);
		exit;
	}
}