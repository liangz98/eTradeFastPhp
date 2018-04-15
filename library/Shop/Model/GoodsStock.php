<?php
/**
 * 商品库存表模型 (snshop_goods_stocks)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_GoodsStock extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'goods_stocks';
    
    
    /**
     * 查询商品在途数量
     *
     * @param	array	$condition	AND条件查询数组
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	array|string	$order_by	查询记录排序
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    public function fetchGoodsStockesOnOrderCount($condition , $fetch_fields="*" , $order_by=null , $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix."orders as o",array('SUM(og.goods_number)'));
            $select->joinleft($this->_prefix."order_goods AS og", 'og.order_id = o.order_id', null);
            if(is_array($condition)){
                foreach ($condition as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where("o.".$k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }
            if($order_by!=null)
                $select->order($order_by);
            $sql = $select->__toString();
            if($debug)
                echo $sql;
            $count = $this->_db->fetchOne($sql);
            return $count;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
