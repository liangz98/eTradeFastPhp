<?php
/**
 * Seed_Filter
 */
class Seed_Filter
{
    private static $_instance;
    /**
     * 获取实例
     * @return object
     */
    public static function getInstance()
    {
    	if(!self::$_instance instanceof self){
    		self::$_instance=new self();
    	}
    	return self::$_instance;
    }
	
	/**
	 * 返回正确的电子邮箱地址
	 * @param string $value
	 * @return string
	 */
	public function email($value)
   	{
   		if (strlen($value)>6 && preg_match("/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$/",$value)){
           return $value;
        }
   	}
   
   	/**
   	 * 返回正确的日期
   	 * @param string $value
   	 * @return string
   	 */
   	public function date($value)
   	{
   		if (preg_match("/^(19|20)[0-9]{2}-(0[0-9]|1[0-2])-(0[0-9]|1[0-9]|2[0-9]|3[0-1])$/",$value)){
           return $value;
        }
        if (preg_match("/^(19|20)[0-9]{2}-([1-9]|0[0-9]|1[0-2])-([1-9]|1[0-9]|2[0-9]|3[0-1])$/",$value)){
           return $value;
        }
   	}
   
   	/**
   	 * 返回正确文本，返回纯文本
   	 * @param string $value
   	 * @return string
   	 */
   	public function text($value)
   	{
   		// 如果过滤标记开启，将首先过滤反斜杠
		if(get_magic_quotes_gpc()){
			$value=stripslashes($value);
		}
		$text =  $this->trim($value);
        //完全过滤注释
        $text = preg_replace('/<!--?.*-->/','',$text);
        //完全过滤动态代码
        $text =  preg_replace('/<\?|\?'.'>/','',$text);
        //完全过滤js
        $text = preg_replace('/<script?.*\/script>/','',$text);

        $text =  str_replace('[','&#091;',$text);
        $text = str_replace(']','&#093;',$text);
        $text =  str_replace('|','&#124;',$text);
        //过滤换行符
        $text = preg_replace('/\r?\n/','',$text);
        //br
        $text =  preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
        $text = preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
        //过滤危险的属性，如：过滤on事件lang js
        while(preg_match('/(<[^><]+)(lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
            $text=str_replace($mat[0],$mat[1],$text);
        }
        while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
            $text=str_replace($mat[0],$mat[1].$mat[3],$text);
        }
        $allowTags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
        //允许的HTML标签
        $text =  preg_replace('/<('.$allowTags.')( [^><\[\]]*)>/i','[\1\2]',$text);
        //过滤多余html
        $banTag = 'html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml';
        $text =  preg_replace('/<\/?('.$banTag.')[^><]*>/i','',$text);
        //过滤合法的html标签
        while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
            $text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
        }
        //转换引号
        while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
            $text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
        }
        //空属性转换
        $text =  str_replace('\'\'','||',$text);
        $text = str_replace('""','||',$text);
        //过滤错误的单个引号
        while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
            $text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
        }
        //转换其它所有不合法的 < >
        $text =  str_replace('<','&lt;',$text);
        $text = str_replace('>','&gt;',$text);
        $text = str_replace('"','&quot;',$text);
        //反转换
        $text =  str_replace('[','<',$text);
        $text =  str_replace(']','>',$text);
        $text =  str_replace('|','"',$text);
        //过滤多余空格
        $text =  str_replace('  ',' ',$text);
        return $text;
   	}
   	
   	
  	/**
   	 * 返回安全的HTML代码
   	 * @param string $value
   	 * @return string
   	 */
   	public function safeHtml($value)
   	{
   		// 如果过滤标记开启，将首先过滤反斜杠
		if(get_magic_quotes_gpc()){
			$value=stripslashes($value);
		}
		$text =  $this->trim($value);
        //完全过滤注释
        $text = preg_replace('/<!--?.*-->/','',$text);
        //完全过滤动态代码
        $text =  preg_replace('/<\?|\?'.'>/','',$text);
        //完全过滤js
        $text = preg_replace('/<script?.*\/script>/','',$text);
         //完全过滤样式
        $text = preg_replace('/<style?.*\/style>/','',$text);

        //过滤危险的属性，如：过滤on事件lang js
        while(preg_match('/(<[^><]+)(lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
            $text=str_replace($mat[0],$mat[1],$text);
        }
        while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
            $text=str_replace($mat[0],$mat[1].$mat[3],$text);
        }
        return $text;
   	}
   	
  	/**
   	 * 对POST数据进行转义处理
   	 * @param string $value
   	 * @return string
   	 */
   	public function addslashespost($value)
   	{
   		// 如果过滤标记开启则直接返回
		if(get_magic_quotes_gpc())return $value;
		// 如果不是数组则直接进行转义
		if(!is_array($value))return addslashes($value);
		foreach ($value as $k=>$v){
			if(!is_array($v)){
				$array[$k]=addslashes($v);
			}else{
				$array[$k]=self::addslashespost($v);
			}
		}
		return $array;
   	}
   
   	/**
   	 * 返回正确的手机号码
   	 * @param string $value
   	 * @return string
   	 */
   	public function mobile($value)
   	{
   	    $mymobile=false;
        if(preg_match("/^10086$/",$value))$mymobile=true;
        if(preg_match("/^13\d{9}$/",$value))$mymobile=true;
        if(preg_match("/^15\d{9}$/",$value))$mymobile=true;
        if(preg_match("/^18\d{9}$/",$value))$mymobile=true;
		if ($mymobile){
           return $value;
        }
   	}
   
   	/**
   	 * 返回整型
   	 * @param string $value
   	 * @return int
   	 */
   	public function int($value)
   	{
   	    if(preg_match("/^[\d]+$/i",$value)){
        	return $value;
		}
   	}
   
   	/**
   	 * 返回浮点
   	 * @param string $value
   	 * @return float
   	 */
   	public function float($value)
   	{
   	    if(preg_match("/^[0-9.]+$/i",$value)){
        	return $value;
		}
   	}
   	
   	/**
   	 * 过滤图片地址
   	 * @param string $value
   	 * @return string
   	 */
   	public function imageUrl($value)
   	{
   		$postfix = strtolower(substr($value,strrpos($value,".")));
    	if(in_array($postfix,array(".jpg",".gif",".jpeg",".png"))){
    		return $value;
    	}
   	}
   	
   	/**
   	 * 过滤由字母或数字组成的字符串
   	 * @param string $value
   	 * @return alnum
   	 */
   	public function alnum($value)
   	{
   	 	if(preg_match("/^[a-zA-Z_0-9\-]+$/i",$value)){
        	return $value;
		}
   	}
   	
   	/**
   	 * 返回中文
   	 * @param string $value
   	 * @return string
   	 */
   	public function chinese($value)
   	{
   	    if(preg_match("/^[\x7f-\xff]+$/",$value)){
        	return $value;
		}
   	}
   	
  	/**
  	 * 返回正确的主机地址
  	 * @param string $value
  	 * @return string
  	 */
  	public function host($value)
   	{
   	    if(preg_match("/^[a-zA-Z0-9\-]+$/i",$value)){
        	return $value;
		}
   	}
   	
   	/**
   	 * 删除字符串首末空白
   	 * @param string $value
   	 * @return string
   	 */
   	public function trim($value)
   	{
   		return trim($value);
   	}
   	
   	/**
   	 * 删除反斜杠
   	 * @param string $value
   	 * @return string
   	 */
   	public function stripslashes($value)
   	{
   		// 如果过滤标记开启
   		if(get_magic_quotes_gpc()){
			$value=stripslashes($value);
		}
		return $value;
   	}
   	
   	/**
   	 * 返回正确的身份证号码
   	 * @param string $value
   	 */
   	public function idCard($value)
   	{
   		if(strlen($value) == 18) 
		{ 
			// 检测18位身份证号码的有效性
			$idcard_base = substr($value, 0, 17);
			//加权因子 
			$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); 
			//校验码对应值 
			$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
			$checksum = 0; 
			for ($i = 0; $i < strlen($idcard_base); $i++) 
			{ 
				$checksum += substr($idcard_base, $i, 1) * $factor[$i]; 
			} 
			$mod = $checksum % 11; 
			$verify_number = $verify_number_list[$mod]; 
			if ($verify_number != strtoupper(substr($value, 17, 1))){ 
				return false; 
			}else{ 
				return $value; 
			} 
		} 
		elseif((strlen($value) == 15)) 
		{ 
			$idcard=$value;
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码 
			if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){ 
				$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9); 
			}else{ 
				$idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9); 
			}
			
			// 验证号码开始
			// 检测18位身份证号码的有效性
			$idcard_base = substr($idcard, 0, 17);
			//加权因子 
			$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); 
			//校验码对应值 
			$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
			$checksum = 0; 
			for ($i = 0; $i < strlen($idcard_base); $i++) 
			{ 
				$checksum += substr($idcard_base, $i, 1) * $factor[$i]; 
			} 
			$mod = $checksum % 11; 
			$verify_number = $verify_number_list[$mod]; 
			// 验证号码结束
			
			$idcard = $idcard . $verify_number; 
			
			
			
			
			// 检测18位身份证号码的有效性
			$idcard_base = substr($value, 0, 17);
			//加权因子 
			$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); 
			//校验码对应值 
			$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
			$checksum = 0; 
			for ($i = 0; $i < strlen($idcard_base); $i++) 
			{ 
				$checksum += substr($idcard_base, $i, 1) * $factor[$i]; 
			} 
			$mod = $checksum % 11; 
			$verify_number = $verify_number_list[$mod]; 
			
			
			if($verify_number != strtoupper(substr($value, 17, 1))){
				return $value;
			}else{
				return false; 
			}
		} 
		else 
		{ 
			return false; 
		} 
   	}
   	
   	/**
   	 * 删除HTML等标记
   	 * @param string $value
   	 * @return string
   	 */
   	public function striptags($value)
   	{
   		// 如果过滤标记开启
   		if(get_magic_quotes_gpc()){
			$value=stripslashes($value);
		}
		return strip_tags($value);
   	}
}