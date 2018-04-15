<?php
/**
 * friend_code红包表模型 (snsys_user_code)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_UserCode extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'user_code';
    
    
    /**
     * 查询friend_code红包
     *
     * @param	int	$user_id	用户ID
     * @param	int	$code_value	红包
     * @param	int	$expire_time	有效期
     * @param	int	$admin_id	操作人ID
     * @param	int	$year_add	有效期年份
     * @return	int
     *
     */
    function createCode($user_id,$code_value,$expire_time = 0,$admin_id = 0,$year_add = 2){
        $user_id = intval($user_id);
        $admin_id = intval($admin_id);
        $expire_time = intval($expire_time);
        $code_value = $code_value;
        
        $time = time();
        if(($expire_time <= 0)){
            $H = date('H',$time);
            $i =  date('i',$time);
            $s = date('s',$time);
            $m = date('m',$time);
            $d =  date('d',$time);
            $Y = date('Y',$time);
            if($m == 2 && ($d == 28 || $d == 29)){//二月
                $is_leapYear = Seed_Date::isLeapYear($Y);
                $expire_is_leapYear = Seed_Date::isLeapYear($Y+$year_add);
                if($is_leapYear && !$expire_is_leapYear && $d == 29){
                     $d = 28;
                }elseif(!$is_leapYear && $expire_is_leapYear && $d == 28){
                     $d = 29;
                }
            }
            $expire_time = mktime($H,$i,$s,$m,$d,$Y+$year_add);
        }
        $dataSet = array();
        $dataSet['first_user_id'] = $user_id;
        $dataSet['user_id'] = $user_id;
        $dataSet['code_value'] = $code_value;
        $dataSet['admin_id'] = $admin_id;
        $dataSet['add_time'] = $time;
        $dataSet['add_date'] = date('Ymd',$time);
        $dataSet['add_month'] = date('Ym',$time);
        $dataSet['add_week'] = date('oW',$time);
        $dataSet['expire_time'] = $expire_time;
        $dataSet['expire_str'] = date('YmdHis',$expire_time);
        $dataSet['last_transfer_time'] = date('YmdHis',$time);
        if(!!$code_id = $this->insertRow($dataSet)){
//            $code_sn = $code_id;
//            if(strlen(strval($code_id)) < 10){
//                $code_sn = str_repeat('0',10-strlen(strval($code_id))).$code_id;
//            }
            $code_sn = $this->getCodeSN();
            $this->updateRow(array('code_sn'=>$code_sn), array('code_id'=>$code_id));
            return $code_id;
        }
        return false;
    }
    
    /**
     * 获取红包SN
     *
     * @return	string
     *
     */
    function getCodeSN(){
        $code_sn = Seed_Common::genRandomStringBy(12,'n_lu');
        $check = $this->fetchRow(array('code_sn = BINARY ?'=>$code_sn));
        if($check){
            return $this->getCodeSN();
        }else{
            return $code_sn;
        }
    }
    
    /**
     * 红包转让
     *
     * @param	int	$user_id	用户ID
     * @param	int	$to_user_id	被转让用户ID
     * @param	int	$code_id	红包
     * @return	string
     *
     */
    function transferTo($user_id,$to_user_id,$code_id){
        
        try{
            $this->_db->beginTransaction();
            $userCodeTransferM = new Seed_Model_UserCodeTransfer('system');
            $userM = new Seed_Model_User('system');
            $wechatUserM = new Wechat_Model_User('wechat');
            $usercode = $this->fetchRow(array('code_id'=>$code_id));
            if(empty($usercode)){
                return('对不起，数据有误！');
            }
            
            if(!($usercode['user_id'] == $user_id)){
                return('对不起，该码不在您的账下！');
            }
            
            if($user_id == $to_user_id){
                return('对不起，不能转让给自己！');
            }
            
            $touser = $userM->fetchRow(array('user_id'=>$to_user_id));
//            $wechatuser = $wechatUserM->fetchRow(array('user_id'=>$to_user_id));
            if(empty($touser)){
                return('对不起，找不到目标用户！');
            }
            
            if(!($usercode['code_status'] == '0')){
                 return('对不起，该码状态不可转让！');
            }
            
            if($usercode['expire_time'] < time()){
                $usercode = $this->checkCodeValid($usercode);//检测码是否有效
                return('对不起，该码已过有效期！');
            }
            
            $time = time();
            $dataSet = array();
            $dataSet['code_id'] = $code_id;
            $dataSet['code_sn'] = $usercode['code_sn'];
            $dataSet['user_id'] = $user_id;
            $dataSet['to_user_id'] = $to_user_id;
            $dataSet['add_time'] = $time;
            $dataSet['add_date'] = date('Ymd',$time);
            $dataSet['add_month'] = date('Ym',$time);
            $dataSet['add_week'] = date('oW',$time);
            $dataSet['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $dataSet['ip'] = Seed_Browser::get_client_ip();
            if(!!$cf_id = $userCodeTransferM->insertRow($dataSet)){
                  $this->updateRow(array('user_id'=>$to_user_id,'last_transfer_time'=>date('YmdHis',$time)), array('code_id'=>$code_id));
                  $this->_db->commit();
                  return 'ok';//返回转让成功标志
            }  
            $this->_db->rollBack();
        } catch (Exception $ex) {
            $this->_db->rollBack();
        }
        return false;
    }
    
    
   /**
     * 格式化friend_code红包状态
     *
     * @param	int	$code_status	红包
     * @return	string
     *
     */
    public static function getCodeStatus($code_status){
            $res = '未知';
            switch($code_status){
                case '0':
                   $res = '正常';
                    break;
                case '1':
                    $res = '已用';
                    break;
                case '2':
                    $res = '过期';
                    break;
                default :
                    break;
            }
            return $res;
    }
    
    
    /**
     * 检查friend_code红包
     *
     * @param	string	$arr	红包
     * @return	string
     *
     */
    function checkCodeValid($arr){
           if($arr['code_status'] == '0' && $arr['expire_time'] < time()){
               $this->updateRow(array('code_status'=>'2'), array('code_id'=>$arr['code_id']));
               $arr['code_status'] = '2';
           }
           return $arr;
    }
}
