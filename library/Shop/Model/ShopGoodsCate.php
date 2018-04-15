<?php
/**
 * 经销商商品分类表模型 (snshop_shop_goods_cates)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_ShopGoodsCate extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'shop_goods_cates';
    
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
     * 分类列表数组
     *
     * @var array
     */
    public $_parent_nav=array();

    /**
     * 分类列表每行缩进
     *
     * @var string
     */
    public $_implode_string="&nbsp;&nbsp;";
    
    /**
     * 设置每行缩进
     *
     * @param	string	$str	缩进格式
     *
     */
    public function setImplodeString($str){
        $this->_implode_string=$str;
    }
    
    /**
     * 查询经销商商品分类
     *
     * @param	int	$parent	分类ID
     * @param	int	$level	深度	
     * @param	int	$shop_id	经销商ID
     * @return	array
     *
     */
    public function getParentOption($parent=0,$level=0,$shop_id=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            if ($shop_id > 0) {$select->where("shop_id = ?",$shop_id);}
            $select->where("parent = ?",$parent);
            $select->order("order_by ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            
            foreach ($rows as $row){
                $tmp_name="";
                for ($i=0;$i<$level;$i++)$tmp_name.=$this->_implode_string;
                $row['cate_name']=$tmp_name.$row['cate_name'];
                $this->_parent_option[]=$row;
                $level++;
                $this->getParentOption($row['cate_id'],$level,$shop_id);
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
     * 根据上级分类及商品类型查询经销商商品分类
     *
     * @param	int	$parent	分类ID
     * @param	int	$level	深度
     * @param	int	$type_id	商品类型ID
     * @param	int	$shop_id	经销商ID
     * @return	array
     *
     */
    public function getStypeParentOption($parent=0,$level=0,$type_id=0,$shop_id=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("parent = ?",$parent);
            $select->where("type_id =?",$type_id);
            if ($shop_id > 0) {$select->where("shop_id = ?",$shop_id);}
            $select->order("order_by ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            
            foreach ($rows as $row){
                $tmp_name="";
                for ($i=0;$i<$level;$i++)$tmp_name.=$this->_implode_string;
                $row['cate_name']=$tmp_name.$row['cate_name'];
                $this->_parent_option[]=$row;
                $level++;
                $this->getParentOption($row['cate_id'],$level,$type_id,$shop_id);
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
     * 根据上级分类查询经销商商品分类
     *
     * 返回分类ID数组
     * 
     * @param	int	$cate_id	分类ID
     * @param	int	$shop_id	经销商ID
     * @return	array
     *
     */
    public function fetchChildrenCateIds($cate_id=0,$shop_id=0)
    {
        try {            
            if(count($this->_children_ids)==0)$this->_children_ids[] = $cate_id;
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,array('cate_id'));
            $select->where("parent = ?",$cate_id);
            if ($shop_id > 0) {$select->where("shop_id = ?",$shop_id);}
            $select->order('order_by asc');
            $sql = $select->__toString();        
            $rows = $this->_db->fetchAll($sql);            
            foreach ($rows as $row)
            {
                $this->_children_ids[] = $row['cate_id'];
                $this->fetchChildrenCateIds($row['cate_id'],$shop_id);
            }
            return $this->_children_ids;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 根据当前经销商分类查询上级分类
     *
     * 返回上级经销商分类数组
     *
     * @param	int	$parent	分类ID
     * @param	int	$shop_id	经销商ID
     * @return	array
     *
     */
    public function getParentNav($parent,$shop_id=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("cate_id = ?",$parent);
            if ($shop_id > 0) {$select->where("shop_id = ?",$shop_id);}
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);
            $this->_parent_nav[]=$row;
            if($row['parent']>0)$this->getParentNav($row['parent'],$shop_id);
            if(is_array($this->_parent_nav))
                return array_reverse($this->_parent_nav);
            else
                return array();
        } catch (Exception $e) {
            throw $e;
        }
    }
}