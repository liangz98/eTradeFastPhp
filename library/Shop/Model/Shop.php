<?php
/**
 * 经销商店铺表模型 (snshop_shops)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_Shop extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'shops';
    
	public function fetchadminShops($limit = array(0,0) , $condiction=null ,$group_by=null, $order_by=null , $fetch_fields="*" , $debug=false)
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
			if ($group_by !=null)
			    $select->group($group_by);
			$sql = $select->__toString();
			if($debug)echo $sql;
			$rows = $this->_db->fetchAll($sql);
			return $rows;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
    public function fetchadminCount($condiction=null ,$debug=false){
		try {
			$select = $this->_db->select();
			$select->from($this->_prefix.$this->_table_name,array("*","COUNT(admin_shop_id)"));
			$select->where('admin_shop_id <>?','0');
			$select->group('admin_shop_id');
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
	
	//查询商户角色
	function fetchShopRole($limit = array(0,0) , $condiction=null , $orcondition=null , $order_by=null , $fetch_fields="*" , $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name." as t1",array('shop_id','shop_name','shop_host_name'));
            $select->joinleft($this->_prefix."shop_roles AS t2", 't1.shop_id = t2.shop_id', array('role_id',));
            if (is_array($limit) && $limit[1] > 0)
                $select->limit($limit[1], $limit[0]);
            if(isset($condiction) && is_array($condiction)){
                foreach ($condiction as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $select->where($k." = ?", $v);
                    }else{
                        $select->where($k, $v);
                    }
                }
            }

            if(isset($orcondition) && is_array($orcondition)){
                $orwhere=array();
                foreach ($orcondition as $k=>$v){
                    if(preg_match("/^[a-zA-Z_0-9.]+$/i",$k)){
                        $orwhere[]=$this->_db->quoteInto("t1.".$k." = ?", $v);
                    }else{
                        $orwhere[]=$this->_db->quoteInto($k, $v);
                    }
                }
                if(count($orwhere)>0)
                    $select->where(implode(" OR ",$orwhere));
            }

            if($order_by!=null)
                $select->order($order_by);
                $select->group('t1.shop_id');
            $sql = $select->__toString();
          if ($debug) echo $sql;
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
	
}
