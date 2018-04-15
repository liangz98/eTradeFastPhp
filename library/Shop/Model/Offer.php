<?php
/**
 * 优惠券模型 (snshop_offers)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_Offer extends Seed_Model_Db
{    
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'offers';
  
   /**
     * 检查优惠券是否可用
     *
     * @param	array	$arr	优惠券记录
     * @param	type	$min_amount	最低消费
     * @return	
     *
     */
    function checkValid($arr,$min_amount = 0){
           if(empty($arr) || !is_array($arr) || !isset($arr['cate_id'])){
                return array();
           }
           $offercateM = new Shop_Model_OfferCate('shop');
           $fetch_fields = array(
               'cate_name',
               'begin_time',
               'end_time',
               'min_amount',
               'discount',
               'discount_type'
           );
           $offercate = $offercateM->fetchRow(array('cate_id'=>$arr['cate_id'],'is_actived'=>'1'),$fetch_fields);
           if(empty($offercate)){
               return array();
           }
           if(time() < $offercate['begin_time'] || time() > $offercate['end_time']){
                return array();
           } 
           if($min_amount < $offercate['min_amount'] || $offercate['min_amount'] < 0){
               return array();
           }
           if($offercate['discount'] <= 0){
               return array();
           }
           foreach($offercate as $k=>$v){
               $arr[$k] = $v;
           }
           return $arr;
    }
}