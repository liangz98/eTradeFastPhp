<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Latitude implements Zend_Filter_Interface
{
    public function filter($value)
    {
    	$value=trim($value);
    	if(preg_match('/^-?(?:(?:180(?:\.0{0,9})?)|(?:1[0-7]\d|\d{1,2})(?:\.\d{0,9})?)$/s', $value)){
            return $value;
        }
        return '';
    }
}