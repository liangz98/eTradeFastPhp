<?php

class PasswdController extends Kyapi_Controller_Action
{
    function indexAction(){

        if ($this->_request->isPost()) {
            try {

                $_requestOb=$this->_requestObject;

                /*
                修改密码模块
                 */
                $_contact = array();
                $_contactID = $this->view->userID;
                $_ecommpasswsd = $this->_request->getPost('ecommpasswsd');
                $_newpwd = $this->_request->getPost('newpwd');
                // 设置accountID, 为同一间公司注册用户
                $resultOB= $this->json->changePasswordApi( $_requestOb, $_contactID,$_ecommpasswsd,$_newpwd);
                $resultObject=json_decode($resultOB);
                $existData = $resultObject->result;

                if ($resultObject->status != 1) {
                    //编辑失败
                    Shop_Browser::redirect($this->view->translate('tip_edit_fail'),'/user/passwd');
                } else {
                    //编辑密码成功
                    Shop_Browser::redirect($this->view->translate('tip_edit_success'),'/user/passwd');
                }

            } catch (HttpError $ex) {
                echo $ex->getMessage();
                Shop_Browser::redirect($ex->getMessage());
            }
        }
        if(defined('SEED_WWW_TPL')){
            $content = $this->view->render(SEED_WWW_TPL."/passwd/index.phtml");
            echo $content;
            exit;
        }
    }

}
