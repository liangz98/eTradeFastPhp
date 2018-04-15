<?php
/**
 * 分店表模型 (snhome_branches)
 *
 * @category   Home
 * @package    Home_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Home_Model_Branch extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'branches';

    /**
	 * 获取分店记录
	 *
	 * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array $condiction	AND条件查询数组
	 * @param	array|string $order_by	查询记录排序
	 * @param	array|string $fetch_fields	查询记录字段
	 * @param	boolean $debug	是否输出SQL语句
	 * @return	array
     *
	 */
	public function fetchBranchList($limit = array(0, 0), $condiction=null, $order_by=null, $fetch_fields="*", $group=null, $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
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
			if($order_by!=null) {
				$select->order($order_by);
            }
            if ($group != NULL) {
                $select->group($group);
            }
			$sql = $select->__toString();
			if($debug)echo $sql;
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}
}