<?php
/**
 * 门店表模型 (snshop_stores)
 * 
 */
class Shop_Model_Store extends Seed_Model_Db
{
	/**  
	 * 无前缀的表名
	 *
	 * @var string
	 */
	public $_table_name = 'stores';
	public $_children_ids=array();
	/**
	 * 查询门户数量
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	int
	 */
	public function fetchStoreCount($condiction=null, $debug=false)
	{
		try {	
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1','COUNT(*)');
			$select->joinleft($this->_prefix."stores AS t2", 't1.parent_id = t2.store_id', null);
			if(isset($condiction) && is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where($k." = ?", $v);
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


	/**
	 * 门户明细表
	 * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array	$condiction	AND条件查询数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	boolean	$debug	是否输出SQL语句
	*/
	public function fetchAllStoreList($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {		
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
			$select->joinleft($this->_prefix."stores AS t2", 't1.parent_id = t2.store_id', array('store_id as id','store_name as name'));
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
	
	public function fetchChildrenStoreIds($cate_id=0)
    {
        try {
            if(count($this->_children_ids)==0)$this->_children_ids[] = $cate_id;
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,array('store_id'));
            $select->where("parent_id = ?",$cate_id);
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            foreach ($rows as $row)
            {
                $this->_children_ids[] = $row['store_id'];
                $this->fetchChildrenStoreIds($row['store_id']);
            }
            return $this->_children_ids;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
