<?php
/**
 * 商品分类表模型 (snshop_goods_cates)
 *
 */
class Shop_Model_GoodsCate extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'goods_cates';

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
     * 查询商品分类
     * 
     * 返回带缩进的商品分类数组
     *
     * @param	int	$parent	分类ID
     * @param	int	$level	深度
     * @return	array
     *
     */
    public function getParentOption($parent=0,$level=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
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
                $this->getParentOption($row['cate_id'],$level);
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
     * 查询组合商品分类
     * 
     * 返回带缩进的商品分类数组
     *
     * @param	int	$parent	分类ID
     * @param	int	$level	深度
     * @return	array
     *
     */
    public function getGoodsGroupParentOption($parent=0,$level=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("parent = ?",$parent);
            $select->where("is_group = ?",'1');
            $select->order("order_by ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);

            foreach ($rows as $row){
                $tmp_name="";
                for ($i=0;$i<$level;$i++)$tmp_name.=$this->_implode_string;
                $row['cate_name']=$tmp_name.$row['cate_name'];
                $this->_parent_option[]=$row;
                $level++;
                $this->getParentOption($row['cate_id'],$level);
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
     * 根据上级分类及商品类型查询商品分类
     * 
     * 返回带缩进的商品分类数组
     *
     * @param	int	$parent	分类ID
     * @param	int	$level	深度
     * @param	int	$type_id	商品类型ID
     * @return	array
     *
     */
    public function getStypeParentOption($parent=0,$level=0,$type_id=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("parent = ?",$parent);
            $select->where("type_id =?",$type_id);
            $select->order("order_by ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);

            foreach ($rows as $row){
                $tmp_name="";
                for ($i=0;$i<$level;$i++)$tmp_name.=$this->_implode_string;
                $row['cate_name']=$tmp_name.$row['cate_name'];
                $this->_parent_option[]=$row;
                $level++;
                $this->getParentOption($row['cate_id'],$level);
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
     * 根据上级分类查询商品分类
     * 
     * 返回分类ID数组
     *
     * @param	int	$cate_id	分类ID
     * @return	array
     *
     */
    public function fetchChildrenCateIds($cate_id=0)
    {
        try {
            if(count($this->_children_ids)==0)$this->_children_ids[] = $cate_id;
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,array('cate_id'));
            $select->where("parent = ?",$cate_id);
            $select->order('order_by asc');
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            foreach ($rows as $row)
            {
                $this->_children_ids[] = $row['cate_id'];
                $this->fetchChildrenCateIds($row['cate_id']);
            }
            return $this->_children_ids;
        } catch (Exception $e) {
            throw $e;
        }
    }
 
    
    function sons($arr,$id){
    $son=array();                
    foreach ($arr as $val){
        if($val['parent'] == $id){
            $son[]=$val['cate_id'];
            $son=array_merge($son,$this->sons($arr,$val['cate_id']));
        }  
    }  
    return $son;  
    }  
    function sonsmark($arr,$id){
        $son=array();                
        foreach ($arr as $val){
            if($val['parent'] == $id){
                $son[]=$val['cate_mark'];
                $son=array_merge($son,$this->sonsmark($arr,$val['cate_id']));
            }  
        }  
        return $son;  
    }  
    
    
    public function fetchChildrenCatemark1($cate_id)
    {
        try {            
            if(count($this->_children_ids)==0)$this->_children_ids[] = $cate_id;
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,array('cate_id','cate_mark'));
            $select->where("parent = ?",$cate_id);
        	if($shop_id>0){
            	$select->where("shop_id = ?",$shop_id);
            }
            $select->order('order_by asc');
            $sql = $select->__toString();        
            $rows = $this->_db->fetchAll($sql);            
            foreach ($rows as $k=>$row)
            { 
                $this->_children_ids[] = $row['cate_mark'];
                $this->fetchChildrenCatemark1($row['cate_id']); 
            } 
            return $this->_children_ids;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 根据当前分类查询上级商品分类
     * 
     * 返回上级商品分类数组
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
            $select->where("cate_id = ?",$parent);
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
     * 获取分类树
     * @param int $parent 父id
     * @param bool $recursive 是否向下递归查找分类
     */
    public function getCatesTree($parent = 0,$orderby = array(),$recursive = false){
      try{
           $select = $this->_db->select();
           $select->from($this->_prefix.$this->_table_name);
           $select->where("parent = ?",$parent);
           $select->where("is_actived = 1");
           $select->where("is_group = ?",'0');
           if(!empty($orderby))$select->order($orderby);
           $sql = $select->__toString();
           $rows = $this->_db->fetchAll($sql);
           if($rows && $recursive){
              foreach($rows as $k=>$v){
                 $rows[$k]['cates'] = $this->getCatesTree($v['cate_id'],$orderby,$recursive);
              }
           }
           return $rows;
      } catch (Exception $ex) {
      }
    }
    
    function fetchgoodscateCount($condiction=null , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix."goods"." as t1","COUNT(*)");
            $select->joinleft($this->_prefix.$this->_table_name.' as t2','t1.cate_id = t2.cate_id',null);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where("t1.".$k." = ?", $v);
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