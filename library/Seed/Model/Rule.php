<?php
/**
 * ACL 规则表模型 (snsys_rules)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_Rule extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'rules';
    
    /**
     * 查询ACL 规则记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condition	AND条件查询数组
     * @return	array
     *
     */
    public function fetchJoinRows($limit = array(0,0),$condition=null)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.'rules as t1');
            $select->joinleft($this->_prefix.'resources as t2',"t1.res_name = t2.res_name and t1.mod_name = t2.mod_name");
            if(isset($condition) && is_array($condition)){
                foreach ($condition as $k=>$v){
                    $select->where("t1.".$k." = ?", $v);
                }
            }
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
