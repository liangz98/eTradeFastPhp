<?php
class CheckorderController extends Shop_Controller_Action {
	public function closeAction(){
		$orderM = new Shop_Model_Order('shop');
		$goodsM = new Shop_Model_Goods('shop');
		$orderGoodsM = new Shop_Model_OrderGoods ( 'shop' );
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodsdiscountM = new Shop_Model_GoodsDiscount('shop');

        $today = getdate();
        //前一天的订单
        $begin_time = mktime(0, 0, 0, $today['mon'], $today['mday'] - 1, $today['year']);
        $end_time = mktime(23, 59, 59, $today['mon'], $today['mday'] - 1, $today['year']);

		$dataSet = array();
        $dataSet["add_time >'" . $begin_time . "'"] = null;
        $dataSet["add_time <'" . $end_time . "'"] = null;
		$dataSet["payment_online=".'1'] = null;
		$dataSet["order_cs_status='".Shop_Model_Order::CS_WAIT_PAY."'"] = null;// 等待付款
		$dataSet["order_status='".Shop_Model_Order::OS_CONFIRMED."'"] = null;// 已确认
		$dataSet["payment_status='".Shop_Model_Order::PS_UNPAYED ."'"] = null;// 未付款
		$dataSet["shipping_status='".Shop_Model_Order::SS_UNSHIPPED."'"] = null;// 未发货
        $dataSet["store_id=0"] = null;// 非辅助下单
		$orders = $orderM->fetchRows(null,$dataSet,'add_time ASC',"*");

		$total = 0;
		if (is_array ( $orders ) && count ( $orders ) > 0) {
			foreach ($orders as $order){
				$orderGoods = $orderGoodsM->fetchRows ( array (0, 0 ), array ('order_id' => $order ['order_id'] ) );
				if (is_array ( $orderGoods ) && count ( $orderGoods ) > 0){
					foreach($orderGoods as $orderGood){
						$checkGoods = $goodsM->fetchRow(array('goods_id'=>$orderGood['goods_id']));
						$goodsStockM = new Shop_Model_GoodsStock('shop');
						$checkStock = $goodsStockM->fetchRow(array('stock_id' => $orderGood['stock_id'],'goods_id'=>$orderGood['goods_id']));
						if (time()>$order['close_time']){
							$dataSet = array ('update_last_userid' => $this->view->seed_User ['user_id'], 'update_time' => time (), "order_status" => Shop_Model_Order::OS_CANCELED, "payment_status" => Shop_Model_Order::PS_UNPAYED, "shipping_status" => Shop_Model_Order::SS_UNSHIPPED, 'order_cs_status' => Shop_Model_Order::CS_FINISHED );
							if ($orderM->updateRow ( $dataSet, array ('order_id' => $orderGood['order_id'] ) )) {
								$orderActionM = new Shop_Model_OrderAction ( 'shop' );
								$action_note = '超时未付款';
								$actionData = array ();
								$actionData ['order_status'] = Shop_Model_Order::OS_CANCELED;
								$actionData ['payment_status'] = Shop_Model_Order::PS_UNPAYED;
								$actionData ['shipping_status'] = Shop_Model_Order::SS_UNSHIPPED;
								$actionData ['action_note'] = $action_note;
								$actionData ['log_time'] = time ();
								$actionData ['action_user_id'] = 0;
								$actionData ['action_user_name'] = "系统";
								$actionData ['order_id'] = $orderGood['order_id'];
								$actionData ['action_name'] = "系统自动取消";
								$orderActionM->insertRow ( $actionData );
								//增加回库存
								$goodsStockM->updateRow(array('stock_value'=>new Zend_Db_Expr("stock_value+{$orderGood['goods_number']}")),array('stock_id' => $orderGood['stock_id']));
								//避免减销量出现大数值的情况
								$goodsDetail=$goodsM->fetchRow(array('goods_id' => $orderGood['goods_id']));
								if($goodsDetail['sold']-$orderGood['goods_number']>=0){
									$goodsM->updateRow(array('sold'=>new Zend_Db_Expr("sold-{$orderGood['goods_number']}")),array('goods_id' => $orderGood['goods_id']));
								}
								$total ++;
							}
						}
					}
				}
			}
		}
		exit ( "CLOSE_ORDER_COUNT:{$total}");
	}
}