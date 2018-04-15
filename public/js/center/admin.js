$(document).ready(function() {
	setInterval(function(){
		$("#bodybg").css("margin-top",($(document).height()-$("#bodybg").height())/2);
		$(".mask").css("height",$(document).height());
		$(".subnav").css("width",$(document).width()-200);
	},100);
	$(".menutd").css("height",$(document).height()-78);
	
	$(".leftcontent dl:first").addClass("crumb");
	$(".leftcontent dl dt").click(function(){
		$(".leftcontent dl").removeClass("crumb");
		$(this).parents("dl").addClass("crumb");
	});
	
	$(".subnav li:not(.crumb)").hover(function() {
        $(this).addClass("hover");
    }, function() {
        $(this).removeClass("hover");
    });
	
	$(".admin_bnt2").hover(function() {
        $(this).addClass("crumb");
    }, function() {
        $(this).removeClass("crumb");
    });
	
	$(".view").hover(function() {
        $(this).addClass("view_show");
    }, function() {
        $(this).removeClass("view_show");
    });
	
	$(".admin_list td").hover(function() {
        $(this).parents("tr").addClass("crumb");
    }, function() {
        $(this).parents("tr").removeClass("crumb");
    });
	
	var wid = $(document).width();
	$(".fold a").toggle(function(){
		$(this).parents("div.fold").addClass("fold2");
		$(".leftcontent").hide();
		$(".menutd").hide();
		$(".subnav").css("left","10px");
		$(".if_class").css("width",wid);
      },function(){
		$(this).parents("div.fold").removeClass("fold2");
		$(".leftcontent").show();
		$(".menutd").show();
		$(".subnav").css("left","180px");
		$(".if_class").css("width",wid-169);
      });
	
	
	
	
});