<?php

class PrivacyController extends Kyapi_Controller_Action {

    public function preDispatch() {

    }

    function indexAction() {
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/privacy/privacy.phtml");
            echo $content;
            exit;
        }
    }
}
