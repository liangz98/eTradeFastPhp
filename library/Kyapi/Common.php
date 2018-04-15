<?php
class Kyapi_Common {
	public static function createTree(array $src,$parent="737",$rank=0){
		static $tree=array();
		foreach ($src as $value) {
			if($parent == $value['parent']){
				$value['rank']=$rank;
				$tree[]=$value;
				array_merge($tree,createTree($src,$value['menu_id'],$rank+1));
			}
		}
		return $tree;
	}
	public static function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
        if(is_array($multi_array)){
            foreach ($multi_array as $row_array){
                if(is_array($row_array)){
                    $key_array[] = $row_array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_array,$sort,$multi_array);
        return $multi_array;
    }

    public static function genRandomString($len)
	{ 
		//$chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
		$chars = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
		$charsLen = count($chars) - 1;
		shuffle($chars);
		$output = "";
		for ($i=0; $i<$len; $i++)
		{
			$output.= $chars[mt_rand(0, $charsLen)];
		}
		return $output;
	}

        public static function genRandomStringBy($len,$type = '')
	{
                $chars = array();
                $type = trim($type);

                $type_arr = preg_split("/_/",$type,null,PREG_SPLIT_NO_EMPTY);
                foreach($type_arr as $sign){
                    $chars = array_merge($chars,self::gzseed_array_type($sign));
                }

                if(empty($chars))$chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
                
		$charsLen = count($chars) - 1;
		shuffle($chars);
		$output = "";
		for ($i=0; $i<$len; $i++)
		{
			$output.= $chars[mt_rand(0, $charsLen)];
		}
		return $output;
	}

        public static function gzseed_array_type($type){
                $n = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
                $ll = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
                $lu = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
                $type = trim(strtolower($type));
                switch ($type){
                    case 'n':   //number:数字
                        return $n;
                    break;
                    case 'll': //letter of Lcase:小字字母
                        return $ll;
                    break;
                    case 'lu': //letter of Ucase:大写字母
                        return $lu;
                    break;
                    default:
                        return array();
                    break;
                }
        }
	
	public static function getHostFromString($str,$checkport = true){
		preg_match("/^((http|https):\/\/)?([^\/]+)/i", $str, $matches);
		if(isset($matches[3]) && !empty($matches[3])){
			if(!$checkport){
				$tmp = explode(":",$matches[3]);
				return $tmp[0];
			}else{
				return $matches[3];
			}
		}
	}
	
	public static function arrayUnique($arr,$filed)
	{
		$arr2 = array ();
		foreach($arr as $key => $value) {
		    $index = $value[$filed];
		    if (isset($arr2[$index])) {
		     unset($arr[$key]);
		    } else {
		     $arr2[$index] = true;
		    }
		}
		return $arr;
	}
	
	public static function returnBytes($val) {
	    $val = trim($val);
	    $last = strtolower($val[strlen($val)-1]);
	    switch($last) {
	        // The 'G' modifier is available since PHP 5.1.0
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }
	
	    return $val;
	}
	
	public static function returnSize($val) {
		$val = ceil($val / 1024);
		if($val>=1024){
			$val = ceil($val / 1024);
			if($val>=1024){
				$val = ceil($val / 1024);
				if($val>=1024){
					$val = ceil($val / 1024);
					$val.=" T";
				}else{
					$val.=" G";
				}
			}else{
				$val.=" M";
			}
		}else{
			$val.=" K";
		}
	    return $val;
	}
	
	public static function asciiDecode($str){
		preg_match_all( "/(d{2,5})/", $str,$a);
	    $a = $a[0];
	    foreach ($a as $dec)
	    {
	        if ($dec < 128)
	        {
	            $utf .= chr($dec);
	        }
	        else if ($dec < 2048)
	       {
	            $utf .= chr(192 + (($dec - ($dec % 64)) / 64));
	            $utf .= chr(128 + ($dec % 64));
	        }
	        else
	        {
	            $utf .= chr(224 + (($dec - ($dec % 4096)) / 4096)); 
	            $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64)); 
	            $utf .= chr(128 + ($dec % 64)); 
	        } 
	    } 
	    return $utf; 
	}

	public static function asciiEncode($c){
		$len = strlen($c); 
	    $a = 0; 
	    $scill="";
	    while ($a < $len)
	    {
	        $ud = 0; 
	        if (ord($c{$a}) >=0 && ord($c{$a})<=127)
	        {
	            $ud = ord($c{$a}); 
	            $a+= 1; 
	        }
	        else if (ord($c{$a}) >=192 && ord($c{$a})<=223)
	        {
	            $ud = (ord($c{$a})-192)*64 + (ord($c{$a+1})-128); 
	            $a+= 2; 
	        }
	        else if (ord($c{$a}) >=224 && ord($c{$a})<=239)
	        {
	            $ud = (ord($c{$a})-224)*4096 + (ord($c{$a+1})-128)*64 + (ord($c{$a+2})-128); 
	            $a+= 3; 
	        }
	        else if (ord($c{$a}) >=240 && ord($c{$a})<=247)
	        {
	            $ud = (ord($c{$a})-240)*262144 + (ord($c{$a+1})-128)*4096 + (ord($c{$a+2})-128)*64 + (ord($c{$a+3})-128); 
	            $a+= 4; 
	        }
	        else if (ord($c{$a}) >=248 && ord($c{$a})<=251)
	        {
	            $ud = (ord($c{$a})-248)*16777216 + (ord($c{$a+1})-128)*262144 + (ord($c{$a+2})-128)*4096 + (ord($c{$a+3})-128)*64 + (ord($c{$a+4})-128); 
	            $a+= 5; 
	        }
	        else if (ord($c{$a}) >=252 && ord($c{$a})<=253)
	        {
	            $ud = (ord($c{$a})-252)*1073741824 + (ord($c{$a+1})-128)*16777216 + (ord($c{$a+2})-128)*262144 + (ord($c{$a+3})-128)*4096 + (ord($c{$a+4})-128)*64 + (ord($c{$a+5})-128); 
	            $a+= 6; 
	        }
	        else if (ord($c{$a}) >=254 && ord($c{$a})<=255)
	        { //error
	            $ud = false; 
	        } 
	        $scill.= "&#$ud;";
	    } 
	    return $scill;
	}
	
	public static function getBrief($desc){
		$patterns=array();
		$replacements=array();
		$patterns[]="/&nbsp;/";	
		$patterns[]="/&ldquo;/";	
		$patterns[]="/&rdquo;/";
		$patterns[]="/&hellip;/";	
		$patterns[]="/&cap;/";
		$patterns[]="/&mdash;/";
		$patterns[]="/&lsquo;/";
		$patterns[]="/&rsquo;/";
		$patterns[]="/&middot;/";
		$replacements[]="";
		$replacements[]="“";
		$replacements[]="”";
		$replacements[]="…";
		$replacements[]="∩";
		$replacements[]="—";
		$replacements[]="‘";
		$replacements[]="’";
		$replacements[]="·";
		$brief = $desc;
		$brief = strip_tags($brief);
		$brief = preg_replace($patterns, $replacements, $brief);
		$brief = mb_substr($brief,0,200,'UTF-8');
		return trim($brief);
	}
	
	public static function rootDomain($url){
		if(empty($url))$url=$_SERVER['HTTP_HOST'];
	    if(!preg_match("/^http:/is", $url))
	        $url="http://".$url;
	    $url=parse_url(strtolower($url));
	    $urlarr=explode(".", $url['host']);
	    $count=count($urlarr);
	    
	    $state_domain=array('al','dz','af','ar','ae','aw','om','az','eg','et','ie','ee','ad','ao','ai','ag','at','au','mo','bb','pg','bs','pk','py','ps','bh','pa','br','by','bm','bg','mp','bj','be','is','pr','ba','pl','bo','bz','bw','bt','bf','bi','bv','kp','gq','dk','de','tl','tp','tg','dm','do','ru','ec','er','fr','fo','pf','gf','tf','va','ph','fj','fi','cv','fk','gm','cg','cd','co','cr','gg','gd','gl','ge','cu','gp','gu','gy','kz','ht','kr','nl','an','hm','hn','ki','dj','kg','gn','gw','ca','gh','ga','kh','cz','zw','cm','qa','ky','km','ci','kw','cc','hr','ke','ck','lv','ls','la','lb','lt','lr','ly','li','re','lu','rw','ro','mg','im','mv','mt','mw','my','ml','mk','mh','mq','yt','mu','mr','us','um','as','vi','mn','ms','bd','pe','fm','mm','md','ma','mc','mz','mx','nr','np','ni','ne','ng','nu','no','nf','na','za','aq','gs','eu','pw','pn','pt','jp','se','ch','sv','ws','yu','sl','sn','cy','sc','sa','cx','st','sh','kn','lc','sm','pm','vc','lk','sk','si','sj','sz','sd','sr','sb','so','tj','tw','th','tz','to','tc','tt','tn','tv','tr','tm','tk','wf','vu','gt','ve','bn','ug','ua','uy','uz','es','eh','gr','hk','sg','nc','nz','hu','sy','jm','am','ac','ye','iq','ir','il','it','in','id','uk','vg','io','jo','vn','zm','je','td','gi','cl','cf','cn','yr');
	    $top_domain=array('com','arpa','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','me','mobi');
	    
	    if ($count<=2){
	        return $url['host'];
	    }else if ($count>2){
	        $last=array_pop($urlarr);
	        
	        $last_1=array_pop($urlarr);
	        if(in_array($last, $top_domain)){
	            return $last_1.'.'.$last;
	        }else if (in_array($last, $state_domain)){
	            $last_2=array_pop($urlarr);
	            if(in_array($last_1, $top_domain)){
	                return $last_2.'.'.$last_1.'.'.$last;
	            }elseif(in_array($last_1, $state_domain)){
	                return $last_2.'.'.$last_1.'.'.$last;
	            }else{
	                return $last_1.'.'.$last;
	            }
	        }
	    }
	}
	
	public static function sysSortArray($ArrayData,$KeyName1,$SortOrder1 = "SORT_DESC",$SortType1 = "SORT_REGULAR")
	{
	    if(!is_array($ArrayData))
	    {
	        return $ArrayData;
	    }
	 
	    // Get args number.
	    $ArgCount = func_num_args();
	 
	    // Get keys to sort by and put them to SortRule array.
	    for($I = 1;$I < $ArgCount;$I ++)
	    {
	        $Arg = func_get_arg($I);
	        if(!eregi("SORT",$Arg))
	        {
	            $KeyNameList[] = $Arg;
	            $SortRule[]    = '$'.$Arg;
	        }
	        else
	        {
	            $SortRule[]    = $Arg;
	        }
	    }
	 
	    // Get the values according to the keys and put them to array.
	    foreach($ArrayData AS $Key => $Info)
	    {
	        foreach($KeyNameList AS $KeyName)
	        {
	            ${$KeyName}[$Key] = $Info[$KeyName];
	        }
	    }
	 
	    // Create the eval string and eval it.
	    $EvalString = 'array_multisort('.join(",",$SortRule).',$ArrayData);';
	    eval ($EvalString);
	    return $ArrayData;
	}
}