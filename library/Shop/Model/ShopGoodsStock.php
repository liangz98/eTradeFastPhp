<?php
/**
 * 经销商商品库存表模型 (snshop_shop_goods_stocks)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_ShopGoodsStock extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'shop_goods_stocks';
    
    /**
     * 查询单条经销商商品库存记录
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	array|string	$order_by	查询记录排序
     * @param	boolean	$debug	是否输出SQL语句
     * @return	
     *
     */
    public function fetchStock($condiction , $fetch_fields="*" , $order_by=null , $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",array('sgs_id','shop_id','goods_id','refer_stock_id', 'stock_price'));
            $select->joinleft($this->_prefix."goods_stocks AS t2", 't1.refer_stock_id = t2.stock_id', $fetch_fields);
            if(is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $select->where($k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }
            if($order_by!=null)
                $select->order($order_by);
            $sql = $select->__toString();
            if($debug)echo $sql;
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }
	
	/**
     * 查询单条经销商所在地区商品库存记录
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	array|string	$order_by	查询记录排序
     * @param	boolean	$debug	是否输出SQL语句
     * @return	
     *
     */
    public function fetchBranchStock($condiction , $fetch_fields="*" , $order_by=null , $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",array('sgs_id','shop_id','goods_id','refer_stock_id', 'stock_price'));
            $select->joinleft($this->_prefix."branches_goods_stocks AS t2", 't1.refer_stock_id = t2.stock_id', $fetch_fields);
            if(is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $select->where($k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }
            if($order_by!=null)
                $select->order($order_by);
            $sql = $select->__toString();
            if($debug)echo $sql;
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 查询经销商商品库存记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array	$orcondition	OR条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    function fetchStocks($limit = array(0,0) , $condiction=null , $orcondition=null , $order_by=null , $fetch_fields="*" , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",array('sgs_id','shop_id','goods_id','refer_stock_id', 'stock_price'));
            $select->joinleft($this->_prefix."goods_stocks AS t2", 't1.refer_stock_id = t2.stock_id', $fetch_fields);
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $select->where($k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }
            
            if(isset($orcondition) && is_array($orcondition)){
                $orwhere=array();
                foreach ($orcondition as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $orwhere[]=$this->_db->quoteInto($k." = ?", $v);
                    }else{
                        $orwhere[]=$this->_db->quoteInto($k, $v);
                    }
                }
                if(count($orwhere)>0)
                    $select->where(implode(" OR ",$orwhere));
            }
            
            if($order_by!=null)
                $select->order($order_by);
            $sql = $select->__toString();
            if($debug)echo $sql;
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 查询经销商商品库存数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array	$orcondition	OR条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
    function fetchStocksCount($condiction=null , $orcondition=null , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(*)");
            $select->joinleft($this->_prefix."goods_stocks AS t2", 't1.refer_stock_id = t2.stock_id', null);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $select->where($k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }
            
            if(isset($orcondition) && is_array($orcondition)){
                $orwhere=array();
                foreach ($orcondition as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $orwhere[]=$this->_db->quoteInto($k." = ?", $v);
                    }else{
                        $orwhere[]=$this->_db->quoteInto($k, $v);
                    }
                }
                if(count($orwhere)>0)
                    $select->where(implode(" OR ",$orwhere));
            }
            
            $sql = $select->__toString();
            if($debug)echo $sql;
            $count = $this->_db->fetchOne($sql);
            return $count;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
