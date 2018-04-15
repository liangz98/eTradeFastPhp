<?php
/**
 * 满减记录表模型 (snshop_goods_act_reduces)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_GoodsActReduce extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'goods_act_reduces';

    /**
     * 查询满减记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    public function fetchActReduceList($limit = array(0, 0), $condiction=null, $order_by=null, $fetch_fields="*", $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." AS t1", $fetch_fields);
            $select->joinLeft($this->_prefix."goods AS t2", 't1.goods_id = t2.goods_id', array('goods_name', 'goods_m_list_image'));
            $select->joinInner($this->_prefix."goods_act AS t3", 't1.act_id = t3.act_id', array('act_name', 'start_time', 'finish_time'));
            if (is_array($limit) && $limit[1] > 0) {
                $select->limit($limit[1], $limit[0]);
            }
            if (isset($condiction) && is_array($condiction)) {
                foreach ($condiction as $k=>$v){
                    if (preg_match("/^[a-zA-Z_0-9]+$/i",$k)) {
                        $select->where("t1.".$k." = ?", $v);
                    }
                    else {
                        $select->where($k, $v);
                    }
                }
            }
            if ($order_by!=null) {
                $select->order($order_by);
            }
            $sql = $select->__toString();
            if ($debug) {echo $sql;}
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询满减记录数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
    public function fetchActReduceCount($condiction=null , $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." AS t1", 'COUNT(*)');
            $select->joinLeft($this->_prefix."goods AS t2", 't1.goods_id = t2.goods_id', NULL);
            $select->joinInner($this->_prefix."goods_act AS t3", 't1.act_id = t3.act_id', NULL);
            if (isset($condiction) && is_array($condiction)) {
                foreach ($condiction as $k=>$v){
                    if (preg_match("/^[a-zA-Z_0-9]+$/i",$k)) {
                        $select->where("t1.".$k." = ?", $v);
                    }
                    else {
                        $select->where($k, $v);
                    }
                }
            }
            $sql = $select->__toString();
            if ($debug) {echo $sql;}
            $count = $this->_db->fetchOne($sql);
            return $count;
        } catch (Exception $e) {
            throw $e;
        }
    }
}