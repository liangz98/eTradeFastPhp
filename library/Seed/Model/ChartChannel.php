<?php
/**
 * 榜单频道表模型 (snsys_chart_channel)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_ChartChannel extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'chart_channel';
    
    /**
     * 频道列表记录
     *
     * @var array
     */
    public $_parent_option;

    /**
     * 查询榜单频道
     * 
     * @param	array	$condition	AND条件查询数组
     * @return	
     *
     */
    public function getParentOption()
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name, array('channel_id', 'channel_name'));
            $select->order("channel_id ASC");
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
}

