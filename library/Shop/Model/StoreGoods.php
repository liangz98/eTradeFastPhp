<?php
/**
 * 门店商品表模型 (snshop_store_goods)
 * 
 */
class Shop_Model_StoreGoods extends Seed_Model_Db
{
	/**  
	 * 无前缀的表名
	 *
	 * @var string
	 */
	public $_table_name = 'store_goods';

	/**
	 * 门店商品明细表
	 * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array	$condiction	AND条件查询数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	boolean	$debug	是否输出SQL语句
	*/

	public function fetchAllStoreGoods($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1', $fetch_fields);
			$select->joinleft($this->_prefix."goods_stocks AS t2", 't1.stock_id = t2.stock_id', array('goods_id','stock_shop_price', 'stock_name','stock_sn','stock_barcode'));
			$select->joinleft($this->_prefix."stores AS t3", 't1.store_id = t3.store_id', array('store_name','store_mobile','default_store_points'));
			$select->joinleft($this->_prefix."goods AS t4", 't2.goods_id = t4.goods_id',array('goods_name','goods_channel','goods_mark','goods_list_image','goods_m_list_image','score_avg'));
			$select->joinleft($this->_prefix."goods_countrys AS t5", 't4.country_id = t5.country_id', array('country_name'));
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
	
	public function fetchStoreGoodsCount($condiction=null,$debug=false){
		try {			
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1','COUNT(*)');
			$select->joinleft($this->_prefix."goods_stocks AS t2", 't1.stock_id = t2.stock_id', null);
			$select->joinleft($this->_prefix."stores AS t3", 't1.store_id = t3.store_id', null);
			$select->joinleft($this->_prefix."goods AS t4", 't2.goods_id = t4.goods_id',null);
			$select->joinleft($this->_prefix."goods_countrys AS t5", 't4.country_id = t5.country_id', null);

				
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
			$count = $this->_db->fetchOne($sql);
			return $count;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	
}
