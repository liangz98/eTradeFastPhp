var i=-1; 
var offset = 5000; 
var timer = null;
function autoroll(){
	n = $(".flash dt span").length-1;
	i++;
	if(i > n){
		i = 0;
	}
	slide(i);
	timer = window.setTimeout(autoroll, offset);
}
function slide(i){
	$(".flash dt span").eq(i).addClass("crumb").siblings().removeClass("crumb");
	$(".flash dd").eq(i).addClass("block").siblings("dd").removeClass("block");
}
function hookThumb(){    
	$(".flash dt span").hover(function () {
		if (timer) {
			clearTimeout(timer);
		i = $(this).prevAll().length;
		 slide(i); 
		}
	},function () {
  		timer = window.setTimeout(autoroll, offset);  
		this.blur();            
		return false;
	});
}
$(document).ready(function(){
	$(".flash dt span:first").addClass("crumb");
	$(".flash dd:first").addClass("block");
	autoroll();
	hookThumb();
});