<?php
class Zend_View_Helper_MoneyFormat
{
	function moneyFormat($price, $decial = 0)
	{
		if ($decial == 0) 
			$price = sprintf("￥%u", $price);
		return $price;
	}
}
