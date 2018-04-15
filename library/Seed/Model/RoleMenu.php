<?php
/**
 * 角色菜单关联表模型 (snsys_role_menus)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_RoleMenu extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'role_menus';
    
    /**
     * 查询角色菜单关联记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	type	$fileds	
     * @return	array
     *
     */
    public function fetchJoinRows($limit = array(0,0),$condiction=null,$order_by=null,$fileds=array('*','*','*'))
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.'role_menus as t1',$fileds[0]);
            $select->joinleft($this->_prefix.'roles as t2','t2.role_id = t1.role_id',$fileds[1]);
            $select->joinleft($this->_prefix.'menus as t3','t3.menu_id = t1.menu_id',$fileds[2]);
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    $select->where("t1.".$k." = ?", $v);
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
    
    /**
     * 查询角色菜单关联
     *
     * @param	type	$role_id	
     * @param	type	$parent	
     * @return	
     *
     */
    public function checkChildRow($role_id,$parent){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.'role_menus as t1',array('role_id'));
            $select->joinleft($this->_prefix.'menus as t2','t2.menu_id = t1.menu_id',array('menu_id'));
            $select->where("t2.parent = ?", $parent);
            $select->where("t1.role_id = ?", $role_id);
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }
}