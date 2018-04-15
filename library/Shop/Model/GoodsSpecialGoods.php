<?php
/**
 * Shop_Model_GoodsSpecialGoods
 *
 * @author Biaoest (biaoest@gmail.com)
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 *
 **/
class Shop_Model_GoodsSpecialGoods extends Seed_Model_Db 
{
	public $_table_name = 'goods_special_goodses';
	
	function fetchSpecialGoodses($limit = array(0,0), $condiction , $fetch_fields="t3.*", $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name." as t1",array('rec_id','goods_id','special_id'));
			$select->joinleft($this->_prefix."goods_specials AS t2", 't1.special_id = t2.special_id', array('special_name','special_desc','special_image','special_status'));
			$select->joinleft($this->_prefix."goods AS t3", 't1.goods_id = t3.goods_id', $fetch_fields);
			$select->joinleft($this->_prefix."goods_brands AS t4", 't3.brand_id = t4.brand_id', array('brand_name'));
			if (is_array($limit) && $limit[1] > 0)
				$select->limit($limit[1], $limit[0]);
			if(is_array($condiction)){
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
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	function fetchSpecialGoodsesCount($condiction,$debug=null)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name." as t1","COUNT(*)");
			$select->joinleft($this->_prefix."goods_specials AS t2", 't1.special_id = t2.special_id', null);
			$select->joinleft($this->_prefix."goods AS t3", 't1.goods_id = t3.goods_id', null);
			$select->joinleft($this->_prefix."goods_brands AS t4", 't3.brand_id = t4.brand_id', null);
			if(is_array($condiction)){
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
		} catch (Exception $e) {
			throw $e;
		}
	}
}
?>