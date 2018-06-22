var abc;
function LTrim(s) {
  var theInput=s;
  var theLength=theInput.length;
  if (theLength==0) return "";
  var s="",w=0;
  for (var i=0; i<theLength; i++) {
    var theChar=theInput.substring(i,i+1);
    if (theChar!=" ") {
      w=i;
      break;
    }
  }
  return theInput.substring(w,theInput.length);
}

function RTrim(s) {
	  var theInput=s;
	  var theLength=theInput.length;
	  if (theLength==0) return "";
	  var s="",w=0;
	  for (var i=theLength; i>0; i--) {
	    var theChar=theInput.substring(i-1,i);
	    if (theChar!=" ") {
	      w=i;
	      break;
	    }
	  }
	  return theInput.substring(0,w);
	}


function Trim(s) {
	  return LTrim(RTrim(s));
	}

function IsEmpty(s) {
	  var theInput=s;
	  var theLength=theInput.length;
	  if (theInput=="") return true;
	  for (var i=0; i<theLength; i++) {
	     var theChar=theInput.substring(i,i+1);
	     if (theChar!=" ")  return false;
	   }
	  return true;
	}
function removeLastComma(str){
	if(str!=null&&str!=""){
		if(str.substring(str.length-1,str.length)==","){
			str=str.slice(0,-1);
		}
	}
	return str;
}
function removeLastSemicolon(str){
	if(str!=null&&str!=""){
		if(str.substring(str.length-1,str.length)==";"){
			str=str.slice(0,-1);
		}
	}
	return str;
}
function parseDate(dateStr){
	dateStr = dateStr.replace("T"," ");
	var strArray=dateStr.split(" ");
	var strDate=strArray[0].split("-");
	var strTime = new Array("00","00","00");
	if(strArray.length>1 && strArray[1].indexOf(":")!=-1){
		var strTime=strArray[1].split(":");
	}
	var date=new Date(strDate[0],(strDate[1]-parseInt(1)),strDate[2],strTime[0],strTime[1],strTime[2])
	return date;
}
function docWrite(str){
	document.write(str.toString());
}
function formatDataByte(size){
	var sizeStr = "";
	if(isNaN(size)){
		return size.toString();
	}
	var dataUnit=' KB';
	if(size<1048576){
		size = size/1024;
		dataUnit = ' KB';
		sizeStr = Globalize.format(size,"n")+dataUnit;
		return sizeStr;
	}else if(size>=1048576&&size<1073741824){
		size = size/1048576;
		dataUnit = ' MB';
		sizeStr = Globalize.format(size,"n")+dataUnit;
		return sizeStr;
	}else{
		size = size/1073741824;
		dataUnit = ' GB';
		sizeStr = Globalize.format(size,"n")+dataUnit;
		return sizeStr;
	}
}
function replaceSpecialSign(htmlStr){
	if(htmlStr==""){
		return "";
	}
	htmlStr = htmlStr.replace(new RegExp("&lt;","gm"), "<")
		.replace(new RegExp("&gt;","gm"), ">")
		.replace(new RegExp("&amp;","gm"), "&")
		.replace(new RegExp("&quot;","gm"), "\"");
	return htmlStr;
}
/**
 * 将数值四舍五入(保留scale位小数)后格式化成金额形式
 *
 * @param num 数值(Number或者String)
 * @param scale 保留的小数位数(Number)
 * @return 金额格式的字符串,如'1,234,567.45'
 * @type String
 */
function formatCurrency(num, scale) {
	var accur = 100;
	if(scale){
		accur = Math.pow(10, scale);
	}
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
		num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*accur+0.50000000001);
    cents = (num%accur).toString();;
    num = Math.floor(num/accur).toString();
    while(cents.length < scale){
    	cents = "0" + cents;
    }
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
    num = num.substring(0,num.length-(4*i+3))+','+
    num.substring(num.length-(4*i+3));
    return (((sign)?'':'-') + num + '.' + cents);
}

