<?php
/**
 * Seed_Model_Vcode
 * 
 * @author
 * @link
 * @version 1.0.1
 * @copyright
 * 
 **/
class Seed_Model_Vcode
{
    /**
     * 输出验证码图片
     *
     * @param	string	$randcode	验证码
     *
     */
	public function show($randcode)
	{
		Header("Content-type: image/gif");
		/*
		* 初始化
		*/
		$how=strlen($randcode);//长度
		$border = 0; //是否要边框 1要:0不要
		$w = $how*26.8; //图片宽度
		$h = 32; //图片高度
		$fontsize = 28; //字体大小
		
		$im = ImageCreate($w, $h); //创建验证图片
		
		/*
		* 绘制基本框架
		*/
		$bgcolor = ImageColorAllocate($im, 255, 255, 255); //设置背景颜色
		ImageFill($im, 0, 0, $bgcolor); //填充背景色
		if($border)
		{
		    $black = ImageColorAllocate($im, 0, 0, 0); //设置边框颜色
		    ImageRectangle($im, 0, 0, $w-1, $h-1, $black);//绘制边框
		}
		
		for($i=0; $i<$how; $i++)
		{   
			$code=$randcode{$i};
		    $j = !$i ? 4 : $j+26.8; //绘字符位置
		    $color3 = ImageColorAllocate($im, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100)); //字符随即颜色
//		    ImageChar($im, $fontsize, $j, 8, $code, $color3); //绘字符
		    imagettftext($im,$fontsize,0,$j,30,$color3,SEED_LIB_ROOT.'/Fonts/antelope.ttf',$code);
		}
		
		/*
		* 添加干扰
		*/
		for($i=0; $i<5; $i++)//绘背景干扰线
		{   
		    $color1 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰线颜色
		    ImageArc($im, mt_rand(-5,$w), mt_rand(-5,$h), mt_rand(20,300), mt_rand(20,200), 55, 44, $color1); //干扰线
		}   
//		for($i=0; $i<$how*40; $i++)//绘背景干扰点
//		{   
//		    $color2 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰点颜色 
//		    ImageSetPixel($im, mt_rand(0,$w), mt_rand(0,$h), $color2); //干扰点
//		}
		
		/*绘图结束*/
		Imagegif($im);
		ImageDestroy($im);
		/*绘图结束*/
	}

    /**
     * 输出后台验证码图片
     *
     * @param	string	$randcode	验证码
     *
     */
	public function show4center($randcode)
	{
		Header("Content-type: image/gif");
		/*
		* 初始化
		*/
		$how=strlen($randcode);//长度
		$border = 1; //是否要边框 1要:0不要
		$w = $how*14; //图片宽度
		$h = 20; //图片高度
		$fontsize = 14; //字体大小
		
		$im = ImageCreate($w, $h); //创建验证图片
		
		/*
		* 绘制基本框架
		*/
		$bgcolor = ImageColorAllocate($im, 255, 255, 255); //设置背景颜色
		ImageFill($im, 0, 0, $bgcolor); //填充背景色
		if($border)
		{
		    $black = ImageColorAllocate($im, 0, 0, 0); //设置边框颜色
		    ImageRectangle($im, 0, 0, $w-1, $h-1, $black);//绘制边框
		}
		
		for($i=0; $i<$how; $i++)
		{   
			$code=$randcode{$i};
		    $j = !$i ? 4 : $j+13; //绘字符位置
		    $color3 = ImageColorAllocate($im, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100)); //字符随即颜色
//		    ImageChar($im, $fontsize, $j, 3, $code, $color3); //绘字符
		    imagettftext($im,$fontsize,0,$j,17,$color3,SEED_LIB_ROOT.'/Fonts/antelope.ttf',$code);
		}
		
		/*
		* 添加干扰
		*/
//		for($i=0; $i<5; $i++)//绘背景干扰线
//		{   
//		    $color1 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰线颜色
//		    ImageArc($im, mt_rand(-5,$w), mt_rand(-5,$h), mt_rand(20,300), mt_rand(20,200), 55, 44, $color1); //干扰线
//		}   
//		for($i=0; $i<$how*40; $i++)//绘背景干扰点
//		{   
//		    $color2 = ImageColorAllocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); //干扰点颜色 
//		    ImageSetPixel($im, mt_rand(0,$w), mt_rand(0,$h), $color2); //干扰点
//		}
		
		/*绘图结束*/
		Imagegif($im);
		ImageDestroy($im);
		/*绘图结束*/
	}
	
    /**
     * 生成随机字符串
     *
     * @param	string	$how	长度
     * @return	string
     *
     */
	public function random($how = 5)
	{
		$alpha = "abcdefghijkmnopqrstuvwxyz"; //验证码内容1:字母
		$number = "023456789"; //验证码内容2:数字
		$randcode = ""; //验证码字符串初始化
		srand((double)microtime()*1000000); //初始化随机数种子
		/*
		* 逐位产生随机字符
		*/
		for($i=0; $i<$how; $i++)
		{   
		    $alpha_or_number = mt_rand(0, 0); //字母还是数字
		    $str = $alpha_or_number ? $alpha : $number;
		    $which = mt_rand(0, strlen($str)-1); //取哪个字符
		    $code = substr($str, $which, 1); //取字符
		    $randcode .= $code; //逐位加入验证码字符串
		}
		return $randcode;
	}

    /**
     * 将字符串转换为图片输出
     *
     * @param	string	$randcode	验证码
     *
     */
    public function showImg($str,$len = 4,$imgWidth = 140,$imgHeight = 40,$font_size = 20,$x = 14,$y = 30,$fonts = 'simhei.ttf'){
        header("Content-type: image/PNG");
        $str = mb_substr($str, 0, $len, 'utf-8');
        $authimg = imagecreate($imgWidth,$imgHeight);
        $bgColor = ImageColorAllocate($authimg,255,255,255);
        $fontfile = SEED_LIB_ROOT.'/Fonts/'.$fonts;
        $white=imagecolorallocate($authimg,150,0,0);
//            imagearc($authimg,rand(1,$imgWidth), rand(1,$imgHeight), 20, 10, 0, 360, $white);
//            imagearc($authimg,rand(1,$imgWidth), rand(1,$imgHeight), 50, 20, 0, 360, $white);
//            imageline($authimg,20,20,180,30,$white);
//            imageline($authimg,20,18,170,50,$white);
//            imageline($authimg,25,50,80,50,$white);
        $noise_num = $imgWidth * 3;
        $line_num = 20;
        imagecolorallocate($authimg,0xff,0xff,0xff);
        $rectangle_color=imagecolorallocate($authimg,0xAA,0xAA,0xAA);
        $noise_color=imagecolorallocate($authimg,0x00,0x00,0x00);
        $font_color=imagecolorallocate($authimg,0x00,0x00,0x00);
        $line_color=imagecolorallocate($authimg,0x00,0x00,0x00);
        for($i=0;$i<$noise_num;$i++){
                imagesetpixel($authimg,mt_rand(0,$imgWidth),mt_rand(0,$imgHeight),$noise_color);
        }
        ImageTTFText($authimg, $font_size ,0, $x, $y, $font_color, $fontfile, $str);
        ImagePNG($authimg);
        ImageDestroy($authimg);
    }

}