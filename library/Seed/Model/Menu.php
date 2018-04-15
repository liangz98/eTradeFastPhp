<?php
/**
 * 系统菜单表模型 (snsys_menus)
 *
 */
class Seed_Model_Menu extends Seed_Model_Db
{
    /**
     * 菜单列表数组
     *
     * @var array
     */
    public $_parent_nav=array();
    
    /**
     * 菜单列表记录
     *
     * @var array
     */
    public $_parent_option=array();
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'menus';

    /**
     * 查询单条系统菜单记录
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	array|string	$order_by	查询记录排序
     * @param	boolean	$debug	是否输出SQL语句
     * @return	
     *
     */
    public function fetchMenu($condiction=null,$fetch_fields="*",$order_by=null,$debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
            $select->joinleft($this->_prefix.'role_menus as t2','t2.menu_id = t1.menu_id',null);
            $select->joinleft($this->_prefix.'roles as t3','t3.role_id = t2.role_id',null);
            if(is_array($condiction)){
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
            $sql = $select->__toString();
            if($debug)echo $sql;
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询多条系统菜单记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    public function fetchMenuList($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
            $select->joinleft($this->_prefix.'role_menus as t2','t2.menu_id = t1.menu_id',null);
            $select->joinleft($this->_prefix.'roles as t3','t3.role_id = t2.role_id',null);
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
            $sql = $select->__toString();
            if($debug)echo $sql;
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据当前系统菜单ID查询上级菜单
     * 
     * 返回上级菜单数组
     *
     * @param	int	$parent	当前系统菜单ID
     * @return array
     *
     */
    public function getParentNav($parent)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.'menus');
            $select->where("menu_id = ?",$parent);
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
     * 查询系统菜单
     * 
     * 返回带缩进的系统菜单数组
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
            $select->from($this->_prefix.'menus');
            $select->where("parent = ?",$parent);
            $select->order("order_by ASC");
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);

            foreach ($rows as $k=>$row){
                $tmp_name="";
                for ($i=0;$i<$level;$i++)$tmp_name.="&nbsp;&nbsp;";
                $row['menu_name']=$tmp_name.$row['menu_name'];
                $this->_parent_option[]=$row;
                $level++;
                $this->getParentOption($row['menu_id'],$level);
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
}
