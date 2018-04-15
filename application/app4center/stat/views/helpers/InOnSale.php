<?php
class Zend_View_Helper_InOnSale
{
	function inOnSale($is_on_sale = 0)
	{
		switch ($is_on_sale) 
		{
			case 0:
				return '<font color="#FF0000">未上市</font>';
				break;
			case 1:
				return '<font color="#0000FF">上市中</font>';
				break;
			default:
				return '已下市';
				break;
		}
	}
}
