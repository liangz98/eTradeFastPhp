<?php

class PasswdController extends Kyapi_Controller_Action {

    function indexAction() {
        $this->view->resultMsg = $this->_request->getParam('resultMsg');
        $this->view->resultSuccessMsg = $this->_request->getParam('resultSuccessMsg');

        if ($this->_request->isPost()) {
            $requestObject = $this->_requestObject;
            $loginPwd = $this->_request->getPost('loginPwd');
            $password = $this->_request->getPost('password');
            $resultOB = $this->json->changePasswordApi($requestObject, $loginPwd, $password);
            $resultObject = json_decode($resultOB);

            if ($resultObject->status != 1) {
                $this->view->resultMsg = $this->view->translate('tip_edit_fail');
            } else {
                $this->view->resultSuccessMsg = $this->view->translate('tip_edit_success');
            }
        }
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/passwd/index.phtml");
            echo $content;
            exit;
        }
    }

}
