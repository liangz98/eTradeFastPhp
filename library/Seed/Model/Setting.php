<?php
/**
 * 系统参数设置表模型 (snsys_settings)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_Setting extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'settings';
    
    /**
     * 获取系统参数设置
     *
     * @param	string	$mod_name	
     * @return	
     *
     */
    function getSetting($mod_name){
        $settings = $this->fetchRows(null,array('mod_name'=>$mod_name),'order_by asc');
        $data=array();
        foreach ($settings as $setting){
            $data[$setting['setting_variable']]=$setting['setting_content'];
        }
        return $data;
    }
}
