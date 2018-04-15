<?php
/**
 * 订单拆分类
 *
 * @category   Biz
 * @package    Shop_Biz
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Biz_OrderSplit
{
	/**
	 * 拆分订单，订单拆分结束则会自动删除原有订单
	 * @param array $orderDetail
	 */
	public static function split($orderDetail){
		///拆分订单开始
		if(empty($orderDetail) || !is_array($orderDetail) || $orderDetail['order_id']<1){
			return false;
		}
		$seed_Setting = Shop_Biz_Base::getSetting();
		$orderM = new Shop_Model_Order('shop');
		$orderGoodsM = new Shop_Model_OrderGoods('shop');
        $goodsM = new Shop_Model_Goods('shop');
        $goodsStockM = new Shop_Model_GoodsStock('shop');
        
        $conditions = array();
        $conditions['order_id']=$orderDetail['order_id'];
        $conditions['is_split']='1';
        $orderDetail = $orderM->fetchRow($conditions);
        if($orderDetail['order_id']<1)return false;
        
        $agent_ids = $orderGoodsM->fetchRowsPairs(null,array('order_id' => $orderDetail['order_id']),null,array('agent_id','agent_id'));
		if(is_array($agent_ids) && count($agent_ids)>0){
			$orderBackup=array();//用来保存订单数据为了将同一个商家的商品放到同一订单。by:ll 2014.12.11 11:20
			foreach ($agent_ids as $k=>$agent_id){
				
				$total_number = 0;
				$goods_amount = 0;
				$total_weight = 0;
				$discount_amount = 0;
				$total_union_price = 0;
				$recommend_amount = 0;
				
				$order = $orderM->fetchRow(array('order_id'=>$orderDetail['order_id']));
				$order_goodses = $orderGoodsM->fetchRows(null,array('order_id' => $order['order_id'],'agent_id'=>$agent_id));
				if(is_array($order_goodses) && count($order_goodses)>0){
					foreach ($order_goodses as $order_goods){
						$total_number+=$order_goods['goods_number'];
						$goods_amount+=$order_goods['goods_price'] * $order_goods['goods_number'];
						$total_weight+=$order_goods['goods_weight'] * $order_goods['goods_number'];
						$total_union_price+=$order_goods['union_price'] * $order_goods['goods_number'];
						$recommend_amount+=$order_goods['recommend_price'] * $order_goods['goods_number'];
					}
				}
				
				//这里做一个判断，如果订单里有了这个商品的分销商就把商品加入到这个订单。by:ll 2014.12.11 12:00
				if(!empty($orderBackup)){
					foreach($orderBackup as $k1=>$backup){
						if($agent_id==$backup['agent_id']){
							$orderdata = $orderM->fetchRow(array('order_id'=>$backup['order_id']));
							$orderData = array();
							$orderData['goods_amount'] = $orderdata['goods_amount']+$goods_amount;
							$orderData['goods_union_amount'] = $orderdata['goods_union_amount']+$total_union_price;
							$orderData['recommend_amount'] = $orderdata['recommend_amount']+$recommend_amount;
							$orderData['shop_account'] = $orderdata['shop_account']+$orderData['goods_amount'] - $orderData['goods_union_amount'];
							$orderData['goods_weight'] = $orderdata['goods_weight']+$total_weight;
							$orderData['order_amount'] = $orderdata['order_amount']+$goods_amount;
							$orderData['payment_real_amount'] = $orderdata['payment_real_amount']+$goods_amount;
							$order_id = $orderM->updateRow($orderData,array('order_id' => $backup['order_id']));
							$orderGoodsM->updateRow(array('order_id' => $backup['order_id']), array('order_id' => $backup['order_id'],'agent_id'=>$agent_id));
							break;
						}else{
							$orderData = array();
							$orderData = $order;
							unset($orderData['order_id']);
							$orderData['order_type'] = 5; //拆过的订单 添加这个类型用来区分这个订单是不是拆出来的。by:ll 2014.12.15 15:17
							$orderData['is_split'] = 0; //避免再次拆分
							$orderData['agent_id'] = $agent_id;
							$order_prefix = (isset($seed_Setting['order_prefix'])) ? $seed_Setting['order_prefix'] : "";
							$orderData['order_sn'] = $order_prefix . date("YmdHis") . mt_rand(10000, 99999);
							$orderData['goods_amount'] = $goods_amount;
							$orderData['goods_union_amount'] = $total_union_price;
							$orderData['recommend_amount'] = $recommend_amount;
							$orderData['shop_account'] = $orderData['goods_amount'] - $orderData['goods_union_amount'];
							$orderData['goods_weight'] = $total_weight;
							$orderData['order_amount'] = $goods_amount;
							$orderData['payment_real_amount'] = $goods_amount;
							$order_token = $orderData['order_token'] = md5($orderData['order_sn'] . rand(1000, 9999));
							$order_id = $orderM->insertRow($orderData);
							$orderGoodsM->updateRow(array('order_id' => $order_id), array('order_id' => $order['order_id'],'agent_id'=>$agent_id));
						}
					}
				}else{
					
					///准备订单数据
					$orderData = array();
					$orderData = $order;
					unset($orderData['order_id']);
					$orderData['order_type'] = 5; //拆过的订单 添加这个类型用来区分这个订单是不是拆出来的。by:ll 2014.12.15 15:17
					$orderData['is_split'] = 0; //避免再次拆分
					$orderData['agent_id'] = $agent_id;
					$order_prefix = (isset($seed_Setting['order_prefix'])) ? $seed_Setting['order_prefix'] : "";
					$orderData['order_sn'] = $order_prefix . date("YmdHis") . mt_rand(10000, 99999);
					$orderData['goods_amount'] = $goods_amount;
					$orderData['goods_union_amount'] = $total_union_price;
					$orderData['recommend_amount'] = $recommend_amount;
					$orderData['shop_account'] = $orderData['goods_amount'] - $orderData['goods_union_amount'];
					$orderData['goods_weight'] = $total_weight;
					$orderData['order_amount'] = $goods_amount;
					$orderData['payment_real_amount'] = $goods_amount;
					$order_token = $orderData['order_token'] = md5($orderData['order_sn'] . rand(1000, 9999));
					$order_id = $orderM->insertRow($orderData);
					$orderGoodsM->updateRow(array('order_id' => $order_id), array('order_id' => $order['order_id'],'agent_id'=>$agent_id));
				}
				//将order_id和agent_id保存备用。by:ll 2014.12.12 12:08
				if($k<(count($agent_ids))){
					$orderBackup[$k]['order_id']=$orderData['order_id'];
					$orderBackup[$k]['order_sn']=$orderData['order_sn'];
					$orderBackup[$k]['agent_id']=$orderData['agent_id'];
				}
				
			}
		}
		///删除原来的订单
		$orderM->deleteRow(array('order_id'=>$orderDetail['order_id']));
		return true;
	}
}