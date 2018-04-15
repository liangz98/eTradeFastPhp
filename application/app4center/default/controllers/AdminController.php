<?php
class AdminController extends Seed_Controller_Action4Admin
{
	function loginAction()
	{   
		if ($this->_request->isPost()) {
			try{
				$session = Zend_Registry::get('session');
				$f = new Zend_Filter_StripTags();
				$username = $f->filter($this->_request->getPost('username'));
				$password = $f->filter($this->_request->getPost('password'));
				$vcode = $f->filter($this->_request->getPost('vcode'));
				if (empty($username)) {
					$this->view->message = '请输入用户名！';
				}elseif(empty($password)){
					$this->view->message = '请输入密码！';
				}elseif(empty($vcode)){
					$this->view->message = '请输入验证码！';
				}elseif(strlen($vcode)!=5 || $vcode!=$session->vcode){
					$this->view->message = '验证码不正确！';
				}else {
					//登录认证
					$adminM = new Seed_Model_User('system');
					$loginRs = $adminM->isAdminValid($username,$password);
					$token = $loginRs['token'];
					$msg = $loginRs['msg'];
					
					if ($token) {
						//写入SESSION
						$auth = Seed_Auth::getInstance();
						$auth->getStorage()->write($token);
                        $userInfo = Seed_Token::decode($token);
                        $wcUserM = new Wechat_Model_User('wechat');
                        $isService = $wcUserM->fetchOne(array('user_id' => $userInfo['user_id']), 'is_service');
                        $url = $isService=='1'?$this->view->seed_BaseUrl.'/wechat/livehelp':$this->view->seed_BaseUrl.'/';
						
                        //记录用户日志
						$user = $adminM->fetchRow(array('user_id'=>$userInfo['user_id']));					
						$userLogM = new Seed_Model_UserLog('system');	
						$insertData = array();
		                $insertData['user_name'] = $user['user_name'];
		                $insertData['real_name'] = $user['real_name'];
		                $insertData['login_ip'] = $user['login_ip'];
		                $insertData['login_time'] = $user['login_time'];
		                $insertData['user_type'] = 'a';
		                $userLogM->insertRow($insertData);

						Seed_Browser::redirect('登录成功！',$url);
					} else {
						$this->view->message = $msg;
					}
				}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
		}
	}
	
	function logoutAction()
	{
		//删除SESSION
		$adminM = new Seed_Model_User('system');
		$updateData = array();
		$updateData['token']='';
		$adminM->updateRow($updateData,array('user_id'=>$this->view->seed_User['user_id']));
		Seed_Auth::getInstance()->clearIdentity();
		Seed_Browser::redirect('退出成功！',$this->view->seed_BaseUrl.'/admin/login',1000,"top");
	}
	
	function passwdAction()
	{
		if ($this->_request->isPost()) {
			try{
				$user_id=$this->view->seed_User['user_id'];
				if($user_id<1)Seed_Browser::tip_show('用户错误！');
	    		$adminM = new Seed_Model_User('system');
				$userDetail = $adminM->fetchRow(array('user_id'=>$user_id));
				
				$updateData=array();
	    		$f1 = new Zend_Filter();
	    		$f1->addFilter(new Zend_Filter_StripTags());
	    		$old_passwd = $f1->filter($this->_request->getPost('old_password'));
	    		$passwd = $f1->filter($this->_request->getPost('password'));
	    		$confirm_passwd = $f1->filter($this->_request->getPost('confirm_password'));
	    		$updateData['user_password'] = md5($userDetail['user_name'].md5($passwd));
	    		
	    		if($userDetail['user_password']!=md5($userDetail['user_name'].md5($old_passwd))){
		    		Seed_Browser::tip_show('原密码错误！');
	    		}elseif(empty($passwd)){
	    			Seed_Browser::tip_show('请输入密码！');
	    		}elseif($passwd!=$confirm_passwd){
	    			Seed_Browser::tip_show('两次输入密码不一致！');
	    		}elseif($passwd==$old_passwd){
	    			Seed_Browser::tip_show('密码没有改动！');
	    		}else{
		    		if($adminM->updateRow($updateData,array('user_id'=>$user_id))){
						Seed_Browser::tip_show('密码修改成功！');
		    		}else{
		    			Seed_Browser::tip_show('密码修改失败！');
		    		}
	    		}
			} catch (Exception $e) {
				Seed_Browser::tip_show($e->getMessage());
			}
    	}
	}
}
?>