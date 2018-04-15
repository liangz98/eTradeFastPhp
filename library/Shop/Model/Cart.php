<?php
/**
 * 购物车表模型 (snshop_cart)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_Cart extends Seed_Model_Db
{ 
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'cart';

    public function getDiscount($goods_sn) {
        # 返回值
        $rs = 0;

        # 视图对象
		$fileM = new Seed_Model_Cache2File();
		$mod_name = CURRENT_MODULE_NAME."_".CURRENT_MODULE_TYPE;
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$cachefile = $mod_name."_".strtolower($seed_host_name)."_setting";
		}else{
			$cachefile = $mod_name."_setting";
		}

		$setting = $fileM->get($cachefile);

        # 检查参数
        if( !empty($setting['discount_goods_sns']) && !empty($setting['discount_amount'])) {
            # 前台参数赋值
            $discount_goods_sns = $setting['discount_goods_sns'];//折扣编码
            $discount_amount = $setting['discount_amount'];//折扣值
            $discount_goods_sns_arr = explode("\n", str_replace("\r", '', $discount_goods_sns));//拆分折扣编码
            # 检查传递的编码参数是否折扣编码，以及折扣值是否大于0
            if(in_array($goods_sn, $discount_goods_sns_arr) && $discount_amount > 0) {
                $rs = $setting['discount_amount'];//返回折扣值
            }
        }

        # 返回
        return $rs;
    }
    
    //查询购物车中的数量
	public function fetchCartCount($param=array()){  
		$cartM = new Shop_Model_Cart('shop');
		$goodsM = new Shop_Model_Goods('shop');
		$goodsStockM = new Shop_Model_GoodsStock('shop');
		$goodsStockGroupM = new Shop_Model_GoodsStockGroup('shop');
		$goodsGroupM = new Shop_Model_GoodsGroup('shop');
		
		$condition = array();
		if ($param['user_id']>0){
		  $condition['user_id']=$param['user_id'];
		}else {
		  $condition['user_id']='0';
		  $condition['session_id']=$param['session_id'];
		}
        $condition['is_gift']='0';
        $condition['shop_id']='0';
        $condition['goods_channel']=$param['goods_channel'];
        $mycarts = $cartM->fetchRows(null,$condition);

        $total_number = 0;
		foreach ($mycarts as $k => $cart) {
			$goods = $goodsM->fetchGoods(array('goods_id' => $cart['goods_id'], 'is_on_sale' => '1', 'is_actived' => '1', 'is_auth' => '1'), array('goods_id', 'goods_sn', 'goods_name', 'type_id', 'goods_m_list_image', 'goods_list_image','goods_m_detail_image', 'goods_detail_image','goods_mark', 'goods_weight', 'unit_name', 'coupon_type', 'goods_tips','is_group'));
			if ($goods['is_group']=='0'){///普通商品
				if ($goods['goods_id'] > 0) {
					$total_number += $cart['goods_number'];
				}
			}else{//组合商品
				$group = $goodsGroupM->fetchRow(array('group_id'=>$cart['goods_id']));
				if ($group['group_id']>0){
					$goods_groups = $goodsGroupM->fetchRows(null,array('group_id'=>$cart['goods_id']));
					if (count($goods_groups)>0){
						foreach ($goods_groups as $gk=>$gv){
							$goods = $goodsM->fetchGoods(array('goods_id' => $gv['goods_id'], 'is_on_sale' => '1', 'is_actived' => '1', 'is_auth' => '1'), array('goods_id', 'goods_sn', 'goods_name', 'type_id', 'goods_m_list_image', 'goods_list_image','goods_m_detail_image', 'goods_detail_image','goods_mark', 'goods_weight', 'unit_name', 'coupon_type', 'goods_tips'));
							if ($goods['goods_id'] > 0) {
								$total_number += $gv['goods_number'] * $cart['goods_number'];
            					}
            				}
            			}
            		}
            	}
            }
            return $total_number;
    }
    
}
