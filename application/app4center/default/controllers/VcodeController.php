<?php   
class VcodeController extends Zend_Controller_Action   
{   
    public function indexAction()
    {  
    	$vcode = new Seed_Model_Vcode();
    	$randcode = $vcode->random();
    	$session = Zend_Registry::get('session');
		$session->vcode = $randcode;	
    	$vcode->show4center($randcode);
    	exit;
    }
}
?>