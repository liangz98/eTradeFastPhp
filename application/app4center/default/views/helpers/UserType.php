<?php
class Zend_View_Helper_UserType
{
	function userType($id)
	{
		switch ($id){
			case 0:
				return "普通会员";
				break;
			case 1:
				return "银卡会员";
				break;
			case 2:
				return "金卡会员";
				break;
			case 3:
				return "供货商";
				break;
			case 4:
				return "代理商";
				break;
			case 5:
				return "运营商";
				break;
			default:
				return "未知";
		}
	}
}
