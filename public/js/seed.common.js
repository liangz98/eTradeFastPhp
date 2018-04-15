$(document).ready(function() {
	$(".state dl").hover(function() {
        $(this).addClass("hover");
    }, function() {
        $(this).removeClass("hover");
    });
	
	$(".choose dl dt").hover(function() {
        $(this).addClass("hover");
    }, function() {
        $(this).removeClass("hover");
    });
	$(".choose dt").toggle(function () {
		$(this).parents("dl").addClass("screen");
	},function () {
		$(this).parents("dl").removeClass("screen");
	});
	
	$(".sales_list li:first").addClass("hover");
	$(".sales_list li").hover(function() {
        $(".sales_list li").removeClass("hover");
		$(this).addClass("hover");
    }, function() {
    });
	$(".related_list li").hover(function() {
		$(this).addClass("hover");
    }, function() {
		$(this).removeClass("hover");
    });
	
	$(".filter dt small").toggle(function () {
		$(this).parents("dl").addClass("back");
	},function () {
		$(this).parents("dl").removeClass("back");
	});
	
	$(".getdiv dd:first").addClass("block");
	$(document).ready(function(){
		$(".getdiv dt span label,.getdiv dt span label input").each(function(index){
			$(this).click(function(){ 
				$(".getdiv dd").removeClass("block");
				$(".getdiv dd").eq(index).addClass("block");
			});
		});
	
	});
	
	$(".image .uright").click(function(){
		var lis = $(".image .ulcont li").length - 5;
		var left = $(".image .ulcont ul").css("left");
		left = ($(".image .ulcont ul").css("left")).substr(0,left.length-2);
		var maxleft = lis * -68;
		if ($(".image .ulcont li").css("left") == "auto" && lis == 0) left = 0;
		if (maxleft >= left) return false;
		$(".image .ulcont ul").animate({left: '-=68'}, 1);
	});
	$(".image .uleft").click(function(){
		var left = $(".image .ulcont ul").css("left");
		left = ($(".image .ulcont ul").css("left")).substr(0,left.length-2);
		if ($(".image .ulcont ul").css("left") == "auto" || left >= 0 ) return;
		$(".image .ulcont ul").animate({left: '+=68'}, 1);
 	});
	
	setInterval(function(){
		$(".cursor_l , .cursor_r").css("height",$(".view_txt center img").height());
	},100);
	$(".breviary_r").click(function(){
		var lis = $(".breviary_u li").length - 6;
		var left = $(".breviary_u ul").css("left");
		left = ($(".breviary_u ul").css("left")).substr(0,left.length-2);
		var maxleft = lis * -120;
		if ($(".breviary_u li").css("left") == "auto" && lis == 0) left = 0;
		if (maxleft >= left) return false;
		$(".breviary_u ul").animate({left: '-=120'}, 1);
	});
	$(".breviary_l").click(function(){
		var left = $(".breviary_u ul").css("left");
		left = ($(".breviary_u ul").css("left")).substr(0,left.length-2);
		if ($(".breviary_u ul").css("left") == "auto" || left >= 0 ) return;
		$(".breviary_u ul").animate({left: '+=120'}, 1);
 	});

	
});