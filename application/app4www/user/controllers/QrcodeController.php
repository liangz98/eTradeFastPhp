<?php

class QrcodeController extends Zend_Controller_Action {
    function preDispatch() {
        $this->view->cur_pos = $this->_request->getParam('controller');
    }

    public function indexAction() {
        $url = $this->_request->getParam('url');
        if ($url == "")
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]; else $url = urldecode($url);
        $level = "M";
        $size = 8;
        include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";
        QRcode::png($url, false, $level, $size, 2);
        exit;
    }

    public function appAction() {
        // dev
        $appURL = "http://" . $_SERVER['HTTP_HOST'] . '/app/etradefast-release.apk';

        // prod
        //$appURL = "http://" . $_SERVER['HTTP_HOST'] . '/app/etradefast_v_0.1.apk';

        $level = "M";
        $size = 8;
        include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";
        QRcode::png($appURL, false, $level, $size, 2);
        exit;
    }

    public function androidAction() {
        if (defined('SEED_WWW_TPL')) {
            $content = $this->view->render(SEED_WWW_TPL . "/app/index.phtml");
            echo $content;
            exit;
        }
    }
}
