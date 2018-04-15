<?php 

//require_once ( PATH . 'hessianPHP/src/HessianClient.php');

include_once 'HessianClient.php';

//Hessian::errorReporting(HESSIAN_SILENT);
try {
	
	$url = "http://122.13.0.200:8088/jcrAuth/dataDictApi";
	$proxy = new HessianClient($url);

	$account = $proxy->accountView('00808832-B08B-4340-B9E3-8EDC865329DC');
	//$tmparr = (array)$account;
	echo $account->accountName,"<br />",date_format($account->createTime, 'Y-m-d H:i:s');

} catch (HttpError $ex) {
	echo $ex->getMessage();
}

?> 
