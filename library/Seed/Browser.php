<?php
class Seed_Browser {
	public static function get_client_ip(){ 
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
	
	public static function view_page($url,$referer=0,$timeOut=30,$header=0,$cookie=""){
		
		$ch = curl_init();
		if(!$ch)return null;
		$userAgent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36';
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERAGENT, $userAgent);
		if(!empty($referer))curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt ($ch, CURLOPT_HEADER, $header);
		if(!empty($cookie))curl_setopt ($ch, CURLOPT_COOKIE,$cookie);
		curl_setopt	($ch, CURLOPT_FOLLOWLOCATION,true); 
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
		$sec = $wait_time/1000;
		$html=<<<EOD
		<script type="text/javascript" src="/ky/layer/layer.js"></script>
	   <script type='text/javascript'>
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
			</script>
EOD;
		die($html);
	}


	
	public static function redirect($msg,$redirect_url,$wait_time=1000,$target="window"){
		$sec = $wait_time/1000;
   		$html=<<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>页面跳转</title>
<style type="text/css">
	html{ font-size:12px; font-family:"宋体"; background:#fafafa;color:#666;}
	body{position:relative;}
	body,div,p,input{ margin:0; padding:0;}
	ul{ list-style:none;}
	img{border:0;}
	#pageshow{width:600px;margin:auto;height:330px;background:url(/images/center/divbg2.jpg) no-repeat bottom center;padding-top:180px; position:relative;}
	#pageshow img {
	   azimuth: expression(
	    this.pngSet?this.pngSet=true:
	           (this.nodeName == "IMG" &&
	                   this.src.toLowerCase().indexOf('.png')>-1?
	                            (this.runtimeStyle.backgroundImage = "none",
	                             this.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + this.src + "', sizingMethod='image')",
	                             this.src = "/images/center/trans.gif")
	                           :(this.origBg = this.origBg?
	                                     this.origBg
	                                    :this.currentStyle.backgroundImage.toString().replace('url("','').replace('")',''),
	                                     this.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + this.origBg + "', sizingMethod='crop')",
	                                    this.runtimeStyle.backgroundImage = "none")),
	           this.pngSet=true);
	  } 
	.pageshow_div{width:470px;margin:auto;padding:20px 8px 0 0;}
	    .pageshow_title{width:94%;padding:0 3% 9px;hieght:33px; float:left;border-bottom:1px solid #c9c9c9;}
		   .pageshow_title strong{float:left;width:60%;}
		   .pageshow_title span{float:right;width:40%; text-align:right;}
		.pageshow_content{width:94%;padding:10px 3%;border-top:1px solid #fff;float:left;}
		  .pageshow_content p{line-height:30px;font-size:14px;font-weight:bold;}
		  .pageshow_content p.tz{line-height:20px;}
		    .pageshow_content p.tz a{color:#cc0000;}
	  .pageshowbnt{position:absolute;width:92px;hieght:28px;right:250px;bottom:95px;}
	    .pageshowbnt input{border:none;width:92px;height:28px;cursor:pointer;color:#eef0f1;font-weight:bold;background:url(/images/center/bntbg2.gif);}
</style>
</head>
<body id="body1">
<div id="pageshow">
  <div class="pageshow_div">
    <div class="pageshow_title"><strong><img src="/images/center/titlebg.png" /></strong><span><img src="/images/center/logo.png" /></span></div>
    <div class="pageshow_content">
      <p>{$msg}</p>
      <p>页面跳转中，请稍后 <span id="SeedTimeWaitSet">{$sec}</span> 秒  请点击<a href='{$redirect_url}'>这里</a>直接跳转</p>
      <p style="text-align:center;padding-top:20px;"><img src="/images/center/wait.gif" /></p>
    </div>
  </div>
</div>
</body>
</html>
<script type='text/javascript'>
	function seed_settime(){
		seedTimeWait = seedTimeWait-1;
		if(seedTimeWait==0){
			clearInterval(redirectTimeOutId);
			{$target}.location.href = '{$redirect_url}';
		}else if(seedTimeWait<0){
			return;
		}
		if(document.getElementById('SeedTimeWaitSet'))
			document.getElementById('SeedTimeWaitSet').innerHTML=seedTimeWait;
	}
	var seedTimeWait={$sec};
	var redirectTimeOutId = setInterval('seed_settime()', 1000);
</script>
EOD;
    	die($html);
    }

    public static function autoclose($msg,$redirect_url,$wait_time=1000,$target="window"){
        $sec = $wait_time/1000;
        $html=<<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>页面跳转</title>
<style type="text/css">
	html{ font-size:12px; font-family:"宋体"; background:#fafafa;color:#666;}
	body{position:relative;}
	body,div,p,input{ margin:0; padding:0;}
	ul{ list-style:none;}
	img{border:0;}
	#pageshow{width:600px;margin:auto;height:330px;background:url(/images/center/divbg2.jpg) no-repeat bottom center;padding-top:180px; position:relative;}
	#pageshow img {
	   azimuth: expression(
	    this.pngSet?this.pngSet=true:
	           (this.nodeName == "IMG" &&
	                   this.src.toLowerCase().indexOf('.png')>-1?
	                            (this.runtimeStyle.backgroundImage = "none",
	                             this.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + this.src + "', sizingMethod='image')",
	                             this.src = "/images/center/trans.gif")
	                           :(this.origBg = this.origBg?
	                                     this.origBg
	                                    :this.currentStyle.backgroundImage.toString().replace('url("','').replace('")',''),
	                                     this.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + this.origBg + "', sizingMethod='crop')",
	                                    this.runtimeStyle.backgroundImage = "none")),
	           this.pngSet=true);
	  } 
	.pageshow_div{width:470px;margin:auto;padding:20px 8px 0 0;}
	    .pageshow_title{width:94%;padding:0 3% 9px;hieght:33px; float:left;border-bottom:1px solid #c9c9c9;}
		   .pageshow_title strong{float:left;width:60%;}
		   .pageshow_title span{float:right;width:40%; text-align:right;}
		.pageshow_content{width:94%;padding:10px 3%;border-top:1px solid #fff;float:left;}
		  .pageshow_content p{line-height:30px;font-size:14px;font-weight:bold;}
		  .pageshow_content p.tz{line-height:20px;}
		    .pageshow_content p.tz a{color:#cc0000;}
	  .pageshowbnt{position:absolute;width:92px;hieght:28px;right:250px;bottom:95px;}
	    .pageshowbnt input{border:none;width:92px;height:28px;cursor:pointer;color:#eef0f1;font-weight:bold;background:url(/images/center/bntbg2.gif);}
</style>
</head>
<body id="body1">
<div id="pageshow">
  <div class="pageshow_div">
    <div class="pageshow_title"><strong><img src="/images/center/titlebg.png" /></strong><span><img src="/images/center/logo.png" /></span></div>
    <div class="pageshow_content">
      <p>{$msg}</p>
      <p style="text-align:center;padding-top:20px;"><img src="/images/center/wait.gif" /></p>
    </div>
  </div>
</div>
</body>
</html>
<script type='text/javascript'>

	function seed_settime(){
		seedTimeWait = seedTimeWait-1;
		if(seedTimeWait==0){
			clearInterval(redirectTimeOutId);
				{$target}.opener=null;{$target}.open(' ','_self');{$target}.close();
		}else if(seedTimeWait<0){
			return;
		}
		if(document.getElementById('SeedTimeWaitSet'))
			document.getElementById('SeedTimeWaitSet').innerHTML=seedTimeWait;
	}
	var seedTimeWait={$sec};
	var redirectTimeOutId = setInterval('seed_settime()', 1000);
</script>
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
<style type="text/css">
	html{ font-size:12px; font-family:"宋体"; background:#fafafa;color:#666;}
	body{position:relative;}
	body,div,p,input{ margin:0; padding:0;}
	ul{ list-style:none;}
	img{border:0;}
	#pageshow{width:600px;margin:auto;height:330px;background:url(/images/center/divbg2.jpg) no-repeat bottom center;padding-top:180px; position:relative;}
	#pageshow img {
	   azimuth: expression(
	    this.pngSet?this.pngSet=true:
	           (this.nodeName == "IMG" &&
	                   this.src.toLowerCase().indexOf('.png')>-1?
	                            (this.runtimeStyle.backgroundImage = "none",
	                             this.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + this.src + "', sizingMethod='image')",
	                             this.src = "/images/center/trans.gif")
	                           :(this.origBg = this.origBg?
	                                     this.origBg
	                                    :this.currentStyle.backgroundImage.toString().replace('url("','').replace('")',''),
	                                     this.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + this.origBg + "', sizingMethod='crop')",
	                                    this.runtimeStyle.backgroundImage = "none")),
	           this.pngSet=true);
	  } 
	  .pageshow_div{width:470px;margin:auto;padding:20px 8px 0 0;}
	    .pageshow_title{width:94%;padding:0 3% 9px;hieght:33px; float:left;border-bottom:1px solid #c9c9c9;}
		   .pageshow_title strong{float:left;width:60%;}
		   .pageshow_title span{float:right;width:40%; text-align:right;}
		.pageshow_content{width:94%;padding:10px 3%;border-top:1px solid #fff;float:left;}
		  .pageshow_content p{line-height:30px;font-size:14px;font-weight:bold;}
		  .pageshow_content p.tz{line-height:20px;}
		    .pageshow_content p.tz a{color:#cc0000;}
	  .pageshowbnt{position:absolute;width:92px;hieght:28px;right:250px;bottom:95px;}
	    .pageshowbnt input{border:none;width:92px;height:28px;cursor:pointer;color:#eef0f1;font-weight:bold;background:url(/images/center/bntbg2.gif);}
</style>
</head>
<body id="body1">
<div id="pageshow">
  <div class="pageshow_div">
    <div class="pageshow_title"><strong><img src="/images/center/titlebg.png" /></strong><span><img src="/images/center/logo.png" /></span></div>
    <div class="pageshow_content">
      <p>{$msg}</p>
    </div>
  </div>
  <div class="pageshowbnt"><input type="button" value="返回 Back" onclick="history.back()" /></div>
</div>
</body>
</html>
EOD;
    	die($html);
    }
    
    public static function tip_show($msg,$redirect_url=null,$wait_time=1000,$target='window'){
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
    	}
    	
    	die($html);
    }
    
    public static function turn_url($msg,$redirect_url){
    	if(empty($redirect_url))
    		 $redirect_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['HTTP_REFERER'];
    	
    	echo "<script type='text/javascript'>window.location.href='".$redirect_url."'</script>";
    	exit;
    }
    
}
?>