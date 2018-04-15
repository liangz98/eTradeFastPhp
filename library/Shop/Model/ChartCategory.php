<?php
class Shop_Model_ChartCategory extends Seed_Model_Db
{
	public $_table_name = 'chart_category';
    public $_rel_table_name = 'chart_channel';
    public $_parent_option;

    public function getParentOption($condition)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name, array('cate_id', 'cate_title'));
            if(isset($condition) && is_array($condition)){
				foreach ($condition as $k=>$v){
					$select->where($k." = ?", $v);
				}
			}
			$select->order("cate_id ASC");
			$sql = $select->__toString();
			$rows = $this->_db->fetchAll($sql);

			foreach ($rows as $k=>$row){
				$this->_parent_option[]=$row;
			}

			if(is_array($this->_parent_option))
				return $this->_parent_option;
			else
				return array();
		} catch (Exception $e) {
			throw $e;
		}
	}

    function getCates($limit = array(0,0),$condiction=null,$order_by=null,$fetch_fields="*")
    {
        try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name." as t1",$fetch_fields);
			$select->joinleft($this->_prefix.$this->_rel_table_name.' as t2','t1.channel_id = t2.channel_id');
			if (is_array($limit) && $limit[1] > 0)
				$select->limit($limit[1], $limit[0]);
			if(isset($condiction) && is_array($condiction)){
				foreach ($condiction as $k=>$v){
                    if('channel_name' == $k or 'channel_desc' == $k or 'channel_intime' == $k) {
                        $select->where("t2.".$k." = ?", $v);
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
