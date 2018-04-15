<?php
class ForgetController extends Kyapi_Controller_Action
{
//	public function preDispatch()
//	{
//		$this->view->cur_pos = 'info';
//		if($this->view->userID<1){
//			Mobile_Browser::redirect('请先登录系统！',$this->view->seed_Setting['user_app_server']."/login");
//		}
//	}

	function indexAction()
	{
		if ($this->_request->isPost()) {
//			$authCode=$_SERVER['authCode'];
//			$authCode =base64_decode($authCode);

			try {

				$_requestOb=$this->_requestObject;
				$loginName=trim($this->_request->getPost('ecommloginname'));
				$contactname=trim($this->_request->getPost('contactname'));

				// 请求Hessian服务端方法
				$userKY= $this->json->forgotPasswordApi($_requestOb,$loginName,$contactname);
				$resultObject =json_decode($userKY);
				if ($resultObject->status != 1) {
				    //验证错误，请5分钟后再试
					Shop_Browser::redirect($this->view->translate('tip_email_error'),$this->view->seed_BaseUrl . "/forget");
				} else {
				    //请到邮箱激活
					Shop_Browser::redirect($this->view->translate('tip_email_active'),$this->view->seed_BaseUrl . "/forget");
				}
			} catch (Exception $e) {
				Shop_Browser::redirect($e->getMessage());
			}
		}

		if(defined('SEED_WWW_TPL')){
			$content = $this->view->render(SEED_WWW_TPL."/forget/index.phtml");
			echo $content;
			exit;
		}
	}
	function checkAction(){

		$_requestOb=$this->_requestObject;
		$_userLoginName=$this->_request->getPost('ecommloginname');
		$resultObject= $this->json->checkLoginNameApi( $_requestOb, $_userLoginName);
//		echo $resultObject;
//		exit;
		$checkKY=json_decode($resultObject);
		if ($checkKY->result != 1) {
		    //用户名不存在，请重新输入
			echo $this->view->translate('tip_eloginname_no');
			exit;
		}else{
		    echo $this->view->translate('tip_active_05');
            exit;
        }

	}
	//验证邮件CODE
	function codeAction(){

            if (!empty($this->_request->getParam('authCode'))) {
                $this->view->authCode=$this->_request->getParam('authCode');
            } else {
                //激活码不正确
                Shop_Browser::redirect($this->view->translate('tip_email_check'),$this->view->seed_BaseUrl . "/login");
                exit;
            }

        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/forget/code.phtml");
            echo $content;
            exit;
        }

	}
	//忘记密码 创建新密码

	function pwdAction(){


		if ($this->_request->isPost()) {
			try {
				if (!empty($this->_request->getParam('authCode'))) {
					$_authCode=$this->_request->getParam('authCode');
				} else {
				    //激活码不正确
					Shop_Browser::redirect($this->view->translate('tip_email_check'),$this->view->seed_BaseUrl . "/login");
					exit;
				}

                $_requestOb=$this->_requestObject;
				$_newPwd=$this->_request->getParam('ecommpasswsd');

				// 请求Hessian服务端方法
				$userKY= $this->json->changePasswordByAuthCodeApi($_requestOb,$_authCode,$_newPwd);
				$resultObject =json_decode($userKY);

				if ($resultObject->status != 1) {
				    //重置失败
                    Shop_Browser::redirect($this->view->translate('tip_reset_fail'),$this->view->seed_BaseUrl . "/login");
				} else {
				    //重置成功
                    Shop_Browser::redirect($this->view->translate('tip_reset_sucess'),$this->view->seed_BaseUrl . "/login");
				}
			} catch (Exception $e) {
                Shop_Browser::redirect($e->getMessage(),$this->view->seed_BaseUrl . "/login");
			}
		}

	}



}