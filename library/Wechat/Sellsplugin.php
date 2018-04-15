<?php
/**
 * 微信营销插件
 */
class Wechat_Sellsplugin {
    function coupon($wc_id,$user_id){

        $userWechatM = new Wechat_Model_Wechat('wechat');
        $wechat = $userWechatM->fetchRow(array('id'=>$wc_id,'is_abort'=>'0','is_actived'=>'1','is_del'=>'0'));
        if($wechat && ($user_id > 0)){
            $userSellsCouponM = new Wechat_Model_SellsCoupon('wechat');
            $offerM = new Shop_Model_Offer('shop');
            $offerCateM = new Shop_Model_OfferCate('shop');
            $type = 1;
            $type_id = $wc_id;

            $coupons = $userSellsCouponM->_db->fetchAll("select c.* from ".$userSellsCouponM->_prefix.$userSellsCouponM->_table_name." as c inner join "."
                ".$offerCateM->_prefix.$offerCateM->_table_name." as oc on 
                c.cate_id = oc.cate_id where c.type = '{$type}' and c.type_id = '{$type_id}' and oc.is_actived = '1' and 
                oc.begin_time < '".time()."' and oc.end_time > '".time()."'");

            $cate_ids = $coupon_cate_ids = array();

            if($coupons){
                 foreach($coupons as $k=>$v){
                     
                    $coupon_cate_ids[] = $v['cate_id'];//进行中的优惠券

                    if($v['per_num'] > 0){ //限量领取
                       //过去一小时之内领取数
                        $per_num = $offerM->fetchRowsCount(array('cate_id = ?'=>$v['cate_id'],'user_id > ?'=>0,'user_time > ?'=>(time() - 3600)));
                        if($per_num >= $v['per_num']){
                            continue;
                        }
                    }
                    
                    //该用户已领数
                    $per_persion = $offerM->fetchRowsCount(array('cate_id = ?'=>$v['cate_id'],'user_id = ?'=>$user_id));
                    if($per_persion >= $v['per_persion']){
                        continue;
                    }

                    if($v['per_period'] > 0){//限制每人领取时间间隔
                        $lastest_offer = $offerM->fetchRow(array('cate_id = ?'=>$v['cate_id'],'user_id = ?'=>$user_id), array('user_time'), array('user_time desc'));
                        if($lastest_offer['user_time'] > (time() - $v['per_period'])){
                            continue;
                        }
                    }

                    $cate_ids[] = $v['cate_id'];
                     
                 }

                 if($cate_ids){//该用户有可领优惠券
                    $offer_row = $offerM->_db->fetchRow('select * from '.$offerM->_prefix.$offerM->_table_name." where
                        is_used = '0' and user_id = '0' and cate_id in(".implode(',', $cate_ids).") order by rand() limit 0,1");

                    if($offer_row){
                        $dataSet = array();
                        $dataSet['user_id'] = $user_id;
                        $dataSet['user_time'] = time();
                        $offerM->updateRow($dataSet, array('offer_id'=>$offer_row['offer_id']));

                        $cate_row = $offerCateM->fetchRow(array('cate_id = ?'=>$offer_row['cate_id'],'is_actived = ?'=>'1'));
                        return '尊敬的用户,恭喜您领取了【'.$cate_row['cate_name'].'】优惠券1张'."\r\n"."--------------------"."\r\n".$this->offer_str($cate_row)."\r\n"."--------------------"."\r\n".'券码为：'."\r\n".$offer_row['offer_code']."\r\n".'请尽快使用！';
                    }

                 }else{//该用户没有可领券
                     
                    if($coupon_cate_ids){//查找是否已经领过
                        $unused_coupon = $offerM->fetchRows(null,array('cate_id in (?)'=>implode(',',$coupon_cate_ids),'user_id = ?'=>$user_id,'is_used = ?'=>'0'));
                        if($unused_coupon){
                            $result_arr = array();
                            foreach($unused_coupon as $k=>$v){
                               $result_arr[$v['cate_id']]['res'][] = $v;
                               if(!isset($result_arr[$v['cate_id']]['cate'])){
                                   $result_arr[$v['cate_id']]['cate'] = $offerCateM->fetchRow(array('cate_id'=>$v['cate_id']));
                               }
                            }

                            if($result_arr){
                                $return_str = '尊敬的用户，您领取的优惠券如下：'."\r\n";
                                foreach($result_arr as $k=>$v){
                                    $return_str .= '【'.$v['cate']['cate_name'].'】'.'优惠券'.count($v['res']).'张'."\r\n"."--------------------"."\r\n".$this->offer_str($v['cate'])."\r\n"."--------------------"."\r\n"."券码为:";
                                    foreach($v['res'] as $rk=>$rv){
                                        $return_str .= "\r\n".$rv['offer_code']."\r\n--------------------";
                                    }
                                    $return_str .= "\r\n";
                                }
                                $return_str .= "请尽快使用！";
                                return $return_str;
                            }
                            return '尊敬的用户，您领取的优惠券已经发放，请尽快使用！';
                        }
                        $used_coupon = $offerM->fetchRowsCount(array('cate_id in (?)'=>implode(',',$coupon_cate_ids),'user_id = ?'=>$user_id,'is_used = ?'=>'1'));
                        if($used_coupon > 0)return '尊敬的用户，谢谢您的光顾，暂时没有可领优惠券，请继续关注！';
                    }


                 }

            }

        }
        return '对不起，优惠券已领完，请继续关注。';
    }

    function offer_str($cate_row){
         if(!is_array($cate_row))return '';
         return ($cate_row['discount_type'] == '0'? '满'.$cate_row['min_amount'].'直减'.$cate_row['discount']:'满'.$cate_row['min_amount'].'打'.intval($cate_row['discount']));
    }

}
