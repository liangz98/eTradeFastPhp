<?php
/**
 * 新闻分类表模型 (snhome_news_cates)
 *
 * @category   Home
 * @package    Home_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Home_Model_NewsCate extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'news_cates';
    
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
     * 新闻分类列表数组
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
     * 查询新闻分类
     * 
     * 返回带缩进的新闻分类数组
     *
     * @param	int	$parent	分类ID
     * @param	int	$level	深度
     * @param   int $shop_id    商户/代理ID
     * @return	array
     *
     */
    public function getParentOption($parent,$level=0,$shop_id=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("parent = ?",$parent);
        	if($shop_id>0){
            	$select->where("shop_id = ?",$shop_id);
            }
            $select->order("order_by ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            
            foreach ($rows as $k=>$row){
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
     * 根据上级分类查询新闻分类
     * 
     * 返回分类ID数组
     *
     * @param	int	$cate_id	分类ID
     * @param   int $shop_id    商户/代理ID
     * @return	array
     *
     */
    public function fetchChildrenCateIds($cate_id,$shop_id=0)
    {
        try {            
            if(count($this->_children_ids)==0)$this->_children_ids[] = $cate_id;
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,array('cate_id'));
            $select->where("parent = ?",$cate_id);
        	if($shop_id>0){
            	$select->where("shop_id = ?",$shop_id);
            }
            $select->order('order_by asc');
            $sql = $select->__toString();        
            $rows = $this->_db->fetchAll($sql);            
            foreach ($rows as $k=>$row)
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
     * 根据当前分类查询上级新闻分类
     * 
     * 返回上级新闻分类数组
     *
     * @param	int	$parent	分类ID
     * @param   int $shop_id    商户/代理ID
     * @return array
     *
     */
    public function getParentNav($parent,$shop_id=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("cate_id = ?",$parent);
            if($shop_id>0){
            	$select->where("shop_id = ?",$shop_id);
            }
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