<?php
/**
 * Seed_Model_Db
 *
 * @author Biaoest (biaoest@.gmailcom)
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 *
 **/
class Seed_Model_Db{

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
	 * 自动更加标签引入相应的数据库，并注册，如果同一进程需要引用相同的数据库则无需二次连接
	 *
	 * @param	string	$label	标签
	 * @param	string	$config_file	配置文件名
     *
	 */
	public function __construct($label, $config_file=null)
	{
        //首先引入事务版的授权文件，事务版的不存在则引入传统版的授权文件，by brave，2015-06-02 14:55:30
        if(file_exists(SEED_LICENSE_ROOT."/check_tran.php")) {
            require(SEED_LICENSE_ROOT . "/check_tran.php");
        }elseif(file_exists(SEED_LICENSE_ROOT."/check.php")){
			require(SEED_LICENSE_ROOT."/check.php");
		}else{
			exit("License File Not Found!");
		}
	}

	/**
	 * 获取列表
	 *
	 * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array $condiction	AND条件查询数组
	 * @param	array|string $order_by	查询记录排序
	 * @param	array|string $fetch_fields	查询记录字段
	 * @param	boolean $debug	是否输出SQL语句
	 * @return	array
     *
	 */
	public function fetchRows($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
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

	/**
	 * 获取记录列表
	 *
	 * @param	array $limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array $condiction	AND条件查询数组
     * @param	array	$orcondition	OR条件查询数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	array|string	$fetch_fields	查询记录字段
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	array
     *
	 */
	public function searchRows($limit = array(0,0) , $condiction=null , $orcondition=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
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
			if(isset($orcondition) && is_array($orcondition)){
				$orwhere=array();
				foreach ($orcondition as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto($k." = ?", $v);
					}else{
						$orwhere[]=$this->_db->quoteInto($k, $v);
					}
				}
				if(count($orwhere)>0)
					$select->where(implode(" OR ",$orwhere));
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
	 * 获取字段关联数组
	 *
	 * @param	array $limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array $condiction	AND条件查询数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	array|string	$fetch_fields	查询记录字段
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	array
     *
	 */
	public function fetchRowsPairs($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
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
			$rows = $this->_db->fetchPairs($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 获取关联记录列表
	 *
	 * @param	array $limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array $condiction	AND条件查询数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	array|string	$fetch_fields	查询记录字段
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	array
     *
	 */
	public function fetchRowsAssoc($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
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
			$rows = $this->_db->fetchAssoc($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 执行SQL获取记录列表
	 *
     * @param	string	$sql	SQL语句
     * @param	boolean	$debug	是否输出SQL语句
	 * @return array
     *
	 */
	public function fetchRowsBySql($sql , $debug=false){
		try {
			if($debug)echo $sql;
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 执行SQL获取某条记录
	 *
     * @param	string	$sql	SQL语句
     * @param	boolean	$debug	是否输出SQL语句
	 * @return array
     *
	 */
	public function fetchRowBySql($sql , $debug=false){
		try {
			if($debug)echo $sql;
			$row = $this->_db->fetchRow($sql);
			return $row;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 执行SQL获取某个字段值
	 *
     * @param	string	$sql	SQL语句
     * @param	boolean	$debug	是否输出SQL语句
	 * @return array
     *
	 */
	public function fetchRowOneBySql($sql , $debug=false){
		try {
			if($debug)echo $sql;
			$count = $this->_db->fetchOne($sql);
			return $count;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 根据ID获取记录列表
	 *
	 * @param	string $id_field	ID字段名
	 * @param	array $ids_arr	ID数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	array|string	$fetch_fields	查询记录字段
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	array
     *
	 */
	public function fetchRowsByIds($id_field , $ids_arr , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		if(!is_string($id_field))return;
		if(count($ids_arr)==0)return;
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
			$select->where($id_field." in (".implode(',',$ids_arr).")");
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
	 * 根据ID获取字段关联数组
	 *
	 * @param	string $id_field	ID字段名
	 * @param	array $ids_arr	ID数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	array|string	$fetch_fields	查询记录字段
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	array
     *
	 */
	public function fetchRowsByIdsPairs($id_field , $ids_arr , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		if(!is_string($id_field))return;
		if(count($ids_arr)==0)return;
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
			$select->where($id_field." in (".implode(',',$ids_arr).")");
			if($order_by!=null)
				$select->order($order_by);
			$sql = $select->__toString();
			if($debug)echo $sql;
			$rows = $this->_db->fetchPairs($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
     * 获取记录数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
	public function fetchRowsCount($condiction=null , $debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,'COUNT(*)');
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

    /**
     * 获取记录数量
     *
     * @param	array	$condiction	AND条件查询数组
     * @param	array	$orcondition	OR条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     * @return	int
     *
     */
	public function searchRowsCount($condiction=null , $orcondition=null , $debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,'COUNT(*)');
			if(is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where($k." = ?", $v);
					}else{
						$select->where($k, $v);
					}
				}
			}
			if(isset($orcondition) && is_array($orcondition)){
				$orwhere=array();
				foreach ($orcondition as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto($k." = ?", $v);
					}else{
						$orwhere[]=$this->_db->quoteInto($k, $v);
					}
				}
				if(count($orwhere)>0)
					$select->where(implode(" OR ",$orwhere));
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
	 * 获取某个字段的和
	 *
     * @param	string	$sum_field	求和字段
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$other
     * @param	boolean	$debug	是否输出SQL语句
	 * @return int
     *
	 */
	public function fetchSum($sum_field , $condiction=null , $other=null, $debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,'sum('.$sum_field.')');
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
			$sum = $this->_db->fetchOne($sql);
			return $sum;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 插入记录
	 *
	 * @param	array	$dataSet	插入数据
	 * @return int
     *
	 */
	public function insertRow($dataSet)
	{
		try {
			$this->_db->insert($this->_prefix.$this->_table_name, $dataSet);
			$mod_id = $this->_db->lastInsertId();
			return $mod_id;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 获取某条记录
	 *
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	array|string	$order_by	查询记录排序
     * @param	boolean	$debug	是否输出SQL语句
	 * @return array
     *
	 */
	public function fetchRow($condiction=null,$fetch_fields="*",$order_by=null,$debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_fields);
			if(is_array($condiction)){
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
			$row = $this->_db->fetchRow($sql);
			return $row;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 获取某个字段值
	 *
     * @param	array	$condiction	AND条件查询数组
     * @param	array|string	$fetch_field	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
	 * @return array
     *
	 */
	public function fetchOne($condiction,$fetch_field,$debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,$fetch_field);
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
			$one = $this->_db->fetchOne($sql);
			return $one;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 更新记录
	 *
	 * @param	array	$dataSet	更新数据
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$affect	是否返回更新数
	 * @return	boolean|int
     *
	 */
	public function updateRow($dataSet,$condiction,$affect = false){
		try {
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
			$affectRow = $this->_db->update($this->_prefix.$this->_table_name, $dataSet, $where);
			return $affect ? $affectRow : true;
		} catch (Exception $e) {
			throw $e;
		}
	}

    /**
	 * 删除记录
	 *
	 * @param	array	$condiction	AND条件查询数组
	 * @return	int
     *
	 */
	public function deleteRow($condiction){
		try {
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
			$result = $this->_db->delete($this->_prefix.$this->_table_name, $where);
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
	 * 返回完整表名
	 *
	 * @return	string
	 */
	public function getTableName()
	{
	    return  $this->_prefix. $this->_table_name;
	}
}