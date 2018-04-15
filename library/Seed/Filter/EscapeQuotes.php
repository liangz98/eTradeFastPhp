<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_EscapeQuotes implements Zend_Filter_Interface
{
    public function filter($value)
    {
        // perform some transformation upon $value to arrive on $valueFiltered
		if(get_magic_quotes_gpc()){
			$value=stripslashes($value);
		}
        return $value;
    }
}
