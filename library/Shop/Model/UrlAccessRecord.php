<?php
/**
 * URL链接地址库表模型 (snwc_urls)
 *
 * @category   Wechat
 * @package    Wechat_Model
 * @copyright  Copyright (c) 2015 萌芽网络 (http://www.gzseed.com)
 */
class Shop_Model_UrlAccessRecord  extends  Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'url_access_record';

    /**
     * 创建/更新URL链接地址
     *
     */
    public function updateUrl($user_id,$url){
        $dataSet = array();
        $dataSet['lately_url'] = $url;
        $dataSet['update_time'] = time();
        if(!$this->is_exist_urlrecord($user_id)){
            $dataSet['user_id'] = $user_id;
            $uar_id = $this->insertRow($dataSet);
            return $uar_id;
        }else{
            $uar_id = $this->updateRow($dataSet,array('user_id'=>$user_id));
            return $uar_id;
        }
    }

    /**
     * 查询URL链接地址是否存在
     *
     * @param	string	$user_id	用户ID
     * @return	
     *
     */
    public function is_exist_urlrecord($user_id){
        if($this->fetchRow(array('user_id'=>$user_id))){
           return true;
        }else{
           return false;
        }
    }
}