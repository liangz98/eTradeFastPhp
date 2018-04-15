<?php
/**
 * 优惠活动表模型 (snshop_goods_act)
 *
 * @category   Shop
 * @package    Shop_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_GoodsAct extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'goods_act';

    /* 活动类型 */
    const AT_FULLGIFT   = 1; // 满送
    const AT_FULLREDUCE = 2; // 满减
}