<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Alnum implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if(preg_match("/^[a-zA-Z_0-9\-]+$/i",$value)){
        	return $value;
		}
    }
}