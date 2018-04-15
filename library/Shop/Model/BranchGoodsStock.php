<?php
/**
 * Shop_Model_Branch
 *
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 *
 **/
class Shop_Model_BranchGoodsStock extends Seed_Model_Db
{
	/**
     * 分类列表数组
     *
     * @var array
     */
    public $_parent_nav=array();
    
    /**
     * 分类列表记录
     *
     * @var array
     */
    public $_parent_option=array();
    
    /**
     * 子分类ID集合
     *
     * @var array
     */
    public $_children_ids=array();
    /**
     * 无前缀的表名
     *
     * @var string
     */
	public $_table_name = 'branches_goods_stocks';
	
    /**
     * 根据当前分店分区查询上级分店分区
     * 
     * 返回上级分店分区数组
     *
     * @param	int	$parent	分类ID
     * @return array
     *
     */
    public function getParentNav($parent)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("br_id = ?",$parent);
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
     * 查询分店分区
     * 
     * 返回带缩进的分店分区数组
     *
     * @param	int	$parent	分类ID
     * @param	int	$level	深度
     * @return	array
     *
     */
    public function getParentOption($parent,$level=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("parent = ?",$parent);
            $select->order("order_by ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            
            foreach ($rows as $k=>$row){
                $tmp_name="";
                for ($i=0;$i<$level;$i++)$tmp_name.="&nbsp;&nbsp;";
                $row['br_name']=$tmp_name.$row['br_name'];
                $this->_parent_option[]=$row;
                $level++;
                $this->getParentOption($row['br_id'],$level);
                $level--;
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
     * 根据当前分店分区查询下级级分店分区
     *
     * 返回下级分店分区数组
     *
     * @param	int	$br_id	分店分区ID
     * @return array
     *
     */
    public function fetchChildrenRegionIds($br_id=0)
    {
        try {            
            if(count($this->_children_ids)==0)$this->_children_ids[] = $br_id;
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,array('br_id'));
            $select->where("parent = ?",$br_id);
            $select->order('order_by asc');
            $sql = $select->__toString();        
            $rows = $this->_db->fetchAll($sql);            
            foreach ($rows as $k=>$row)
            {
                $this->_children_ids[] = $row['br_id'];
                $this->fetchChildrenRegionIds($row['br_id']);
            }
            return $this->_children_ids;
        } catch (Exception $e) {
        	throw $e;
        }
    }

    public function fetchChildBranches($parent = 0,$orderby = array(),$recursive = false)
    {
    	try {
    		$select = $this->_db->select();
    		$select->from($this->_prefix.$this->_table_name);
    		$select->where("parent = ?",$parent);
    		if(!empty($orderby))$select->order($orderby);
    		$sql = $select->__toString();
    		$rows = $this->_db->fetchAll($sql);
    		if($rows && $recursive){
    			foreach($rows as $k=>$v){
    				$rows[$k]['childs'] = $this->fetchChildBranches($v['br_id'],$orderby,$recursive);
    			}
    		}
           return $rows;
        } catch (Exception $e) {
            throw $e;
        } 
    }
}