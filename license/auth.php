<?php
//decode by QQ:270656184 http://www.yunlu99.com/
$auth_hosts = array();
$auth_hosts[] = 'dd800.com';
//$auth_date = '20161124';
$curhost = getdomain($_SERVER['HTTP_HOST']);
$cur_date = date('Ymd');

define('GZSEED_AUTH_STEP1', '1');
define('GZSEED_AUTH_STEP2', '1');

function getdomain($_var_0)
{
	$_var_1 = strtolower($_var_0);
	if (strpos($_var_1, '/') !== false) {
		$_var_2 = @parse_url($_var_1);
		$_var_1 = $_var_2 ['host'];
	}
	$_var_3 = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me');
	$_var_4 = '';
	foreach ($_var_3 as $_var_5) {
		$_var_4 .= ($_var_4 ? '|' : '') . $_var_5;
	}
	$_var_6 = '[^\\.]+\\.(?:(' . $_var_4 . ')|\\w{2}|((' . $_var_4 . ')\\.\\w{2}))$';
	if (preg_match('/' . $_var_6 . '/ies', $_var_1, $_var_7)) {
		$_var_8 = $_var_7 ['0'];
	} else {
		$_var_8 = $_var_1;
	}
	return $_var_8;
}
