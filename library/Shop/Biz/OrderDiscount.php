<?php
/**
 * 订单打折通知
 * @param unknown_type $order
 */
class Shop_Biz_OrderDiscount {
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
				$goods_list .= " ";
			}
		}

		$f = new Seed_Filter_Mobile ();

		//微信通知(消费者)
		$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
		$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_shipping', 'is_actived' => '1' ) );
		if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $wc_customer ) && ! empty ( $wc_customer ['wc_username'] )) {
			preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
			if (isset ( $matches [1] )) {
				$replacements = array();
				$replacements[] = '尊敬的用户，我们很高兴的通知您，订单已发货';
				$replacements[] = $order['order_sn'];
				$replacements[] = $shipping_desc;
				$replacements[] = $invoice_no;
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
			$mobileTemp = $mobileTempM->fetchRow(array('temp_name'=>'order_shipping','is_actived'=>'1'));
			if($mobileTemp['temp_id']>0){
				$content = $mobileTemp['temp_content'];
				$patterns=array();
				$replacements=array();
				$patterns[]='/{nick_name}/';
				$patterns[]='/{order_sn}/';
				$patterns[]='/{shipping_name}/';
				$patterns[]='/{shipping_no}/';
				$patterns[]='/{goods_list}/';
				$patterns[]='/{time}/';
				$replacements[]=$order['user_name'];
				$replacements[] = $order['order_sn'];
				$replacements[] = $shipping_desc;
				$replacements[] = $invoice_no;
				$replacements[]=$goods_list;
				$replacements[]=date('Y-m-d H:i:s');
				$content = preg_replace($patterns, $replacements, $content);
				$mobileOutboxM=new Seed_Model_MobileOutbox('system');
				$mobileOutboxM->mobileSend($order['telephone'], $content, time());
			}
		}
		 
		//创业者短信提醒
	    //代理订单
		if ($order ['order_type'] == Shop_Model_OrderType::TYPE_AGENT) {

			//代理旗下订单
	     } else if ($order ['order_type'] == Shop_Model_OrderType::TYPE_PERSON) {
	     	
		//商户订单
		} else if ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP) {
			//不发信息
			
			
			
			//商户旗下
		} else if ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP_PERSON) {
		      
			//其他商户售出
		} elseif ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP_SHOP) {
		    
				
			//商户旗下售出其他商户的产品
		} elseif ($order ['order_type'] == Shop_Model_OrderType::TYPE_NOT_ADMIN_SHOP) {
		  
			
	    } else {
		
	   }
    
   }
}