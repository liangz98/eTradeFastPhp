<?php
/**
 * Seed_Image
 * 
 * @author Biaoest (biaoest@gmail.com)
 * @link http://www.gzseed.com
 * @version 1.0.1
 * @copyright guangzhou seed studio
 * 
 **/
class Seed_Image{
	public function getExt($fileName){
		$extStr=explode('.',$fileName);
		$count=count($extStr)-1;
		return strtolower($extStr[$count]);
	}
	
	public function makeThumb1($path,$orgFileName,$thumb_width=0,$thumb_height=0){
		$orgFile=realpath($path."/".$orgFileName);
		
		$ext=$this->getExt($orgFileName);
		$name="thumb_".Seed_Common::genRandomString(12);
		$name.=".".$ext;
		$ndate=date("Ymd");
		$dest=$path.'/'.$ndate;
		if(!is_dir($dest)){
			mkdir($dest);
			chmod($dest,0777);
		}
		$thumbFileName = $ndate."/".$name;
		$thumbFile=$dest."/".$name;
		
		$gd = $this->gdVersion(); //获取 GD 版本。0 表示没有 GD 库，1 表示 GD 1.x，2 表示 GD 2.x
		if ($gd == 0)
		{
			 throw new Exception("gd not supported!");
			 return false;
		}
		/* 检查缩略图宽度和高度是否合法 */
        if ($thumb_width == 0 && $thumb_height == 0)
        {
            return false;
        }
        
        /* 检查原始文件是否存在及获得原始文件的信息 */
        $org_info = @getimagesize($orgFile);
        if (!$org_info)
        {
            throw new Exception("image not exists!");
            return false;
        }
        if (!$this->checkImgFunction($org_info[2]))
        {
            throw new Exception("image type not supported!");
            return false;
        }
        
        
        $img_org = $this->imgResource($orgFile, $org_info[2]);
        
        /* 原始图片以及缩略图的尺寸比例 */
        $scale_org      = $org_info[0] / $org_info[1];
        /* 处理只有缩略图宽和高有一个为0的情况，这时背景和缩略图一样大 */
        if ($thumb_width == 0)
        {
            $thumb_width = $thumb_height * $scale_org;
        }
        if ($thumb_height == 0)
        {
            $thumb_height = $thumb_width / $scale_org;
        }
        $scale_width = $org_info[0]/$thumb_width;
        $scale_height = $org_info[1]/$thumb_height;
        
        /* 如果原图没有缩略图大*/
        if($scale_width<1 && $scale_height<1){
       		return false;
        }
		if($scale_width<1){
			$thumb_width=$org_info[0];
		}
		if($scale_height<1){
			$thumb_height=$org_info[1];
		}
		
        $scale_width = $org_info[0]/$thumb_width;
        $scale_height = $org_info[1]/$thumb_height;
        
        $scale_thumb      = $thumb_width / $thumb_height;
        $scale = $scale_width/$scale_height;
        if($scale<1){
        	$org_x = 0;
        	$org_y = ($org_info[1] - $org_info[1]*$scale)/2;
        	$org_w = $org_info[0];
        	$org_h = $org_info[1]*$scale;
        }else{
        	$scale = $scale_height/$scale_width;
        	$org_y = 0;
        	$org_x = ($org_info[0] - $org_info[0]*$scale)/2;
        	$org_w = $org_info[0]*$scale;
        	$org_h = $org_info[1];
        }
        /* 创建缩略图的标志符 */
        if ($gd == 2)
        {
            $img_thumb  = imagecreatetruecolor($thumb_width, $thumb_height);
        }
        else
        {
            $img_thumb  = imagecreate($thumb_width, $thumb_height);
        }
        /* 背景颜色 */
        $clr = imagecolorallocate($img_thumb, 255, 255, 255);
        imagefilledrectangle($img_thumb, 0, 0, $thumb_width, $thumb_height, $clr);

        /* 将原始图片进行缩放处理 */
        if ($gd == 2)
        {
            imagecopyresampled($img_thumb, $img_org, 0, 0, $org_x, $org_y, $thumb_width, $thumb_height, $org_w, $org_h);
        }
        else
        {
            imagecopyresized($img_thumb, $img_org, 0, 0, $org_x, $org_y, $thumb_width, $thumb_height, $org_w, $org_h);
        }
        
        
        /* 生成文件 */
        if (function_exists('imagejpeg'))
        {
            imagejpeg($img_thumb, $thumbFile,100);
        }
        elseif (function_exists('imagegif'))
        {
            imagegif($img_thumb, $thumbFile);
        }
        elseif (function_exists('imagepng'))
        {
            imagepng($img_thumb, $thumbFile);
        }
        else
        {
            throw new Exception("thumb error!");
            return false;
        }

        imagedestroy($img_thumb);
        imagedestroy($img_org);

        //确认文件是否生成
        if (file_exists($thumbFile))
        {
        	chmod($thumbFile,0666);
            return $thumbFileName;
        }
        else
        {
            throw new Exception("thumb write error!");
            return false;
        }
        
	}
	
