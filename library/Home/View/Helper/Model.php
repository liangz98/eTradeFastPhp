<?php
class Zend_View_Helper_Model
{
	protected $_fetchParams = array('limit'=>array(0,0),'condiction'=>array(),'order_by'=>array(),'fetch_fields'=>'*','debug'=>false);
	protected $_params = array();
	protected $_modelName = null;
	protected $_model = null;
	
	function model($model_name,$db_name)
	{
		if(!isset($this->_model[$model_name])){
			$model = new $model_name($db_name);
			if(is_object($model)){
				$this->_model[$model_name] = $model;
				$this->_modelName = $model_name;
			}
		}
		return $this;
	}
	
	public function fetchRow()
	{
		if(is_object($this->_model[$this->_modelName])){
			$this->parse(); 
			$row = $this->_model[$this->_modelName]->fetchRow($this->_fetchParams['condiction'], $this->_fetchParams['order_by'], $this->_fetchParams['fetch_fields'], $this->_fetchParams['debug']);
			return $row;
		}
	}
	
	public function fetchRows()
	{
		if(is_object($this->_model[$this->_modelName])){
			$this->parse();
			$rows = $this->_model[$this->_modelName]->fetchRows($this->_fetchParams['limit'], $this->_fetchParams['condiction'], $this->_fetchParams['order_by'], $this->_fetchParams['fetch_fields'], $this->_fetchParams['debug']);
			return $rows;
		}
	}
	
	public function fetchRowsCount()
	{
		if(is_object($this->_model[$this->_modelName])){
			$this->parse();
			$rows = $this->_model[$this->_modelName]->fetchRowsCount($this->_fetchParams['condiction'], $this->_fetchParams['order_by'], $this->_fetchParams['debug']);
			return $rows;
		}
	}
	
	public function __call($method,$args)
	{
		$method = '_' . $method;
		if(method_exists($this, $method)){
			$this->$method($args);
		}
		return $this;
	}
	
	private function _debug($debug)
	{
		$this->_params['debug'] = true;
	}
	
	private function _order($order)
	{
		$this->_params['order'] = $order;
		return $this;
	}
	
	private function _limit($limit)
	{
		$this->_params['limit'] = array($limit['0'],$limit['1']);
		return $this;
	}
	
	private function _field($field)
	{
		$this->_params['field'] = $field;
		return $this;
	}
	
	private function _where($where)
	{
		$this->_params['where'] = $where;
		return $this;
	}
	
	private function parse()
	{
		if(isset($this->_params['limit']) && $this->_params['limit']['1'] > 0){
			$this->_fetchParams['limit'] = array($this->_params['limit']['0']-1,$this->_params['limit']['1']);
		}
			
		if(isset($this->_params['field']) && !empty($this->_params['field'])){
			$this->_fetchParams['fetch_fields'] = $this->_params['field'];
		}
			
		if(isset($this->_params['order']) && !empty($this->_params['order'])){
			$this->_fetchParams['order_by'] = $this->_params['order'];
		}
			
		if(isset($this->_params['where']) && is_array($this->_params['where'])){
			$condiction = array();
			$wheres = $this->_params['where'];
			foreach ($wheres as $where){
				$condiction[$where] = null;
			}
			$this->_fetchParams['condiction'] = $condiction;
		}
			
		if(isset($this->_params['debug']) && is_bool($this->_params['debug'])){
			$this->_fetchParams['debug'] = true;
		}
	}
}