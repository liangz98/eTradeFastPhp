<?php

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

    public function appTestAction() {
        include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";

        // dev
        $appURL = "http://" . $_SERVER['HTTP_HOST'] . '/qrcode/android';

        $level = "M";
        $size = 8;

        $filename = '/app/qrcode/'.microtime().'.png';
        QRcode::png($appURL, $filename, $level, $size, 2);

        // $logo = 'app/android.jpg';
        $QR = $filename;
        //
        // $QR = imagecreatefromstring(file_get_contents($QR));   		//目标图象连接资源。
        // $logo = imagecreatefromstring(file_get_contents($logo));   	//源图象连接资源。
        // $QR_width = imagesx($QR);			//二维码图片宽度
        // $QR_height = imagesy($QR);			//二维码图片高度
        // $logo_width = imagesx($logo);		//logo图片宽度
        // $logo_height = imagesy($logo);		//logo图片高度
        // $logo_qr_width = $QR_width / 4;   	//组合之后logo的宽度(占二维码的1/5)
        // $scale = $logo_width/$logo_qr_width;   	//logo的宽度缩放比(本身宽度/组合后的宽度)
        // $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
        // $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点
        // imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);

        //输出图片
        imagepng($QR, '/app/qrcode.png');
        imagedestroy($QR);
        // imagedestroy($logo);
        return '<img src="/app/qrcode.png" alt="使用微信扫描支付">';
        // exit;
    }

    public function androidAction() {
            $content = $this->view->render(SEED_WWW_TPL . "/qrcode/index.phtml");
            echo $content;
            exit;
    }
}