	public function makeThumb2($path,$orgFileName,$thumb_width=0,$thumb_height=0){
		$orgFile=realpath($path."/".$orgFileName);
		
		$gd = $this->gdVersion(); //获取 GD 版本。0 表示没有 GD 库，1 表示 GD 1.x，2 表示 GD 2.x
		if ($gd == 0)
		{
			 throw new Exception("gd not supported!");
			 return false;
		}
		/* 检查缩略图宽度和高度是否合法 */
        if ($thumb_width == 0 && $thumb_height == 0)
        {
            return false;
        }
        
        /* 检查原始文件是否存在及获得原始文件的信息 */
        $org_info = @getimagesize($orgFile);
        if (!$org_info)
        {
            throw new Exception("image not exists!");
            return false;
        }
        if (!$this->checkImgFunction($org_info[2]))
        {
            throw new Exception("image type not supported!");
            return false;
        }
        
        
        $img_org = $this->imgResource($orgFile, $org_info[2]);
        
        /* 原始图片以及缩略图的尺寸比例 */
        $scale_org      = $org_info[0] / $org_info[1];
        /* 处理只有缩略图宽和高有一个为0的情况，这时背景和缩略图一样大 */
        if ($thumb_width == 0)
        {
            $thumb_width = $thumb_height * $scale_org;
        }
        if ($thumb_height == 0)
        {
            $thumb_height = $thumb_width / $scale_org;
        }
        $scale_width = $org_info[0]/$thumb_width;
        $scale_height = $org_info[1]/$thumb_height;
        
        /* 如果原图没有缩略图大*/
        if($scale_width<1 && $scale_height<1){
        	return false;
       }
		if($scale_width<1){
			$thumb_width=$org_info[0];
		}
		if($scale_height<1){
			$thumb_height=$org_info[1];
		}
        
        $scale_width = $org_info[0]/$thumb_width;
        $scale_height = $org_info[1]/$thumb_height;
        
        $scale_thumb      = $thumb_width / $thumb_height;
        $scale = $scale_width/$scale_height;
        if($scale<1){
        	if($thumb_width>$thumb_height){
	        	$tmp=$thumb_width;
	        	$thumb_width=$thumb_height;
	        	$thumb_height=$tmp;
        	}
        }else{
        	if($thumb_width<$thumb_height){
        		$tmp=$thumb_width;
	        	$thumb_width=$thumb_height;
	        	$thumb_height=$tmp;
        	}
        }
        
        return $this->makeThumb1($path,$orgFileName,$thumb_width,$thumb_height);
	}
	
	public function gdVersion()
    {
        static $version = -1;

        if ($version >= 0)
        {
            return $version;
        }

        if (!extension_loaded('gd'))
        {
            $version = 0;
        }
        else
        {
            // 尝试使用gd_info函数
            if (PHP_VERSION >= '4.3')
            {
                if (function_exists('gd_info'))
                {
                    $ver_info = gd_info();
                    preg_match('/\d/', $ver_info['GD Version'], $match);
                    $version = $match[0];
                }
                else
                {
                    if (function_exists('imagecreatetruecolor'))
                    {
                        $version = 2;
                    }
                    elseif (function_exists('imagecreate'))
                    {
                        $version = 1;
                    }
                }
            }
            else
            {
                if (preg_match('/phpinfo/', ini_get('disable_functions')))
                {
                    /* 如果phpinfo被禁用，无法确定gd版本 */
                    $version = 1;
                }
                else
                {
                  // 使用phpinfo函数
                   ob_start();
                   phpinfo(8);
                   $info = ob_get_contents();
                   ob_end_clean();
                   $info = stristr($info, 'gd version');
                   preg_match('/\d/', $info, $match);
                   $version = $match[0];
                }
             }
        }

        return $version;
     }
     
