<?php
/**
 * 订单商品表模型 (snshop_order_goods)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_OrderGoods extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'order_goods';
    
    /**
     * 供应商是否已发货
     *
     * @param	int	$orderId	订单ID
     * @param	int	$agentId	供应商ID
     * @return	boolean
     *
     */
    function isGoodShiped($orderId, $agentId)
    {
        try {
            $orderUnshipGoodsCount = $this->fetchRowsCount(array('order_id'=>$orderId,
                'agent_id' => $agentId,
                'shipping_status'=>'0'
                ));
            return ($orderUnshipGoodsCount > 0) ? FALSE : TRUE;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 实体店是否已发货
     *
     * @param	int	$orderId	订单ID
     * @return	boolean
     *
     */
    function isGoodShipedByStore($orderId)
    {
        try {
            $orderUnshipGoodsCount = $this->fetchRowsCount(array('order_id'=>$orderId,
                'shipping_status'=>'0'
                ));
            return ($orderUnshipGoodsCount > 0) ? FALSE : TRUE;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 所有商品的供应商是否唯一
     *
     * @param	int	$orderId	订单ID
     * @return	boolean
     *
     */
    function isUserUnique($orderId)
    {
        try {
            $subSql = "(SELECT agent_id FROM {$this->_prefix}order_goods WHERE order_id={$orderId} GROUP BY agent_id)";
            $select = $this->_db->select();
            $sql = $select->from(new Zend_Db_Expr($subSql), 'COUNT(1)')
                          ->__toString();
            $count = $this->_db->fetchOne($sql);
            return ($count > 1) ? FALSE : TRUE;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 统计所有供应商的商品总价
     *
     * @param	int	$orderId	订单ID
     * @return	array
     *
     */
    function getTotalPrice($orderId)
    {
        try {
            $select = $this->_db->select();
            $sql = $select->from($this->_prefix.$this->_table_name, array('agent_id', 'SUM(union_price*goods_number) AS total_price'))
                          ->where('order_id=?', $orderId)
                          ->group('agent_id')
                          ->__toString();
            $rows = $this->_db->fetchAll($sql);
            if ($rows === false) {$rows = array();}
            return $rows;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 获取所有供应商mall_id
     *
     * @param	int	$orderId	订单ID
     * @return	array
     *
     */
    function getAllMallId($orderId)
    {
        try {
            $select = $this->_db->select();
            $sql = $select->from($this->_prefix.$this->_table_name, array('agent_id'))
                          ->where('order_id=?', $orderId)
                          ->group('agent_id')
                          ->__toString();
            $rows = $this->_db->fetchAll($sql);
            if ($rows === false) {$rows = array();}
            return $rows;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}