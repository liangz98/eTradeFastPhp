<?php
class Zend_View_Helper_Showtwonumber extends Shop_View_Helper
{
	function Showtwonumber($key)
	{
		if(gettype($key)=='string'){
            $str= sprintf("%.2f",$key);
        }else{
            $str=round($key,2);
        }

		return $str;
	}
}