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

    function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');
        $this->view->resultSuccessMsg = $this->_request->getParam('resultSuccessMsg');

        if ($this->_request->isPost()) {
            $_requestOb = $this->_requestObject;
            $loginName = trim($this->_request->getPost('ecommloginname'));
            $contactName = trim($this->_request->getPost('contactName'));

            // 请求Hessian服务端方法
            $userKY = $this->json->forgotPasswordApi($_requestOb, $loginName, $contactName);
            $resultObject = json_decode($userKY);


            if ($resultObject->status != 1) {
                $this->view->resultMsg = $this->view->translate('tip_email_error');
            } else {
                $this->view->resultSuccessMsg = $this->view->translate('tip_email_active');
            }
        }

        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/forget/index.phtml");
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
        $this->view->resultMsg = $this->_request->getParam('resultMsg');

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

	// 忘记密码 创建新密码
	function pwdAction(){
		if ($this->_request->isPost()) {
            $requestObject = $this->_requestObject;
            $password = $this->_request->getParam('ecommpasswsd');
            $authCode = $this->_request->getParam('authCode');


            // 请求Hessian服务端方法
            $userKY = $this->json->changePasswordByAuthCodeApi($requestObject, $authCode, $password);
            $resultObject = json_decode($userKY);

            if ($resultObject->status != 1) {
                $this->view->resultMsg = $this->view->translate('tip_reset_fail') . $resultObject->error;
            } else {
                //注册成功
                Shop_Browser::redirect($this->view->translate('tip_reset_success'), $this->view->seed_BaseUrl . "/login");
            }
		}
	}
}
