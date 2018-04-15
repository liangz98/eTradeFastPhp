<?php
class Shop_Biz_OrderReceive {	
    /**
	 * 收货通知
	 * @param unknown_type $order
	 */
	public static function sendNotice($order_id) {
		$seed_Setting = Shop_Biz_Base::getSetting();
	    $orderM = new Shop_Model_Order('shop');
        $userM = new Seed_Model_User('system');
        $shopM = new Shop_Model_Shop('shop');
        $mobileTempM = new Seed_Model_MobileTemplate('system');
        $mobileOutboxM = new Seed_Model_MobileOutbox('system');
        $wechatUserM = new Wechat_Model_User ( 'wechat' );
        $noteTplM = new Wechat_Model_Notetpl('wechat');
        $userAddressM = new Seed_Model_UserAddress('system');
        
        $order = $orderM->fetchRow(array('order_id' => $order_id));
        //卖家店铺信息
        if (isset($order['shop_id']) && $order['shop_id'] > 0) {
            $shop = $shopM->fetchRow(array('shop_id' => $order['shop_id']));
            if ($shop['shop_id'] > 0) {
                $shop_user = $userM->fetchRow(array('user_id' => $shop['user_id']));
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
        
		$goods_list = "";
			$orderGoodsM = new Shop_Model_OrderGoods ( 'shop' );
			$goodses = $orderGoodsM->fetchRows ( null, array ('order_id' => $order_id ) );
			foreach ( $goodses as $v ) {
				$goods_list .= "[" . $v ['goods_number'] . "] × " . $v ['goods_name'] . "";
				if (! empty ( $v ['goods_attr'] ))
				$goods_list .= " " . $v ['goods_attr'];
				$goods_list .= "";
			}

		$f = new Seed_Filter_Mobile();
		
		 //消费者短信提醒
		 if ($order['user_id']>0 && strlen ( $f->filter ( $useraddress['mobile'] ) ) == 11){
			$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_receive', 'is_actived' => '1' ) );
			if ($mobileTemp ['temp_id'] > 0) {
				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{user_name}/';
				$patterns [] = '/{order_sn}/';
				$patterns [] = '/{shipping_name}/';
				$patterns [] = '/{shipping_no}/';
				$patterns [] = '/{time}/';
				$replacements [] = $order ['user_name'];
				$replacements [] = $order ['order_sn'];
				$replacements [] = $order ['shipping_desc'];
				$replacements [] = $order ['shipping_no'];
				$replacements [] = date ( 'Y-m-d H:i:s' );
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $useraddress['mobile'], $content, time () );
			}
		}
	}	
}