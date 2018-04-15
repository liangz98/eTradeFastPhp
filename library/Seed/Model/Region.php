<?php
/**
 * 地区表模型 (snsys_regions)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_Region extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'regions';
    
    /**
     * 分类列表数组
     *
     * @var array
     */
    public $_parent_nav=array();
    
    /**
     * 根据当前地区ID查询上级地区
     * 
     * 返回上级地区数组
     *
     * @param	int	$parent	当前地区ID
     * @return array
     *
     */
    public function getParentNav($parent)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.'regions');
            $select->where("reg_id = ?",$parent);
            $select->order(array('order_by ASC','reg_name ASC'));
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
    
    /**
     * 根据上级地区ID查询下级地区
     * 
     * 返回下级地区数组
     *
     * @param	int	$reg_id	上级地区ID
     * @return array
     *
     */
    public function fetchChildrenNav($reg_id)
    {
        try {            
            $select = $this->_db->select();
            $select->from($this->_prefix.'regions',array('reg_id','reg_name'));
            $select->where("parent = ?",$reg_id);
            $select->order('order_by asc');
            $sql = $select->__toString();        
            $rows = $this->_db->fetchAll($sql);            
            foreach ($rows as $k=>$row)
            {
                $rows[$k]['sub'] = $this->fetchChildrenNav($row['reg_id']);
            }
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
