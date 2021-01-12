<?php

class LoginController extends Kyapi_Controller_Action {

    public function preDispatch() {
        $this->view->cur_pos = 'login';


        if (!empty($this->view->userID)) {

            //已登录，不要重复登录
            Mobile_Browser::redirect($this->view->translate('tip_login_two'), $this->view->seed_Setting['user_app_server'] . "/index");
        }
    }

    /**
     * @throws Exception
     */
    function indexAction() {
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/welcome/index.phtml");
            echo $content;
            exit;
        }
    }

    function loginAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');
        $this->view->needAuthCode = $this->_request->getParam('needAuthCode');

        if ($this->_request->isPost()) {
            // 请求服务端方法
            $_requestOb = $this->_requestObject;
            $loginName = trim($this->_request->getParam('ecommloginname'));
            $password = $this->_request->getParam('ecommpasswsd');
            $password = trim($password);

            $authCode = $this->_request->getParam('authCode');
            $authCode = trim($authCode);

            // Login
            $resultObject = $this->json->loginApi($_requestOb, $loginName, $password, $authCode);
            $userKY = $this->objectToArray(json_decode($resultObject));

            // 登录失败，重定向到登录页
            if ($userKY['status'] != 1) {
                // 需要验证码的cookie
                setcookie("needAuthCode", "1", time() + 3600);
                $_COOKIE["needAuthCode"] = "1";

                $resultMsg = $this->view->translate('tip_login_fail_n');
                if ($userKY['errorCode'] == 'ACCOUNT_FREEZE') {
                    $resultMsg = $this->view->translate('tip_login_fail_TryTimes');
                }

                $this->view->resultMsg = $resultMsg;
                $this->view->needAuthCode = 1;
            } else {
                // 删除需要验证码的cookie
                setcookie("needAuthCode", "", time() - 1);
                $_COOKIE["needAuthCode"] = "";

                $existData = $userKY['result'];
                // 更新缓存和redis
                $this->refreshUserSessionAndRedis($existData);

                if ($existData['account']['accountType'] == 'CO07') {
                    $this->redirect("/finance/channel");
                } else if ($existData['account']['accountType'] == 'CO09') {
                    $this->redirect("/transport");
                } else {
                    $this->redirect("/");
                }
            }
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/login/index.phtml");
            echo $content;
            exit;
        }
    }

    // 获取登录Qr图片
    public function qrloginimgAction() {
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->getLoginQrImgApi($_requestOb);
        // 接收到的文件是utf8的二进制文件, 在页面呈现时要转成base64
        $base64_image = base64_encode($resultObject);
        echo $base64_image;
        exit;
    }

    // 轮询检查二维码登录状态
    public function qrloginAction() {
        // $msg = 0;
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->qrLoginApi($_requestOb);

        // 取回接口请求状态
        $apiStatus = json_decode($resultObject)->status;
        if ($apiStatus == 1) {
            $userKY = $this->objectToArray(json_decode($resultObject));

            $existData = $userKY['result'];
            // 更新缓存和redis
            $this->refreshUserSessionAndRedis($existData);
            // 状态返回
            $msg = $apiStatus;
        } else {
            // 接口请求错误的情况下, 将接口错误返回给页面
            $msg = $this->view->translate(trim(json_decode($resultObject)->errorCode));
        }
        echo $msg;
        exit;
    }
}
