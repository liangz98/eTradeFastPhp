<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_CnChar implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if(preg_match("/^[\x7f-\xff]+$/",$value)){
        	return $value;
		}
    }
}