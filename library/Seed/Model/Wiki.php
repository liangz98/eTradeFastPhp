<?php
/**
 * 百科表模型 (snsys_images)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_Wiki extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'wikis';
	
	/**
     * 帮助信息列表数组
     *
     * @var array
     */
    public $_parent_nav=array();
    
    /**
     * 根据当前帮助信息查询上级帮助信息
     * 
     * 返回上级帮助信息列表
     *
     * @param	int	$parent	帮助信息ID
     * @return array
     *
     */
    public function getParentNav($parent)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("help_id = ?",$parent);
            $select->order('order_by asc');
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);
            $this->_parent_nav[]=$row;
            if($row['parent']>0)$this->getParentNav($row['parent']);
            if(is_array($this->_parent_nav))
                return array_reverse($this->_parent_nav);
            else
                return array();
        } catch (Exception $e) {
            throw $e;
        }
    }
}