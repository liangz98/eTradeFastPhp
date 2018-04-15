<?php
class Shop_Model_ChartChannel extends Seed_Model_Db
{
	public $_table_name = 'chart_channel';
    public $_parent_option;

    public function getParentOption()
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name, array('channel_id', 'channel_name'));
			$select->order("channel_id ASC");
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
}
?>
