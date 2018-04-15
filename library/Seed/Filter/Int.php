<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Int implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if(preg_match("/^[\d]+$/i",$value)){
        	return $value;
		}
    }
}