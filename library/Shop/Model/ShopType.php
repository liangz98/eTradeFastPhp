<?php
/**
 * 商户分类表模型 (snshop_shop_types)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_ShopType extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'goods_cates';
    
    /**
     * 平台商户类型
     * @var unknown_type
     */
    const TYPE_SHOP = 'TYPE_SHOP';
    
    /**
     * 平台个人类型
     * @var unknown_type
     */
    const TYPE_PERSON = 'TYPE_PERSON';  
    
    /**
     * 平台商户旗下个人类型
     * @var unknown_type
     */
    const TYPE_SHOP_PERSON = 'TYPE_SHOP_PERSON';
}