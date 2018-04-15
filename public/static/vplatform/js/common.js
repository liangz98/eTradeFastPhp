$(function(){
	//根据购物车数量判断是否显示
	function hide_b(){
		if($('#total_carts').text()==0){
	 		$('#total_carts').hide();
	 	}else{
	 		$('#total_carts').show();
		}
	}
	hide_b();
})