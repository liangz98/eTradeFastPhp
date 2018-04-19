<?php

class IndexController extends Kyapi_Controller_Action {
    
    public function indexAction() {
        
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/index/index.phtml");
            echo $content;
            exit;
        }
    }
}
