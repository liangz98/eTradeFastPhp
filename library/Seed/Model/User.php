<?php
/**
 * 用户表模型 (snsys_users)
 *
 * @category   Seed
 * @package    Seed_Model
 * @copyright  Copyright (c) 2014 萌芽网络 (http://www.gzseed.com)
 */
class Seed_Model_User extends Seed_Model_Db
{
    /**
     * 无前缀的表名
     *
     * @var string
     */
    public $_table_name = 'users';

    /**
     * 推荐树用户集合
     *
     * @var array
     */
    public $_line_users=array();

    /**
     * 推荐人链条集合
     *
     * @var array
     */
    public $_parent_nav=array();

    /**
     * 查询上级的级别深度
     *
     * @var array
     */
    public $_level=0;

    /**
     * 推荐人链条集合
     *
     * @var array
     */
    public $_recommender=array();

    /**
     * 检查后台用户是否有效
     *
     * @param	string	$adm_name	用户名
     * @param	string	$adm_pwd	用户密码
     * @return	string
     *
     */
    public function isAdminValid($adm_name,$adm_pwd)
    {
        $select = $this->_db->select();
        $select->from($this->_prefix.'users');
        $select->where("user_name = ?", $adm_name);
        $select->where("is_admin = ?", '1');
        $select->where("is_actived = ?", '1');
        
        $sql = $select->__toString();
        $row = $this->_db->fetchRow($sql);
        
        if($row['user_id']>0 && $row['login_error_cnt']>=5 && (time()-$row['login_error_time'])<3600){
        	return array('msg'=>'登录错误超过5次，请'.date('H:i',($row['login_error_time']+3600)).'后再试！','detail'=>false);
        }
        
        if($row['user_id']>0 && $row['user_password']==md5($adm_name.md5($adm_pwd))){
            //更新登录信息
            $updateData = array();
            $updateData['login_cnt']=$row['login_cnt']+1;
            $updateData['login_time']=time();
            $updateData['action_time']=time();
            $updateData['login_ip']=Seed_Browser::get_client_ip();
            $token = md5($adm_name.Seed_Common::genRandomString(16));
            $updateData['token']=$token;
            $updateData['login_error_cnt']=0;
            $updateData['login_error_time']=0;
            $updateData['login_error_ip']='';
            $this->updateRow($updateData,array('user_id'=>$row['user_id']));
            $row['token']=$token;
            return array('msg'=>'','token'=>Seed_Token::encode($row['user_id'],$row['token']));
        }
        
        if($row['user_id']>0 && $row['user_password']!=md5($adm_name.md5($adm_pwd))){
        	$updateData = array();
            $updateData['login_error_cnt']=$row['login_error_cnt']+1;
            $updateData['login_error_time']=time();
            $updateData['login_error_ip']=Seed_Browser::get_client_ip();
            $this->updateRow($updateData,array('user_id'=>$row['user_id']));
            
        	return array('msg'=>'登录用户名/密码错误！你还有'.(5-$updateData['login_error_cnt']).'次机会！','token'=>'');
        }
        
        return array('msg'=>'登录用户名不存在或者不可用！','token'=>'');
    }

    /**
     * 检查用户是否有效
     *
     * @param	string	$adm_name	用户名
     * @param	string	$adm_pwd	用户密码
     * @return	string
     *
     */
    public function isPartnerValid($adm_name,$adm_pwd)
    {
        $select = $this->_db->select();
        $select->from($this->_prefix.'users');
        $select->where("user_name = ?", $adm_name);
        $select->where("user_password = ?", md5($adm_name.md5($adm_pwd)));
        $select->where("is_actived = ?", '1');

        $sql = $select->__toString();
        $row = $this->_db->fetchRow($sql);
        if($row['user_id']>0){
            //更新登录信息
            $updateData = array();
            $updateData['login_cnt']=$row['login_cnt']+1;
            $updateData['login_time']=time();
            $updateData['action_time']=time();
            $updateData['login_ip']=Seed_Browser::get_client_ip();
            $token = md5($adm_name.Seed_Common::genRandomString(16));
            $updateData['token']=$token;
            $this->updateRow($updateData,array('user_id'=>$row['user_id']));
            $row['token']=$token;
            return Seed_Token::encode($row['user_id'],$row['token']);
        }else {
            return false;
        }
    }

