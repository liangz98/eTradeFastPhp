<?php
/**
 * 配送地区关联表模型 (snshop_shipping_region_details)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_ShippingRegionDetail extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'shipping_region_details';

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
}
