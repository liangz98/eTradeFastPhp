<?php
class Zend_View_Helper_ShowToRptype extends Shop_View_Helper
{
	function ShowToRptype($key)
	{
		//$rptype 判断 输出类型  ‘R +’ ‘P-’ ‘o 不显示’；
		if($key=='O'){
		    $str="";
        }else{
		    if($key=='R'){
		        $str='+';
            }elseif ($key='P'){
		        $str='-';
            }else{
                $str="";
            }

        };

		return $str;
	}
}