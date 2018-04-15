<?php
class Zend_View_Helper_SetDate
{
	function setDate($time , $format = 'Y-m-d H:i:s')
	{
		return date($format , $time);
	}
}
