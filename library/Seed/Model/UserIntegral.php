<?php
/**
 * 用户积分表模型 (snsys_user_integrals)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_UserIntegral extends Seed_Model_Db 
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'user_integrals';
        
    /**
     * 登陆检查获取积分
     * 
     * @param type $user
     * 
     */
    function AddWechatSubscribeIntegral($user){
            //只是微信用户登陆才允许添加积分
            if(empty($user) || !is_array($user) || !isset($user['wc_username']) || empty($user['wc_username']))return false;
            $shareuserid = intval(Seed_Cookie::getCookie('shareuserid'));
            $sharetoken = strip_tags(trim(Seed_Cookie::getCookie('sharetoken')));
            if($shareuserid > 0 && !empty($sharetoken)){ //推荐人积分
                 if($shareuserid == $user['user_id'])return false;
                 try{
                    $userM = new Seed_Model_User('system');
                    $recommand_user = $userM->fetchRow(array('user_id'=>$shareuserid));
                    if($recommand_user){
                       $inteltoken = Seed_Model_UserIntegralsParam::getValue('sharetoken', '');
                       $start_time = Seed_Model_UserIntegralsParam::getValue('picker_start_date', '');
                       $end_time = Seed_Model_UserIntegralsParam::getValue('picker_end_date', '');
                       $start_time = intval(strtotime($start_time));
                       $end_time = intval(strtotime($end_time));
                       if(!empty($inteltoken) && ($inteltoken == $sharetoken) && $start_time < time() && $end_time > time()){
                            $source = 'recommend';
                            $distinct_str = $source.'_'.$user['user_id'].'_'.$sharetoken;
                            $condiction = array();
                            $condiction['user_id'] = $recommand_user['user_id'];
                            $condiction['source'] = $source;
                            $condiction['distinct_str'] = $distinct_str;
                            $check = $this->fetchRow($condiction);
                            if(empty($check)){
                                $shareclick_a_value = Seed_Model_UserIntegralsParam::getValue('shareclick_a', 0);
                                $shareclick_b_value = Seed_Model_UserIntegralsParam::getValue('shareclick_b', 0);
                                $shareclick_each = Seed_Model_UserIntegralsParam::getValue('shareclick_each', 0);
                                $shareclick_max = Seed_Model_UserIntegralsParam::getValue('shareclick_max', 0);

                                //检查上限
                                $sum_value = $this->fetchSum('integral_value',array('add_date'=>date('Ymd'),"source ='recommend' OR source = 'access_recommend'"=>null));
                                if(($sum_value+$shareclick_a_value+$shareclick_b_value)<=$shareclick_max){
                                    $integral_desc = '推荐好友'."[{$user['nick_name']}]".'点击';
                                    $this->addIntegral($recommand_user['user_id'], $shareclick_a_value, $integral_desc, $source, '推荐',$user['user_id'],0,$distinct_str);

                                    $source = 'access_recommend';
                                    if($shareclick_each=='1'){
                                        //互刷
                                        $distinct_str = $source.'_'.$recommand_user['user_id'].'_'.$sharetoken;
                                    }else{
                                        //第一次
                                        $distinct_str = $source.'_'.$sharetoken;
                                    }

                                    $condiction = array();
                                    $condiction['user_id'] = $user['user_id'];
                                    $condiction['source'] = $source;
                                    $condiction['distinct_str'] = $distinct_str;
                                    $check = $this->fetchRow($condiction);
                                    if(empty($check)){
                                        $integral_desc = '接受'."[{$recommand_user['nick_name']}]".'推荐';
                                        $this->addIntegral($user['user_id'], $shareclick_b_value, $integral_desc, $source, '接受推荐',$recommand_user['user_id'],0,$distinct_str);
                                    }
                                }
                            }
                        }
                    }
                 }  catch (Exception $e){
                 }
            }
            Seed_Cookie::delete('sharetoken');//清除cookie
    }

    /**
     * 添加积分
     * 
     * @param int $user_id 用户id
     * @param int $integral_value 积分值
     * @param type $integral_desc 积分说明
     * @param string $source 积分来源
     * @param string $source_desc 积分来源说明
     * @param int $relate_user_id 关联用户id
     * @param int $admin_id 人工操作员id
     * @param string $distinct_str 内部区分码
     * @param string $time 自定义积分添加时间
     * @return int
     * 
     */
    function addIntegral($user_id,$integral_value,$integral_desc,$source,$source_desc,$relate_user_id = 0,$admin_id = 0,$distinct_str = '',$time = 0){
            if($integral_value>0){
                $user_id = intval($user_id);
                $integral_value = abs(intval($integral_value));
                $integral_desc = strip_tags(trim($integral_desc));
                $source = strip_tags(trim($source));
                $source_desc = strip_tags(trim($source_desc));
                $admin_id = intval($admin_id);
                $distinct_str = strip_tags(trim($distinct_str));
                $time = intval($time);
                if($time < 1)$time = time();
                $dataSet = array();
                $dataSet['admin_id'] = $admin_id;
                $dataSet['user_id'] = $user_id;
                $dataSet['add_time'] = $time;
                $dataSet['add_month'] = date('Ym',$time);
                $dataSet['add_date'] = date('Ymd',$time);
                $dataSet['add_week'] = date('oW',$time);
                $dataSet['integral_value'] = $integral_value;
                $dataSet['integral_desc'] = $integral_desc;
                $dataSet['source'] = $source;
                $dataSet['source_desc'] = $source_desc;
                $dataSet['distinct_str'] = $distinct_str;
                $dataSet['relate_user_id'] = $relate_user_id;
                $dataSet['integral_type'] = '0';//添加标志
                $dataSet['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                try{
                    $this->_db->beginTransaction();
                    if(!!$integral_id = $this->insertRow($dataSet)){
                        $userM = new Seed_Model_User('system');
                        $user = $userM->fetchRow(array('user_id'=>$user_id));
                        if($user){
                            $userM->updateRow(array('user_integral'=>$user['user_integral'] + $integral_value), array('user_id'=>$user_id));
                        }
                        $this->_db->commit();
                        return $integral_id;
                    } 
                }catch(Exception $e){
                     $this->_db->rollBack();
                }
                return false;
            }else{
                return false;
            }
    }

    /**
     * 减去积分
     * 
     * @param int $user_id 用户id
     * @param int $integral_value 积分值
     * @param string $integral_desc 积分说明
     * @param string $source 积分来源
     * @param string $source_desc 积分来源说明
     * @param int $relate_user_id 关联用户id
     * @param int $admin_id 人工操作员id
     * @return int
     */
    function minusIntegral($user_id,$integral_value,$integral_desc,$source,$source_desc,$relate_user_id = 0,$admin_id = 0,$distinct_str = ''){
        if($integral_value>0){
            $user_id = intval($user_id);
            $integral_value = abs(intval($integral_value));
            $integral_desc = strip_tags(trim($integral_desc));
            $source = strip_tags(trim($source));
            $source_desc = strip_tags(trim($source_desc));
            $admin_id = intval($admin_id);
            $distinct_str = strip_tags(trim($distinct_str));
            $time = time();
            $dataSet = array();
            $dataSet['admin_id'] = $admin_id;
            $dataSet['user_id'] = $user_id;
            $dataSet['add_time'] = $time;
            $dataSet['add_month'] = date('Ym',$time);
            $dataSet['add_date'] = date('Ymd',$time);
            $dataSet['add_week'] = date('oW',$time);
            $dataSet['integral_value'] = $integral_value;
            $dataSet['integral_desc'] = $integral_desc;
            $dataSet['source'] = $source;
            $dataSet['source_desc'] = $source_desc;
            $dataSet['distinct_str'] = $distinct_str;
            $dataSet['relate_user_id'] = $relate_user_id;
            $dataSet['integral_type'] = '1';//减去标志
            $dataSet['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            try{
                $this->_db->beginTransaction();
                if(!!$integral_id = $this->insertRow($dataSet)){
                    $userM = new Seed_Model_User('system');
                    $user = $userM->fetchRow(array('user_id'=>$user_id));
                    if($user){
                        if($user['user_integral'] < $integral_value){

                        }else{
                              $userM->updateRow(array('user_integral'=>$user['user_integral'] - $integral_value), array('user_id'=>$user_id));
                              $this->_db->commit();
                              return $integral_id;
                        }
                    }
                }
                $this->_db->rollBack();
            }catch(Exception $e){
                $this->_db->rollBack();
            }
            return false;
        }else{
            return false;
        }
    }

    /*
     * 积分来源说明
     * 
     * @param string $source
     * @return string
     * 
     */
   public static function getSourceDesc($source){
        //tips:有添加新来源请在数组里加上
        $array = array(
            "access_recommend"=>"接受推荐"
            ,"buy_code"=>"换购红包"
            ,"cancel_order"=>"取消订单"
            ,"integral_fee"=>"积分抵扣"
            ,"manual"=>"人工操作"
            ,"recommend"=>"推荐"
            ,"signin"=>"签到"
            ,"subscribe"=>"关注"
            ,"signin_everydayofmonth"=>"月全签"
            ,"order_good_comment"=>"订单商品评价"
            ,"increase_week"=>"周增长"
            ,"receive_order"=>"购物积分"
            ,"receive_order_incre"=>"购物赠送积分"
            ,"open_shop"=>"开店"
            ,"recommend_open_shop"=>"推荐开店"
            ,"access_recommend_open_shop"=>"接受推荐开店"
        );
        return isset($array[$source]) ? $array[$source] : '';
    }
}
