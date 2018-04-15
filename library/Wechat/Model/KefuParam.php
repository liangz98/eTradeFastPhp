<?php
/**
 * 客服参数表模型 (snwc_kefu_param)
 *
 * @category   Wechat
 * @package    Wechat_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Wechat_Model_KefuParam extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = "kefu_param";
    
    /**
     * 查询客服参数
     *
     * @param	string	$content_desc	参数描述
     * @param	string	$default_str	默认值
     * @return	
     *
     */
    function getContent($content_desc,$default_str = ''){
        if(is_string($content_desc)){
                $content = $this->fetchOne(array('content_desc'=>$content_desc),'content');
                return empty($content) ? $default_str : $content;
        }
        return $default_str;
    }
}