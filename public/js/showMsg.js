if(document.getElementById('msg')){
    var msg = document.getElementById('msg');
    if(msg.parentNode){
        msg.parentNode.removeChild(msg);
    }
}
document.writeln("<style type=\"text\/css\">");
document.writeln("");
document.writeln(".window {");
document.writeln("	width:80%;");
document.writeln("	position:fixed;");
document.writeln("	display:none;");
document.writeln("	bottom:45%;");
document.writeln("	left:10%;");
document.writeln("	 z-index:9999;");
document.writeln("	padding:2px;");
document.writeln("	border-radius:0.6em;");
document.writeln("	-webkit-border-radius:0.6em;");
document.writeln("	-moz-border-radius:0.6em;");
document.writeln("	background-color: #ccc;");
document.writeln("	-webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);");
document.writeln("	-moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);");
document.writeln("	-o-box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);");
document.writeln("	box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);");
document.writeln("	font:14px\/1.5 Microsoft YaHei,Helvitica,Verdana,Arial,san-serif;");
document.writeln("}");
document.writeln(".window .title {");
document.writeln("	");
document.writeln("	background-color: #A3A2A1;");
document.writeln("	line-height: 26px;");
document.writeln("    padding: 5px 5px 5px 10px;");
document.writeln("	color:#ffffff;");
document.writeln("	font-size:16px;");
document.writeln("	border-radius:0.5em 0.5em 0 0;");
document.writeln("	-webkit-border-radius:0.5em 0.5em 0 0;");
document.writeln("	-moz-border-radius:0.5em 0.5em 0 0;");
document.writeln("	background-image: -webkit-gradient(linear, left top, left bottom, from( #585858 ), to( #565656 )); \/* Saf4+, Chrome *\/");
document.writeln("	background-image: -webkit-linear-gradient(#585858, #565656); \/* Chrome 10+, Saf5.1+ *\/");
document.writeln("	background-image:    -moz-linear-gradient(#585858, #565656); \/* FF3.6 *\/");
document.writeln("	background-image:     -ms-linear-gradient(#585858, #565656); \/* IE10 *\/");
document.writeln("	background-image:      -o-linear-gradient(#585858, #565656); \/* Opera 11.10+ *\/");
document.writeln("	background-image:         linear-gradient(#585858, #565656);");
document.writeln("	");
document.writeln("}");
document.writeln(".window .content {");
document.writeln("	\/*min-height:100px;*\/");
document.writeln("	overflow:auto;");
document.writeln("	padding:10px;");
document.writeln("	background: #3f3f3f;");
document.writeln("    color: #FFFFFF;");
//document.writeln("    text-shadow: 0 1px 0 #AAAAAA;");
document.writeln("	border-radius: 0.4em 0.4em 0.4em 0.4em;");
document.writeln("	-webkit-border-radius: 0.4em 0.4em 0.4em 0.4em;");
document.writeln("	-moz-border-radius: 0.4em 0.4em 0.4em 0.4em;");
document.writeln("}");
document.writeln(".window #txt {");
document.writeln("	min-height:30px;text-align:center;font-size:16px;");
document.writeln("}");
document.writeln(".window .txtbtn {");
document.writeln("	");
document.writeln("	background: #f1f1f1;");
document.writeln("	background-image: -webkit-gradient(linear, left top, left bottom, from( #DCDCDC ), to( #f1f1f1 )); \/* Saf4+, Chrome *\/");
document.writeln("	background-image: -webkit-linear-gradient( #ffffff , #DCDCDC ); \/* Chrome 10+, Saf5.1+ *\/");
document.writeln("	background-image:    -moz-linear-gradient( #ffffff , #DCDCDC ); \/* FF3.6 *\/");
document.writeln("	background-image:     -ms-linear-gradient( #ffffff , #DCDCDC ); \/* IE10 *\/");
document.writeln("	background-image:      -o-linear-gradient( #ffffff , #DCDCDC ); \/* Opera 11.10+ *\/");
document.writeln("	background-image:         linear-gradient( #ffffff , #DCDCDC );");
document.writeln("	border: 1px solid #CCCCCC;");
document.writeln("	border-bottom: 1px solid #B4B4B4;");
document.writeln("	color: #555555;");
document.writeln("	font-weight: bold;");
document.writeln("	text-shadow: 0 1px 0 #FFFFFF;");
document.writeln("	border-radius: 0.6em 0.6em 0.6em 0.6em;");
document.writeln("	display: block;");
document.writeln("	width: 100%;");
document.writeln("	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);");
document.writeln("	text-overflow: ellipsis;");
document.writeln("	white-space: nowrap;");
document.writeln("	cursor: pointer;");
document.writeln("	text-align: msg;");
document.writeln("	font-weight: bold;");
document.writeln("	font-size: 18px;");
document.writeln("	padding:6px;");
document.writeln("	margin:10px 0 0 0;");
document.writeln("}");
document.writeln(".window .txtbtn:visited {");
document.writeln("	background-image: -webkit-gradient(linear, left top, left bottom, from( #ffffff ), to( #cccccc )); \/* Saf4+, Chrome *\/");
document.writeln("	background-image: -webkit-linear-gradient( #ffffff , #cccccc ); \/* Chrome 10+, Saf5.1+ *\/");
document.writeln("	background-image:    -moz-linear-gradient( #ffffff , #cccccc ); \/* FF3.6 *\/");
document.writeln("	background-image:     -ms-linear-gradient( #ffffff , #cccccc ); \/* IE10 *\/");
document.writeln("	background-image:      -o-linear-gradient( #ffffff , #cccccc ); \/* Opera 11.10+ *\/");
document.writeln("	background-image:         linear-gradient( #ffffff , #cccccc );");
document.writeln("}");
document.writeln(".window .txtbtn:hover {");
document.writeln("	background-image: -webkit-gradient(linear, left top, left bottom, from( #ffffff ), to( #cccccc )); \/* Saf4+, Chrome *\/");
document.writeln("	background-image: -webkit-linear-gradient( #ffffff , #cccccc ); \/* Chrome 10+, Saf5.1+ *\/");
document.writeln("	background-image:    -moz-linear-gradient( #ffffff , #cccccc ); \/* FF3.6 *\/");
document.writeln("	background-image:     -ms-linear-gradient( #ffffff , #cccccc ); \/* IE10 *\/");
document.writeln("	background-image:      -o-linear-gradient( #ffffff , #cccccc ); \/* Opera 11.10+ *\/");
document.writeln("	background-image:         linear-gradient( #ffffff , #cccccc );");
document.writeln("}");
document.writeln(".window .txtbtn:active {");
document.writeln("	background-image: -webkit-gradient(linear, left top, left bottom, from( #cccccc ), to( #ffffff )); \/* Saf4+, Chrome *\/");
document.writeln("	background-image: -webkit-linear-gradient( #cccccc , #ffffff ); \/* Chrome 10+, Saf5.1+ *\/");
document.writeln("	background-image:    -moz-linear-gradient( #cccccc , #ffffff ); \/* FF3.6 *\/");
document.writeln("	background-image:     -ms-linear-gradient( #cccccc , #ffffff ); \/* IE10 *\/");
document.writeln("	background-image:      -o-linear-gradient( #cccccc , #ffffff ); \/* Opera 11.10+ *\/");
document.writeln("	background-image:         linear-gradient( #cccccc , #ffffff );");
document.writeln("	border: 1px solid #C9C9C9;");
document.writeln("	border-top: 1px solid #B4B4B4;");
document.writeln("	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) inset;");
document.writeln("}");
document.writeln("");
document.writeln(".window .title .close {");
document.writeln("	float:right;");
document.writeln("	width:26px;");
document.writeln("	height:26px;");
document.writeln("	display:block;	");
document.writeln("}");
document.writeln("<\/style>");
document.writeln("<div class=\"window\" id=\"msg\">");
document.writeln(" <div class=\"content\">");
document.writeln("    <div id=\"txt\"><\/div>");
document.writeln(" <\/div>");
document.writeln(" <div id=\"SeedMsgTxt\"><\/div>");
document.writeln("<\/div>");


