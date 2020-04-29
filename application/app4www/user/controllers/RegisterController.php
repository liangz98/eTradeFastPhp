<?php

class RegisterController extends Kyapi_Controller_Action {
    public function preDispatch() {
        $this->view->cur_pos = 'login';
        if (!empty($this->view->userID)) {
            //已登录，不要重复登录
            Mobile_Browser::redirect($this->view->translate('tip_login_two'), $this->view->seed_Setting['user_app_server'] . "/index");
        }
    }

    function indexAction() {
        if ($this->_request->isPost()) {
            $_requestOb = $this->_requestObject;

            $verification = $this->_request->getParam('verification');
            if (!empty($_SESSION['regVerificationCode'])) {
                if ($verification == $_SESSION['regVerificationCode']) {
                    /* 注册会员模块 */
                    $_contact = new Kyapi_Model_contact();
                    $_contact->accountName = $this->_request->getParam('accountName');
                    $_contact->regdAddress = $this->_request->getParam('regdAddress');
                    $_contact->name = $this->_request->getParam('name');
                    $_contact->mobilePhone = $this->_request->getParam('mobilePhone');
                    $_contact->ecommloginname = $this->_request->getParam('ecommloginname');
                    $_contact->ecommpasswsd = $this->_request->getParam('ecommpasswsd');
                    $_contact->regdCountryCode = $this->_request->getParam('regdCountryCode');
                    $_contact->accountEmail = $this->_request->getParam('email');
                    $_contact->isPersonAccount = $this->_request->getParam('isPersonAccount');
                    $_contact->ecommrole = "CompUser,CompAdmin";

                    if ($_contact->regdCountryCode == "CN") {
                        $_contact->crnCode = 'CNY';
                        $_contact->identityType = '01'; // 默认证件类型: 身份证
                    } else {
                        $_contact->crnCode = 'USD';
                    }

                    // 设置accountID, 为同一间公司注册用户
                    $resultObject = $this->json->registerApi($_requestOb, $_contact);
                    $userKY = $this->objectToArray(json_decode($resultObject));

                    if ($userKY['status'] != 1) {
                        //增加失败
                        Shop_Browser::redirect($this->view->translate('tip_add_fail') . $userKY['error'], $this->view->seed_Setting['user_app_server'] . "/register");
                    } else {
                        //销毁绑定sessionID的key

                        $resultObject = $this->json->loginApi($_requestOb, $_contact->ecommloginname, $_contact->ecommpasswsd, '');
                        $userLOGIN = $this->objectToArray(json_decode($resultObject));
                        if ($userLOGIN['status'] != 1) {
                            //自动登陆失败
                            Shop_Browser::redirect($this->view->translate('tip_login_fail') . $userLOGIN['error'], $this->view->seed_Setting['user_app_server'] . "/login");
                        } else {
                            // 删除需要验证码的cookie
                            setcookie("needAuthCode", "", time() - 1);
                            $_COOKIE["needAuthCode"] = "";

                            $existData = $userLOGIN['result'];
                            // 更新缓存和redis
                            $this->refreshUserSessionAndRedis($existData);

                            Shop_Browser::redirect($this->view->translate('tip_register_success'), $this->view->seed_Setting['user_app_server'] . "/index");
                        }
                    }
                } else {
                    //增加失败
                    Shop_Browser::redirect($this->view->translate('tip_add_fail') . ' 验证码不正确. ' , $this->view->seed_Setting['user_app_server'] . "/register");
                }
            }
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/register/index.phtml");
            echo $content;
            exit;
        }
    }

    function checkAction() {
        $_requestOb = $this->_requestObject;
        $_userLoginName = $this->_request->getPost('ecommloginname');
        $resultObject = $this->json->checkLoginNameApi($_requestOb, $_userLoginName);
        echo $resultObject;
        exit;
    }

    function checkLoginNameAjaxAction() {
        $msg = array();
        $requestObject = $this->_requestObject;
        $loginName = $this->_request->getPost('ecommloginname');

        $resultObject = $this->json->checkLoginNameApi($requestObject, $loginName);
        $msg["status"] = json_decode($resultObject)->status;
        $msg["result"] = json_decode($resultObject)->result;

        echo json_encode($msg);
        exit;
    }

    public function servicehtmlAction() {
        $content = $this->view->render(SEED_WWW_TPL . "/register/service_.phtml");
        echo $content;
        exit;
    }
}
