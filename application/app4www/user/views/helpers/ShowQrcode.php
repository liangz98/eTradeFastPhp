<?php

class Zend_View_Helper_ShowQrcode extends Shop_View_Helper {
    function showQrcode($name) {

        $url = $this->_request->getParam('url');
        if ($url == "")
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]; else $url = urldecode($url);
        $level = "M";
        $size = 8;
        include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";
        QRcode::png($url, false, $level, $size, 2);
        exit;
    }

    function ShowAppQrcode() {
        include SEED_LIB_ROOT . "/Plugin/Phpqrcode.php";
        $appURL = "http://" . $_SERVER['HTTP_HOST'] . '/qrcode/android';
        $level = "M";
        $size = 8;

        $outfile = "./app/" . date('Ymd', time()) . '.png';
        QRcode::png($appURL, $outfile, $level, $size, 2, true);

        $logo = './app/android.jpg'; 	// 准备好的logo图片
        $QR = $outfile;			// 已经生成的原始二维码图

        if (file_exists($logo)) {
            $QR = imagecreatefromstring(file_get_contents($QR));   		//目标图象连接资源。
            $logo = imagecreatefromstring(file_get_contents($logo));   	//源图象连接资源。
            $QR_width = imagesx($QR);			//二维码图片宽度
            $QR_height = imagesy($QR);			//二维码图片高度
            $logo_width = imagesx($logo);		//logo图片宽度
            $logo_height = imagesy($logo);		//logo图片高度
            $logo_qr_width = $QR_width / 4;   	//组合之后logo的宽度(占二维码的1/5)
            $scale = $logo_width/$logo_qr_width;   	//logo的宽度缩放比(本身宽度/组合后的宽度)
            $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
            $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点

            //重新组合图片并调整大小
            /*
             *	imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
             */
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
        }

        //输出图片
        imagepng($QR, './app/qrcode_pay.png');
        imagedestroy($QR);
        imagedestroy($logo);
        // return '/app/qrcode_pay.png';
        // return '<img src="/app/qrcode_pay.png" alt="使用微信扫描支付">';
        return '111111';
        // exit;
    }
}
