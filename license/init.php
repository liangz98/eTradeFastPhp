<?php
//decode by QQ:270656184 http://www.yunlu99.com/
if (!defined('GZSEED_AUTH_STEP1') || GZSEED_AUTH_STEP1 != '1') {
	$ch = curl_init();
	if (!$ch) exit;
	$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36';
	curl_setopt($ch, CURLOPT_URL, 'http://lic.gzseed123.cn/?from=sendtou&host=' . $_SERVER['HTTP_HOST'] . '&ip=' . $_SERVER['SERVER_ADDR']);
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_exec($ch);
	exit('Invalid License file!');
}