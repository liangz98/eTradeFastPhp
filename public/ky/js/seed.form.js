function SeedMsgBox(str){
	if(document.getElementById("SeedMsgBoxDiv")){
		document.body.removeChild(document.getElementById("SeedMsgBoxDiv"));
	}
	
	var sWidth,sHeight,dWidth,dHeight;
	sWidth=window.screen.width;
	if (window.navigator.userAgent.indexOf("MSIE")>=1) {
		sHeight=document.body.clientHeight;
		if(sHeight<(window.screen.availHeight-230))sHeight=window.screen.availHeight-230;
	}else{
		sHeight=document.documentElement.scrollHeight;
	}

	if (document.body.scrollTop)
	{
	dHeight=document.body.scrollTop+((window.screen.availHeight-320)/2);
	}
	if (document.documentElement.scrollTop)
	{
	dHeight=document.documentElement.scrollTop+((window.screen.availHeight-320)/2);
	}
	dWidth=((sWidth-448)/2);
	var msgObj=document.createElement("div");
	msgObj.setAttribute("id","SeedMsgBoxDiv");
	document.body.appendChild(msgObj);
	document.getElementById("SeedMsgBoxDiv").innerHTML="";
	
	var bgObj=document.createElement("div");
	bgObj.setAttribute("id","SeedMsgBgDiv");
	bgObj.style.position="absolute";
	bgObj.style.filter="Alpha(Opacity=60);-moz-opacity:0.6;";
	bgObj.style.opacity="0.48";
	bgObj.style.width="100%";
	bgObj.style.height=sHeight+"px";
	bgObj.style.top="0";
	bgObj.style.left="0";
	bgObj.style.background="#000";
	document.getElementById("SeedMsgBoxDiv").appendChild(bgObj);
	
	var tipbigdivObj=document.createElement("div");
	tipbigdivObj.setAttribute("id","SeedMsgDiv");
	tipbigdivObj.setAttribute("className","SeedMsgDiv");	
	tipbigdivObj.setAttribute("class","SeedMsgDiv");
	tipbigdivObj.innerHTML='<div class="close"><a href="javascript:void(0)" onclick="CloseSeedMsgBox()"><img src="/images/center/close.gif" /></a></div><div class="tips_text"><p style="font-weight:bold;">温馨提示：</p><p id="SeedMsgTxt">'+str+'</p></div>';
	tipbigdivObj.style.top=dHeight+"px";
	tipbigdivObj.style.left=dWidth+"px";
	document.getElementById("SeedMsgBoxDiv").appendChild(tipbigdivObj);
	
	var md=false,mobj,ox,oy;
	document.onmousedown=function(ev){
		var ev=ev||window.event;
		var evt=ev.srcElement||ev.target;
		if(typeof(evt.getAttribute("canmove"))=="undefined"){
			return;
		}
		if(evt.getAttribute("canmove")){
			md = true;
			mobj = document.getElementById(evt.getAttribute("forid"));
			ox = mobj.offsetLeft - ev.clientX;
			oy = mobj.offsetTop - ev.clientY;
		}
	}
	document.onmouseup = function(){md=false;}
	document.onmousemove = function(ev){
		var ev=ev||window.event;
		if(md){
			mobj.style.left= (ev.clientX + ox)+"px";
			mobj.style.top= (ev.clientY + oy)+"px";
		}
	}
	var sels = document.getElementsByTagName("SELECT");
	for(var i=0; i<sels.length; i++){
		sels[i].style.visibility = 'hidden';
	}
	this.CloseSeedMsgBox = function(){
		document.body.removeChild(document.getElementById("SeedMsgBoxDiv"));
		for(var i=0; i<sels.length; i++){
			sels[i].style.visibility = '';
		}
	}
}