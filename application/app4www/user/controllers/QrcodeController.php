<?php
include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";

class QrcodeController extends Kyapi_Controller_Action {

    function preDispatch() {
        $this->view->cur_pos = $this->_request->getParam('controller');
    }

    public function indexAction() {
        $url = $this->_request->getParam('url');
        if ($url == "")
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]; else $url = urldecode($url);
        $level = "M";
        $size = 8;
        // include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";
        QRcode::png($url, false, $level, $size, 2);
        exit;
    }

    public function appAction() {
        // include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";
        $appURL = "http://" . $_SERVER['HTTP_HOST'] . '/qrcode/android';
        $level = "M";
        $size = 8;
        QRcode::png($appURL, false, $level, $size, 2);
        exit;
    }

    public function androidAction() {
        $content = $this->view->render(SEED_WWW_TPL . "/qrcode/index.phtml");
        echo $content;
        exit;
    }

    // 依据公司快移码生成二维码 accountNumber
    public function numberAction() {
        $numberStr = $this->_request->getParam('number');
        $level = "M";
        $size = 8;
        QRcode::png($numberStr, false, $level, $size, 2);
        exit;
    }
}
