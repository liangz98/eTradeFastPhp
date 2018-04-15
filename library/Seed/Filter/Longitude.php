<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Longitude implements Zend_Filter_Interface
{
    public function filter($value)
    {
    	$value=trim($value);
    	if(preg_match('/^-?(?:90(?:\.0{0,9})?|(?:(?:[0-8]?\d)(?:\.\d{0,9})?))$/s', $value)){
            return $value;
        }
        return '';
    }
}