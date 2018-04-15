<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Email implements Zend_Filter_Interface
{
    public function filter($value)
    {
    	$value=strtolower($value);
        preg_match("/^[\w][\w-\._]+@[\w][\w-]+\.[a-z]{2,3}(\.[a-z]{2,3})?$/", $value, $matches);
    	if (isset($matches[0]) && !empty($matches[0])) {
    		return $matches[0];
    	}
    }
}