    /**
     * 检查后台用户角色
     *
     * @param	string	$token	令牌
     * @return	array
     *
     */
    public function checkRole($token)
    {
        if(empty($token))return false;
        $my = Seed_Token::decode($token);
        if(!is_array($my))return false;

        $select = $this->_db->select();
        $select->from($this->_prefix.'users');
        $select->where("user_id = ?", $my['user_id']);
        $select->where("is_admin = ?", '1');
        $select->where("is_actived = ?", '1');
        $select->where("token = ?", $my['token']);
        $sql = $select->__toString();
        $row = $this->_db->fetchRow($sql);
        if($row['user_id']>0){
            $select1 = $this->_db->select();
            $select1->from($this->_prefix.'user_roles',array('mod_name','role_name'));
            $select1->where("user_id = ?", $row['user_id']);
            $sql = $select1->__toString();
            $rows = $this->_db->fetchAll($sql);
            $row['roles'] = $rows;
            $row['token']=$my['token'];
            return $row;
        }else{
            return false;
        }
    }

    /**
     * 检查用户角色
     *
     * @param	string	$token	令牌
     * @return	array
     *
     */
    public function checkValid($token)
    {
        if(empty($token))return false;
        $my = Seed_Token::decode($token);
        if(!is_array($my))return false;

        $select = $this->_db->select();
        $select->from($this->_prefix.'users');
        $select->where("user_id = ?", $my['user_id']);
        $select->where("is_actived = ?", '1');
        $select->where("token = ?", $my['token']);
        $sql = $select->__toString();
        $row = $this->_db->fetchRow($sql);
        if($row['user_id']>0){
            $select1 = $this->_db->select();
            $select1->from($this->_prefix.'user_roles',array('mod_name','role_name'));
            $select1->where("user_id = ?", $row['user_id']);
            $sql = $select1->__toString();
            $rows = $this->_db->fetchAll($sql);
            $row['roles'] = $rows;
            $row['token']=Seed_Token::encode($row['user_id'],$row['token']);
            return $row;
        }else{
            return false;
        }
    }

    /**
     * 检查用户是否有效
     *
     * @param	string	$user_name	用户名
     * @param	string	$user_password	用户密码
     * @return	array	用户信息数组
     *
     */
    public function isValid($user_name,$user_password)
    {
        $select = $this->_db->select();
        $select->from($this->_prefix.'users');
        $select->where("user_name = ?" , $user_name);
        $select->where("is_actived = ?", '1');
        
        $sql = $select->__toString();
        $row = $this->_db->fetchRow($sql);
        
        if($row['user_id']>0 && $row['login_error_cnt']>=5 && (time()-$row['login_error_time'])<3600){
        	return array('msg'=>'登录错误超过5次，请'.date('H:i',($row['login_error_time']+3600)).'后再试！','detail'=>false);
        }
        
        if($row['user_id']>0 && $row['user_password']==md5($user_name.md5($user_password))){
            //更新登录信息
            $updateData = array();
            $updateData['login_cnt']=$row['login_cnt']+1;
            $updateData['last_login_time']=$row['login_time']?$row['login_time']:"";
            $updateData['last_login_ip']=$row['login_ip']?$row['login_ip']:"";
            $updateData['login_time']=time();
            $updateData['action_time']=time();            
            $updateData['login_ip']=Seed_Browser::get_client_ip();
            $updateData['token']=md5($user_name.Seed_Common::genRandomString(16));
            $updateData['login_error_cnt']=0;
            $updateData['login_error_time']=0;
            $updateData['login_error_ip']='';
            $this->updateRow($updateData,array('user_id'=>$row['user_id']));

            $row['token']=Seed_Token::encode($row['user_id'],$updateData['token']);
            
            return array('msg'=>'','detail'=>$row);
        }
        
        if($row['user_id']>0 && $row['user_password']!=md5($user_name.md5($user_password))){
        	$updateData = array();
            $updateData['login_error_cnt']=$row['login_error_cnt']+1;
            $updateData['login_error_time']=time();
            $updateData['login_error_ip']=Seed_Browser::get_client_ip();
            $this->updateRow($updateData,array('user_id'=>$row['user_id']));
            
        	return array('msg'=>'登录用户名/密码错误！你还有'.(5-$updateData['login_error_cnt']).'次机会！','detail'=>false);
        }
        
        return array('msg'=>'登录用户名不存在或者不可用！','detail'=>false);
    }

