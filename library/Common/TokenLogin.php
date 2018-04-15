<?php
// 获取参数的TOKEN
if(isset($this->view->seed_Setting['wechat_user_need_bind']) && $this->view->seed_Setting['wechat_user_need_bind']=='1'){
$token = $this->_request->getParam('token');
if(!empty($token)){
    $my = Seed_Token::decode($token);
    if(is_array($my) && isset($my['user_id']) && isset($my['token'])){
        $wc_username = $my['user_id'];
        $wc_phpsessid = $my['token'];
        $phpsessid = session_id();
        $userM = new Seed_Model_User('system');
        $userWechatUserM = new Wechat_Model_User('wechat');
        

			
        $check =$userWechatUserM->fetchRow(array('wc_username'=>$wc_username));
        if($check['wu_id']>0 && $check['wc_phpsessid']==$wc_phpsessid){
            $userWechatUserM->updateRow(array('wc_phpsessid'=>""),array('wu_id'=>$check['wu_id']));
            
	        if(isset($this->view->seed_Setting['wechat_user_need_bind']) && $this->view->seed_Setting['wechat_user_need_bind']=='1'){//需绑定账号
					
			}else{//自动注册会员
				$wc_user = $userWechatUserM->fetchRow(array('wc_username'=>$wc_username));
				if($wc_user['wu_id']>0){
					$my = $userM->registerWechatUser($wc_username,$wc_user['invi_uid']);
					$userWechatUserM->updateRow(array('user_id'=>$my['user_id']), array('wu_id'=>$wc_user['wu_id']));
				}
			}           
            
            //登录用户
            if ($user = $userM->openLogin($my['user_id'])) {
                    //设置cookie
                    $expiretime = time()+$this->view->seed_Setting['cookie_expire'];
                    Seed_Cookie::setCookie('seed_UserId',$user['user_id'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
                    Seed_Cookie::setCookie('seed_UserName',$user['user_name'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
                    Seed_Cookie::setCookie('seed_Token',$user['token'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
                    
                    if($user['is_shop'] == '1'){
                        Seed_Cookie::setCookie('seed_ShopUserId',$user['user_id'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
						Seed_Cookie::setCookie('seed_ShopUserName',$user['user_name'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
						Seed_Cookie::setCookie('seed_ShopToken',$user['token'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
                    }
                    if($user['is_agent'] == '1'){
                        Seed_Cookie::setCookie('seed_AgentUserId',$user['user_id'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
						Seed_Cookie::setCookie('seed_AgentUserName',$user['user_name'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
						Seed_Cookie::setCookie('seed_AgentToken',$user['token'],$expiretime,$this->view->seed_Setting['cookie_path'],$this->view->seed_Setting['cookie_host']);
                    }
            }
        }
    }
	Seed_Cookie::delete('seed_VmallLastUrl');//从菜单过来则清除
}
}