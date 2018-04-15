<?php
class Zend_View_Helper_GetRandomString
{
	function getRandomString($len = 8)
	{
		$str = 'abcdefghijklmnopqrstuvwxyz';//abcdefghijklmnopqrstuvwxyzABCDEFGHIJLMNOPQRSTUVWXYZ0123456789
		return substr(str_shuffle($str), 0, $len);
	}
}

?>