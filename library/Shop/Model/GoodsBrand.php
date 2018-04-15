<?php
/**
 * 商品品牌表模型 (snshop_goods_brands)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_GoodsBrand extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'goods_brands';
    
    /**
     * 查询商品品牌记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    function fetchBrands($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
            $select->joinleft($this->_prefix."goods_type_brands AS t2", 't1.brand_id = t2.brand_id',array('type_id'));
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where("t1.".$k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }
            if($order_by!=null)
                $select->order($order_by);
            $select->group('t1.brand_id');
            $sql = $select->__toString();
            if($debug)echo $sql;
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
