<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Mobile implements Zend_Filter_Interface
{
    public function filter($mobile)
    {
        $mymobile=false;
        if(preg_match("/^10086$/",$mobile))$mymobile=true;
        if(preg_match("/^13\d{9}$/",$mobile))$mymobile=true;
        if(preg_match("/^14\d{9}$/",$mobile))$mymobile=true;
        if(preg_match("/^15\d{9}$/",$mobile))$mymobile=true;
        if(preg_match("/^17\d{9}$/",$mobile))$mymobile=true;
        if(preg_match("/^18\d{9}$/",$mobile))$mymobile=true;
				if($mymobile){
           return $mobile;
        }
    }
	    
    public function telefilter($telephone)
    {
    	return $telephone;
		}
}