<?php
/**
 * 配送表模型 (snshop_shippings)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_Shipping extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'shippings';

    /**
     * 本构造函数的目的是为了让不同的模块访问不同的数据库，达到了配置不同的快递方式参数的目的
     * @param	string	$label	标签
     * @param	string	$config_file	配置文件名
     *
     */
    public function __construct($label, $config_file=null)
    {
        parent::__construct($label, $config_file);
        if(defined('CURRENT_MODULE_NAME') && (CURRENT_MODULE_NAME=='www_hk' || CURRENT_MODULE_NAME=='center_hk' || CURRENT_MODULE_NAME=='api_hk')){
            $this->_prefix=$this->_prefix.'hk_';
        }
    }

    /**
     * 根据地区查询配送
     *
     * @param	array	$regArr	地区ID
     * @return
     *
     */
    function getShippingsByRegion($regArr,$shop_id=0){
        try {
        	if($shop_id>0){
            	$sql = "SELECT * FROM
                        (SELECT a.* , b.sr_id , b.sr_configure FROM
                            " . $this->_prefix.$this->_table_name. " AS a
                             LEFT JOIN  " . $this->_prefix."shipping_regions AS b
                             ON a.shipping_name = b.shipping_name WHERE a.is_actived = '1' and b.shop_id = $shop_id) AS c
                   LEFT JOIN
                           " . $this->_prefix. "shipping_region_details AS d
                   ON c.sr_id = d.sr_id
                   WHERE
                          d.reg_id in (".implode(',',$regArr).")  GROUP BY shipping_id ORDER BY order_by ASC";//添加根据商户shop_id条件查询地区，by:ll 2014.12.22 10.17
            }else{
            	$sql = "SELECT * FROM
                        (SELECT a.* , b.sr_id , b.sr_configure FROM
                            " . $this->_prefix.$this->_table_name. " AS a
                             LEFT JOIN  " . $this->_prefix."shipping_regions AS b
                             ON a.shipping_name = b.shipping_name WHERE a.is_actived = '1') AS c
                   LEFT JOIN
                           " . $this->_prefix. "shipping_region_details AS d
                   ON c.sr_id = d.sr_id
                   WHERE
                          d.reg_id in (".implode(',',$regArr).")  GROUP BY shipping_id ORDER BY order_by ASC";
            }
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据地区查询配送
     *
     * 手机版调用
     *
     * @param	type	$regArr	地区ID
     * @return
     *
     */
    function getShippingsByRegionMobile($regArr,$shop_id=0){
        try {
        	if($shop_id>0){
            	$sql = "SELECT * FROM
                        (SELECT a.* , b.sr_id , b.sr_configure FROM
                            " . $this->_prefix.$this->_table_name. " AS a
                             LEFT JOIN  " . $this->_prefix."shipping_regions AS b
                             ON a.shipping_name = b.shipping_name WHERE a.is_m_actived = '1' and b.shop_id = $shop_id ) AS c
                   LEFT JOIN
                           " . $this->_prefix. "shipping_region_details AS d
                   ON c.sr_id = d.sr_id
                   WHERE
                          d.reg_id in (".implode(',',$regArr).")  GROUP BY shipping_id ORDER BY order_by ASC";//添加根据商户shop_id条件查询地区，by:ll 2014.12.5 15.46
            }else{
            	$sql = "SELECT * FROM
                        (SELECT a.* , b.sr_id , b.sr_configure FROM
                            " . $this->_prefix.$this->_table_name. " AS a
                             LEFT JOIN  " . $this->_prefix."shipping_regions AS b
                             ON a.shipping_name = b.shipping_name WHERE a.is_m_actived = '1') AS c
                   LEFT JOIN
                           " . $this->_prefix. "shipping_region_details AS d
                   ON c.sr_id = d.sr_id
                   WHERE
                          d.reg_id in (".implode(',',$regArr).")  GROUP BY shipping_id ORDER BY order_by ASC";
            }
            $rows = $this->_db->fetchAll($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据地区查询配送
     * 指定配送标识
     *
     * API版调用
     *
     * @param	 array	 $regArr         地区ID
     * @param   string  $shippingName   配送标识
     * @return
     *
     */
    function getShippingByName($regArr,$shippingName){
        try {
            $sql = "SELECT * FROM
                    (SELECT a.* , b.sr_id , b.sr_configure FROM
                        " . $this->_prefix.$this->_table_name. " AS a
                         LEFT JOIN  " . $this->_prefix."shipping_regions AS b
                         ON a.shipping_name = b.shipping_name WHERE a.is_actived = '1') AS c
               LEFT JOIN " . $this->_prefix. "shipping_region_details AS d
               ON c.sr_id = d.sr_id
               WHERE d.reg_id in (".implode(',',$regArr).") AND c.shipping_name='" . $shippingName
                    . "'  GROUP BY shipping_id";
            $rows = $this->_db->fetchRow($sql);
            return $rows;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
