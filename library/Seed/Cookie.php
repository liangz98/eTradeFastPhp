<?php
class Seed_Cookie {
        /**
         * 设置cookie
        * @param string $name cookie名称
         * @param string $value cookie值
         * @param bool $encode 是否编码
         * @param string|int $expire 过期时间，默认为空即会话cookie，随着会话结束失效
         * @param string $path cookie保存路径
         * @param string $domain cookie所属域
         * @param bool $secure	是否采用安全连接
         * @param bool $httponly 是否可通过客户端脚本访问，默认为false即客户端脚本可以访问
         * @return bool 设置成功返回true，否则false
         */
	static function setCookie($name,$value,$expire = 0,$path="/",$domain=null,$secure=false, $httponly=false, $encode=true){
		$expire == 0 && $expire = time() + 86400;
                $encode && $value && $value = base64_encode(serialize($value));
                $path = $path ? $path : '/';
                if($domain === null && isset($_SERVER['HTTP_HOST']) && !preg_match('/^\d+$/', str_replace($_SERVER['HTTP_HOST'], '.', ''))){
                    $HTTP_HOST_ARRAY = explode('.',$_SERVER['HTTP_HOST']);
                    $count = count($HTTP_HOST_ARRAY);
                    $domain = (isset($HTTP_HOST_ARRAY[$count-3]) && $HTTP_HOST_ARRAY[$count-3] != 'www' ? '.'.$HTTP_HOST_ARRAY[$count-3] : '').(isset($HTTP_HOST_ARRAY[$count-2]) ? '.'.$HTTP_HOST_ARRAY[$count-2] : '').(isset($HTTP_HOST_ARRAY[$count-1]) ? '.'.$HTTP_HOST_ARRAY[$count-1] : '');
                }
                $name = self::transformCookieName($name);
                return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly); 
	}
	
        /**
         * 获取cookie
         * @param string $name
         * @return type
         */
	static function getCookie($name,$decode = true){
                $value='';
                $name = self::transformCookieName($name);
                if(isset($_COOKIE[$name])){
                     $value = $_COOKIE[$name];
                     $value && $decode && $value = unserialize(base64_decode($value));
                }
                return $value;
	}
        
        /**
         * 转化cookie名
         * @param type $name
         * @param type $seperator
         */
        static function transformCookieName($name,$seperator = '_'){
//             if(defined('SEED_HOST_NAME')){
//                    $name = preg_replace('/[^\da-zA-Z]/is', '',SEED_HOST_NAME).$seperator.$name;
//             }
             return $name;
        }
        
        /**
         * 解析cookie名
         * @param type $name
         * @param type $seperator
         */
        static function parseCookieName($name,$seperator = '_'){
             if(defined('SEED_HOST_NAME')){
                 $pre = preg_replace('/[^\da-zA-Z]/is', '',SEED_HOST_NAME).$seperator;
                 if(strpos($name, $pre) === 0){
                     $name = substr(strlen($pre), $name);
                 }
             }
             return $name;
        }
        
        /**
         * 检查cookie是否存在
         * @param string $name cookie名称
         * @return bool
         */
        public static function exists($name,$transform = true)
        {
            $transform && $name = self::transformCookieName($name);
            return isset($_COOKIE[$name]);
        }
        
        
        /**
         * 删除指定的cookie
         * @return bool
         */
        public static function delete($name,$path="/",$domain=null,$secure=false, $httponly=false,$transform=true)
        {
            if($transform){
                $name = self::transformCookieName($name);
            }
            if(self::exists($name,false)){
                $path = $path ? $path : '/';
                if($domain === null && isset($_SERVER['HTTP_HOST']) && !preg_match('/^\d+$/', str_replace($_SERVER['HTTP_HOST'], '.', ''))){
                    $HTTP_HOST_ARRAY = explode('.',$_SERVER['HTTP_HOST']);
                    $count = count($HTTP_HOST_ARRAY);
                    $domain = (isset($HTTP_HOST_ARRAY[$count-3]) && $HTTP_HOST_ARRAY[$count-3] != 'www' ? '.'.$HTTP_HOST_ARRAY[$count-3] : '').(isset($HTTP_HOST_ARRAY[$count-2]) ? '.'.$HTTP_HOST_ARRAY[$count-2] : '').(isset($HTTP_HOST_ARRAY[$count-1]) ? '.'.$HTTP_HOST_ARRAY[$count-1] : '');
                }
                setcookie($name, '', -3600, $path, $domain, $secure, $httponly); 
                setcookie($name, '', -3600, $path, ltrim($domain,'.'), $secure, $httponly); //修复有些手机不能区别有'.'
                
                $root_domain = self::getdomain($domain);
                setcookie($name, '', -3600, $path, $root_domain, $secure, $httponly); 
                setcookie($name, '', -3600, $path, ".".$root_domain, $secure, $httponly);
                
                unset($_COOKIE[$name]);
            }
            return true;
        }
        
        
        /**
         * 清除所有的cookie
         */
        public static function clear($path="/",$domain=null,$secure=false, $httponly=false,$transform=false)
        {
            foreach ($_COOKIE as $name=>$value)
            {
                self::delete($name,$path,$domain,$secure, $httponly,$transform);
            }
            return true;
        }

        public static function getdomain($url){ 
			$host = strtolower ( $url ); 
			if (strpos ( $host, '/' ) !== false) { 
				$parse = @parse_url ( $host ); 
				$host = $parse ['host']; 
			} 
			$topleveldomaindb = array ('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me' ); 
			$str = ''; 
			foreach ( $topleveldomaindb as $v ) { 
				$str .= ($str ? '|' : '') . $v; 
			} 
			$matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$"; 
			if (preg_match ( "/" . $matchstr . "/ies", $host, $matchs )) { 
				$domain = $matchs ['0']; 
			} else { 
				$domain = $host; 
			} 
			return $domain; 
		}
}
?>