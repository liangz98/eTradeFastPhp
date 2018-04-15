<?php
/**
 * 企业新闻表模型 (snhome_news)
 *
 * @category   Home
 * @package    Home_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Home_Model_News extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'news';
    
    /**
     * 无前缀的外联表名
     *
     * @var string
     */
    public $_rel_table_name = 'news_cates';
    
    /**
     * 查询企业新闻记录
     *
     * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$order_by	查询记录排序
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    function fetchNewses($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
            $select->joinleft($this->_prefix.$this->_rel_table_name.' as t2','t1.cate_id = t2.cate_id');
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
     * 查询企业新闻数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	integer
     *
     */
    function fetchNewsesCount($condiction=null , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1","COUNT(*)");
            $select->joinleft($this->_prefix.$this->_rel_table_name.' as t2','t1.cate_id = t2.cate_id',null);
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
    
    /**
     * 查询单条企业新闻记录
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	array|string	$order_by	查询记录排序
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    public function fetchNews($condiction,$fetch_fields="*",$order_by=null,$debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
            $select->joinleft($this->_prefix.$this->_rel_table_name.' as t2','t1.cate_id = t2.cate_id',array('cate_name','cate_mark'));
            if(is_array($condiction)){
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
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }
}