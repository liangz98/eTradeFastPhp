<?php
/**
 * 订单退货表模型 (snshop_order_refunds)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_OrderRefund extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'order_refunds';
    
    /* 订单退货状态 */
    const ORS_APPLY   = 0; //提交申请
    const ORS_ACCEPT  = 1; //接受申请
    const ORS_REFUSE  = 2; //拒绝申请
    const ORS_CONFIRM = 3; //确认收货
    const ORS_DENY    = 4; //拒绝收货
    const ORS_FINISH  = 5; //退换货完成
    const ORS_CANCEL  = 6; //用户取消

    /**
     * 查询订单退货记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    function fetchRefundList($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
            $select->joinInner($this->_prefix."orders AS t2", 't1.order_id = t2.order_id', array('order_sn'));
            $select->joinInner($this->_prefix."order_goods AS t3", 't1.rec_id = t3.rec_id', array('goods_name', 'goods_m_list_image'));
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
            $sql = $select->__toString();
            if($debug)echo $sql;
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询订单退货数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
    function fetchRefundCount($condiction=null , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(*)");
            $select->joinInner($this->_prefix."orders AS t2", 't1.order_id = t2.order_id', NULL);
            $select->joinInner($this->_prefix."order_goods AS t3", 't1.rec_id = t3.rec_id', NULL);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where("t1.".$k." = ?", $v);
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
