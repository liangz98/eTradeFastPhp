/*
 @ 文本框插入表情插件
*/
$(function() {
	$.fn.smohanfacebox = function(options) {
		var defaults = {
		Event : "click", //响应事件		
		divid : "Smohan_FaceBox", //表单ID（textarea外层ID）
		textid : "TextArea", //文本框ID
		imgpath : "images/face/",
                SmohanFaceBox:"SmohanFaceBox",
		textObj :{},
		etotal : 60,
		cols : 15
		};
		var options = $.extend(defaults,options);
		var $btn = $(this);//取得触发事件的ID
		
		//创建表情框
		var faceimg = '';
	    for(var i=0;i<options.etotal;i++){  //通过循环创建60个表情，可扩展
                 var tips = (typeof options.textObj[i] == 'undefined' ?'':options.textObj[i]);
		 faceimg+='<li><a href="javascript:void(0)"><img title="'+tips+'" alt="'+tips+'" src="'+options.imgpath+(i)+'.gif" face="'+(i)+'"/></a></li>';
		 };
		$("#"+options.divid).prepend("<div id='"+options.SmohanFaceBox+"'><span class='Corner'></span><div class='Content'><h3><span>常用表情</span><a class='close' title='关闭'></a></h3><ul>"+faceimg+"</ul></div></div>");
	        $('#'+options.SmohanFaceBox).css("display",'none');//创建完成后先将其隐藏
		//创建表情框结束
		
		var $facepic = $("#"+options.SmohanFaceBox+" li img");
		//BTN触发事件，显示或隐藏表情层
		$btn.bind(options.Event,function(e) {
			if($('#'+options.SmohanFaceBox).is(":hidden")){
                            $('#'+options.SmohanFaceBox).css({'top':($(this).offset().top - 90)+'px','margin-left':($(this).offset().left)+'px'});
                            $('#'+options.SmohanFaceBox).show(360);
			$btn.addClass('in');
			}else{
                            $('#'+options.SmohanFaceBox).hide(360);
                            $btn.removeClass('in');
                            }
			});
		//插入表情
		$facepic.unbind().click(function(){
		     $('#'+options.SmohanFaceBox).hide(360);
			 //$("#"+options.textid).focus();
			 //$("#"+options.textid).val($("#"+options.textid).val()+$(this).attr("face"));
			 $("#"+options.textid).unbind().insertContent(typeof options.textObj[$(this).attr("face")] == 'undefined' ? '<emt>' + $(this).attr("face") + '</emt>' : options.textObj[$(this).attr("face")]);
			 $btn.removeClass('in');
			});			
		//关闭表情层
		$('#'+options.SmohanFaceBox+' h3 a.close').click(function() {
			$('#'+options.SmohanFaceBox).hide(360);
			 $btn.removeClass('in');
			});	
		//当鼠标移开时，隐藏表情层，如果不需要，可注释掉
		/* $('#SmohanFaceBox').mouseout(function(){
			 $('#SmohanFaceBox').hide(560);
			 $btn.removeClass('in');
			 });*/
                
		//设置自动适应样式
		if(options.cols > options.etotal)options.cols = options.etotal;
		$('#'+options.SmohanFaceBox+' .Content,#'+options.SmohanFaceBox+' .Content h3').css('width',(options.cols*26+38)+'px');
		$('#'+options.SmohanFaceBox+' .Content').css("height",(Math.ceil(options.etotal/options.cols)*26 + 55)+'px');
		
  };  
  
  // 【漫画】 光标定位插件
	$.fn.extend({  
		insertContent : function(myValue, t) {  
			var $t = $(this)[0];  
			if (document.selection) {  
				this.focus();  
				var sel = document.selection.createRange();  
				sel.text = myValue;  
				this.focus();  
				sel.moveStart('character', -l);  
				var wee = sel.text.length;  
				if (arguments.length == 2) {  
				var l = $t.value.length;  
				sel.moveEnd("character", wee + t);  
				t <= 0 ? sel.moveStart("character", wee - 2 * t	- myValue.length) : sel.moveStart("character", wee - t - myValue.length);  
				sel.select();  
				}  
			} else if ($t.selectionStart || $t.selectionStart == '0') {  
				var startPos = $t.selectionStart;  
				var endPos = $t.selectionEnd;  
				var scrollTop = $t.scrollTop;  
				$t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos,$t.value.length);  
				this.focus();  
				$t.selectionStart = startPos + myValue.length;  
				$t.selectionEnd = startPos + myValue.length;  
				$t.scrollTop = scrollTop;  
				if (arguments.length == 2) { 
					$t.setSelectionRange(startPos - t,$t.selectionEnd + t);  
					this.focus(); 
				}  
			} else {                              
				this.value += myValue;                              
				this.focus();  
			}  
		}  
	});
 
 //表情解析
 $.fn.extend({
	  replaceface : function(faces){
//		  for(i=0;i<60;i++){
//			  faces=faces.replace('<emt>'+ (i) +'</emt>','<img src="images/face/'+(i)+'.gif">');
//			  }
		   $(this).html(faces);
		   }
	  });
	  
  
});
