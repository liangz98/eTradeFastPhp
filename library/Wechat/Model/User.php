<?php
/**
 * 微信用户表模型 (snwc_users)
 *
 * @category   Wechat
 * @package    Wechat_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Wechat_Model_User extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'users';

    /**
     * 令牌有效期
     *
     * @var integer
     */
    const _sendmsg_period = 172800;//2*24*3600 两天

    /**
     * 查询微信用户
     *
     * @param	array	$user	用户信息
     * @param	integer	$wc_id	公众号ID
     * @return	
     *
     */
    public function recordWechatUser($user,$wc_id){
        if(!isset($user['user_id']))return false;
        $wechatuser = $this->fetchRow(array('user_id'=>$user['user_id'],'wc_id'=>$wc_id));
        if($wechatuser){//更新最近访问微信公众号时间
            $time = time();
            $this->updateRow(array('last_time'=>$time), array('wu_id'=>$wechatuser['wu_id']));
            $wechatuser['last_time'] = $time;
            return $wechatuser;
        }else{//添加新访问用户记录
            $dataSet = array();
            $dataSet['wc_id'] = $wc_id;
            $dataSet['user_id'] = $user['user_id'];
            $dataSet['add_time'] = $dataSet['last_time'] = time();
            $wu_id = $this->insertRow($dataSet);
            $wechatuser = $this->fetchRow(array('wu_id'=>$wu_id,'wc_id'=>$wc_id));
            return $wechatuser;
        }
        return false;
    }

    /**
     * 查询微信用户
     *
     * @param	array	$user	用户信息
     * @param	integer	$wc_id	公众号ID
     * @param	integer	$inviuid	推荐用户ID
     * @return	
     *
     */
    public function record($wc_username,$wc_id,$inviuid=0){
        if(empty($wc_username))return false;
        $wechatuser = $this->fetchRow(array('wc_username'=>$wc_username,'wc_id'=>$wc_id));
        if($wechatuser){//更新最近访问微信公众号时间
            $time = time();
            $this->updateRow(array('last_time'=>$time), array('wu_id'=>$wechatuser['wu_id']));
            $wechatuser['last_time'] = $time;
            return $wechatuser;
        }else{//添加新访问用户记录
            $dataSet = array();
            $dataSet['wc_id'] = $wc_id;
            $dataSet['wc_username'] = $wc_username;
            $dataSet['invi_uid'] = $inviuid;
            $dataSet['add_time'] = $dataSet['last_time'] = time();
            $wu_id = $this->insertRow($dataSet);
            $wechatuser = $this->fetchRow(array('wu_id'=>$wu_id,'wc_id'=>$wc_id));
            return $wechatuser;
        }
        return false;
    }
    
    /**
     * 关注微信用户
     *
     * @param	integer	$wu_id	微信用户ID
     * @return	
     *
     */
    public function follow($wu_id){
        $this->updateRow(array('is_unfollow'=>'0'), array('wu_id'=>intval($wu_id)));
        $userWechatFollowM = new Wechat_Model_Follow('wechat');
        $dataSet = array();
        $dataSet['wu_id'] = $wu_id;
        $dataSet['follow_status'] = '1';
        $dataSet['add_time'] = time();
        $userWechatFollowM->insertRow($dataSet);
    }

    /**
     * 取消关注微信用户
     *
     * @param	integer	$wu_id	微信用户ID
     * @return	
     *
     */
    public function unfollow($wu_id){
        $this->updateRow(array('is_unfollow'=>'1'), array('wu_id'=>intval($wu_id)));
        $userWechatFollowM = new Wechat_Model_Follow('wechat');
        $dataSet = array();
        $dataSet['wu_id'] = $wu_id;
        $dataSet['follow_status'] = '0';
        $dataSet['add_time'] = time();
        $userWechatFollowM->insertRow($dataSet);
    }

    /**
     * 获取令牌有效期
     *
     * @return	integer
     *
     */
    public static function getSendmsgPeriod(){
        return self::_sendmsg_period;
    }
}