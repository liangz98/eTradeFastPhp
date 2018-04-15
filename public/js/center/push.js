var l=-1; 
var offset1 = 3000; 
var timer1 = null;
function autoroll1(){
	k = $(".push dt li").length-1;
	l++;
	if(l > k){
		l = 0;
	}
	slide1(l);
	timer1 = window.setTimeout(autoroll1, offset1);
}
function slide1(l){
	$(".push dt li").eq(l).addClass("crumb").siblings().removeClass("crumb");
	$(".push dd").eq(l).addClass("block").siblings("dd").removeClass("block");
}
function hookThumb1(){    
	$(".push dt li").hover(function () {
		if (timer1) {
			clearTimeout(timer1);
		l = $(this).prevAll().length;
		 slide1(l); 
		}
	},function () {
  		timer1 = window.setTimeout(autoroll1, offset1);  
		this.blur();            
		return false;
	}); 
}
$(document).ready(function(){
	$(".push dt li:first").addClass("crumb");
	$(".push dd:first").addClass("block");
	autoroll1();
	hookThumb1();
	
	$(".img_r").click(function(){
		var lis = $(".img_u li").length - 4;
		var left = $(".img_u ul").css("left");
		left = ($(".img_u ul").css("left")).substr(0,left.length-2);
		var maxleft = lis * -70;
		if ($(".img_u li").css("left") == "auto" && lis == 0) left = 0;
		if (maxleft >= left) return false;
		$(".img_u ul").animate({left: '-=70'}, 1);
	});
	$(".img_l").click(function(){
		var left = $(".img_u ul").css("left");
		left = ($(".img_u ul").css("left")).substr(0,left.length-2);
		if ($(".img_u ul").css("left") == "auto" || left >= 0 ) return;
		$(".img_u ul").animate({left: '+=70'}, 1);
 	});
	
});