<?php
/**
 * 榜单分类表模型 (snsys_chart_category)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_ChartCategory extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'chart_category';
    
    /**
     * 无前缀的外联表名
     *
     * @var string
     */
    public $_rel_table_name = 'chart_channel';
    
    /**
     * 分类列表记录
     *
     * @var array
     */
    public $_parent_option;

    /**
     * 查询榜单分类
     *
     * @param	array	$condition	AND条件查询数组
     * @return	
     *
     */
    public function getParentOption($condition)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name, array('cate_id', 'cate_title'));
            if(isset($condition) && is_array($condition)){
                foreach ($condition as $k=>$v){
                    $select->where($k." = ?", $v);
                }
            }
            $select->order("cate_id ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);

            foreach ($rows as $row){
                $this->_parent_option[]=$row;
            }

            if(is_array($this->_parent_option))
                return $this->_parent_option;
            else
                return array();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询榜单分类记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @return	array
     *
     */
    function getCates($limit = array(0,0),$condiction=null,$order_by=null,$fetch_fields="*")
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
            $select->joinleft($this->_prefix.$this->_rel_table_name.' as t2','t1.channel_id = t2.channel_id');
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if('channel_name' == $k or 'channel_desc' == $k or 'channel_intime' == $k) {
                        $select->where("t2.".$k." = ?", $v);
                    } else{
                        $select->where("t1.".$k." = ?", $v);
                    }
                }
            }
            if($order_by!=null)
                $select->order($order_by);
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

