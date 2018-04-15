<?php
class Seed_Qrcode{
    //googleapi生成，稳定性根据google服务器
    public static  function encode($url,$width = 200,$height = 200,$charset = 'UTF-8',$chld = 'L|4'){
        //参考地址：https://developers.google.com/chart/infographics/docs/qr_codes#details
        $chld = strip_tags($chld);
        $width = intval($width);
        $height = intval($height);
        $url = 'https://chart.googleapis.com/chart?cht=qr&chs='.$width.'x'.$height.'&choe='.$charset.'&chld='.$chld.'&chl='.$url;
        return $url;
    }
    
    /**
     * phpqrcode生成
     * @param type $url 链接地址
     * @param boolean $file_name 保存路径
     * @param string $errorCorrectionLevel 误差等级 L,M,Q,H
     * @param int $width //图片大小，固定宽
     * @param int $height //图片大小，固定高
     * @param int $matrixPointSize //图片大小，会自动计算($width或$height不为0时此参数则无效)
     * @param int $margin //距边大小
     */
    public static function encode2($url,$file_name = false,$width=0,$height=0,$matrixPointSize=4,$margin=2,$errorCorrectionLevel="L"){
        $file_path = false;
        if($file_name){
            $file_path = SEED_IMAGE_ROOT.DIRECTORY_SEPARATOR.ltrim(str_replace(SEED_IMAGE_ROOT, '',$file_name),DIRECTORY_SEPARATOR);
            if(is_file($file_path)){
                return $file_name;
            }
            if(substr($file_name,  strrpos($file_name,'.')) != '.png')return '';//必须是png后缀
        }
        include_once SEED_LIB_ROOT.DIRECTORY_SEPARATOR.'Plugin'.DIRECTORY_SEPARATOR.'QRcode'.DIRECTORY_SEPARATOR.'phpqrcode.php';
        $QRcode = new QRcode();
        $QRcode->setWidthHeight($width, $height);
        $QRcode->png2($url, $file_path, $errorCorrectionLevel, $matrixPointSize,$margin);  
        if($file_path){
             return file_exists($file_path) ? $file_name : '';
        }
    }
}