$(document).ready(function(){
	$("#banner_box ol li:first").addClass("on");
	
	/*头部分类*/
	$(".sort").click(function(){
		$(this).parents("body").removeClass("barright");
		$(this).parents("body").addClass("barleft");
	});
	$(".head_sort dt a").click(function(){
		$(this).parents("body").addClass("barright");
		$(this).parents("body").removeClass("barleft");
	});
	$(".head_sort li").each(function(index){
		$(this).find("span").click(function(){
			if($(this).parents("li").hasClass("crumb")){
				$(this).parents("li").removeClass("crumb");
			}else{
				$(".head_sort li").removeClass("crumb");
				$(this).parents("li").addClass("crumb");
			}
		});
	});
	/*头部二维码*/
	$(".logo").click(function(){
		$(this).parents("body").removeClass("barbottom");
		$(this).parents("body").addClass("bartop");
	});
	$(".head_wechat dt a").click(function(){
		$(this).parents("body").addClass("barbottom");
		$(this).parents("body").removeClass("bartop");
	});
	
	/*首页品牌切换*/
	$(".index_brand").each(function(index){
		var len = $(this).find("li").length;
		var wid = $(".index_brand_list").width();
		$(this).find("li").css("width",wid/3);
		$(this).find("ul").css("width",len*140);
		if(len > 3){
			$(this).find(".item2_r").show();
			$(this).find(".item2_r").click(function(){
				var lis = $(this).parents(".index_brand").find("li").length - 3;
				var left = $(this).parents(".index_brand").find("ul").css("left");
				left = ($(this).parents(".index_brand").find("ul").css("left")).substr(0,left.length-2);
				var maxleft = lis * -(wid/3);
				if ($(this).parents(".index_brand").find("li").css("left") == "auto" && lis == 0) left = 0;
				if (maxleft >= left) return false;
				$(this).parents(".index_brand").find("ul").animate({left:'-='+wid},200);
			});
			$(this).find(".item2_l").click(function(){
				var left = $(this).parents(".index_brand").find("ul").css("left");
				left = ($(this).parents(".index_brand").find("ul").css("left")).substr(0,left.length-2);
				if ($(this).parents(".index_brand").find("ul").css("left") == "auto" || left >= 0 ) return;
				$(this).parents(".index_brand").find("ul").animate({left:'+='+wid},200);
			});
		}
	});
	
	/*发表回复*/
	$(".thread").click(function(){
		$(".reply_div").fadeIn("slow");
	});
	$(".cancel").click(function(){
		$(".reply_div").fadeOut("slow");
	});
	
	
	/*回复伸缩*/
	$(".comment_reply").each(function(index){
		var len = $(this).find("li").length;
		if(len < 2){
			$(this).find(".stretch").hide();
		}
		$(this).find("li:gt(0)").addClass("hide");
		$(this).find(".stretch i").toggle(function () {
			$(this).addClass("crumb");
			$(this).parents(".comment_reply").find("li").removeClass("hide");
		},function () {
			$(this).removeClass("crumb");
			$(this).parents(".comment_reply").find("li:gt(0)").addClass("hide");
		});
	});
	
	/*支付方式/优惠选择*/
	$(".cart_form li").each(function(index){
		$(this).find("label:first").addClass("crumb");
		$(this).find("label:first").children('input').attr('checked',true);
		$(this).find("label").click(function(){
			$(this).parents("li").find("label").removeClass("crumb");
			$(this).find("label:first").children('input').attr('checked',true);
			$(this).addClass("crumb");
			$(this).children('input').attr('checked',true);
		});
	});
	
	/*订单评论*/
	$(".give span i").each(function(index){
		var w = 28;
        var id;
        switch (index) {
            case 0:
                id = "#desc_score";break;
            case 1:
                id = "#ship_score";break;
            case 2:
                id = "#service_score";break;
            case 3:
                id = "#logist_score";break;
        }
		$(this).click(function(e) {
			var x = e.clientX-$(".give span i").offset().left;
			if(x>=0 && x<=w){
				$(this).find("em").css("width",w);
                $(id).val(1);
			};
			if(x>=w && x<=w*2){
				$(this).find("em").css("width",w*2);
                $(id).val(2);
			};
			if(x>=w*2 && x<=w*3){
				$(this).find("em").css("width",w*3);
                $(id).val(3);
			};
			if(x>=w*3 && x<=w*4){
				$(this).find("em").css("width",w*4);
                $(id).val(4);
			};
			if(x>=w*4 && x<=w*5){
				$(this).find("em").css("width",w*5);
                $(id).val(5);
			};
		});
	});
});
