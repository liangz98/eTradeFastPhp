<?php
class Shop_Biz_OrderPay {
	/**
	 * 发送通知
	 * @param unknown_type $order
	 */
	public static function sendNotice($order_id) {
		$seed_Setting = Shop_Biz_Base::getSetting();		
		$orderM = new Shop_Model_Order('shop');
		$userM = new Seed_Model_User ( 'system' );
		$shopM = new Shop_Model_Shop ( 'shop' );
		$wechatUserM = new Wechat_Model_User ( 'wechat' );
		$noteTplM = new Wechat_Model_Notetpl('wechat');
		$mobileTempM = new Seed_Model_MobileTemplate('system');
		$mobileOutboxM = new Seed_Model_MobileOutbox('system');
		$userAddressM = new Seed_Model_UserAddress('system');
		
		$order = $orderM->fetchRow ( array ('order_id' => $order_id ) );
		
		//卖家店铺信息
		if (isset ( $order ['shop_id'] ) && $order ['shop_id'] > 0) {
			$shop = $shopM->fetchRow ( array ('shop_id' => $order ['shop_id'] ) );
			if ($shop ['shop_id'] > 0) {
				$shop_user = $userM->fetchRow ( array ('user_id' => $shop ['user_id'] ) );
				$wc_user = $wechatUserM->fetchRow ( array ('user_id' => $order ['user_id'] ) );
			}
		}

		//发货店铺信息
		if (isset ( $order ['agent_id'] ) && $order ['agent_id'] > 0) {
			$agent = $shopM->fetchRow ( array ('shop_id' => $order ['agent_id'] ) );
			if ($agent ['shop_id'] > 0) {
				$agent_user = $userM->fetchRow ( array ('user_id' => $agent ['user_id'] ) );
				$wc_agent_user = $wechatUserM->fetchRow ( array ('user_id' => $agent ['user_id'] ) );
			}
		}

		//消费者信息
		if (isset($order['user_id']) && $order['user_id'] >0){
			$useraddress = $userAddressM->fetchRow(array('user_id'=>$order['user_id'],'is_default'=>'1'));
			$customer = $userM->fetchRow(array('user_id' => $order['user_id']));
			$wc_customer = $wechatUserM->fetchRow ( array ('user_id' => $order['user_id'] ) );
		}
		 
		$f = new Seed_Filter_Mobile ();
		
		$goods_list = "";
		$orderGoodsM = new Shop_Model_OrderGoods ( 'shop' );
		$goodses = $orderGoodsM->fetchRows ( null, array ('order_id' => $order_id ) );

		foreach ( $goodses as $v ) {
			$goods_list .= "[" . $v ['goods_number'] . "] × " . $v ['goods_name'] . "";
			if (! empty ( $v ['goods_attr'] ))
			$goods_list .= " " . $v ['goods_attr'];
			$goods_list .= "";
		}
		
	    //短信提醒(管理员)
        $mobileTempM = new Seed_Model_MobileTemplate('system');
        $mobileTemps = $mobileTempM->fetchRows(null, array('temp_name' => 'payed_notice', 'is_actived' => '1'));
        $mobileOutboxM = new Seed_Model_MobileOutbox('system');
        foreach ($mobileTemps as $mobileTemp) {
            if ($mobileTemp['temp_id'] > 0 && strlen($mobileTemp['send_to']) == 11) {
                $content = $mobileTemp['temp_content'];
                $patterns = array();
                $replacements = array();
                $patterns[] = '/{user_name}/';
                $patterns[] = '/{order_sn}/';
                $patterns[] = '/{order_amount}/';
                $patterns [] = '/{shipping_fee}/';
				$patterns [] = '/{payment_fee}/';
				$patterns [] = '/{tax_amount}/';
                $patterns[] = '/{time}/';
                $replacements[] = $order['user_name'];
                $replacements[] = $order['order_sn'];
                $replacements[] = $order['order_amount'];
                $replacements [] = $order ['shipping_fee'];
				$replacements [] = $order ['payment_fee'];
				$replacements [] = $order ['tax_amount'];
                $replacements[] = date('Y-m-d H:i:s');
                $content = preg_replace($patterns, $replacements, $content);
                $mobileOutboxM->mobileSend($mobileTemp['send_to'],$content,time());
            }
        }
	
		//消费者短信
		if ($order['user_id']>0 && strlen ( $f->filter ( $useraddress['mobile'] ) ) == 11){
			$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_payed_notice', 'is_actived' => '1' ) );
			if ($mobileTemp ['temp_id'] > 0) {
				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{user_name}/';
				$patterns [] = '/{order_sn}/';
				$patterns [] = '/{order_amount}/';
				$patterns [] = '/{shipping_fee}/';
				$patterns [] = '/{payment_fee}/';
				$patterns [] = '/{tax_amount}/';
				$patterns [] = '/{time}/';
				$replacements [] = $order ['user_name'];
				$replacements [] = $order ['order_sn'];
				$replacements [] = $order ['order_amount'];
				$replacements [] = $order ['shipping_fee'];
				$replacements [] = $order ['payment_fee'];
				$replacements [] = $order ['tax_amount'];
				$replacements [] = date ( 'Y-m-d H:i:s' );
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $useraddress['mobile'], $content, time () );
			}
		}
		
	}
}