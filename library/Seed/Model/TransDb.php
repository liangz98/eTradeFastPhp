<?php
/**
 * 数据库事务操作
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_TransDb extends Zend_Db{
    
    /**
     * 数据库资源
     *
     * @var resource
     */
	public $_db;
    
    /**
     * 数据表前缀
     *
     * @var string
     */
	public $_prefix;
    
    /**
     * 数据库配置
     *
     * @var object
     */
	public $_config;
    
    /**
     * 数据库名称
     *
     * @var string
     */
	public $_dbname;
	
	/**
	 * 构造函数
	 * 
	 * @param string $config_file 配置文件名
	 */
	public function __construct($label, $config_file=null)
	{
		if(defined('SEED_HOST_NAME')){
			$seed_host_name = str_replace(".","_",SEED_HOST_NAME);
			$dbfile = strtolower($seed_host_name).".xml";
		}else{ 
			$dbfile = "mydbconfig.xml";
		}
		if($config_file==null)$config_file=SEED_CONF_ROOT.'/'.$dbfile;

		/** 引入数据库配置文件 */
		try {
			$this->_config = new Zend_Config_Xml ($config_file, $label);
		}catch (Exception $e){
			throw $e;
		}
		
		/** 连接数据库 */
		try {
			$dbconfig = $this->_config->db->params->toArray();
			$this->_db = Zend_Db::factory ('PDO_MYSQL',$dbconfig);
			$this->_db->beginTransaction();
		}catch (Exception $e){
			throw $e;
		}
	}
	
    /**
     * 插入记录
     * 
     * insertRow、updateRow、deleteRow方法的 
     * $model 可为Model的实例化对象 
     * $model 也可以是完整的数据表名称
     * 
	 * @param	object|string	$model	数据表模型或表名
	 * @param	array	$dataSet	插入数据
	 * @return int
     */
	public function insertRow($model,$dataSet)
	{		
		try {
			if(is_object($model)){
				$table = $model->_prefix.$model->_table_name;
			}else{
				$table = $model;
			}		
			$this->_db->insert($table, $dataSet);
			$mod_id = $this->_db->lastInsertId();
			return $mod_id;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * 更新记录
	 *
	 * @param	object|string	$model	数据表模型或表名
	 * @param	array	$dataSet	更新数据
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$affect	是否返回更新数
	 * @return	boolean|int
     * 
	 */
	public function updateRow($model,$dataSet,$condiction,$affect = false){
		try {
			if(is_object($model)){
				$table = $model->_prefix.$model->_table_name;
			}else{
				$table = $model;
			}	
			if(!is_array($condiction)){
				throw new Exception('invalid condition!');
			}
			$where=array();
			foreach ($condiction as $k=>$v){
				if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
					$where[] = $this->_db->quoteInto($k." = ?", $v);
				}else{
					$where[] = $this->_db->quoteInto($k , $v);
				}
			}
			$affectRow = $this->_db->update($table, $dataSet, $where);
			return $affect ? $affectRow : true;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
    /**
	 * 删除记录
	 *
	 * @param	object|string	$model	数据表模型或表名
	 * @param	array	$condiction	AND条件查询数组
	 * @return	int
     * 
	 */
	public function deleteRow($model,$condiction){
		try {
			if(is_object($model)){
				$table = $model->_prefix.$model->_table_name;
			}else{
				$table = $model;
			}		
			if(!is_array($condiction)){
				throw new Exception('invalid condition!');
			}
			$where=array();
			foreach ($condiction as $k=>$v){
				if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
					$where[] = $this->_db->quoteInto($k." = ?", $v);
				}else{
					$where[] = $this->_db->quoteInto($k , $v);
				}
			}
			$result = $this->_db->delete($table, $where);
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
    /**
	 * 执行SQL
	 *
	 * @param	string	$sql	SQL语句
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	mixed
     * 
	 */
	public function query($sql , $debug=false){
		if($debug)echo $sql;
		return $this->_db->query($sql);
	}
	
    /**
	 * 提交事务
     * 
	 */
	public function commit()
	{
		$this->_db->commit();
	}
	
    /**
	 * 回滚事务
     * 
	 */
	public function rollBack()
	{
		$this->_db->rollBack();
	}
	
    /**
	 * 检查数据表是否存在
     * 
	 * @return	boolean
	 */
	protected function table_exits($table)
	{
		$sql="show tables like '$table'";
		$check=$this->_db->fetchOne($sql);
		if($check){
			return true;
		}else{
			return false;
		}
	}
}