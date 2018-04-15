<?php
class Zend_View_Helper_ShowToPrice extends Shop_View_Helper
{
	function ShowToPrice($key)
	{
		//$attArr 服务端返回附件信息数组；
        if(is_numeric($key)){
            $str=empty($key)?0:$key;
//        if(gettype($str)=='string'){
            $data= sprintf("%.2f",$str);
//        }else{
//            $data=round($str,2);
//        }

        }else{
            $data=$key;
        }
        return $data;
	}
}