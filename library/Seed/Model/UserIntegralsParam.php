<?php
/**
 * 积分参数表模型 (snsys_user_integralsparam)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_UserIntegralsParam extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'user_integralsparam';
    
    /**
     * 查询积分参数
     *
     * @param	string	$content_desc	积分描述
     * @param	string	$def_val	默认值
     * @return	
     *
     */
    public static function getValue($content_desc , $def_val = null){
        $userIntegralsParamM = new Seed_Model_UserIntegralsParam('system');
        $select = $userIntegralsParamM->_db->select();
        $select->from($userIntegralsParamM->getTableName(),'content');
        $select->where('content_desc = ?',$content_desc);
        $content = $userIntegralsParamM->_db->fetchOne($select);
        return $content ? $content : $def_val;
    }
    
}