<?php
/**
 * 保存所有项目系统参数设置表模型(snsys_settingsBridge)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_SettingBridge extends Seed_Model_Db 
{
    /**
     * 无前缀表名
     *
     * @var string
     */
    public $_table_name = 'settings_bridge';
    
    /**
     * 获取系统参数设置
     *
     * @param	string	$mod_name	
     * @return	
     *
     */
	public function fetchSettings($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from('weixiao_gzseed.snsys_'.$this->_table_name,$fetch_fields);
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
	
	public function copyData($dataSet)
	{
		try {
			$this->_db->insert('weixiao_gzseed.snsys_'.$this->_table_name, $dataSet);
			$mod_id = $this->_db->lastInsertId();
			return $mod_id;
		} catch (Exception $e) {
			throw $e;
		}
	}
}
