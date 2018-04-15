<?php
/**
 * 访问行为表模型 (snstat_hits)
 *
 * @category   Stat
 * @package    Stat_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Stat_Model_Hits extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'hits';
    
    /**
     * 查询用户访问页面的次数
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	type	$depth	从第$depth个标志字符开始截取页面地址
     * @param	type	$split	页面地址截取点标识字符
     * @param	boolean	$debug	是否输出SQL语句
     * @return	
     *
     */
    public function fetchHitGroup($condiction=null, $depth=1, $split='?', $debug=false)
    {
        try {
            $depthTrue = $depth + 1;
            $field = array("SUBSTRING_INDEX(SUBSTRING_INDEX( resource,  '{$split}', 1 ), '/', {$depthTrue}) AS res", "COUNT(*) AS total");
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name, $field);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where($k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }
            $select->group('res');
            $select->order('total DESC');
            $sql = $select->__toString();
            if($debug)echo $sql;
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