    /**
     * 根据用户ID获取用户登录信息
     *
     * @param	int	$user_id	用户ID
     * @return	array	用户信息数组
     *
     */
    public function openLogin($user_id)
    {
        $select = $this->_db->select();
        $select->from($this->_prefix.'users');
        $select->where("user_id = ?" , $user_id);
        $select->where("is_actived = ?", '1');

        $sql = $select->__toString();
        $row = $this->_db->fetchRow($sql);
        if($row['user_id']>0){
            //更新登录信息
            $updateData = array();
            $updateData['login_cnt']=$row['login_cnt']+1;
            $updateData['last_login_time']=$row['login_time'];
            $updateData['last_login_ip']=$row['login_ip'];
            $updateData['login_time']=time();
            $updateData['action_time']=time();
            $updateData['login_ip']=Seed_Browser::get_client_ip();
            $updateData['token']=md5($row['user_name'].Seed_Common::genRandomString(16));
            $this->updateRow($updateData,array('user_id'=>$row['user_id']));

            $row['token']=Seed_Token::encode($row['user_id'],$updateData['token']);
            return $row;
        }else {
            return false;
        }
    }

    /**
     * 查询用户推荐人链条
     *
     * @param	type	$recommender	底端推荐人ID
     * @param	type	$top_user_id	顶点推荐人ID
     * @return	array
     *
     */
    public function getParentNav($recommender,$top_user_id=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("user_id = ?",$recommender);
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);
            $this->_parent_nav[]=$row;
            if($row['user_recommender']>0 && $row['user_id']!=$top_user_id)$this->getParentNav($row['user_recommender'],$top_user_id);
            if(is_array($this->_parent_nav))
                return array_reverse($this->_parent_nav);
            else
                return array();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询用户推荐人链条
     *
     * @param	type	$recommender	底端推荐人ID
     * @param   type    $level          级别限定
     * @return	array
     *
     */
    public function getRecommender($recommender,$level=0)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("user_id = ?",$recommender);
            $sql = $select->__toString();
            $row = $this->_db->fetchRow($sql);

            if($level<$this->_level){
                if($row['user_id']>0){
                    $this->_recommender[]=$row;
                    $level++;
                    $this->getRecommender($row['user_recommender'],$level);
                    $level--;
                }
            }

