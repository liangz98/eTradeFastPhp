<?php

class HelperController extends Kyapi_Controller_Action {

    public function helpAction() {
        $content = $this->view->render(SEED_WWW_TPL . "/help/help.phtml");
        echo $content;
        exit;
    }

}