function setOpacity(obj, value) {
   // if ($.browser.msie) {
    //    if (value == 100) {
    //        obj.style.filter = "";
    //    } else {
    //        obj.style.filter = "alpha(opacity="+value+")";
   //     }
  //  } else if($.browser.msie) {
  //      obj.style.MozOpacity = value/100;
   // } else{
        obj.style.opacity = value/100;
   // }
}

function changeOpacity(obj, startValue, endValue, step, speed) {
    if (step>0 && startValue<endValue || step<0 && startValue>endValue) {
        setOpacity(obj,endValue);
        return;
    }
    setOpacity(obj,startValue);
    setTimeout(function () {
        changeOpacity(obj,startValue-step,endValue,step,speed);
    },speed);
}

var seedTimeWait,settimeIntervalId = 0;
function seed_settime(u){
    seedTimeWait = seedTimeWait-1;
    if(seedTimeWait<=0){
        clearInterval(settimeIntervalId);
        window.location.href = u;
    }
}

function closeMsg(a){
    var step = 30, speed = 60;
    var msg = document.getElementById(a);
    changeOpacity(msg,100,0,step,speed);
    window.setTimeout(function(){document.getElementById(a).style.display = 'none';document.getElementById('txt').innerHTML = '';},(100/step)*speed);
}

function opacityshowMsg(a){
    document.getElementById(a).style.display = 'block';
    var step = -30, speed = 60;
    var msg = document.getElementById(a);
    changeOpacity(msg,0,100,step,speed);
}


function showMsg(a,o,t,u){
    document.getElementById('txt').innerHTML = a;
    opacityshowMsg(o);
    var step =10, speed = 60;
    if(typeof t === 'undefined')t = 2000;
    setTimeout("closeMsg('"+o+"')",t);
    if((typeof(u) !== 'undefined')){
        seedTimeWait=t/1000;
        settimeIntervalId =setInterval('seed_settime("'+u+'")', 1000);
    }
}


function handleMsg(d,o){

}

function waitMsg(a,o){
    document.getElementById('txt').innerHTML = a;
    opacityshowMsg(o);
}