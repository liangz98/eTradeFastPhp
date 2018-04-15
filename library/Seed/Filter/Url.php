<?php
require_once 'Zend/Filter/Interface.php';
class Seed_Filter_Url implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if (preg_match('/^https?:\/\/[\w\.]+[\w\/\-]*[\w\.]*\??[\w=&\+\%]*/is', $value))
        {
            return $value;
        }
        return '';
    }
}