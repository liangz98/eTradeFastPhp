<?php
class Shop_Browser {
	public static function get_client_ip(){
	    echo "in client"; exit;
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
		else
		$ip = "unknown";
		return($ip);
	}

	public static function view_page($url,$timeOut=30,$header=0,$cookie=""){
        echo "in view page"; exit;
		$ch = curl_init();
		if(!$ch)return null;
		$userAgent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E)';
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt ($ch, CURLOPT_HEADER, $header);
		if(!empty($cookie))curl_setopt ($ch, CURLOPT_COOKIE,$cookie);
		//curl_setopt	($ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_TIMEOUT, $timeOut);
		$content = curl_exec ($ch);
		if (curl_errno($ch)) {
			//print curl_error($ch);
		} else {
			curl_close($ch);
		}
		return $content;
	}
    public static function info($msg,$redirect_url=null,$wait_time=1000,$target="window"){
        echo "in info"; exit;
        $sec = $wait_time/1000;
        $html= <<<EOD
		<script type="text/javascript" src="/ky/layer-v3.1.1/layer.js"></script>
	   <script type='text/javascript'>
	   $(document).ready(function(){
		layer.open({
  			type: 1 //Page层类型
  			,area: ['300px', '200px']
 			 ,title: false
  			,shade: 0.6 //遮罩透明度
 			 ,time: 2000 //2秒后自动关闭
  			,maxmin: false //允许全屏最小化
 			 ,anim: 1 //0-6的动画形式，-1不开启
 			 ,content: '<div style="padding:50px;">{$msg}</div>'
			 ,end : function(){window.location='{$redirect_url}'}
			});
			});
			</script>
EOD;
        echo $html;
    }
	public static function redirect($msg,$redirect_url,$wait_time=1000,$target="window"){
   		$wait_time1 = $wait_time/1000;
		$html=<<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>温馨提示</title>
<link type="text/css"  rel="stylesheet" href="/css/common.css" />
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" src="/js/seed.form.js"></script>
</head>
<body id="body1">
<div id="SeedMsgDiv" name="SeedMsgDiv" class="SeedMsgDiv">
<div class="close"><a href="javascript:void(0)" onclick="CloseSeedMsgBox()"><img src="/images/ui/close.gif" /></a></div>
<div class="tips_text"><p style="font-weight:bold;">温馨提示：</p>
<p id="SeedMsgTxt">{$msg}</p>
<p>页面跳转中，请稍后 <span id="SeedTimeWaitSet">{$wait_time1}</span> 秒 请点击<a href="{$redirect_url}">这里</a>直接跳转</p>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function seed_redirect(){
	clearTimeout(redirectTimeOutId);
	{$target}.location.href = '{$redirect_url}';
}
function seed_settime(){
	seedTimeWait = seedTimeWait-1;
	if(seedTimeWait<0)return;
	if(document.getElementById('SeedTimeWaitSet')){
		document.getElementById('SeedTimeWaitSet').innerHTML=seedTimeWait;
	}
}
var seedTimeWait="{$wait_time1}";
var redirectTimeOutId = setTimeout('seed_redirect()', {$wait_time});
setInterval('seed_settime()', 1000);
</script>'
EOD;
    	die($html);
    }



    public static function error($msg){
		$html=<<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>错误提示</title>
<link type="text/css"  rel="stylesheet" href="/css/common.css" />
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.validate.js"></script>
<script type="text/javascript" src="/js/seed.form.js"></script>
</head>
<body id="body1">
<div id="SeedMsgDiv" name="SeedMsgDiv" class="SeedMsgDiv">
<div class="close"><a href="javascript:void(0)"><img style='visibility:hidden' src="/images/ui/close.gif" /></a></div>
<div class="tips_text"><p style="font-weight:bold;">错误提示：</p>
<p id="SeedMsgTxt">{$msg}</p>
<p>请点击<a href="javascript:;" onclick="history.back();">这里</a>返回。</p>
</div>
</div>
</body>
</html>'
EOD;
    	die($html);
    }



public static function tip_show($msg,$redirect_url=null,$wait_time=2000,$target='window'){
   		$html="";
    	if($redirect_url!=null){
    		if($redirect_url=="reload"){
    			$html.= '<p>'.$msg.'</p>';
	    		$html.= '<p>页面跳转中，请稍后 <span id="SeedTimeWaitSet">'.($wait_time/1000).'</span> 秒</p>';
		    	$html.="
		    	<script type=\"text/javascript\">
		    	function seed_redirect(){
		    	 	CloseSeedMsgBox();
		    	 	clearTimeout(redirectTimeOutId);
		    	 	window.location.reload();
		    	}
		    	function seed_settime(){
		    		seedTimeWait = seedTimeWait-1;
		    		if(seedTimeWait<0)return;
		    		if(document.getElementById('SeedTimeWaitSet')){
		    			document.getElementById('SeedTimeWaitSet').innerHTML=seedTimeWait;
		    		}
		    	}
		    	var seedTimeWait=".($wait_time/1000).";
		    	var redirectTimeOutId = setTimeout('seed_redirect()', ".$wait_time.");
		    	setInterval('seed_settime()', 1000);
		    	</script>";
    		}else{
	    		$html.= '<p>'.$msg.'</p>';
	    		$html.= '<p>页面跳转中，请稍后 <span id="SeedTimeWaitSet">'.($wait_time/1000).'</span> 秒 请点击<a href="'.$redirect_url.'">这里</a>直接跳转</p>';
		    	$html.="
		    	<script type=\"text/javascript\">
		    	function seed_redirect(){
		    	 	CloseSeedMsgBox();
		    	 	clearTimeout(redirectTimeOutId);
		    	 	{$target}.location.href = '".$redirect_url."';
		    	}
		    	function seed_settime(){
		    		seedTimeWait = seedTimeWait-1;
		    		if(seedTimeWait<0)return;
		    		if(document.getElementById('SeedTimeWaitSet')){
		    			document.getElementById('SeedTimeWaitSet').innerHTML=seedTimeWait;
		    		}
		    	}
		    	var seedTimeWait=".($wait_time/1000).";
		    	var redirectTimeOutId = setTimeout('seed_redirect()', ".$wait_time.");
		    	setInterval('seed_settime()', 1000);
		    	</script>";
    		}
    	}else{
    		$html.= '<p>'.$msg.'</p>';
    		$html.="
		    	<script type=\"text/javascript\">
		    	function seed_redirect(){
		    	 	CloseSeedMsgBox();
		    	 	clearTimeout(redirectTimeOutId);
		    	}
		    	function seed_settime(){
		    		if(document.getElementById('SeedTimeWaitSet')){
		    			document.getElementById('SeedMsgDiv').style.display = 'none';
		    		}
		    	}
		    	var redirectTimeOutId = setTimeout('seed_redirect()', ".$wait_time.");
		    	setInterval('seed_settime()', 3000);
		    	</script>";
    	}

    	die($html);
    }

    public static function  isMobile(){
        echo "in is mobile"; exit;
    	$useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    	$useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
    	function CheckSubstrs($substrs,$text){
    		foreach($substrs as $substr)
    		if(false!==strpos($text,$substr)){
    			return true;
    		}
    		return false;
    	}
    	$mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    	$mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');

    	$found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||
    	CheckSubstrs($mobile_token_list,$useragent);

    	if ($found_mobile){
    		return true;
    	}else{
    		return false;
    	}
    }
}
?>
