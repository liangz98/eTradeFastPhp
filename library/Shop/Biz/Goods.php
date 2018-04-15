<?php
class Shop_Biz_Goods {
	//加销量
	static function soldAdd($order_id){
		$seed_Setting = Shop_Biz_Base::getSetting();
		$goodsM = new Shop_Model_Goods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$orderM = new Shop_Model_Order('shop');
		$orderGoodsM = new Shop_Model_OrderGoods('shop');

		$order =$orderM->fetchRow(array('order_id'=>$order_id));
		$ordergoodses=$orderGoodsM->fetchRows(null,array('order_id'=>$order_id));
		
		foreach ($ordergoodses as $k =>$ordergoods){
		  if ($ordergoods['order_goods_type'] == 0) {//一般商品
			if ($ordergoods['stock_id']>0){
				$stock = $goodsStockM->fetchOne(array('stock_id' => $ordergoods['stock_id']),'stock_value');
				if ($stock < $ordergoods['goods_number']) {
					if (isset($seed_Setting['sold_add']) && $seed_Setting['sold_add']=='1'){
						Mobile_Browser::tip_showMsg('库存不足！', null, 2000);
					}else {
						Seed_Browser::tip_show('库存不足！');
					}
				}
				$goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold+{$ordergoods['goods_number']}")),array('goods_id' => $ordergoods['goods_id']));
			}else{
				$stock = $goodsStockM->fetchRow(array('goods_id' => $ordergoods['goods_id'], 'group_id' => 0), array('stock_id','stock_value'));
				if ($stock['stock_value'] < $ordergoods['goods_number']){
				    if (isset($seed_Setting['sold_add']) && $seed_Setting['sold_add']=='1'){
						Mobile_Browser::tip_showMsg('库存不足！', null, 2000);
					}else {
						Seed_Browser::tip_show('库存不足！');
					}
				}
				$goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold+{$ordergoods['goods_number']}")),array('goods_id' => $ordergoods['goods_id']));
			}
		  }
		}
	}

	//减库存
	static function stockReduce($order_id, $is_force='0'){
		$goodsM = new Shop_Model_Goods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$orderM = new Shop_Model_Order('shop');
		$orderGoodsM = new Shop_Model_OrderGoods('shop');

		$ordergoodses=$orderGoodsM->fetchRows(null,array('order_id'=>$order_id));
		foreach ($ordergoodses as $k =>$ordergoods){
			if ($ordergoods['order_goods_type'] == 0 && $is_force == 0) {//一般商品
				if ($ordergoods['stock_id']>0){
					$stock = $goodsStockM->fetchOne(array('stock_id' => $ordergoods['stock_id']),'stock_value');
					if ($stock < $ordergoods['goods_number']) {
						if (isset($seed_Setting['stock_reduce']) && $seed_Setting['stock_reduce']=='1'){
							Mobile_Browser::tip_showMsg('库存不足！', null, 2000);
						}else {
							Seed_Browser::tip_show('库存不足！');
						}
					}
					$goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value-{$ordergoods['goods_number']}")),array('stock_id' => $ordergoods['stock_id']));
				}else{
					$stock = $goodsStockM->fetchRow(array('goods_id' => $ordergoods['goods_id'], 'group_id' => 0), array('stock_id','stock_value'));
					if ($stock['stock_value'] < $ordergoods['goods_number']){
						if ($stock['stock_value'] < $ordergoods['goods_number']){
							if (isset($seed_Setting['stock_reduce']) && $seed_Setting['stock_reduce']=='1'){
								Mobile_Browser::tip_showMsg('库存不足！', null, 2000);
							}else {
								Seed_Browser::tip_show('库存不足！');
							}
						}
					}
					$goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value-{$ordergoods['goods_number']}")),array('stock_id' => $stock['stock_id']));
				}
			}else {
				//抢购商品跳过跟新库存
			}
		}
	}
	
	//退货:减销量,加库存
	static function soldReduce($order_id){
	    $seed_Setting = Shop_Biz_Base::getSetting();
		$goodsM = new Shop_Model_Goods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$orderM = new Shop_Model_Order('shop');
		$orderGoodsM = new Shop_Model_OrderGoods('shop');

		$order =$orderM->fetchRow(array('order_id'=>$order_id));
		$ordergoodses=$orderGoodsM->fetchRows(null,array('order_id'=>$order_id));
		
		foreach ($ordergoodses as $k =>$ordergoods){
		  if ($ordergoods['order_goods_type'] == 0) {//一般商品
			if ($ordergoods['stock_id']>0){
				$stock = $goodsStockM->fetchOne(array('stock_id' => $ordergoods['stock_id']),'stock_value');
                //避免减销量出现大数值的情况，by brave 2015-06-18 16:16:21
                $goodsDetail=$goodsM->fetchRow(array('goods_id' => $ordergoods['goods_id']));
                if($goodsDetail['sold']-$ordergoods['goods_number']>=0){
                    $goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold-{$ordergoods['goods_number']}")),array('goods_id' => $ordergoods['goods_id']));
                }
			}else{
				$stock = $goodsStockM->fetchRow(array('goods_id' => $ordergoods['goods_id'], 'group_id' => 0), array('stock_id','stock_value'));
                //避免减销量出现大数值的情况，by brave 2015-06-18 16:16:21
                $goodsDetail=$goodsM->fetchRow(array('goods_id' => $ordergoods['goods_id']));
                if($goodsDetail['sold']-$ordergoods['goods_number']>=0){
                    $goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold-{$ordergoods['goods_number']}")),array('goods_id' => $ordergoods['goods_id']));
                }
			}
		  }else{//抢购商品退货不减销量
		     
		  }
		}
	}
    
	static function stockAdd($order_id){
	    $seed_Setting = Shop_Biz_Base::getSetting();
		$goodsM = new Shop_Model_Goods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$orderM = new Shop_Model_Order('shop');
		$orderGoodsM = new Shop_Model_OrderGoods('shop');

		$order =$orderM->fetchRow(array('order_id'=>$order_id));
		$ordergoodses=$orderGoodsM->fetchRows(null,array('order_id'=>$order_id));
		
		foreach ($ordergoodses as $k =>$ordergoods){
		  if ($ordergoods['order_goods_type'] == 0) {//一般商品
			if ($ordergoods['stock_id']>0){
				$stock = $goodsStockM->fetchOne(array('stock_id' => $ordergoods['stock_id']),'stock_value');
				$goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value+{$ordergoods['goods_number']}")),array('stock_id' => $ordergoods['stock_id']));
			}else{
				$stock = $goodsStockM->fetchRow(array('goods_id' => $ordergoods['goods_id'], 'group_id' => 0), array('stock_id','stock_value'));
				$goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value+{$ordergoods['goods_number']}")),array('stock_id' => $stock['stock_id']));
			}
		  }else{//抢购商品退货不加库存
		  
		  }
		}
	} 
}