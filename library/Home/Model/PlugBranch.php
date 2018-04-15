<?php
/**
 * 活动分店关联表模型 (snhome_plug_branch)
 *
 * @category   Home
 * @package    Home_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Home_Model_PlugBranch extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'plug_branch';

    /**
     * 无前缀的外联表名
     *
     * @var string
     */
    public $_rel_table_name = 'branches';

    /**
	 * 获取活动和分店关联记录
	 *
	 * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array $condiction	AND条件查询数组
	 * @param	array|string $order_by	查询记录排序
	 * @param	array|string $fetch_fields	查询记录字段
	 * @param	boolean $debug	是否输出SQL语句
	 * @return	array
     *
	 */
    public function fetchBranchList($limit = array(0, 0), $condiction = NULL, $order_by = NULL, $fetch_fields = "*", $debug = FALSE)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' AS t1', $fetch_fields);
            $select->joinInner($this->_prefix.$this->_rel_table_name.' AS t2', 't1.branch_id = t2.branch_id', 'branch_name');
			if (is_array($limit) && $limit[1] > 0) {
				$select->limit($limit[1], $limit[0]);
            }
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
			$sql = $select->__toString();
			if($debug)echo $sql;
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}
}