function CenterWindow(link,theWidth,theHeight,theOther){
	if(abc){
		//abc.close();
	}
	var xMax = 640;
	var yMax = 480;
	if (window.screen){
	xMax = window.screen.width;
	yMax = window.screen.height;
	}else{
	if(document.layers){
	xMax = window.outerWidth;
	yMax = window.outerHeight;
	}
	}
	var xOffset = (xMax - 200)/2;
	var yOffset = (yMax - 200)/2;
	xOffset -= (parseInt(theWidth)/2) -100
	yOffset -= (parseInt(theHeight)/2) - 60
	if (theOther == ''){
		abc = window.open('' + link + '','','width=' + theWidth + ',height=' + theHeight + ',screenX='+xOffset+',screenY='+yOffset+', top='+yOffset+',left='+xOffset+'');
	}else{
		abc = window.open('' + link + '','','width=' + theWidth + ',height=' + theHeight + ',screenX='+xOffset+',screenY='+yOffset+', top='+yOffset+',left='+xOffset+',' + theOther+'');
	}
}
function modalDialog(link,theWidth,theHeight,theOther){
	var retval = new Array();
	//alert(theWidth+","+theHeight+","+theOther);
	if (theOther == ''){
	retval = window.showModalDialog('' + link + '', '','dialogWidth:' + theWidth + ';dialogHeight:' + theHeight + '');
	}else{
	retval = window.showModalDialog('' + link + '', '','dialogWidth:' + theWidth + ';dialogHeight:' + theHeight + ';'+ theOther+'');
	}
	if(retval == null){
	//alert('retval is null.');
	}else{
	var result = new Array();
	result = retval[0];
	if(result=='refresh'){
	window.location.reload();
	}else{
	}
	}
}
function bsModalDialog(id, link, args, render){
	$.ajax({
     	type:'post',
		dataType:'json',
		contentType:"application/json",
		data:$.toJSON(args),
		url:link,
		beforeSend: function(){

		},
		error: function(xhr, textStatus, throwError) {
		    alert("error");
		    return false;
		},
		success: function(data){
			var result = render(data);
			$('#'+id).find(".modal-title").html("");
			$('#'+id).find(".modal-body").html("");
			if(data.title != undefined){
				$('#'+id).find(".modal-title").html(result.title);
			}
			if(data.content != undefined){
				$('#'+id).find(".modal-body").html(result.content);
			}
			$('#'+id).modal('show')
		}
    });
}
function getRandomColor(){
	return '#'+('00000'+(Math.random()*0x1000000<<0).toString(16)).slice(-6);
}
function doButtonAction(form,actionURL,confirmStr,windowOpen,theWidth,theHeight,isPrompt,prompt,promptVar,theOther,args,judgeFunc,ajaxFunc,e){
	if(e){
		$(e).attr("disabled", true);
	}
	var promptStr = "";
  	var str = "";
	if(actionURL.indexOf("?")<0){
    	str=actionURL + "?" + "random="+Math.random();
	}else{
    	str=actionURL + "&random="+Math.random();
	}
  	if(judgeFunc!=undefined&&judgeFunc!=''){
		if(!eval(judgeFunc+'()')){
			if(e){
				$(e).attr("disabled", false);
			}
			return false;
		}
	}
  	try{
	  	if(showShade!=undefined && typeof(showShade)=="function"){
	  		showShade();
	  	}
  	}catch(e){}
  	if(args){
  		var arg = args.split(",");
  		$.each(arg ,function(i, a){
  			var arg_ =  a.split(":");
				if(arg_.length>=2){
					if($("input[name='"+arg_[1]+"']").length>0){
						$.each($("input[name='"+arg_[1]+"']") ,function(j, b){
							str+= "&"+arg_[0]+"="+b.value;
			  			});
						$.each($("select[name='"+arg_[1]+"']") ,function(j, b){
			  				str+= "&"+arg_[0]+"="+b.value;
			  			});
					}else{
						str+= "&"+arg_[0]+"="+arg_[1];
					}

				}else{
					if($("input[name='"+a+"']").length>0){
						$.each($("input[name='"+a+"']") ,function(j, b){
							str+= "&"+a+"="+b.value;
			  			});
						$.each($("select[name='"+a+"']") ,function(j, b){
			  				str+= "&"+a+"="+b.value;
			  			});
					}else{
						str+= "&"+a+"="+a;
					}
				}
  		});
  	}
  	if(confirmStr!=""){
    	if(confirm(confirmStr)){
				if(windowOpen=="CW"){
				CenterWindow(str,theWidth,theHeight,theOther);
				if(e){
					$(e).attr("disabled", false);
				}
				return false;
			}else if(windowOpen=="MD"){
				modalDialog(str,theWidth,theHeight,theOther);
				if(e){
					$(e).attr("disabled", false);
				}
   				return false;
			}else if(windowOpen=="HL"){
				window.location.href = str;
				if(e){
					$(e).attr("disabled", false);
				}
   				return false;
			}else{
				if(isPrompt=="true"){
				    promptStr = window.prompt(prompt,"");
				    if (promptStr == null){
				    	if(e){
							$(e).attr("disabled", false);
						}
				        return false;
				    }else if(promptStr.replace(/^\\s+|\\s+$/g,"")==""){
				        alert("Required Desc!");
				        if(e){
							$(e).attr("disabled", false);
						}
				        return false;
				    }
				    str += "&" + promptVar + "=" + promptStr;
				}
				if(form!=undefined&&form!=""){
					if(ajaxFunc!=undefined&&ajaxFunc!=""){
						var options = {
								type:'post',
								dataType:'json',
								contentType:"application/json",
								url:str,
								success: function(data){
									if(e){
										$(e).attr("disabled", false);
									}
									if(hideShade&&typeof(hideShade)=="function"){
										hideShade();
								  	}
									return eval(ajaxFunc+"(data)");
								}
							};
						$("#"+form).ajaxSubmit(options);
						if(e){
							$(e).attr("disabled", false);
						}
						return false;
					}else{
						$("#"+form).attr("action",str);
			            $("#"+form).submit();
			            if(e){
							$(e).attr("disabled", false);
						}
		   				return false;
					}
				}else{
					$.ajax({
					    type:'post',
					    dataType:'json',
					    contentType:"application/json",
					    data:$.toJSON({}),
						url:str,
						success: function(data){
							if(ajaxFunc!=undefined&&ajaxFunc!=""){
								if(e){
									$(e).attr("disabled", false);
								}
								return eval(ajaxFunc+"(data)");
							}
							if(e){
								$(e).attr("disabled", false);
							}
							return;
						}
					});
				}

			}
		}else{
			if(e){
				$(e).attr("disabled", false);
			}
			try{
			if(hideShade&&typeof(hideShade)=="function"){
				hideShade();
		  	}
			}catch(e){}
			return;
		}
	}else{
		if(windowOpen=="CW"){
			CenterWindow(str,theWidth,theHeight,theOther);
			if(e){
				$(e).attr("disabled", false);
			}
			try{
			if(hideShade&&typeof(hideShade)=="function"){
				hideShade();
		  	}
			}catch(e){}
   			return false;
		}else if(windowOpen=="MD"){
			modalDialog(str,theWidth,theHeight,theOther);
			if(e){
				$(e).attr("disabled", false);
			}
			try{
			if(hideShade&&typeof(hideShade)=="function"){
				hideShade();
		  	}
			}catch(e){}
   			return false;
		}else if(windowOpen=="HL"){
			window.location.href = str;
			if(e){
				$(e).attr("disabled", false);
			}
			try{
			if(hideShade&&typeof(hideShade)=="function"){
				hideShade();
		  	}
			}catch(e){}
			return false;
		}else{
			if(isPrompt=="true"){                  // 判断是否Explorer用户提示
				promptStr = window.prompt(prompt,"");
				if (promptStr == null){
					if(e){
						$(e).attr("disabled", false);
					}
					return false;
				}else if(promptStr.replace(/^\\s+|\\s+$/g,"")==""){
					alert("Required Desc!");
					if(e){
						$(e).attr("disabled", false);
					}
					return false;
				}
				str += "&" + promptVar + "=" + promptStr;
			}
			if(form!=undefined&&form!=""){
				if(ajaxFunc!=undefined&&ajaxFunc!=""){
					var options = {
							type:'post',
							dataType:'json',
							contentType:"application/json",
							url:str,
							success: function(data){
								if(e){
									$(e).attr("disabled", false);
								}
								if(hideShade&&typeof(hideShade)=="function"){
									hideShade();
							  	}
								return eval(ajaxFunc+"(data)");
							}
						};
					$("#"+form).ajaxSubmit(options);
					if(e){
						$(e).attr("disabled", false);
					}
					try{
					if(hideShade&&typeof(hideShade)=="function"){
						hideShade();
				  	}
					}catch(e){}
					return false;
				}else{
					$("#"+form).attr("action",str);
		            $("#"+form).submit();
		            if(e){
						$(e).attr("disabled", false);
					}
		            try{
		            if(hideShade&&typeof(hideShade)=="function"){
						hideShade();
				  	}
		            }catch(e){}
	   				return false;
				}
			}else{
				$.ajax({
				    type:'post',
				    dataType:'json',
				    contentType:"application/json",
				    data:$.toJSON({}),
					url:str,
					success: function(data){
						if(ajaxFunc!=undefined&&ajaxFunc!=""){
							if(e){
								$(e).attr("disabled", false);
							}
							try{
							if(hideShade&&typeof(hideShade)=="function"){
								hideShade();
						  	}
							}catch(e){}
							return eval(ajaxFunc+"(data)");
						}
						if(e){
							$(e).attr("disabled", false);
						}
						try{
						if(hideShade&&typeof(hideShade)=="function"){
							hideShade();
					  	}
						}catch(e){}
						return;
					}
				});
			}

		}
	}
}
function showShade() {
	if($("#pageshade")[0]){
		$("#pageshade").show();
		$("#pageshadeimg").show();
	}
	return true;
}
function hideShade() {
	if($("#pageshade")[0]){
		$("#pageshade").hide();
		$("#pageshadeimg").hide();
	}
}
/* 扩展格式化时间 */
Date.prototype.format = function(fmt) {
    var o = {
        "M+" : this.getMonth()+1,                 //月份
        "d+" : this.getDate(),                    //日
        "h+" : this.getHours(),                   //小时
        "m+" : this.getMinutes(),                 //分
        "s+" : this.getSeconds(),                 //秒
        "q+" : Math.floor((this.getMonth()+3)/3), //季度
        "S"  : this.getMilliseconds()             //毫秒
    };
    if(/(y+)/.test(fmt)) {
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
}
