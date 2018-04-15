<?php
///**
// * 商品表模型 (snshop_goods)
// *
// * @category   Shop
// * @package    Shop_Model
// * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
// */
//class Shop_Model_Goods extends Seed_Model_Db
//{
//    /**
//     * 无前缀的表名
//     *
//     * @var string
//     */
//    public $_table_name = 'goods';
//
//    /**
//     * 查询单条商品记录
//     *
//     * @param	array	$condiction	AND条件查询数组
//     * @param	array|string	$fetch_fields	查询记录字段
//     * @param	array|string	$order_by	查询记录排序
//     * @param	boolean	$debug	是否输出SQL语句
//     * @return	array
//     *
//     */
//    public function fetchGoods($condiction , $fetch_fields="*" , $order_by=null , $debug=false)
//    {
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
//            $select->joinleft($this->_prefix."goods_brands AS t2", 't1.brand_id = t2.brand_id', array('brand_name', 'brand_mark'));
//            $select->joinleft($this->_prefix."goods_cates AS t3", 't3.cate_id = t1.cate_id', array('cate_name', 'cate_mark'));
//            $select->joinleft($this->_prefix."goods_types AS t4", 't4.type_id = t1.type_id', array('type_desc', 'type_name', 'type_id', 'type_model'));
//            $select->joinleft($this->_prefix."goods_countrys AS t5", 't5.country_id = t1.country_id', array('country_name', 'country_id', 'country_m_logo'));
//            if(is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//            if($order_by!=null)
//                $select->order($order_by);
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $row = $this->_db->fetchRow($sql);
//            return $row;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//    /**
//     * 查询多条商品记录
//     *
//     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
//     * @param	array	$condiction	AND条件查询数组
//     * @param	array	$orcondition	OR条件查询数组
//     * @param	array|string	$order_by	查询记录排序
//     * @param	array|string	$fetch_fields	查询记录字段
//     * @param	boolean	$debug	是否输出SQL语句
//     * @return	array
//     *
//     */
//    function fetchGoodses($limit = array(0,0) , $condiction=null , $orcondition=null , $order_by=null , $fetch_fields="*" , $debug=false){
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
//            $select->joinleft($this->_prefix."goods_brands AS t2", 't1.brand_id = t2.brand_id', array('brand_name', 'brand_mark'));
//            $select->joinleft($this->_prefix."goods_cates AS t3", 't3.cate_id = t1.cate_id', array('cate_name', 'cate_mark'));
//            $select->joinleft($this->_prefix."goods_types AS t4", 't4.type_id = t1.type_id', array('type_desc', 'type_id' , 'type_name','type_model'));
//            $select->joinleft($this->_prefix."goods_countrys AS t5", 't5.country_id = t1.country_id', array('country_name', 'country_id', 'country_m_logo'));
//            $select->joinleft($this->_prefix."goods_stocks AS t6", 't6.goods_id = t1.goods_id', array('stock_shop_price', 'stock_id', 'stock_name','stock_sn','stock_barcode','stock_market_price','stock_value','tax_rate','SUM(t6.stock_value) as sum_stock_value'));
//            if (is_array($limit) && $limit[1] > 0)
//                $select->limit($limit[1], $limit[0]);
//            if(isset($condiction) && is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//
//            if(isset($orcondition) && is_array($orcondition)){
//                $orwhere=array();
//                foreach ($orcondition as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
//                    }else{
//                        $orwhere[]=$this->_db->quoteInto($k, $v);
//                    }
//                }
//                if(count($orwhere)>0)
//                    $select->where(implode(" OR ",$orwhere));
//            }
//
//            if($order_by!=null)
//                $select->order($order_by);
//
//                $select->distinct(true);
//                $select->group('t1.goods_id');
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $rows = $this->_db->fetchAll($sql);
//            return $rows;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//    /**
//     * 查询商品数量
//     *
//     * @param	array	$condiction	AND条件查询数组
//     * @param	array	$orcondition	OR条件查询数组
//     * @param	boolean	$debug	是否输出SQL语句
//     * @return	int
//     *
//     */
//    function fetchGoodsesCount($condiction=null , $orcondition=null , $debug=false){
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(DISTINCT t6.goods_id )");
//            $select->joinleft($this->_prefix."goods_brands AS t2", 't1.brand_id = t2.brand_id', null);
//            $select->joinleft($this->_prefix."goods_cates AS t3", 't3.cate_id = t1.cate_id', null);
//            $select->joinleft($this->_prefix."goods_types AS t4", 't4.type_id = t1.type_id', null);
//            $select->joinleft($this->_prefix."goods_countrys AS t5", 't5.country_id = t1.country_id', null);
//            $select->joinleft($this->_prefix."goods_stocks AS t6", 't6.goods_id = t1.goods_id', null);
//            if(isset($condiction) && is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//
//            if(isset($orcondition) && is_array($orcondition)){
//                $orwhere=array();
//                foreach ($orcondition as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
//                    }else{
//                        $orwhere[]=$this->_db->quoteInto($k, $v);
//                    }
//                }
//                if(count($orwhere)>0)
//                    $select->where(implode(" OR ",$orwhere));
//            }
//
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $count = $this->_db->fetchOne($sql);
//            return $count;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//    /**
//     * 查询商品记录
//     *
//     * 用于商品属性检索
//     *
//     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
//     * @param	array	$condiction	AND条件查询数组
//     * @param	array	$orcondition	OR条件查询数组
//     * @param	array|string	$order_by	查询记录排序
//     * @param	array|string	$fetch_fields	查询记录字段
//     * @param	boolean	$debug	是否输出SQL语句
//     * @return	array
//     *
//     */
//    function searchGoodses($limit = array(0,0) , $condiction=null , $orcondition=null , $order_by=null , $fetch_fields="*" , $debug=false){
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
//            $select->joinleft($this->_prefix."goods_brands AS t2", 't1.brand_id = t2.brand_id', array('brand_name', 'brand_mark'));
//            $select->joinleft($this->_prefix."goods_cates AS t3", 't3.cate_id = t1.cate_id', array('cate_name', 'cate_mark'));
//            $select->joinleft($this->_prefix."goods_types AS t4", 't4.type_id = t1.type_id', array('type_desc', 'type_id' , 'type_name','type_model'));
//            $select->joinleft($this->_prefix."goods_exts AS t5", 't5.goods_id = t1.goods_id', null);
//            $select->joinleft($this->_prefix."goods_countrys AS t6", 't6.country_id = t1.country_id', array('country_name', 'country_id', 'country_m_logo'));
//            $select->joinleft($this->_prefix."goods_stocks AS t7", 't7.goods_id = t1.goods_id', array('stock_shop_price', 'stock_id', 'stock_name', 'SUM(t7.stock_value) as sum_stock_value'));
//            if (is_array($limit) && $limit[1] > 0)
//                $select->limit($limit[1], $limit[0]);
//
//            if(isset($condiction) && is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//
//            if(isset($orcondition) && is_array($orcondition)){
//                $orwhere=array();
//                foreach ($orcondition as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
//                    }else{
//                        $orwhere[]=$this->_db->quoteInto($k, $v);
//                    }
//                }
//                if(count($orwhere)>0)
//                    $select->where(implode(" OR ",$orwhere));
//            }
//
//            if($order_by!=null)
//                $select->order($order_by);
//            $select->distinct(true);
//            $select->group('t1.goods_id');
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $rows = $this->_db->fetchAll($sql);
//            return $rows;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//    /**
//     * 查询商品数量
//     *
//     * 用于商品属性检索
//     *
//     * @param	array	$condiction	AND条件查询数组
//     * @param	array	$orcondition	OR条件查询数组
//     * @param	boolean	$debug	是否输出SQL语句
//     * @return	int
//     *
//     */
//    function searchGoodsesCount($condiction=null ,$orcondition=null , $debug=false){
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(DISTINCT t1.goods_id)");
//            $select->joinleft($this->_prefix."goods_brands AS t2", 't1.brand_id = t2.brand_id', null);
//            $select->joinleft($this->_prefix."goods_cates AS t3", 't3.cate_id = t1.cate_id', null);
//            $select->joinleft($this->_prefix."goods_types AS t4", 't4.type_id = t1.type_id', null);
//            $select->joinleft($this->_prefix."goods_exts AS t5", 't5.goods_id = t1.goods_id', null);
//            $select->joinleft($this->_prefix."goods_countrys AS t6", 't6.country_id = t1.country_id', null);
//            $select->joinleft($this->_prefix."goods_stocks AS t7", 't7.goods_id = t1.goods_id', null);
//            if(isset($condiction) && is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//
//            if(isset($orcondition) && is_array($orcondition)){
//                $orwhere=array();
//                foreach ($orcondition as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
//                    }else{
//                        $orwhere[]=$this->_db->quoteInto($k, $v);
//                    }
//                }
//                if(count($orwhere)>0)
//                    $select->where(implode(" OR ",$orwhere));
//            }
//
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $count = $this->_db->fetchOne($sql);
//            return $count;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//
//
//    public function fetchGroupStockId($condiction , $fetch_fields="*" , $order_by=null , $debug=false)
//    {
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
//            $select->joinleft($this->_prefix."goods_stocks AS t6", 't6.goods_id = t1.goods_id', array('stock_id'));
//            if(is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//            if($order_by!=null)
//                $select->order($order_by);
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $count = $this->_db->fetchOne($sql);
//            return $count;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//
//    //商品库存列表
//    function fetchGoodsesStock($limit = array(0,0) , $condiction=null , $orcondition=null , $order_by=null , $fetch_fields="*" , $debug=false){
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
//            $select->joinleft($this->_prefix."goods_brands AS t2", 't1.brand_id = t2.brand_id', array('brand_name', 'brand_mark'));
//            $select->joinleft($this->_prefix."goods_cates AS t3", 't3.cate_id = t1.cate_id', array('cate_name', 'cate_mark'));
//            $select->joinleft($this->_prefix."goods_types AS t4", 't4.type_id = t1.type_id', array('type_desc', 'type_id' , 'type_name','type_model'));
//            $select->joinleft($this->_prefix."goods_countrys AS t5", 't5.country_id = t1.country_id', array('country_name', 'country_id', 'country_m_logo'));
//            $select->joinleft($this->_prefix."goods_stocks AS t6", 't6.goods_id = t1.goods_id', array('stock_shop_price', 'stock_id', 'stock_name','stock_sn','stock_barcode','stock_market_price','stock_value','tax_rate'));
//            if (is_array($limit) && $limit[1] > 0)
//                $select->limit($limit[1], $limit[0]);
//            if(isset($condiction) && is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//
//            if(isset($orcondition) && is_array($orcondition)){
//                $orwhere=array();
//                foreach ($orcondition as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
//                    }else{
//                        $orwhere[]=$this->_db->quoteInto($k, $v);
//                    }
//                }
//                if(count($orwhere)>0)
//                    $select->where(implode(" OR ",$orwhere));
//            }
//
//            if($order_by!=null)
//                $select->order($order_by);
//
//                $select->distinct(true);
//                $select->group('t6.stock_id');
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $rows = $this->_db->fetchAll($sql);
//            return $rows;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//
//    //商品库存数量
//    function fetchGoodsesStockCount($condiction=null , $orcondition=null , $debug=false){
//        try {
//            $select = $this->_db->select();
//            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(*)");
//            $select->joinleft($this->_prefix."goods_brands AS t2", 't1.brand_id = t2.brand_id', null);
//            $select->joinleft($this->_prefix."goods_cates AS t3", 't3.cate_id = t1.cate_id', null);
//            $select->joinleft($this->_prefix."goods_types AS t4", 't4.type_id = t1.type_id', null);
//            $select->joinleft($this->_prefix."goods_countrys AS t5", 't5.country_id = t1.country_id', null);
//            $select->joinleft($this->_prefix."goods_stocks AS t6", 't6.goods_id = t1.goods_id', null);
//            if(isset($condiction) && is_array($condiction)){
//                foreach ($condiction as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $select->where("t1.".$k." = ?", $v);
//                    }else{
//                        $select->where($k, $v);
//                    }
//                }
//            }
//
//            if(isset($orcondition) && is_array($orcondition)){
//                $orwhere=array();
//                foreach ($orcondition as $k=>$v){
//                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
//                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
//                    }else{
//                        $orwhere[]=$this->_db->quoteInto($k, $v);
//                    }
//                }
//                if(count($orwhere)>0)
//                    $select->where(implode(" OR ",$orwhere));
//            }
//
//            $sql = $select->__toString();
//            if($debug)echo $sql;
//            $count = $this->_db->fetchOne($sql);
//            return $count;
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//
//
//}
//
//
//
