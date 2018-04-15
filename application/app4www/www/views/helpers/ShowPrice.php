<?php
class Zend_View_Helper_ShowPrice
{
	function showPrice($price, $decial = 2, $field_name="")
	{
		$decial = intval($decial);
		if(!empty($field_name))$myid=' id="'.$field_name.'"';
		else $myid="";
		$price = sprintf("{$myid}¥%.{$decial}f", $price);
		return $price;
	}
}
