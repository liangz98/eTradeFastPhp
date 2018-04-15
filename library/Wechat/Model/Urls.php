<?php
/**
 * URL链接地址库表模型 (snwc_urls)
 *
 * @category   Wechat
 * @package    Wechat_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Wechat_Model_Urls  extends  Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'urls';

    /**
     * 创建URL链接地址
     *
     * @param	string	$true_url	真实地址
     * @param	string	$run_code	运行代码
     * @param	string	$url_code	地址码
     * @param	string	$allow	允许执行类型
     * @return	
     *
     */
    public function createUrl($true_url,$run_code = '',$url_code = '',$allow = ''){
        $dataSet = array();
        $dataSet['true_url'] = $true_url;
        $dataSet['run_code'] = $run_code;
        $dataSet['allow'] = $allow;
        $dataSet['add_time'] = time();
        if(trim($url_code) != ''){
            $dataSet['url_code'] = $url_code;
            if(!$this->is_exist_urlcode($url_code))$this->insertRow($dataSet);
            return $url_code;
        }else{
            $u_id = $this->insertRow($dataSet);
            if($u_id){
                $url_code = md5($dataSet['true_url'].md5($u_id));
                $this->updateRow(array('url_code'=>$url_code),array('u_id'=>$u_id));
                return $url_code;
            }
        }
        return '';
    }

    /**
     * 查询URL链接地址是否存在
     *
     * @param	string	$url_code	地址码
     * @return	
     *
     */
    public function is_exist_urlcode($url_code){
        if($this->fetchRow(array('url_code'=>$url_code))){
           return true;
        }else{
           return false;
        }
    }
}