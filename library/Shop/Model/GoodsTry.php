<?php
/**
 * 商品试用信息表模型 (snshop_goods_try)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_GoodsTry extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'goods_try';

    /**
     * 查询商品试用信息记录
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
    function fetchGoodsTryList($limit = array(0,0) , $condiction=null , $orcondition=null , $order_by=null , $fetch_fields="*" , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
            $select->joinInner($this->_prefix."goods AS t2", 't1.goods_id = t2.goods_id',array('goods_name', 'goods_m_list_image'));
            $select->joinLeft($this->_prefix."goods_brands AS t3", 't2.brand_id = t3.brand_id', array('brand_name', 'brand_mark'));
            $select->joinLeft($this->_prefix."goods_cates AS t4", 't2.cate_id = t4.cate_id', array('cate_name', 'cate_mark'));
            $select->joinLeft($this->_prefix."goods_types AS t5", 't2.type_id = t5.type_id', array('type_desc', 'type_name', 'type_id'));
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

            if(isset($orcondition) && is_array($orcondition)){
                $orwhere=array();
                foreach ($orcondition as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
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
     * 查询商品试用信息数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array	$orcondition	OR条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
    function fetchGoodsTryCount($condiction=null , $orcondition=null, $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(*)");
            $select->joinInner($this->_prefix."goods AS t2", 't1.goods_id = t2.goods_id',null);
            $select->joinLeft($this->_prefix."goods_brands AS t3", 't2.brand_id = t3.brand_id', null);
            $select->joinLeft($this->_prefix."goods_cates AS t4", 't2.cate_id = t4.cate_id', null);
            $select->joinLeft($this->_prefix."goods_types AS t5", 't2.type_id = t5.type_id', null);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where("t1.".$k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }

            if(isset($orcondition) && is_array($orcondition)){
                $orwhere=array();
                foreach ($orcondition as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
                    }else{
                        $orwhere[]=$this->_db->quoteInto($k, $v);
                    }
                }
                if(count($orwhere)>0)
                    $select->where(implode(" OR ",$orwhere));
            }
            $sql = $select->__toString();
            if($debug)echo $sql;
            $rows = $this->_db->fetchOne($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询非商品试用信息记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    function fetchGoodsWithoutTry($limit = array(0, 0), $condiction = null, $order_by = null, $fetch_fields = '*', $debug = false) {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.'goods AS t1', $fetch_fields);
            $select->joinLeft($this->_prefix.$this->_table_name." AS t2", 't1.goods_id = t2.goods_id', NULL);
            $select->where('tid IS NULL', NULL);
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
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
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询非商品试用信息数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
    function fetchGoodsWithoutTryCount($condiction = null, $debug = false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.'goods AS t1','COUNT(*)');
            $select->joinLeft($this->_prefix.$this->_table_name." AS t2", 't1.goods_id = t2.goods_id', NULL);
            $select->where('tid IS NULL', NULL);
            if(is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where($k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
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