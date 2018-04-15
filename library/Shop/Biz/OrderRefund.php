<?php
/**
 *  退款通知
 */
class Shop_Biz_OrderRefund {
	public static function sendNotice($order) {
		$seed_Setting = Shop_Biz_Base::getSetting();
		$order_id = $order ['order_id'];
		$wechatUserM = new Wechat_Model_User ( 'wechat' );
		$userM = new Seed_Model_User ( 'system' );
		$shopM = new Shop_Model_Shop ( 'shop' );
		$noteTplM = new Wechat_Model_Notetpl('wechat');
		$mobileTempM = new Seed_Model_MobileTemplate('system');
		$mobileOutboxM = new Seed_Model_MobileOutbox('system');
		$userAddressM = new Seed_Model_UserAddress('system');

		//卖家店铺信息
		if (isset ( $order ['shop_id'] ) && $order ['shop_id'] > 0) {
			$shop = $shopM->fetchRow ( array ('shop_id' => $order ['shop_id'] ) );
			if ($shop ['shop_id'] > 0) {
				$shop_user = $userM->fetchRow ( array ('user_id' => $shop ['user_id'] ) );
				$wc_user = $wechatUserM->fetchRow ( array ('user_id' => $order ['user_id'] ) );
			}
		}

		//发货店铺信息
		if (isset($order['agent_id']) && $order['agent_id'] > 0) {
			$agent = $shopM->fetchRow(array('shop_id' => $order['agent_id']));
			if ($agent['shop_id'] > 0) {
				$agent_user = $userM->fetchRow(array('user_id' => $agent['user_id']));
				$wc_agent_user = $wechatUserM->fetchRow ( array ('user_id' => $agent ['user_id'] ) );
			}
		}

		//卖家上级店铺信息
		if (isset($order['shop_id']) && $order['shop_id']>0){
			$shop = $shopM->fetchRow(array('shop_id'=>$order['shop_id']));
			if ($shop['admin_shop_id']>0){
				$admin_shop = $shopM->fetchRow(array('shop_id'=>$shop['admin_shop_id']));
				$admin_user = $userM->fetchRow(array('user_id' => $admin_shop['user_id']));
				$wc_admin_user = $wechatUserM->fetchRow ( array ('user_id' => $admin_user ['user_id'] ) );
			}
		}

		//消费者短信
		if (isset($order['user_id']) && $order['user_id'] >0){
			$useraddress = $userAddressM->fetchRow(array('user_id'=>$order['user_id'],'is_default'=>'1'));
			$customer = $userM->fetchRow(array('user_id' => $order['user_id']));
			$wc_customer = $wechatUserM->fetchRow ( array ('user_id' => $order['user_id'] ) );
		}

		$bid = 0;
		$wechatM = new Wechat_Model_Wechat ( 'wechat' );
		$wechat = $wechatM->fetchRow ( array ('is_del' => '0' ) );
		if ($wechat ['id'] > 0) {
			$bid = $wechat ['id'];
			$kefuAPI = new Wechat_AdvanceAPI_KefuAPI ( $bid );
		}

		$goods_list = "";
		if ($bid > 0) {
			$orderGoodsM = new Shop_Model_OrderGoods ( 'shop' );
			$goodses = $orderGoodsM->fetchRows ( null, array ('order_id' => $order_id ) );
				
			foreach ( $goodses as $v ) {
				$goods_list .= "[" . $v ['goods_number'] . "] × " . $v ['goods_name'] . "";
				if (! empty ( $v ['goods_attr'] ))
				$goods_list .= " " . $v ['goods_attr'];
				$goods_list .= "
";
			}
		}

		$f = new Seed_Filter_Mobile ();

		//微信通知(消费者)
		$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
		$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
		if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $wc_customer ) && ! empty ( $wc_customer ['wc_username'] )) {
			preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
			if (isset ( $matches [1] )) {
				$replacements = array();
				$replacements[] = '尊敬的用户，您好!由于您购买的商品库存不足,已退款';
				$replacements[] = '库存不足';
				$replacements[] = $order['order_amount'];
				$replacements[] = "商品列表：" . $goods_list;

				$Template = new Wechat_AdvanceAPI_Template ( $bid );
				foreach ( $matches [1] as $key => $value ) { //处理数据组合
					$Template->combineData ( $value, $replacements [$key] );
				}
				$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $wc_customer ['wc_username'];
				$template_id = $tpl ['tpl_id'];
				$url = $seed_Setting ['vuser_app_server'] . "/order/info?order_id=" . $order ['order_id'];
				$res = $Template->send ( $touser, $template_id, $url );
			}
		}

		//短信提醒(消费者)
		$f = new Seed_Filter_Mobile();
		if(strlen($f->filter($order['telephone']))==11){
			$mobileTempM = new Seed_Model_MobileTemplate('system');
			$mobileTemp = $mobileTempM->fetchRow(array('temp_name'=>'order_refund','is_actived'=>'1'));
			if($mobileTemp['temp_id']>0){
				$content = $mobileTemp['temp_content'];
				$patterns=array();
				$replacements=array();
				$patterns[]='/{nick_name}/';
				$patterns[]='/{order_sn}/';
				$patterns[]='/{order_amount}/';
				$patterns[]='/{time}/';
				$replacements[]=$order['user_name'];
				$replacements[]=$order['order_sn'];
				$replacements[]=$order['order_amount'];
				$replacements[]=date('Y-m-d H:i:s');
				$content = preg_replace($patterns, $replacements, $content);
				$mobileOutboxM=new Seed_Model_MobileOutbox('system');
				$mobileOutboxM->mobileSend($order['telephone'], $content, time());
			}
		}
		 
		//创业者短信提醒
	    //代理订单
		if ($order ['order_type'] == Shop_Model_OrderType::TYPE_AGENT) {
			    $mobileTempM = new Seed_Model_MobileTemplate('system');
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_shop_refund', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns=array();
				    $patterns[]='/{nick_name}/';
				    $patterns[]='/{order_sn}/';
					$patterns[]='/{order_amount}/';
					$patterns[]='/{time}/';
					$replacements[]=$shop['shop_name'];
					$replacements[]=$order['order_sn'];
					$replacements[]=$order['order_amount'];
					$replacements[]=date('Y-m-d H:i:s');
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time () );
				}

				   //卖家
				    $noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
					$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
					if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
				       preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				       if (isset ( $matches [1] )) {
						  	$replacements = array();
						  	$replacements[] = '尊敬的创业者，您分销商品的订单由于库存不足,已退款,请注意!';
						  	$replacements[] = '库存不足';
				            $replacements[] = $order['order_amount'];
				            $replacements[] = "商品列表：" . $goods_list;
	
				  	$Template = new Wechat_AdvanceAPI_Template ( $bid );
				  	foreach ( $matches [1] as $key => $value ) { //处理数据组合
				  		$Template->combineData ( $value, $replacements [$key] );
				  	}
				  	$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
				  	$template_id = $tpl ['tpl_id'];
				  	$url = $seed_Setting ['mobunion_app_server'] . "/order";
				  	$res = $Template->send ( $touser, $template_id, $url );
				  }
				}
			//代理旗下订单
	     } else if ($order ['order_type'] == Shop_Model_OrderType::TYPE_PERSON) {
	     	$mobileTempM = new Seed_Model_MobileTemplate('system');
			$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_shop_refund', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns=array();
				    $replacements=array();
					$patterns[]='/{nick_name}/';
				    $patterns[]='/{order_sn}/';
					$patterns[]='/{order_amount}/';
					$patterns[]='/{time}/';
					$replacements[]=$shop['shop_name'];
					$replacements[]=$order['order_sn'];
					$replacements[]=$order['order_amount'];
					$replacements[]=date('Y-m-d H:i:s');
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user['user_mobile'], $content, time ());

					$content = $mobileTemp ['temp_content'];
					$patterns=array();
				    $replacements=array();
					$patterns[]='/{nick_name}/';
				    $patterns[]='/{order_sn}/';
					$patterns[]='/{order_amount}/';
					$patterns[]='/{time}/';
					$replacements[]=$admin_shop['shop_name'];
					$replacements[]=$order['order_sn'];
					$replacements[]=$order['order_amount'];
					$replacements[]=date('Y-m-d H:i:s');
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $admin_user['user_mobile'], $content, time ());
				}
				    //卖家
				    $noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
					$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
					if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
				       preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				       if (isset ( $matches [1] )) {
						  	$replacements = array();
						  	$replacements[] = '尊敬的创业者，您分销商品的订单由于库存不足,已退款,请注意!';
						  	$replacements[] = '库存不足';
				            $replacements[] = $order['order_amount'];
				            $replacements[] = "商品列表：" . $goods_list;
	
				  	$Template = new Wechat_AdvanceAPI_Template ( $bid );
				  	foreach ( $matches [1] as $key => $value ) { //处理数据组合
				  		$Template->combineData ( $value, $replacements [$key] );
				  	}
				  	$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
				  	$template_id = $tpl ['tpl_id'];
				  	$url = $seed_Setting ['mobunion_app_server'] . "/order";
				  	$res = $Template->send ( $touser, $template_id, $url );
				  }
				}
				
				    //卖家上级
				    $noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
					$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
					if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $admin_user ) && ! empty ( $admin_user ['wc_username'] )) {
				       preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				       if (isset ( $matches [1] )) {
						  	$replacements = array();
						  	$replacements[] = '尊敬的创业者，您分销商品的订单由于库存不足,已退款,请注意!';
						  	$replacements[] = '库存不足';
				            $replacements[] = $order['order_amount'];
				            $replacements[] = "商品列表：" . $goods_list;
	
				  	$Template = new Wechat_AdvanceAPI_Template ( $bid );
				  	foreach ( $matches [1] as $key => $value ) { //处理数据组合
				  		$Template->combineData ( $value, $replacements [$key] );
				  	}
				  	$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $admin_user ['wc_username'];
				  	$template_id = $tpl ['tpl_id'];
				  	$url = $seed_Setting ['mobunion_app_server'] . "/order";
				  	$res = $Template->send ( $touser, $template_id, $url );
				  }
				}
			

		//商户订单
		} else if ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP) {
			//不发信息
			
			
			
			//商户旗下
		} else if ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP_PERSON) {
		      $mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_shop_refund', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns=array();
				    $replacements=array();
					$patterns[]='/{nick_name}/';
				    $patterns[]='/{order_sn}/';
					$patterns[]='/{order_amount}/';
					$patterns[]='/{time}/';
					$replacements[]=$shop['shop_name'];
					$replacements[]=$order['order_sn'];
					$replacements[]=$order['order_amount'];
					$replacements[]=date('Y-m-d H:i:s');
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time ());
				 }
			
		          //卖家
				    $noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
					$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
					if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
				       preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				       if (isset ( $matches [1] )) {
						  	$replacements = array();
						  	$replacements[] = '尊敬的创业者，您分销商品的订单由于库存不足,已退款,请注意!';
						  	$replacements[] = '库存不足';
				            $replacements[] = $order['order_amount'];
				            $replacements[] = "商品列表：" . $goods_list;
	
				  	$Template = new Wechat_AdvanceAPI_Template ( $bid );
				  	foreach ( $matches [1] as $key => $value ) { //处理数据组合
				  		$Template->combineData ( $value, $replacements [$key] );
				  	}
				  	$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
				  	$template_id = $tpl ['tpl_id'];
				  	$url = $seed_Setting ['mobunion_app_server'] . "/order";
				  	$res = $Template->send ( $touser, $template_id, $url );
				  }
				}
			
			//其他商户售出
		} elseif ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP_SHOP) {
		     $mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_shop_refund', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns=array();
				    $replacements=array();
					$patterns[]='/{nick_name}/';
				    $patterns[]='/{order_sn}/';
					$patterns[]='/{order_amount}/';
					$patterns[]='/{time}/';
					$replacements[]=$shop['shop_name'];
					$replacements[]=$order['order_sn'];
					$replacements[]=$order['order_amount'];
					$replacements[]=date('Y-m-d H:i:s');
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time ());
				 }
			
		          //卖家
				    $noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
					$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
					if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
				       preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				       if (isset ( $matches [1] )) {
						  	$replacements = array();
						  	$replacements[] = '尊敬的创业者，您分销商品的订单由于库存不足,已退款,请注意!';
						  	$replacements[] = '库存不足';
				            $replacements[] = $order['order_amount'];
				            $replacements[] = "商品列表：" . $goods_list;
	
				  	$Template = new Wechat_AdvanceAPI_Template ( $bid );
				  	foreach ( $matches [1] as $key => $value ) { //处理数据组合
				  		$Template->combineData ( $value, $replacements [$key] );
				  	}
				  	$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
				  	$template_id = $tpl ['tpl_id'];
				  	$url = $seed_Setting ['mobunion_app_server'] . "/order";
				  	$res = $Template->send ( $touser, $template_id, $url );
				  }
				}
				
			//商户旗下售出其他商户的产品
		} elseif ($order ['order_type'] == Shop_Model_OrderType::TYPE_NOT_ADMIN_SHOP) {
		   $mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_shop_refund', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns=array();
				    $replacements=array();
					$patterns[]='/{nick_name}/';
				    $patterns[]='/{order_sn}/';
					$patterns[]='/{order_amount}/';
					$patterns[]='/{time}/';
					$replacements[]=$shop['shop_name'];
					$replacements[]=$order['order_sn'];
					$replacements[]=$order['order_amount'];
					$replacements[]=date('Y-m-d H:i:s');
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time ());
					
					$content = $mobileTemp ['temp_content'];
					$patterns=array();
				    $replacements=array();
					$patterns[]='/{nick_name}/';
				    $patterns[]='/{order_sn}/';
					$patterns[]='/{order_amount}/';
					$patterns[]='/{time}/';
					$replacements[]=$admin_shop['shop_name'];
					$replacements[]=$order['order_sn'];
					$replacements[]=$order['order_amount'];
					$replacements[]=date('Y-m-d H:i:s');
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $admin_user ['user_mobile'], $content, time ());
				 }
			
		            //卖家
				    $noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
					$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
					if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
				       preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				       if (isset ( $matches [1] )) {
						  	$replacements = array();
						  	$replacements[] = '尊敬的创业者，您分销商品的订单由于库存不足,已退款,请注意!';
						  	$replacements[] = '库存不足';
				            $replacements[] = $order['order_amount'];
				            $replacements[] = "商品列表：" . $goods_list;
	
				  	$Template = new Wechat_AdvanceAPI_Template ( $bid );
				  	foreach ( $matches [1] as $key => $value ) { //处理数据组合
				  		$Template->combineData ( $value, $replacements [$key] );
				  	}
				  	$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
				  	$template_id = $tpl ['tpl_id'];
				  	$url = $seed_Setting ['mobunion_app_server'] . "/order";
				  	$res = $Template->send ( $touser, $template_id, $url );
				  }
				}
		            //卖家上级
				    $noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
					$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_refund', 'is_actived' => '1' ) );
					if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $admin_user ) && ! empty ( $admin_user ['wc_username'] )) {
				       preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				       if (isset ( $matches [1] )) {
						  	$replacements = array();
						  	$replacements[] = '尊敬的创业者，您分销商品的订单由于库存不足,已退款,请注意!';
						  	$replacements[] = '库存不足';
				            $replacements[] = $order['order_amount'];
				            $replacements[] = "商品列表：" . $goods_list;
	
				  	$Template = new Wechat_AdvanceAPI_Template ( $bid );
				  	foreach ( $matches [1] as $key => $value ) { //处理数据组合
				  		$Template->combineData ( $value, $replacements [$key] );
				  	}
				  	$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $admin_user ['wc_username'];
				  	$template_id = $tpl ['tpl_id'];
				  	$url = $seed_Setting ['mobunion_app_server'] . "/order";
				  	$res = $Template->send ( $touser, $template_id, $url );
				  }
				}
	    } else {
		
	    }
    }
    
    /*增加库存*/
	 function addStock($order){
		$orderGoodsM = new Shop_Model_OrderGoods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodsM = new Shop_Model_Goods('shop');
		$logM = new Seed_Model_Log('system');
		
		$ordergoodses=$orderGoodsM->fetchRows(null,array('order_id'=>$order['order_id']));
		foreach ($ordergoodses as $k =>$ordergoods){
		     if ($ordergoods['stock_id']>0){
				$stock = $goodsStockM->fetchOne(array('stock_id' => $ordergoods['stock_id']),'stock_value');
				$goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value+{$ordergoods['goods_number']}")),array('stock_id' => $ordergoods['stock_id']));
				$goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold-{$ordergoods['goods_number']}")),array('goods_id' => $ordergoods['goods_id']));
			  }else{
				$stock = $goodsStockM->fetchRow(array('goods_id' => $ordergoods['goods_id'], 'group_id' => 0), array('stock_id','stock_value'));
                $goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value+{$ordergoods['goods_number']}")),array('stock_id' => $stock['stock_id']));
				$goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold-{$ordergoods['goods_number']}")),array('goods_id' => $ordergoods['goods_id']));
			  }
		}
	}

}