    public function checkImgFunction($img_type)
    {
        switch ($img_type)
        {
            case 'image/gif':
            case 1:

                if (PHP_VERSION >= '4.3')
                {
                    return function_exists('imagecreatefromgif');
                }
                else
                {
                    return (imagetypes() & IMG_GIF) > 0;
                }
            break;

            case 'image/pjpeg':
            case 'image/jpeg':
            case 2:
                if (PHP_VERSION >= '4.3')
                {
                    return function_exists('imagecreatefromjpeg');
                }
                else
                {
                    return (imagetypes() & IMG_JPG) > 0;
                }
            break;

            case 'image/x-png':
            case 'image/png':
            case 3:
                if (PHP_VERSION >= '4.3')
                {
                     return function_exists('imagecreatefrompng');
                }
                else
                {
                    return (imagetypes() & IMG_PNG) > 0;
                }
            break;

            default:
                return false;
        }
    }
    
    public function imgResource($img_file, $mime_type)
    {
        switch ($mime_type)
        {
            case 1:
            case 'image/gif':
                $res = imagecreatefromgif($img_file);
                break;

            case 2:
            case 'image/pjpeg':
            case 'image/jpeg':
                $res = imagecreatefromjpeg($img_file);
                break;

            case 3:
            case 'image/x-png':
            case 'image/png':
                $res = imagecreatefrompng($img_file);
                break;

            default:
                return false;
        }

        return $res;
    }
    
    public function cut($path,$orgFileName,$params){
    	$orgFile=realpath($path."/".$orgFileName);
    	
    	$ext=$this->getExt($orgFileName);
		$name="cut_".Seed_Common::genRandomString(12);
		$name.=".".$ext;
		$ndate=date("Ymd");
		$dest=$path.'/'.$ndate;
		if(!is_dir($dest)){
			mkdir($dest);
			chmod($dest,0777);
		}
		$thumbFileName = $ndate."/".$name;
		$thumbFile=$dest."/".$name;
		
    	/* 检查原始文件是否存在及获得原始文件的信息 */
        $org_info = @getimagesize($orgFile);
        if (!$org_info)
        {
            throw new Exception("image not exists!");
            return false;
        }
        if (!$this->checkImgFunction($org_info[2]))
        {
            throw new Exception("image type not supported!");
            return false;
        }
        
       // print_r($org_info);
        $img_org = $this->imgResource($orgFile, $org_info[2]);
        //print_r($img_org);
    	//print_r($params);
    	
    	$thumb_width=$params['cwidth'];
    	$thumb_height=$params['cheight'];
    	
    	$scale=$org_info[0]/$params['owidth'];
    	
    	$org_x = $params['pleft']*$scale;
    	$org_y = $params['ptop']*$scale;
    	$org_w = $params['pwidth']*$scale;
    	$org_h = $params['pheight']*$scale;
    	
    /*	print_r("org_x:".$org_x."<br/>");
    	print_r("org_y:".$org_y."<br/>");
    	print_r("org_w:".$org_w."<br/>");
    	print_r("org_h:".$org_h."<br/>");*/
      
    	
    	$img_thumb  = imagecreatetruecolor($thumb_width, $thumb_height);
    	imagecopyresampled($img_thumb, $img_org, 0, 0, $org_x, $org_y, $thumb_width, $thumb_height, $org_w, $org_h);
    	/* 生成文件 */
        if (function_exists('imagejpeg'))
        {
            imagejpeg($img_thumb, $thumbFile,100);
        }
        elseif (function_exists('imagegif'))
        {
            imagegif($img_thumb, $thumbFile);
        }
        elseif (function_exists('imagepng'))
        {
            imagepng($img_thumb, $thumbFile);
        }
        else
        {
            throw new Exception("thumb error!");
            return false;
        }

        imagedestroy($img_thumb);
        imagedestroy($img_org);

        //确认文件是否生成
        if (file_exists($thumbFile))
        {
        	chmod($thumbFile,0666);
            return $thumbFileName;
        }
        else
        {
            throw new Exception("thumb write error!");
            return false;
        }
    }
    
    function getExtByHeader($header_str){
           $extArr = array('image/bmp'=>'bmp', 'image/x-windows-bmp'=>'bmp','image/gif'=>'gif','image/jpeg'=>'jpg', 'image/pjpeg'=>'jpg','image/png'=>'png',  'image/x-png'=>'png');
           foreach($extArr as $k=>$v){
               if(strpos($header_str, $k) > 0){
                   return $v;
               }
           }          
           return '';
    }
    
}
?>