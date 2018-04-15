<?php
class Zend_View_Helper_BranchType
{
	function branchType($type)
	{
		switch($type){
			case "1":
				return "销售中心";
				break;
			case "2":
				return "客服中心";
				break;
			case "3":
				return "仓库";
				break;
			case "3":
				return "办公室";
				break;
			default:
				return "未知";
		}
	}
}
