<?php
/**
 * 订单类型类
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_OrderType
{
    /**
     * 平台类型订单，平台自营的订单（订单产品均是平台上传的）
     * @var CONST
     */
    CONST TYPE_PLATFORM = '0';
    
    /**
     * 平台类型订单，平台自营的订单（订单产品含有其他商户的，这种订单则需要拆分订单）
     * @var CONST
     */
    CONST TYPE_PLATFORM_VAR = '1';
    
    /**
     * 平台个人分销订单，即平台下的个人分销订单
     * @var CONST
     */
    CONST TYPE_PLATFORM_PERSON = '2';
    
    /**
     * 商户订单，商户自己上传的订单销售出去的订单，b2c
     * @var CONST
     */
    CONST TYPE_SHOP = '3';
    
    /**
     * 商户旗下个人分销订单，即商户下分销商销售出去的订单
     * @var CONST
     */
    CONST TYPE_SHOP_PERSON = '4';
    
    /**
     * 平台旗下拆分出来的商户订单，即平台下分销商销售出去的订单     这是新加入的。 by:ll 2014.12.15 15:00
     * @var CONST
     */
    CONST TYPE_PLATFORM_SHOP = '5';
}
