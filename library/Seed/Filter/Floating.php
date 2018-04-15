<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Floating implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if(preg_match("/^[0-9.]+$/i",$value)){
        	return (float) ((string) $value);
		}
        return 0;
    }
}