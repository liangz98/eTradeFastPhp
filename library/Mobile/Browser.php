<?php
class Mobile_Browser {
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

	public static function view_page($url,$timeOut=30,$header=0,$cookie=""){
		$ch = curl_init();
		if(!$ch)return null;
		$userAgent=$_SERVER['HTTP_USER_AGENT'];
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt ($ch, CURLOPT_HEADER, $header);
		if(!empty($cookie))curl_setopt ($ch, CURLOPT_COOKIE,$cookie);
		curl_setopt	($ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_TIMEOUT, $timeOut);
		$content = curl_exec ($ch);
		if (curl_errno($ch)) {
			print curl_error($ch);
		} else {
			curl_close($ch);
		}
		return $content;
	}

    public static function error($msg){
$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport" />
<title>提示</title>
</head>
<body>
<div id="SeedMsgTxt">
<div class="SeedMsgDiv" id="SeedMsgDiv">
<style type="text/css">
a,a:link,a:visited{color:#333;cursor:pointer;text-decoration:none;}
.SeedMsgDiv{position:absolute;left:4%;top:40%;width:90%;background:#fff;border:2px solid #ccc;border-radius:10px;background:#c6c6c6;background:-webkit-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-o-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-moz-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);box-shadow:1px 1px 1px #616161;z-index:999;}
.Seed_text{padding:10px 5%;border-bottom:1px solid #a7a7a7;}
.Seed_text dt{font:bold 20px/36px "Microsoft YaHei";}
.Seed_text dd{line-height:30px;color:#676767;padding-top:5px;}
.Seed_text dd img{width:60px;height:60px;border-radius:50%;float:left;margin-right:10px;padding:3px;background:#fff;border:2px solid #a0a4a5;}
.Seed_text dd p strong{font:16px/36px "Microsoft YaHei";color:#000;}
.Seed_text dd p span{text-shadow:1px 1px 1px #fff;}
.Seed_bnt{padding:10px 0; text-align:center;}
.Seed_bnt a{width:60%;margin:0 1%;height:40px;display:inline-block;font:16px/40px "Microsoft YaHei";border-radius:8px;}
.tips_bnt1{background:#d5d1d1;background:-webkit-linear-gradient(top,#d5d1d1 0%,#d5d5d5 100%);background:-o-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);background:-moz-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);border:1px solid #787878;}
.tips_bnt2{background:#4bc652;background:-webkit-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-o-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-moz-linear-gradient(top,#4bc652 0%,#077b13 100%);color:#fff;border:1px solid #425b46;}
</style>
<dl class="Seed_text">
    <dd>
        <p><strong>'.$msg.'</strong></p>
    </dd>
</dl>
</div>
</div>
</body>
</html>';
die($html);
    }

    public static function tip_redirect($msg,$redirect_url=null,$target='window'){
$html = '
<script type="text/javascript">
if(!window.jQuery){
  var sss = document.createElement("script");
  sss.setAttribute("src","/js/jquery-1.7.2.min.js");
  document.getElementsByTagName("head")[0].appendChild(sss);
}
';
$html .= 'location.href=\''.$redirect_url.'\'';
$html .= '</script>';
die($html);
}
public static function show_carts($num=0){
	$html = '<script type="text/javascript">';
    $html .= "try{
                  if(document.getElementById('total_carts')){
                  	document.getElementById('total_carts').innerHTML='{$num}';
                  	document.getElementById('total_carts1').innerHTML='{$num}';
                  }";
    $html .= '}catch(e){}</script>';
    echo $html;
}

//加入购物车成功特效。by:ll 2015.3.23 18:34
public static function add_carts_success(){
    $html = '<script type="text/javascript">';
    $html .= 'try{
        var cartimg = $(".box_swipe li:first").html();
        $("#content").append(\'<div class="cartimg">\'+cartimg+\'</div>\');
        $(".cartimg").animate({ 
                width: "10px",
                height: "10px",
                top: "0%",
                left: "95%",
        }, 1000);
        setTimeout(function(){$("div").remove(".cartimg");},1000);
        ';
    $html .= '}catch(e){}</script>';
    echo $html;
}

public static function tip_showMsg($msg,$redirect_url=null,$wait_time=1500,$target='window'){
    $html = '<script type="text/javascript">';
    $html .= "try{
                  if(document.getElementById('msg') && typeof showMsg === 'function'){";
    $html .= $redirect_url ? "showMsg('{$msg}','msg','{$wait_time}','{$redirect_url}');" : "showMsg('{$msg}','msg','{$wait_time}');";
    $html .= "}else{
                  var ss = document.createElement('script');
                  ss.setAttribute('src','/js/showMsg.js');
                  document.body.appendChild(ss);
                  window.setTimeout(function(){";
    $html .= $redirect_url ? "showMsg('{$msg}','msg','{$wait_time}','{$redirect_url}');" : "showMsg('{$msg}','msg','{$wait_time}');";
    $html .= "},300);
              }";
    $html .= '}catch(e){}</script>';
    die($html);
}

//添加一个新的超库存提示并回退函数。by:ll 2015.3.26 11:06
public static function tip_showMsg_black($msg,$id=null,$goods_number=null,$goods_amount=null,$redirect_url=null,$wait_time=1500,$target='window'){
    $html = '<script type="text/javascript">';
    $html .= "try{
                $('#goods_number_{$id}').val({$goods_number});
                $('#goods_price_{$id}').html('<em id=\"goods_price_{$id}\">¥{$goods_amount}<\/em>');
                  if(document.getElementById('msg') && typeof showMsg === 'function'){";
    $html .= $redirect_url ? "showMsg('{$msg}','msg','{$wait_time}','{$redirect_url}');" : "showMsg('{$msg}','msg','{$wait_time}');";
    $html .= "}else{
                  var ss = document.createElement('script');
                  ss.setAttribute('src','/js/showMsg.js');
                  document.body.appendChild(ss);
                  window.setTimeout(function(){";
    $html .= $redirect_url ? "showMsg('{$msg}','msg','{$wait_time}','{$redirect_url}');" : "showMsg('{$msg}','msg','{$wait_time}');";
    $html .= "},300);
              }";
    $html .= '}catch(e){}</script>';
    die($html);
}

//添加一个新的超库存提示并回退函数。by:jacent 2015.07.6
public static function tip_showMsg_icartblack($msg,$id=null,$goods_number=null,$goods_amount=null,$goods_integral=null,$redirect_url=null,$wait_time=1500,$target='window'){
    $html = '<script type="text/javascript">';
    $html .= "try{
                $('#goods_number_{$id}').val({$goods_number});
                $('#show_shop_price_{$id}').html('<em id=\"show_shop_price_{$id}\">¥{$goods_amount}<\/em>');
                $('#show_integral_price_{$id}').html('<em id=\"show_integral_price_{$id}\">¥{$goods_integral}<\/em>');
                  if(document.getElementById('msg') && typeof showMsg === 'function'){";
    $html .= $redirect_url ? "showMsg('{$msg}','msg','{$wait_time}','{$redirect_url}');" : "showMsg('{$msg}','msg','{$wait_time}');";
    $html .= "}else{
                  var ss = document.createElement('script');
                  ss.setAttribute('src','/js/showMsg.js');
                  document.body.appendChild(ss);
                  window.setTimeout(function(){";
    $html .= $redirect_url ? "showMsg('{$msg}','msg','{$wait_time}','{$redirect_url}');" : "showMsg('{$msg}','msg','{$wait_time}');";
    $html .= "},300);
              }";
    $html .= '}catch(e){}</script>';
    die($html);
}

public static function tip_showMsg_redirect($msg,$redirect_url=null,$wait_time=1000,$target='window'){
    $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport" />
    <title>提示</title>
    </head>';
    $html .= '<body>
    <script type="text/javascript" src="/js/showMsg.js"></script>
    <script type="text/javascript">';
    $html .= $redirect_url ? "showMsg(\"{$msg}\",'msg','{$wait_time}','{$redirect_url}');" : "showMsg(\"{$msg}\",'msg','{$wait_time}');";
    $html .= '</script>
    </body>
    </html>';
    die($html);
}

    public static function tip_show($msg,$redirect_url=null,$wait_time=1000,$target='window'){
$html = '<div class="SeedMsgDiv" id="SeedMsgDiv">
<style type="text/css">
a,a:link,a:visited{color:#333;cursor:pointer;text-decoration:none;}
.SeedMsgDiv{position:absolute;left:4%;top:40%;width:90%;background:#fff;border:2px solid #ccc;border-radius:10px;background:#c6c6c6;background:-webkit-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-o-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-moz-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);box-shadow:1px 1px 1px #616161;z-index:999;}
.Seed_text{padding:10px 5%;border-bottom:1px solid #a7a7a7;}
.Seed_text dt{font:bold 20px/36px "Microsoft YaHei";}
.Seed_text dd{line-height:30px;color:#676767;padding-top:5px;}
.Seed_text dd img{width:60px;height:60px;border-radius:50%;float:left;margin-right:10px;padding:3px;background:#fff;border:2px solid #a0a4a5;}
.Seed_text dd p strong{font:16px/36px "Microsoft YaHei";color:#000;}
.Seed_text dd p span{text-shadow:1px 1px 1px #fff;}
.Seed_bnt{padding:10px 0; text-align:center;}
.Seed_bnt a{width:60%;margin:0 1%;height:40px;display:inline-block;font:16px/40px "Microsoft YaHei";border-radius:8px;}
.tips_bnt1{background:#d5d1d1;background:-webkit-linear-gradient(top,#d5d1d1 0%,#d5d5d5 100%);background:-o-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);background:-moz-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);border:1px solid #787878;}
.tips_bnt2{background:#4bc652;background:-webkit-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-o-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-moz-linear-gradient(top,#4bc652 0%,#077b13 100%);color:#fff;border:1px solid #425b46;}
</style>
<dl class="Seed_text">
    <dd>
        <p><strong>'.$msg.'</strong></p>
    </dd>
</dl>
<div class="Seed_bnt">
   <a class="tips_bnt1" href="javascript:;" onclick="document.getElementById(\'SeedMsgDiv\').style.display = \'none\';">确 定</a>
</div>
</div>
<script type="text/javascript">
if(!window.jQuery){
  var sss = document.createElement("script");
  sss.setAttribute("src","/js/jquery-1.7.2.min.js");
  document.getElementsByTagName("head")[0].appendChild(sss);
}
var windowHeight;
var popHeight;
windowHeight=$(window).height();
popHeight=$(".SeedMsgDiv").height();
var popY=(windowHeight-popHeight)/2;
$(".SeedMsgDiv").css("top",(popY + $(window).scrollTop()) + "px");
';
if($redirect_url){
   $html .= 'setTimeout("location.href=\''.$redirect_url.'\'",'.$wait_time.');';
}
$html .= '</script>';
die($html);
}

    public static function redirect($msg,$redirect_url=null,$wait_time=1000,$target='window'){
    	if(empty($redirect_url))
    		 $redirect_url = "http://".$_SERVER['HTTP_HOST'];
    	header("Location:".$redirect_url);
    	exit;
$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport" />
<title>提示</title>
</head>';
$html .= '<body>
<div id="SeedMsgTxt">
<div class="SeedMsgDiv" id="SeedMsgDiv">
<style type="text/css">
a,a:link,a:visited{color:#333;cursor:pointer;text-decoration:none;}
.SeedMsgDiv{position:absolute;left:4%;top:40%;width:90%;background:#fff;border:2px solid #ccc;border-radius:10px;background:#c6c6c6;background:-webkit-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-o-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-moz-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);box-shadow:1px 1px 1px #616161;z-index:999;}
.Seed_text{padding:10px 5%;border-bottom:1px solid #a7a7a7;}
.Seed_text dt{font:bold 20px/36px "Microsoft YaHei";}
.Seed_text dd{line-height:30px;color:#676767;padding-top:5px;}
.Seed_text dd img{width:60px;height:60px;border-radius:50%;float:left;margin-right:10px;padding:3px;background:#fff;border:2px solid #a0a4a5;}
.Seed_text dd p strong{font:16px/36px "Microsoft YaHei";color:#000;}
.Seed_text dd p span{text-shadow:1px 1px 1px #fff;}
.Seed_bnt{padding:10px 0; text-align:center;}
.Seed_bnt a{width:60%;margin:0 1%;height:40px;display:inline-block;font:16px/40px "Microsoft YaHei";border-radius:8px;}
.tips_bnt1{background:#d5d1d1;background:-webkit-linear-gradient(top,#d5d1d1 0%,#d5d5d5 100%);background:-o-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);background:-moz-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);border:1px solid #787878;}
.tips_bnt2{background:#4bc652;background:-webkit-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-o-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-moz-linear-gradient(top,#4bc652 0%,#077b13 100%);color:#fff;border:1px solid #425b46;}
</style>
<dl class="Seed_text">
    <dd>
        <p><strong>'.$msg.'</strong></p>
    </dd>
</dl>
<div class="Seed_bnt">
   <a class="tips_bnt1" href="javascript:;" onclick="document.getElementById(\'SeedMsgDiv\').style.display = \'none\';">确 定</a>
</div>
</div>
</div>
<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
var windowHeight;
var popHeight;
windowHeight=$(window).height();
popHeight=$(".SeedMsgDiv").height();
var popY=(windowHeight-popHeight)/2;
$(".SeedMsgDiv").css("top",(popY + $(window).scrollTop()) + "px");
';
if($redirect_url){
    $html .= 'setTimeout("location.href=\''.$redirect_url.'\'",'.$wait_time.');';
}
$html .= '</script>
</body>
</html>';
die($html);
    }

    public static function from_wechat(){
    	//目前判断是否为手机客户端
    	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	    $iphone = (strpos($agent, 'iphone')) ? true : false;
	    $ipad = (strpos($agent, 'ipad')) ? true : false;
	    $android = (strpos($agent, 'android')) ? true : false;
	    if($iphone)return "iphone";
	    if($android)return "android";
	    if($ipad)return "ipad";

	    return false;
	}


        /**
        * 获取客户端是否以Ajax方式请求
        * @return bool
        */
       public static function isAjaxRequest()
       {
           if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
               return !strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest');
           }
           return false;
       }

    public static function tipRedirect($msg,$redirect_url=null,$wait_time=1000,$target='window'){
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport" />
                <title>提示</title>
                </head>';
        $html .= '<body>
                <div id="SeedMsgTxt">
                <div class="SeedMsgDiv" id="SeedMsgDiv">
                <style type="text/css">
                a,a:link,a:visited{color:#333;cursor:pointer;text-decoration:none;}
                .SeedMsgDiv{position:absolute;left:4%;top:40%;width:90%;background:#fff;border:2px solid #ccc;border-radius:10px;background:#c6c6c6;background:-webkit-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-o-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);background:-moz-linear-gradient(top,#d5d1d1 0%,#c6c6c6 60%,#a9a9a9 100%);box-shadow:1px 1px 1px #616161;z-index:999;}
                .Seed_text{padding:10px 5%;border-bottom:1px solid #a7a7a7;}
                .Seed_text dt{font:bold 20px/36px "Microsoft YaHei";}
                .Seed_text dd{line-height:30px;color:#676767;padding-top:5px;}
                .Seed_text dd img{width:60px;height:60px;border-radius:50%;float:left;margin-right:10px;padding:3px;background:#fff;border:2px solid #a0a4a5;}
                .Seed_text dd p strong{font:16px/36px "Microsoft YaHei";color:#000;}
                .Seed_text dd p span{text-shadow:1px 1px 1px #fff;}
                .Seed_bnt{padding:10px 0; text-align:center;}
                .Seed_bnt a{width:60%;margin:0 1%;height:40px;display:inline-block;font:16px/40px "Microsoft YaHei";border-radius:8px;}
                .tips_bnt1{background:#d5d1d1;background:-webkit-linear-gradient(top,#d5d1d1 0%,#d5d5d5 100%);background:-o-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);background:-moz-linear-gradient(top,#fefefe 0%,#d5d5d5 100%);border:1px solid #787878;}
                .tips_bnt2{background:#4bc652;background:-webkit-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-o-linear-gradient(top,#4bc652 0%,#077b13 100%);background:-moz-linear-gradient(top,#4bc652 0%,#077b13 100%);color:#fff;border:1px solid #425b46;}
                </style>
                <dl class="Seed_text">
                    <dd>
                        <p><strong>'.$msg.'</strong></p>
                    </dd>
                </dl>
                <div class="Seed_bnt">
                   <a class="tips_bnt1" href="javascript:;" onclick="document.getElementById(\'SeedMsgDiv\').style.display = \'none\';">确 定</a>
                </div>
                </div>
                </div>
                <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
                <script type="text/javascript">
                var windowHeight;
                var popHeight;
                windowHeight=$(window).height();
                popHeight=$(".SeedMsgDiv").height();
                var popY=(windowHeight-popHeight)/2;
                $(".SeedMsgDiv").css("top",(popY + $(window).scrollTop()) + "px");';
        if($redirect_url){
            $html .= 'setTimeout("location.href=\''.$redirect_url.'\'",'.$wait_time.');';
        }
        $html .= '</script></body></html>';
        die($html);
    }

}
?>