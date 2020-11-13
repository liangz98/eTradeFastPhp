<?php

class LoginController extends Kyapi_Controller_Action {

    public function preDispatch() {
        $this->view->cur_pos = 'welcome';
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
}
