<?php
class IndexController extends Shop_Controller_Action
{
	function preDispatch()
	{
		$this->view->cur_pos = $this->_request->getParam('controller');
		
	    if (Shop_Browser::isMobile()){
		    header('location:'.(isset($this->view->seed_Setting['mobile_website'])?$this->view->seed_Setting['mobile_website']:$this->view->seed_Setting['website_domain']));
		}
	}

	function indexAction()
	{	
		
		$shopM = new Shop_Model_Shop('shop');
		$goodsM = new Shop_Model_Goods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodsCateM = new Shop_Model_GoodsCate('shop');
		$goodsBarandM = new Shop_Model_GoodsBrand('shop');
		$countryM = new Shop_Model_GoodsCountry('shop');
		$condition1["t1.goods_channel != 'BBC+BC' && t1.is_hot = '1' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != ''"] = null;
		$condition2["t1.goods_channel != 'BBC+BC' && t1.is_favour = '1' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != ''"] = null;
		$condition3["t1.goods_channel != 'BBC+BC' && t1.is_new = '1' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != ''"] = null;
		$conditions["t1.goods_channel != 'BBC+BC' && t1.goods_channel != 'BBC+BC'"]=null;
		$hot_goodses = $goodsM->fetchGoodses(array(0,5),$condition1,null,array('order_by desc'));
		$favour_goodses = $goodsM->fetchGoodses(array(0,5),$condition2,null,array('goods_id asc'));
		$new_goodses = $goodsM->fetchGoodses(array(0,5),$condition3,null,array('add_time desc'));

		foreach ($hot_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
        	if($stock['stock_id']>0){
        		$hot_goodses[$n]['stock_value'] = $stock['stock_value'];
        		$hot_goodses[$n]['stock_shop_price'] = $stock['stock_shop_price'];
        		$hot_goodses[$n]['stock_market_price'] = $stock['stock_market_price'];
        	}else{
        		$hot_goodses[$n]['stock_value'] = '0';
        		$hot_goodses[$n]['stock_shop_price'] = '0.00';
        		$hot_goodses[$n]['stock_market_price'] = '0.00';
        	}
        }

        foreach ($favour_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
        	if($stock['stock_id']>0){
        		$favour_goodses[$n]['stock_value'] = $stock['stock_value'];
        		$favour_goodses[$n]['stock_shop_price'] = $stock['stock_shop_price'];
        		$favour_goodses[$n]['stock_market_price'] = $stock['stock_market_price'];
        	}else{
        		$favour_goodses[$n]['stock_value'] = '0';
        		$favour_goodses[$n]['stock_shop_price'] = '0.00';
        		$favour_goodses[$n]['stock_market_price'] = '0.00';
        	}
        }
    
    	foreach ($new_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
        	if($stock['stock_id']>0){
        		$new_goodses[$n]['stock_value'] = $stock['stock_value'];
        		$new_goodses[$n]['stock_shop_price'] = $stock['stock_shop_price'];
        		$new_goodses[$n]['stock_market_price'] = $stock['stock_market_price'];
        	}else{
        		$new_goodses[$n]['stock_value'] = '0';
        		$new_goodses[$n]['stock_shop_price'] = '0.00';
        		$new_goodses[$n]['stock_market_price'] = '0.00';
        	}
        }
    
		$AM_id = $countryM->fetchOne(array('country_name'=>'美国'),'country_id');
		$J_id  = $countryM->fetchOne(array('country_name'=>'日本'),'country_id');
		$K_id  = $countryM->fetchOne(array('country_name'=>'韩国'),'country_id');
		$H_id  = $countryM->fetchOne(array('country_name'=>'香港'),'country_id');
		$T_id  = $countryM->fetchOne(array('country_name'=>'台湾'),'country_id');
		// $EU_id = $countryM->fetchOne(array('country_name'=>'欧洲'),'country_id');
		$A_id  = $countryM->fetchOne(array('country_name'=>'澳大利亚'),'country_id');
		$N_id  = $countryM->fetchOne(array('country_name'=>'新西兰'),'country_id');
		$YI_id  = $countryM->fetchOne(array('country_name'=>'意大利'),'country_id');
		$D_id  = $countryM->fetchOne(array('country_name'=>'德国'),'country_id');
		$Y_id  = $countryM->fetchOne(array('country_name'=>'英国'),'country_id');
		$F_id  = $countryM->fetchOne(array('country_name'=>'法国'),'country_id');
		$X_id  = $countryM->fetchOne(array('country_name'=>'西班牙'),'country_id');


        $condition4["t1.goods_channel != 'BBC+BC' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != '' && t1.country_id = '$AM_id'"] = null;
        $condition5["t1.goods_channel != 'BBC+BC' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != '' && t1.country_id in ('$J_id','$K_id') "] = null;
		$condition6["t1.goods_channel != 'BBC+BC' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != '' && t1.country_id in ('$H_id','$T_id') "] = null;
		$condition7["t1.goods_channel != 'BBC+BC' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != '' && t1.country_id in ('$YI_id','$D_id','$Y_id','$F_id','$X_id')"] = null;
		$condition8["t1.goods_channel != 'BBC+BC' && t1.is_actived = '1' && t1.is_auth = '1' && t1.is_on_sale = '1' && t1.is_group = '0' && IFNULL(t1.goods_list_image,'') != '' && IFNULL(t1.goods_detail_image,'') != '' && t1.country_id in ('$A_id','$N_id') "] = null; 

		$AM_goodes = $goodsM->fetchGoodses(array(0,8),$condition4,null,array('order_by desc'));
		$JK_goodes = $goodsM->fetchGoodses(array(0,8),$condition5,null,array('order_by desc'));
		$HT_goodes = $goodsM->fetchGoodses(array(0,8),$condition6,null,array('order_by desc'));
		$EU_goodes = $goodsM->fetchGoodses(array(0,8),$condition7,null,array('order_by desc'));
		$AN_goodes = $goodsM->fetchGoodses(array(0,8),$condition8,null,array('order_by desc'));

        $goodsCateM = new Shop_Model_GoodsCate('shop');
  		$baby_id = $goodsCateM->fetchOne(array('cate_name'=>'母婴用品'),'cate_id');
  		//$skin_id = $goodsCateM->fetchOne(array('cate_name'=>'美妆护肤'),'cate_id');
  		$boby_id = $goodsCateM->fetchOne(array('cate_name'=>'美妆个护'),'cate_id');
  		$health_id = $goodsCateM->fetchOne(array('cate_name'=>'健康保健'),'cate_id');
  		$life_id = $goodsCateM->fetchOne(array('cate_name'=>'家居用品'),'cate_id');
  		$food_id = $goodsCateM->fetchOne(array('cate_name'=>'酒水食品'),'cate_id');
  		$baby_cates = $goodsCateM->getCatesTree($baby_id,null,true);
  		//$skin_cates = $goodsCateM->getCatesTree($skin_id,null,true);
  		$boby_cates = $goodsCateM->getCatesTree($boby_id,null,true);
  		$health_cates = $goodsCateM->getCatesTree($health_id,null,true);
  		$life_cates = $goodsCateM->getCatesTree($life_id,null,true);
  		$food_cates = $goodsCateM->getCatesTree($food_id,null,true);

 		foreach ($baby_cates as $k=>$c){
			$condition = array();
			$condition['is_actived']='1';
			$condition['is_auth']='1';
			$condition['is_on_sale']='1';
			$condition['is_group']='0';//非套装
			$condition["cate_id in (select cate_id from snshop_goods_cates where parent = ".$c['cate_id'].")"]=null;
			$condition["goods_channel != 'BBC+BC'"]=null;
			$condition["IFNULL( goods_list_image, '') != ''"]=null;
			$condition["IFNULL( goods_detail_image, '') != ''"]=null;
			$all_goodses= $goodsM->fetchRows(array(0,6),$condition,array('order_by desc','goods_id desc'));		  
			foreach ($all_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
			  	if($stock['stock_id']>0){
	        		$all_goodses[$n]['goods_number'] = $stock['stock_value'];
	        		$all_goodses[$n]['shop_price'] = $stock['stock_shop_price'];
	        		$all_goodses[$n]['market_price'] = $stock['stock_market_price'];
	        	}else{
	        		$all_goodses[$n]['goods_number'] = '0';
	        		$all_goodses[$n]['shop_price'] = '0.00';
	        		$all_goodses[$n]['market_price'] = '0.00';
	        	}
        	}
			$baby_cates[$k]['all_goodses'] = $all_goodses;
	
 		}
		/*
  		foreach ($skin_cates as $k=>$c){
			$condition = array();
			$condition['is_actived']='1';
			$condition['is_auth']='1';
			$condition['is_on_sale']='1';
			$condition['is_group']='0';//非套装
			$condition["cate_id in (select cate_id from snshop_goods_cates where parent = ".$c['cate_id'].")"]=null;
			$condition["goods_channel != 'BBC+BC'"]=null;
			$condition["IFNULL( goods_list_image, '') != ''"]=null;
			$condition["IFNULL( goods_detail_image, '') != ''"]=null; 
			$all_goodses= $goodsM->fetchRows(array(0,6),$condition,array('order_by desc','goods_id desc'));		  
			foreach ($all_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
			  	if($stock['stock_id']>0){
	        		$all_goodses[$n]['goods_number'] = $stock['stock_value'];
	        		$all_goodses[$n]['shop_price'] = $stock['stock_shop_price'];
	        		$all_goodses[$n]['market_price'] = $stock['stock_market_price'];
	        	}else{
	        		$all_goodses[$n]['goods_number'] = '0';
	        		$all_goodses[$n]['shop_price'] = '0.00';
	        		$all_goodses[$n]['market_price'] = '0.00';
	        	}
        	}
			$skin_cates[$k]['all_goodses'] = $all_goodses;
 		}
		*/
  		foreach ($boby_cates as $k=>$c){
			$condition = array();
			$condition['is_actived']='1';
			$condition['is_auth']='1';
			$condition['is_on_sale']='1';
			$condition['is_group']='0';//非套装
			$condition["cate_id in (select cate_id from snshop_goods_cates where parent = ".$c['cate_id'].")"]=null;
			$condition["goods_channel != 'BBC+BC'"]=null;
			$condition["IFNULL( goods_list_image, '') != ''"]=null;
			$condition["IFNULL( goods_detail_image, '') != ''"]=null;
			$all_goodses= $goodsM->fetchRows(array(0,6),$condition,array('order_by desc','goods_id desc'));		  
			foreach ($all_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
			  	if($stock['stock_id']>0){
	        		$all_goodses[$n]['goods_number'] = $stock['stock_value'];
	        		$all_goodses[$n]['shop_price'] = $stock['stock_shop_price'];
	        		$all_goodses[$n]['market_price'] = $stock['stock_market_price'];
	        	}else{
	        		$all_goodses[$n]['goods_number'] = '0';
	        		$all_goodses[$n]['shop_price'] = '0.00';
	        		$all_goodses[$n]['market_price'] = '0.00';
	        	}
        	}
			$boby_cates[$k]['all_goodses'] = $all_goodses;
 		}

  		foreach ($health_cates as $k=>$c){
			$condition = array();
			$condition['is_actived']='1';
			$condition['is_auth']='1';
			$condition['is_on_sale']='1';
			$condition['is_group']='0';//非套装
			$condition["cate_id in (select cate_id from snshop_goods_cates where parent = ".$c['cate_id'].")"]=null;
			$condition["goods_channel != 'BBC+BC'"]=null;
			$condition["IFNULL( goods_list_image, '') != ''"]=null;
			$condition["IFNULL( goods_detail_image, '') != ''"]=null;
			$all_goodses= $goodsM->fetchRows(array(0,6),$condition,array('order_by desc','goods_id desc'));		  
			foreach ($all_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
			  	if($stock['stock_id']>0){
	        		$all_goodses[$n]['goods_number'] = $stock['stock_value'];
	        		$all_goodses[$n]['shop_price'] = $stock['stock_shop_price'];
	        		$all_goodses[$n]['market_price'] = $stock['stock_market_price'];
	        	}else{
	        		$all_goodses[$n]['goods_number'] = '0';
	        		$all_goodses[$n]['shop_price'] = '0.00';
	        		$all_goodses[$n]['market_price'] = '0.00';
	        	}
        	}
			$health_cates[$k]['all_goodses'] = $all_goodses;
 		}

  		foreach ($life_cates as $k=>$c){
			$condition = array();
			$condition['is_actived']='1';
			$condition['is_auth']='1';
			$condition['is_on_sale']='1';
			$condition['is_group']='0';//非套装
			$condition["cate_id in (select cate_id from snshop_goods_cates where parent = ".$c['cate_id'].")"]=null;
			$condition["goods_channel != 'BBC+BC'"]=null;
			$condition["IFNULL( goods_list_image, '') != ''"]=null;
			$condition["IFNULL( goods_detail_image, '') != ''"]=null;
			$all_goodses= $goodsM->fetchRows(array(0,6),$condition,array('order_by desc','goods_id desc'));		  
			foreach ($all_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
			  	if($stock['stock_id']>0){
	        		$all_goodses[$n]['goods_number'] = $stock['stock_value'];
	        		$all_goodses[$n]['shop_price'] = $stock['stock_shop_price'];
	        		$all_goodses[$n]['market_price'] = $stock['stock_market_price'];
	        	}else{
	        		$all_goodses[$n]['goods_number'] = '0';
	        		$all_goodses[$n]['shop_price'] = '0.00';
	        		$all_goodses[$n]['market_price'] = '0.00';
	        	}
        	}
			$life_cates[$k]['all_goodses'] = $all_goodses;
 		}
  	
		foreach ($food_cates as $k=>$c){
			$condition = array();
			$condition['is_actived']='1';
			$condition['is_auth']='1';
			$condition['is_on_sale']='1';
			$condition['is_group']='0';//非套装
			//$condition['cate_id']=$c['cate_id'];
			$condition["cate_id in (select cate_id from snshop_goods_cates where parent = ".$c['cate_id'].")"]=null;
			$condition["goods_channel != 'BBC+BC'"]=null;
			$condition["IFNULL( goods_list_image, '') != ''"]=null;
			$condition["IFNULL( goods_detail_image, '') != ''"]=null;
			$all_goodses= $goodsM->fetchRows(array(0,6),$condition,array('order_by desc','goods_id desc'));		  
			foreach ($all_goodses as $n=>$goods){
        	$stock = $goodsStockM->fetchRow(array('goods_id'=>$goods['goods_id']),"*", 'stock_shop_price ASC');
			  	if($stock['stock_id']>0){
	        		$all_goodses[$n]['goods_number'] = $stock['stock_value'];
	        		$all_goodses[$n]['shop_price'] = $stock['stock_shop_price'];
	        		$all_goodses[$n]['market_price'] = $stock['stock_market_price'];
	        	}else{
	        		$all_goodses[$n]['goods_number'] = '0';
	        		$all_goodses[$n]['shop_price'] = '0.00';
	        		$all_goodses[$n]['market_price'] = '0.00';
	        	}
        	}
			$food_cates[$k]['all_goodses'] = $all_goodses;
		}
		$brands = $goodsBarandM->fetchBrands(null,array('is_actived'=>'1'),'order_by ASC');
		$this->view->brands = $brands;
		$this->view->hot_goodses = $hot_goodses;
        $this->view->favour_goodses = $favour_goodses;
        $this->view->new_goodses = $new_goodses;
		$this->view->AM_goodes = $AM_goodes;
        $this->view->JK_goodes = $JK_goodes;
        $this->view->HT_goodes = $HT_goodes;
        $this->view->EU_goodes = $EU_goodes;
        $this->view->AN_goodes = $AN_goodes;
		$this->view->baby_cates = $baby_cates;
  		//$this->view->skin_cates = $skin_cates;
  		$this->view->boby_cates = $boby_cates;
  		$this->view->health_cates = $health_cates;
		$this->view->life_cates = $life_cates;
		$this->view->food_cates = $food_cates;
	
		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/index/index.phtml");
			echo $content;
			exit;
		}
	}
}