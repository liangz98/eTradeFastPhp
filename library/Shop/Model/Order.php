<?php
/**
 * 订单表模型 (snshop_orders)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_Order extends Seed_Model_Db
{
	/**
	 * 无前缀的表名
	 *
	 * @var string
	 */
	public $_table_name = 'orders';

	/* 订单状态 */
	const OS_UNCONFIRMED =        0; // 未确认
	const OS_CONFIRMED   =        1; // 已确认
	const OS_CANCELED    =        2; // 已取消
	const OS_INVALID     =        3; // 无效
	const OS_RETURNED    =        4; // 退货

	/* 支付类型 */
	const PAY_ORDER      =        0; // 订单支付
	const PAY_SURPLUS    =        1; // 会员预付款

	/* 配送状态 */
	const SS_UNSHIPPED   =        0; // 未发货
	const SS_SHIPPED     =        1; // 已发货
	const SS_RECEIVED    =        2; // 已收货
	const SS_PREPARING   =        3; // 备货中
	const SS_SHIPPED_PART=        4; // 分批发货中
	const SS_REFUNDED    =        5; // 已退货

	/* 支付状态 */
	const PS_UNPAYED     =        0; // 未付款
	const PS_PAYING      =        1; // 付款中
	const PS_PAYED       =        2; // 已付款
	const PS_REFUND_PART =        3; // 部分退款
	const PS_REFUND      =        4; // 全额退款

	/* 综合状态 */
	const CS_WAIT_PAY    =        1; // 等待付款
	const CS_WAIT_SHIP   =        2; // 等待发货
	const CS_SHIPPED     =        3; // 已发货
	const CS_RECEIVED    =        4; // 已签收
	const CS_SUCCEED     =        5; // 交易成功
	const CS_FINISHED    =        6; // 交易关闭

	/* 退款状态 */
	const RBS_NONE       =        0;//没有退款
	const RBS_REPLIED    =        1;//买家已经申请退款，等待卖家同意
	const RBS_AGREED     =        2;//卖家已经同意退款
	const RBS_COMFIRMED  =        3;//卖家确认退款
	const RBS_DENIED     =        4;//卖家拒绝退款
	const RBS_CLOSED     =        5;//退款关闭
	const RBS_SUCCEED    =        6;//退款成功

	/**
	 * 格式化订单状态
	 *
	 * @param	int	$order_status	订单状态数值
	 * @return	string
	 *
	 */
	public static function order_status($order_status)
	{
		switch ($order_status)
		{
			case self::OS_UNCONFIRMED:
				return '未确定';break;
			case self::OS_CONFIRMED:
				return '<font color="#0000FF">已确定</font>';break;
			case self::OS_CANCELED:
				return '<font color="#FF0000">已取消</font>';break;
			case self::OS_INVALID:
				return '<font color="#FF0000">无效</font>';break;
			case self::OS_RETURNED:
				return '<font color="#FF0000">退货</font>';break;
			default:
				return '';
		}
	}

	/**
	 * 格式化支付状态
	 *
	 * @param	int	$pay_status	支付状态数值
	 * @return	string
	 *
	 */
	public static function payment_status($pay_status)
	{
		switch ($pay_status)
		{
			case Shop_Model_Order::PS_UNPAYED:
				return '未审核';break;
			case Shop_Model_Order::PS_PAYING:
				return '审核中';break;
			case Shop_Model_Order::PS_PAYED:
				return '<font color="#0000FF">已审核</font>';break;
			case Shop_Model_Order::PS_REFUND_PART:
				return '<font color="#FF0000">部分退款</font>';break;
			case Shop_Model_Order::PS_REFUND:
				return '<font color="#FF0000">已退款</font>';break;
			default:
				return '';
		}
	}

	/**
	 * 格式化发货状态
	 *
	 * @param	int	$shipping_status	发货状态数值
	 * @return	string
	 *
	 */
	public static function shipping_status($shipping_status)
	{
		switch ($shipping_status)
		{
			case Shop_Model_Order::SS_UNSHIPPED:
				return '未发货';break;
			case Shop_Model_Order::SS_SHIPPED:
				return '<font color="#0000FF">已发货</font>';break;
			case Shop_Model_Order::SS_RECEIVED:
				return '<font color="#0000FF"><b>已收货</b></font>';break;
			case Shop_Model_Order::SS_PREPARING:
				return '备货中';break;
			case Shop_Model_Order::SS_SHIPPED_PART:
				return '分批发货中';break;
			case Shop_Model_Order::SS_REFUNDED:
				return '已退货';break;
			default:
				return '';
		}
	}

	/**
	 * 查询订单记录
	 *
	 * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array	$condiction	AND条件查询数组
	 * @param	type	$orcondiction	OR条件查询数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	array|string	$fetch_fields	查询记录字段
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	array
	 *
	 */
	public function fetchOrders($limit = array(0,0) , $condiction=null , $orcondiction=null , $order_by=null , $fetch_fields="*" , $catecondiction=null ,$debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id', array('agent_id','goods_id','goods_number'));
			if (is_array($limit) && $limit[1] > 0)
			$select->limit($limit[1], $limit[0]);
			if(isset($condiction) && is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where("t1.".$k." = ?", $v);
					}else{
						$select->where("t1.".$k, $v);
					}
				}
			}

			if(isset($orcondiction) && is_array($orcondiction)){
				$orwhere=array();
				foreach ($orcondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
					}else{
						$orwhere[]=$this->_db->quoteInto("t1.".$k, $v);
					}
				}
				if(count($orwhere)>0)
				$select->where(implode(" OR ",$orwhere));
			}

			if(isset($catecondiction) && is_array($catecondiction)){
				foreach ($catecondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where("t2.".$k." = ?", $v);
					}else{
						$select->where("t2.".$k, $v);
					}
				}
			}
			$select->distinct(true);
			$select->group('t1.order_id');
			if($order_by!=null)
			$select->order($order_by);
			$sql = $select->__toString();
			if ($debug)echo $sql;

			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * 查询订单数量
	 *
	 * @param	array	$condiction	AND条件查询数组
	 * @param	type	$orcondiction
	 * @param	boolean	$debug	是否输出SQL语句
	 * @return	int
	 *
	 */
	public function fetchOrdersCount($condiction=null , $orcondiction=null ,$catecondiction=null , $debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name." as t1","COUNT(DISTINCT t1.order_id)");
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id', null);
			if(is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where("t1.".$k." = ?", $v);
					}else{
						$select->where("t1.".$k, $v);
					}
				}
			}

			if(isset($orcondiction) && is_array($orcondiction)){
				$orwhere=array();
				foreach ($orcondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
					}else{
						$orwhere[]=$this->_db->quoteInto("t1.".$k, $v);
					}
				}
				if(count($orwhere)>0)
				$select->where(implode(" OR ",$orwhere));
			}

			if(isset($catecondiction) && is_array($catecondiction)){
				foreach ($catecondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where("t2.".$k." = ?", $v);
					}else{
						$select->where("t2.".$k, $v);
					}
				}
			}
			//            $select->distinct(true);
			//            $select->group('t1.order_id');
			$sql = $select->__toString();
			if($debug)echo $sql;
			$count = $this->_db->fetchOne($sql);
			return $count;
		} catch (Exception $e) {
			throw $e;
		}
	}


	public function fetchOrderList($limit = array(0,0) , $condiction=null , $orcondiction=null , $order_by=null , $fetch_fields="*" ,$catecondiction=null ,$debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id', array('agent_id','goods_id','goods_number','stock_barcode','goods_name','goods_attr',''));
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

			if(isset($orcondiction) && is_array($orcondiction)){
				$orwhere=array();
				foreach ($orcondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
					}else{
						$orwhere[]=$this->_db->quoteInto($k, $v);
					}
				}
				if(count($orwhere)>0)
				$select->where(implode(" OR ",$orwhere));
			}

			if(is_array($catecondiction)){
				foreach ($catecondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where("t2".$k." = ?", $v);
					}else{
						$select->where($k, $v);
					}
				}
			}

			if($order_by!=null)
			$select->order($order_by);
			$select->group('t1.order_id');
			$sql = $select->__toString();
			if($debug)echo $sql;
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}

	public function fetchOrderListCount($condiction=null , $orcondiction=null , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1','COUNT(*)');
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id', null);
			if(is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where($k." = ?", $v);
					}else{
						$select->where($k, $v);
					}
				}
			}

			if(isset($orcondiction) && is_array($orcondiction)){
				$orwhere=array();
				foreach ($orcondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
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
	 * 供应商订单商品销售数量
	 * 
	 * */
	public function fetchShopOrderList($limit = array(0,0) , $condiction=null , $orcondiction=null , $order_by=null , $fetch_fields="*" ,$catecondiction=null ,$debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id', array('agent_id','goods_id','goods_number','stock_barcode','goods_name','goods_attr','stock_id','sum(t2.goods_number) as sold_total'));
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

			if(isset($orcondiction) && is_array($orcondiction)){
				$orwhere=array();
				foreach ($orcondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
					}else{
						$orwhere[]=$this->_db->quoteInto($k, $v);
					}
				}
				if(count($orwhere)>0)
				$select->where(implode(" OR ",$orwhere));
			}

			if(is_array($catecondiction)){
				foreach ($catecondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where("t2".$k." = ?", $v);
					}else{
						$select->where($k, $v);
					}
				}
			}

			if($order_by!=null)
			$select->order($order_by);
			$select->group('t2.stock_id');
			$sql = $select->__toString();
			if ($debug)echo $sql;
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
    public function fetchShopOrderListCount($condiction=null , $orcondiction=null , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',null);
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id', 'count(DISTINCT t2.stock_id)');
			if(is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where($k." = ?", $v);
					}else{
						$select->where($k, $v);
					}
				}
			}

			if(isset($orcondiction) && is_array($orcondiction)){
				$orwhere=array();
				foreach ($orcondiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
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
	 * 订单明细表
	 * @param	array	$limit	限制查询所返回的记录数量，array(offset, count)
	 * @param	array	$condiction	AND条件查询数组
	 * @param	array|string	$order_by	查询记录排序
	 * @param	boolean	$debug	是否输出SQL语句
	*/
	public function fetchAllOrderList($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id', array('stock_barcode','goods_price','goods_number','unit_name','brand_name','cate_name'));
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
	 * 汇总的销售表
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$debug	是否输出SQL语句
	 */
	public function fetchCount($condiction=null, $debug=false)
	{
		try {	
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',array('substring(order_sn,1,2) as sn','count(*) as count','sum(goods_amount) as goods_amount','sum(shipping_fee) as shipping_fee','sum(tax_amount) as tax_amount'));
			if(isset($condiction) && is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where($k." = ?", $v);
					}else{
						$select->where($k, $v);
					}
				}
			}
			$select->group('sn');				
			$sql = $select->__toString();
			if($debug)echo $sql;	
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}	
	}
	
	/**
	 * 注册用户及订单报表
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$debug	是否输出SQL语句
	 */
	
	public function fetchOrderUser($condiction=null,$debug=false){
		try {
			
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1','COUNT(*)');
			$select->joinleft("snsys_users AS t2", 't1.user_id = t2.user_id',null);
			
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
	 * 商品销量报表
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$debug	是否输出SQL语句
	 */
	
	public function fetchOrderGood($limit = array(0,0),$condiction=null,$order_by=null, $debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',null);
			$select->joinleft($this->_prefix."order_goods AS t2", 't1.order_id = t2.order_id',array('sum(t2.goods_number) as num','goods_name'));
			if (is_array($limit) && $limit[1] > 0)
				$select->limit($limit[1], $limit[0]);
			if(is_array($condiction)){
				foreach ($condiction as $k=>$v){
					if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
						$select->where($k." = ?", $v);
					}else{
						$select->where($k, $v);
					}
				}
			}			
			$select->order('num DESC');	
			$select->group('t2.goods_name');
			$sql = $select->__toString();
			if($debug)echo $sql;
			$row = $this->_db->fetchAll($sql);
			return $row;
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * 注册用户及订单详情
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$debug	是否输出SQL语句
	 */

	public function fetchUserOrder($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false){
		try {
			
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1',$fetch_fields);
			$select->joinleft("snsys_users AS t2", 't1.user_id = t2.user_id',null);
			$select->joinleft($this->_prefix."stores AS t3", 't2.service_site = t3.store_token',array('store_name'));
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
	 * 注册用户及订单详情汇总
	 * @param	array	$condiction	AND条件查询数组
	 * @param	boolean	$debug	是否输出SQL语句
	 */	

	public function fetchUserOrderCount($condiction=null , $debug=false)
	{
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name.' as t1','COUNT(*)');
			$select->joinleft("snsys_users AS t2", 't1.user_id = t2.user_id',null);
			$select->joinleft($this->_prefix."stores AS t3", 't2.service_site = t3.store_token',null);
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
