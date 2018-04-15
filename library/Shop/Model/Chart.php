<?php
class Shop_Model_Chart extends Seed_Model_Db
{
	public $_table_name = 'chart';
    public $_rel_table_name = 'chart_category';

    function getChart($condiction=null,$fetch_fields="*",$order_by=null)
    {
        try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
			$select->joinleft($this->_prefix.$this->_rel_table_name.' as t2','t1.cate_id = t2.cate_id');
            $select->joinleft($this->_prefix.'chart_channel as t3','t1.channel_id = t3.channel_id');
			if(isset($condiction) && is_array($condiction)){
				foreach ($condiction as $k=>$v){
                    if('cate_name' == $k or 'cate_title' == $k or 'cate_desc' == $k or 'cate_limit' == $k or 'channel_id' == $k or 'cate_intime' == $k) {
                        $select->where("t2.".$k." = ?", $v);
                    } elseif('channel_name' == $k or 'channel_desc' == $k or 'channel_intime' == $k) {
                        $select->where("t3.".$k." = ?", $v);
                    } else{
                        $select->where("t1.".$k." = ?", $v);
                    }
				}
			}
			if($order_by!=null)
				$select->order($order_by);
			$sql = $select->__toString();
			$rows = $this->_db->fetchRow($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
    }
    
    function getCharts($limit = array(0,0),$condiction=null,$order_by=null,$fetch_fields="*")
    {
        try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
			$select->joinleft($this->_prefix.$this->_rel_table_name.' as t2','t1.cate_id = t2.cate_id');
            $select->joinleft($this->_prefix.'chart_channel as t3','t1.channel_id = t3.channel_id');
			if (is_array($limit) && $limit[1] > 0)
				$select->limit($limit[1], $limit[0]);
			if(isset($condiction) && is_array($condiction)){
				foreach ($condiction as $k=>$v){
                    if('cate_name' == $k or 'cate_title' == $k or 'cate_desc' == $k or 'cate_limit' == $k or 'channel_id' == $k or 'cate_intime' == $k) {
                        $select->where("t2.".$k." = ?", $v);
                    } elseif('channel_name' == $k or 'channel_desc' == $k or 'channel_intime' == $k) {
                        $select->where("t3.".$k." = ?", $v);
                    } else{
                        $select->where("t1.".$k." = ?", $v);
                    }
				}
			}
			if($order_by!=null)
				$select->order($order_by);
			$sql = $select->__toString();
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
    }
}
?>
