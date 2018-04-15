<?php
/**
 * Seed_Model_Profile
 * 
 * @author Biaoest (biaoest@gmail.com)
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 * 
 **/
class Seed_Model_Profile extends Seed_Model_Db 
{
	public $_table_name = 'profiles';
	
    /**
     * 查询用户信息
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array	$orcondition	OR条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    function fetchUser($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
            $select->joinleft($this->_prefix."users AS t2", 't1.user_id = t2.user_id', $fetch_fields);
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
                        $select->where("t1.".$k." = ?", $v);
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
     * 查询用户数量数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array	$orcondition	OR条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
    function fetchUserCount($condiction=null , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(*)");
            $select->joinleft($this->_prefix."users AS t2", 't1.user_id = t2.user_id', null);
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
?>