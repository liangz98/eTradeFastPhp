<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_CharInt implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if(preg_match("/^[a-z\d]+$/i",$value)){
        	return $value;
		}
    }
}