            if(is_array($this->_recommender))
                return ($this->_recommender);
            else
                return array();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询某个用户及其下级的推荐树
     *
     * @param	type	$user_id	推荐人ID
     * @return	array
     *
     */
    public function getLineUsers($user_id)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("user_recommender = ?",$user_id);
            $select->where("mobile_valid = ?",'1');
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            foreach ($rows as $k=>$row)
            {
                $this->_line_users[] = $row;
                $this->getLineUsers($row['user_id']);
            }
            return $this->_line_users;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 查询某个用户及其下级的推荐树的用户ID集合
     *
     * @param	type	$user_id	推荐人ID
     * @return	array
     *
     */
    public function getLineIds($user_id)
    {
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name);
            $select->where("user_recommender = ?",$user_id);
            $select->where("is_actived = ?",'1');
            $sql = $select->__toString();
            $rows = $this->_db->fetchAll($sql);
            $mylineids = array();
            foreach ($rows as $k=>$row)
            {
                if($row['user_type']>2 && $row['user_type']<6)
                    $mylineids[] = $row['user_id'];
            }
            return $mylineids;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 根据昵称查询用户信息
     *
     * @param	string	$nick_name	用户昵称
     * @param	array|string	$fetch_fields	查询记录字段
     * @param	boolean	$debug	是否输出SQL语句
     * @return	array
     *
     */
    public function fetchByName($nick_name, $fetch_fields="*", $debug=false){
        try {
            $select = $this->_db->select();
            $select->from($this->_prefix.$this->_table_name,$fetch_fields);
            $select->where("nick_name = ?", $nick_name);
            $sql = $select->__toString();
            if($debug)echo $sql;
            $row = $this->_db->fetchRow($sql);
            return $row;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 微信用户注册
     *
     * @param	type	$fromUsername	微信名称
     * @param	type	$inviuid	推荐人ID
     * @return	array
     *
     */
    public function registerWechatUser($fromUsername,$inviuid = '0'){
        #Seed_Log::record('$fromUsername:' .$fromUsername, 'wechat_login.txt');
        #Seed_Log::record('$inviuid:' .$inviuid, 'wechat_login.txt');
        $fromUsername = strip_tags(trim($fromUsername));
        if(empty($fromUsername))return null;

        $user_recommender = 0;
        if($inviuid>0){
            $check = $this->fetchRow(array('user_id'=>$inviuid));
            if($check['user_id']>0)$user_recommender=$check['user_id'];
        }

        $user = $this->fetchRow(array('wc_username = BINARY ?'=>$fromUsername));
        if($user){
            if($user_recommender>0 && $user_recommender!=$user['user_id'] && $user['user_id']!=$check['user_recommender'] && $user['is_shop']=='0'){
                $this->updateRow(array('user_recommender'=>$user_recommender),array('user_id'=>$user['user_id']));
            }
            return $user;
        }else{
            $insertData = array();
            $insertData['is_admin'] = '0';
            $insertData['is_actived'] = '1';
            $insertData['register_time'] = time();
            $insertData['user_name'] = 'wx_'.date('YmdHis').Seed_Common::genRandomStringBy(3,'n');
            $insertData['nick_name'] = '微信用户';
            $insertData['user_recommender'] = $user_recommender;
            $insertData['wc_username'] = $fromUsername;
            #Seed_Log::record('$insertData:' . Zend_Json::encode ( $insertData ), 'wechat_login.txt');
            $id = $this->insertRow($insertData);
            $user = $this->fetchRow(array('user_id'=>$id));
            return $user;
        }
        return null;
    }
    
    
    /*
     * 注册用户及短信
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     
    
    public function fetchUserSms($limit = array(0,0) , $condiction=null , $order_by=null , $fetch_fields="*" , $debug=false){
    	try {
    			
    		$select = $this->_db->select();
    		$select->from($this->_prefix.$this->_table_name.' as t1',array('user_id','user_name','register_time'));
    		$select->joinleft($this->_prefix."mobile_outboxes AS t2", 't1.user_name = t2.send_mobile',array('send_id','send_content'));
    		if (is_array($limit) && $limit[1] > 0)
    			$select->limit($limit[1], $limit[0]);
    		if(isset($condiction) && is_array($condiction)){
    			foreach ($condiction as $k=>$v){
    				if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
    					$select->where($k." = ?", $v);
    				}else{
    					$select->where($k, $v);
    				}
    			}
    		}
    			
    		if($order_by!=null)
    			$select->order($order_by);
    		$sql = $select->__toString();
    		if($debug)echo $sql;
    		$rows = $this->_db->fetchAll($sql);
    		return $rows;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
    
    

     * 注册用户及短信汇总
     * @param	array	$condiction	AND条件查询数组
     * @param	boolean	$debug	是否输出SQL语句
     
    
    public function fetchUserSmsCount($condiction=null , $debug=false){
    	try {
    		 
    		$select = $this->_db->select();
    		$select->from($this->_prefix.$this->_table_name.' as t1','COUNT(*)');
    		$select->joinleft($this->_prefix."mobile_outboxes AS t2", 't1.user_name = t2.send_mobile',null);
    		if (is_array($limit) && $limit[1] > 0)
    			$select->limit($limit[1], $limit[0]);
    		if(isset($condiction) && is_array($condiction)){
    			foreach ($condiction as $k=>$v){
    				if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
    					$select->where($k." = ?", $v);
    				}else{
    					$select->where($k, $v);
    				}
    			}
    		}
    		 
    		if($order_by!=null)
    			$select->order($order_by);
    		$sql = $select->__toString();
			if($debug)echo $sql;
			$count = $this->_db->fetchOne($sql);
			return $count;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
    
    */
    
    public function fetchUserIdcard($condiction=null , $order_by=null , $fetch_fields="*" , $debug=false){
    	try {
    			
    		$select = $this->_db->select();
    		$select->from($this->_prefix.$this->_table_name.' as t1',array('user_id','user_name','real_name','idcard_no','service_site'));
    		$select->joinleft("snshop_stores AS t2", 't1.service_site = t2.store_token',array('store_name'));
    		if(isset($condiction) && is_array($condiction)){
    			foreach ($condiction as $k=>$v){
    				if(preg_match("/^[a-zA-Z_0-9]+$/i",$k)){
    					$select->where($k." = ?", $v);
    				}else{
    					$select->where($k, $v);
    				}
    			}
    		}
    			
    		if($order_by!=null)
    			$select->order($order_by);
    		$sql = $select->__toString();
    		if($debug)echo $sql;
    		$rows = $this->_db->fetchRow($sql);
    		return $rows;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
    
}