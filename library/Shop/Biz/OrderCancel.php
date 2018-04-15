<?php
/**
 * 订单取消通知
 * @param unknown_type $order
 */
class Shop_Biz_OrderCancel {
	
	public static function sendNotice($order,$seed_Setting) {
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
		$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
		if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $wc_customer ) && ! empty ( $wc_customer ['wc_username'] )) {
			preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
			if (isset ( $matches [1] )) {
				$replacements = array();
				$replacements[] = '您好,您的订单已成功取消';
				$replacements[] = $order['order_sn'];
				$replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				$replacements[] = "欢迎您下次购买！我们将竭诚为您服务";

				$Template = new Wechat_AdvanceAPI_Template ( $bid );
				foreach ( $matches [1] as $key => $value ) { //处理数据组合
					$Template->combineData ( $value, $replacements [$key] );
				}
				$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $wc_customer ['wc_username'];
				$template_id = $tpl ['tpl_id'];
				$url = $seed_Setting ['vuser_app_server'] . "/order";
				$res = $Template->send ( $touser, $template_id, $url );
			}
		}

		//短信提醒(消费者)
		if(strlen ( $f->filter ( $useraddress['telephone'] ) ) == 11){
			$mobileTempM = new Seed_Model_MobileTemplate('system');
			$mobileTemp = $mobileTempM->fetchRow(array('temp_name'=>'order_cancel_customer_notice','is_actived'=>'1'));
			if($mobileTemp['temp_id']>0){
				$content = $mobileTemp['temp_content'];
				$patterns=array();
				$replacements=array();
				$patterns[]='/{order_sn}/';
				$patterns[]='/{time}/';
				$replacements[] = $order['order_sn'];
				$replacements[]=date('Y-m-d H:i:s');
				$content = preg_replace($patterns, $replacements, $content);
				$mobileOutboxM=new Seed_Model_MobileOutbox('system');
				$mobileOutboxM->mobileSend($useraddress['telephone'], $content, time());
			}
		}

		//短信提醒创业者
		if ($order ['shop_id'] > 0) {
			//代理订单
			if ($order ['agent_id'] > 0 && strlen ( $f->filter ( $shop_user ['user_mobile'] ) ) == 11 && $order ['order_type'] == Shop_Model_OrderType::TYPE_AGENT) {
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_shop_notice', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns = array ();
					$replacements = array ();
					$patterns [] = '/{order_sn}/';
				    $replacements [] = $order ['order_sn'];
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time () );
				}

			   $mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_agent_notice', 'is_actived' => '1' ) );
			   if ($mobileTemp ['temp_id'] > 0 &&  strlen ( $f->filter ($agent_user ['user_mobile'])) == 11) {
				   $content = $mobileTemp ['temp_content'];
				   $patterns = array ();
				   $replacements = array ();
				   $patterns [] = '/{order_sn}/';
				   $replacements [] = $order ['order_sn'];
				   $content = preg_replace ( $patterns, $replacements, $content );
				   $mobileOutboxM->mobileSend ( $agent_user ['user_mobile'], $content, time () );
			   }
			//微信通知(本店)
			$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
			$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
			if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
				preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				if (isset ( $matches [1] )) {
					$replacements = array();
				    $replacements[] = '您好,您的订单有消费者已取消';
				    $replacements[] = $order['order_sn'];
				    $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				    $replacements[] = "请跟进!";
					
					$Template = new Wechat_AdvanceAPI_Template ( $bid );
					foreach ( $matches [1] as $key => $value ) { //处理数据组合
						$Template->combineData ( $value, $replacements [$key] );
					}
					$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
					$template_id = $tpl ['tpl_id'];
					$url = $seed_Setting ['mobunion_app_server'] . "/order/";
					$res = $Template->send ( $touser, $template_id, $url );
				}
			}

			//发货店铺
			$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
			$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
			if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $agent_user ) && ! empty ( $agent_user ['wc_username'] )) {
				preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				if (isset ( $matches [1] )) {
					$replacements = array();
				    $replacements[] = '您好,您分销商品的订单有消费者已取消';
				    $replacements[] = $order['order_sn'];
				    $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				    $replacements[] = "请跟进!";
					
					$Template = new Wechat_AdvanceAPI_Template ( $bid );
					foreach ( $matches [1] as $key => $value ) { //处理数据组合
						$Template->combineData ( $value, $replacements [$key] );
					}
					$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $agent_user ['wc_username'];
					$template_id = $tpl ['tpl_id'];
					$url = $seed_Setting ['mobunion_app_server'] . "/order/";
					$res = $Template->send ( $touser, $template_id, $url );
				}
			}

				//代理旗下订单
			} elseif ($order ['order_type'] == Shop_Model_OrderType::TYPE_PERSON) {
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_shop_notice', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns = array ();
					$replacements = array ();
					$patterns [] = '/{order_sn}/';
				    $replacements [] = $order ['order_sn'];
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time () );
				}

			$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_agent_notice', 'is_actived' => '1' ) );
			if ($mobileTemp ['temp_id'] > 0 &&  strlen ( $f->filter ($agent_user ['user_mobile'])) == 11) {
				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{order_sn}/';
				$replacements [] = $order ['order_sn'];
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $agent_user ['user_mobile'], $content, time () );

				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{order_sn}/';
				$replacements [] = $order ['order_sn'];
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $admin_user ['user_mobile'], $content, time () );
			}

				//微信通知(本店)
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
						
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}

				//微信通知(本店上级)
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $admin_user ) && ! empty ( $admin_user['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您分销商品的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
				        
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $admin_user['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}

				//发货店铺
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $agent_user ) && ! empty ( $agent_user ['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您分销商品的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
						
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $agent_user ['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}

				//商户订单
			} elseif ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP) {
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_shop_notice', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns = array ();
					$replacements = array ();
					$patterns [] = '/{order_sn}/';
				    $replacements [] = $order ['order_sn'];
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time () );
				}

				//微信通知(本店)
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($order ['order_amount'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
				        
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
				        $template_id = $tpl ['tpl_id'];
				        $url = $seed_Setting ['mobunion_app_server'] . "/order/";
				        $res = $Template->send ( $touser, $template_id, $url );
			      }
		        }
				//商户旗下
			} elseif (strlen ( $f->filter ( $shop_user ['user_mobile'] ) ) == 11 && $order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP_PERSON) {
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_shop_notice', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns = array ();
					$replacements = array ();
					$patterns [] = '/{order_sn}/';
				    $replacements [] = $order ['order_sn'];
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time () );
				}
				
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_agent_notice', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {	
					$content = $mobileTemp ['temp_content'];
					$patterns = array ();
					$replacements = array ();
					$patterns [] = '/{order_sn}/';
				    $replacements [] = $order ['order_sn'];
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $admin_user ['user_mobile'], $content, time () );
				}
				

				//微信通知(本店)
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
						
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}

				//微信通知(本店上级)
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($tpl && $bid > 0 && isset ( $admin_user ) && ! empty ( $admin_user['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您分销商品的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
						
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $admin_user['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}

				//发货店铺
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($tpl && $bid > 0 && isset ( $agent_user ) && ! empty ( $agent_user ['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您分销商品的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
				        
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $agent_user ['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}
					
				//其他商户售出
			} elseif ($order ['order_type'] == Shop_Model_OrderType::TYPE_SHOP_SHOP) {
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_shop_notice', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns = array ();
					$replacements = array ();
					$patterns [] = '/{order_sn}/';
				    $replacements [] = $order ['order_sn'];
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time () );
				}

			  $mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_agent_notice', 'is_actived' => '1' ) );
			  if ($mobileTemp ['temp_id'] > 0 &&  strlen ( $f->filter ($agent_user ['user_mobile'])) == 11) {
				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{order_sn}/';
				$replacements [] = $order ['order_sn'];
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $agent_user ['user_mobile'], $content, time () );
			 }

				//微信通知(本店)
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
				        
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}

				//发货店铺
				$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
				$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
				if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $agent_user ) && ! empty ( $agent_user ['wc_username'] )) {
					preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
					if (isset ( $matches [1] )) {
						$replacements = array();
				        $replacements[] = '您好,您分销商品的订单有消费者已取消';
				        $replacements[] = $order['order_sn'];
				        $replacements[] = date('Y-m-d H:i:s', $order['update_time']);
				        $replacements[] = "请跟进!";
				        
						$Template = new Wechat_AdvanceAPI_Template ( $bid );
						foreach ( $matches [1] as $key => $value ) { //处理数据组合
							$Template->combineData ( $value, $replacements [$key] );
						}
						$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $agent_user ['wc_username'];
						$template_id = $tpl ['tpl_id'];
						$url = $seed_Setting ['mobunion_app_server'] . "/order/";
						$res = $Template->send ( $touser, $template_id, $url );
					}
				}
					
				//商户旗下售出其他商户的产品
			} elseif (strlen ( $f->filter ( $shop_user ['user_mobile'] ) ) == 11 && $order ['order_type'] == Shop_Model_OrderType::TYPE_NOT_ADMIN_SHOP) {
				$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_shop_notice', 'is_actived' => '1' ) );
				if ($mobileTemp ['temp_id'] > 0) {
					$content = $mobileTemp ['temp_content'];
					$patterns = array ();
					$replacements = array ();
					$patterns [] = '/{order_sn}/';
				    $replacements [] = $order ['order_sn'];
					$content = preg_replace ( $patterns, $replacements, $content );
					$mobileOutboxM->mobileSend ( $shop_user ['user_mobile'], $content, time () );
				}

			$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_agent_notice', 'is_actived' => '1' ) );
			if ($mobileTemp ['temp_id'] > 0 &&  strlen ( $f->filter ($agent_user ['user_mobile'])) == 11) {
				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{order_sn}/';
				$replacements [] = $order ['order_sn'];
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $agent_user ['user_mobile'], $content, time () );

				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{order_sn}/';
				$replacements [] = $order ['order_sn'];
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $admin_user ['user_mobile'], $content, time () );
			}
			//微信通知(本店)
			$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
			$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
			if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $shop_user ) && ! empty ( $shop_user ['wc_username'] )) {
				preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				if (isset ( $matches [1] )) {
					$replacements = array();
					$replacements[] = '您好,您的订单有消费者已取消';
					$replacements[] = $order['order_sn'];
					$replacements[] = date('Y-m-d H:i:s', $order['update_time']);
					$replacements[] = "请跟进!";
					$Template = new Wechat_AdvanceAPI_Template ( $bid );
					foreach ( $matches [1] as $key => $value ) { //处理数据组合
						$Template->combineData ( $value, $replacements [$key] );
					}
					$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $shop_user ['wc_username'];
					$template_id = $tpl ['tpl_id'];
					$url = $seed_Setting ['mobunion_app_server'] . "/order/";
					$res = $Template->send ( $touser, $template_id, $url );
				}
			}

			//微信通知(本店上级)
			$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
			$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
			if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $admin_user ) && ! empty ( $admin_user['wc_username'] )) {
				preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				if (isset ( $matches [1] )) {
					$replacements = array();
					$replacements[] = '您好,您分销商品的订单有消费者已取消';
					$replacements[] = $order['order_sn'];
					$replacements[] = date('Y-m-d H:i:s', $order['update_time']);
					$replacements[] = "请跟进!";
					$Template = new Wechat_AdvanceAPI_Template ( $bid );
					foreach ( $matches [1] as $key => $value ) { //处理数据组合
						$Template->combineData ( $value, $replacements [$key] );
					}
					$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $admin_user['wc_username'];
					$template_id = $tpl ['tpl_id'];
					$url = $seed_Setting ['mobunion_app_server'] . "/order/";
					$res = $Template->send ( $touser, $template_id, $url );
				}
			}

			//发货店铺
			$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
			$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
			if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $agent_user ) && ! empty ( $agent_user ['wc_username'] )) {
				preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				if (isset ( $matches [1] )) {
					$replacements = array();
					$replacements[] = '您好,您分销商品的订单有消费者已取消';
					$replacements[] = $order['order_sn'];
					$replacements[] = date('Y-m-d H:i:s', $order['update_time']);
					$replacements[] = "请跟进!";
					$Template = new Wechat_AdvanceAPI_Template ( $bid );
					foreach ( $matches [1] as $key => $value ) { //处理数据组合
						$Template->combineData ( $value, $replacements [$key] );
					}
					$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $agent_user ['wc_username'];
					$template_id = $tpl ['tpl_id'];
					$url = $seed_Setting ['mobunion_app_server'] . "/order/";
					$res = $Template->send ( $touser, $template_id, $url );
				}
			}
			}
		} else { //平台售出商户产品
			$mobileTemp = $mobileTempM->fetchRow ( array ('temp_name' => 'order_cancel_shop_notice', 'is_actived' => '1' ) );
			if ($mobileTemp ['temp_id'] > 0 &&  strlen ( $f->filter ($agent_user ['user_mobile'])) == 11) {
				$content = $mobileTemp ['temp_content'];
				$patterns = array ();
				$replacements = array ();
				$patterns [] = '/{order_sn}/';
				$replacements [] = $order ['order_sn'];
				$content = preg_replace ( $patterns, $replacements, $content );
				$mobileOutboxM->mobileSend ( $agent_user ['user_mobile'], $content, time () );
			}

			//发货店铺
			$noteTplM = new Wechat_Model_Notetpl ( 'wechat' );
			$tpl = $noteTplM->fetchRow ( array ('nt_name' => 'order_cancel_notice', 'is_actived' => '1' ) );
			if ($order ['shop_account'] > 0 && $tpl && $bid > 0 && isset ( $agent_user ) && ! empty ( $agent_user ['wc_username'] )) {
				preg_match_all ( "/\{\{([\w]+)\.DATA\}\}/is", $tpl ['nt_data'], $matches, PREG_PATTERN_ORDER );
				if (isset ( $matches [1] )) {
					$replacements = array();
					$replacements[] = '您好,您分销商品的订单有消费者已取消';
					$replacements[] = $order['order_sn'];
					$replacements[] = date('Y-m-d H:i:s', $order['update_time']);
					$replacements[] = "请跟进!";
					$Template = new Wechat_AdvanceAPI_Template ( $bid );
					foreach ( $matches [1] as $key => $value ) { //处理数据组合
						$Template->combineData ( $value, $replacements [$key] );
					}
					$touser = $tpl ['send_to'] ? $tpl ['send_to'] : $agent_user ['wc_username'];
				$template_id = $tpl ['tpl_id'];
				$url = $seed_Setting ['mobunion_app_server'] . "/order/";
				$res = $Template->send ( $touser, $template_id, $url );
			}
		  }
		} 
	}
	
	//日志
	
}