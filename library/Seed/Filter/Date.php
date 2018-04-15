<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Date implements Zend_Filter_Interface
{
    public function filter($value)
    {
		if (@eregi("^(19|20)[0-9]{2}-(0[0-9]|1[0-2])-(0[0-9]|1[0-9]|2[0-9]|3[0-1])$",$value)){
           return $value;
        }
        if (@eregi("^(19|20)[0-9]{2}-([1-9]|0[0-9]|1[0-2])-([1-9]|1[0-9]|2[0-9]|3[0-1])$",$value)){
           return $value;
        }
    }
}