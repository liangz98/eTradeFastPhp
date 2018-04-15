<?php
/**
 * 每日积分统计表模型 (snsys_user_integrals_day)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_UserIntegralDay extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'user_integrals_day';
    
    /**
     * 额外分类的字段
     *
     * @var array
     */
    public $_s_array = array('recommend','access_recommend');
    
    /**
     * 积分增长统计排除的类别
     *
     * @var array
     */
    public $_plus_except = array('cancel_order');
    
    /**
     * 积分减去统计排除的类别
     *
     * @var array
     */
    public $_minus_except = array();
    
    /**
     * 统计某个日期的积分情况
     *
     * @param	int	$user_id	用户ID
     * @param	string	$date	日期 (Ymd)
     * @param	boolean	$is_update	是否更新
     * @return	
     *
     */
    public function Insert_StatDay($user_id,$date,$is_update = true){
        try{
            $db = $this->_db;
            $db->beginTransaction();//开始事务处理
            $integralM = new Seed_Model_UserIntegral('system');
            $select = $integralM->_db->select();
            $select->from($integralM->getTableName(),'sum(integral_value)');
            $select->where('user_id = ?',$user_id);
            $select->where('add_date = ?',$date);
            $select->where('integral_type = ?','0');
            if($this->_plus_except){
                $select->where('source not in(?)',  $this->_plus_except);
            }
            $plugs_value = floatval($integralM->_db->fetchOne($select));//当天总增长积分

            $select = $integralM->_db->select();
            $select->from($integralM->getTableName(),'sum(integral_value)');
            $select->where('user_id = ?',$user_id);
            $select->where('add_date = ?',$date);
            $select->where('integral_type = ?','1');
            if($this->_minus_except){
                $select->where('source not in(?)',  $this->_minus_except);
            }
            $minus_value = floatval($integralM->_db->fetchOne($select));//当天总减去积分
            
            $select = $integralM->_db->select();
            $select->from($integralM->getTableName(),'sum(integral_value)');
            $select->where('user_id = ?',$user_id);
            $select->where('add_date = ?',$date);
            $select->where('source = ?','recommend');
            $recommend_total = floatval($integralM->_db->fetchOne($select));//当天总推荐积分

            $select = $integralM->_db->select();
            $select->from($integralM->getTableName(),'sum(integral_value)');
            $select->where('user_id = ?',$user_id);
            $select->where('add_date = ?',$date);
            $select->where('source = ?','access_recommend');
            $access_recommend_total = floatval($integralM->_db->fetchOne($select));//当天总接收推荐积分
            
            $time = strtotime($date.' 00:00:01');
            $dataSet = array();
            $dataSet['user_id'] = $user_id;
            $dataSet['add_month'] = date('Ym',$time);//月份
            $dataSet['add_date'] = $date;
            $dataSet['add_week'] = date('oW',$time);//周
            $dataSet['plus_value'] = $plugs_value;
            $dataSet['minus_value'] = $minus_value;
            $dataSet['diff_value'] = $plugs_value - $minus_value;//当天积分变动值
            $dataSet['recommend_total'] = $recommend_total;
            $dataSet['access_recommend_total'] = $access_recommend_total;
            $is_update = (bool)$is_update;
            if(!$is_update){//判断是否执行写入数据库操作
                return $dataSet;
            }
            $inteday_id = $this->insertRow($dataSet);//当天总记录
            if($inteday_id){
                 $integralDayCateM = new Seed_Model_UserIntegralDayCate('system');
                 $s_array = $this->_s_array;
                 $condiction = array();
                 $condiction['user_id = ?'] = $user_id;
                 $condiction['add_date = ?'] = $date;
                 $condiction['source in (?)'] = $s_array;
                 $integrals = $integralM->fetchRows(null, $condiction, array('add_time DESC'));
                 foreach ($integrals as $integral) {
                        $dataSet = array();
                        $dataSet['inteday_id'] = $inteday_id;
                        $dataSet['user_id'] = $user_id;
                        $dataSet['relate_user_id'] = $integral['relate_user_id'];
                        $dataSet['source'] = $integral['source'];
                        $dataSet['add_month'] = $integral['add_month'];
                        $dataSet['add_date'] = $integral['add_date'];
                        $dataSet['add_week'] = $integral['add_week'];
                        $dataSet['add_time'] = $integral['add_time'];
                        $dataSet['integral_value'] = $integral['integral_value'];
                        $integralDayCateM->insertRow($dataSet);                    
                 }
            }
            $integralM->updateRow(array('is_stat'=>'1'), array('user_id'=>$user_id,'add_date'=>$date));//更新是否已经统计标记
            $db->commit();
            return $inteday_id;
         }  catch (Exception $e){
            $db->rollBack();
         }
         return null;
    }
    
}