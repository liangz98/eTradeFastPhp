<?php
require_once 'Zend/Auth.php';
class Seed_Auth extends Zend_Auth
{
	protected static $_instance;
	
	public function __construct(){}
	public static function singleton()
    {
        if (!self::$_instance instanceof self)
        {
            self::$_instance = new self();
            self::$_instance->_init();
        }
        return self::$_instance;
    }
}