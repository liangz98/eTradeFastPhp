<?php
/**
 * 财务对账分类表模型 (snshop_finance_account_cates)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_FinanceAccountCate extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'finance_account_cates';
    
    /**
     * 商户
     * @var unknown_type
     */
    CONST ACCOUNT_SHOP = 'ACCOUNT_SHOP';
    
    /**
     * 平台个人
     * @var unknown_type
     */
    CONST ACCOUNT_PERSON = 'ACCOUNT_PERSON';
    
    /**
     * 商户旗下
     * @var unknown_type
     */
    CONST ACCOUNT_SHOP_PERSON = 'ACCOUNT_SHOP_PERSON';
}
