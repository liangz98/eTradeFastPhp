<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/12/26
 * Time: 16:12
 */
class CheckController extends Kyapi_Controller_Action
{
    function checkloginnameAction(){
        //验证登陆名是否存在
        $_requestOb=$this->_requestObject;
        $_userLoginName=$this->_request->getPost('ecommloginname');
        $resultObject= $this->json->checkLoginNameApi( $_requestOb, $_userLoginName);
        echo json_encode($resultObject->result);
        exit;
    }

    function checkaccountnameAction(){
        //验证公司名是否存在
        $_requestOb=$this->_requestObject;
        $_userLoginName=$this->_request->getPost('ecommloginname');
        $resultObject= $this->json->checkAccountNameApi( $_requestOb, $_userLoginName);
        echo json_encode($resultObject->result);
        exit;
    }
    function accountemailnoticeAction(){
        /*用户邮箱 发送验证邮件*/
        $_requestOb=$this->_requestObject;
        $resultObject= $this->json->accountEmailNotice( $_requestOb);
        $checkKY= $this->objectToArray(json_decode($resultObject));
        if($checkKY['status']!= 1){
            //验证出错请检查邮箱是否存在
            echo json_encode($this->view->translate('tip_active_01'));
        }else{
            //已发送邮件请前往邮箱验证
            echo json_encode($this->view->translate('tip_active_02'));

        }
        exit;
    }
    //公司信息 验证邮箱 回调接收地址
    function accountemailAction(){
        /*用户邮箱 发送验证邮件*/
        $_authCode=$_SERVER['QUERY_STRING'];
        $resultObject= $this->json->verifyAccountEmail( $_authCode);
        $checkKY= $this->objectToArray(json_decode($resultObject));
        if($checkKY['status']!= 1){
            //验证出错请检查邮箱是否存在
            echo json_encode($this->view->translate('tip_active_01'));
        }else{
            //已发送邮件请前往邮箱验证
            echo json_encode($this->view->translate('tip_active_02'));

        }
        exit;
    }
    function contactinviteemailnoticeAction(){
        /*用户邮箱 发送验证邮件*/
        $_contactID=$this->_request->getParam('contactID');
        $_requestOb=$this->_requestObject;
        $resultObject= $this->json->contactInviteEmailNotice( $_requestOb,$_contactID);
        $checkKY= $this->objectToArray(json_decode($resultObject));
        if($checkKY['status']!= 1){
            echo json_encode($checkKY['SEND_EMAIL_TOO_FAST']);
        }else{
            //已成功发送邀请链接至该邮箱
            echo json_encode($this->view->translate('tip_active_03'));

        }
        exit;
    }

    function sendemailAction(){
        /*用户邮箱 发送验证邮件*/
        $_requestOb=$this->_requestObject;
        $resultObject= $this->json->sendVerifyContactEmailNotice( $_requestOb);
        $checkKY= $this->objectToArray(json_decode($resultObject));
        if($checkKY['status']!= 1){
            //验证出错请检查邮箱是否存在
            echo json_encode($this->view->translate('tip_active_01'));
        }else{
            //已发送邮件请前往邮箱验证
            echo json_encode($this->view->translate('tip_active_02'));

        }
        exit;
    }
    function verifyemailAction(){
        /*用户邮箱 接收验证邮件code*/
        $_authCode=$_SERVER['QUERY_STRING'];
        $resultObject= $this->json->verifyContactEmailByAuthCode($_authCode);
        $checkKY= $this->objectToArray(json_decode($resultObject));
        if($checkKY['status']!= 1){
            //验证码超时，请重新验证！
            Shop_Browser::redirect($this->view->translate('tip_active_04'), $this->view->seed_Setting['user_app_server']);
        }else{
            //验证成功
            Shop_Browser::redirect($this->view->translate('tip_active_05'), $this->view->seed_Setting['user_app_server'].'/account');
        }
        exit;
    }

    function sendloginAction(){
        /*用户登录名（邮箱） 发送验证邮件*/
        $_requestOb=$this->_requestObject;
        $resultObject= $this->json->sendVerifyECommLoginEmailNotice( $_requestOb);
        $checkKY= $this->objectToArray(json_decode($resultObject));
        if($checkKY['status']!= 1){
            //验证出错请检查邮箱是否存在
            echo json_encode($this->view->translate('tip_active_01'));
        }else{
            //已发送邮件请前往邮箱验证
            echo json_encode($this->view->translate('tip_active_02'));

        }
        exit;
    }
    function verifyloginAction(){
        /*用户登录名（邮箱）  发送验证邮件*/
        // $_requestOb=$this->_requestObject;
        $_authCode=$_SERVER['QUERY_STRING'];
        $resultObject= $this->json->verifyECommLoginEmailByAuthCode($_authCode);
        $checkKY= $this->objectToArray(json_decode($resultObject));
        if($checkKY['status']!= 1){
            //验证码超时，请重新验证
            echo json_encode($this->view->translate('tip_active_04'));
        }else{
            //验证成功
            Shop_Browser::redirect($this->view->translate('tip_active_05'), $this->view->seed_Setting['user_app_server'].'/account');
        }
        exit;
    }

    // 验证码接口请求
    function codeAction() {
        $_requestOb = $this->_requestObject;
        $resultObject = $this->json->getLoginAuthCodeApi($_requestOb);
        $checkKY = $this->objectToArray(json_decode($resultObject));

        if ($checkKY['status'] != 1) {
            echo 'verification failed';
        } else {
            $vc = new Kyapi_Model_Vcode();
            $pngcode = $vc->show($checkKY['result']);
            echo $pngcode;
            exit;
        }
    }

    function radomVerificationCodeAction() {
        $verificationCode = new Kyapi_Model_Vcode();
        $captchaStr = '';//保存验证码字符串，后面存入session用于session验证
        //验证码随机四个数字
        for ($i=0;$i<4;$i++){
            $fontContent = rand(0,9);//0-9随机数字
            $captchaStr .= $fontContent;//字符串拼接到$captch_code
        }
        $_SESSION['regVerificationCode'] = $captchaStr; //保存验证信息，等待校验
        $pngCode = $verificationCode->show($captchaStr, 1);

        echo $pngCode;
        exit;
    }
}
