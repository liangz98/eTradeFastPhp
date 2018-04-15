<?php
/**
 * 访问用户表模型 (snstat_visits)
 *
 * @category   Stat
 * @package    Stat_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Stat_Model_Visits extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'visits';

    /**
     * 查询访问用户记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    public function fetchVisits($limit = array(0,0) , $condiction=null , $order_by=null , $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,array('user_id', 'user_name', 'nick_name', 'SUM(hits) AS hits'));
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
            $select->group('user_id');
            $sql = $select->__toString();
            if($debug)echo $sql;
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询访问用户数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	integer
     *
     */
    public function fetchVisitsCount($condiction=null , $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name, 'COUNT(DISTINCT user_id)');
            if(isset($condiction) && is_array($condiction)){
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
