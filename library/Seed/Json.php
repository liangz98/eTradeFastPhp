<?php
class Seed_Json{
  
  //不转义中文
   public static function json_encode_unescaped_unicode($data) { 
        if(strcasecmp(phpversion(), '5.4.0') > -1){
           return json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
          $str = json_encode($data); 
          $search = "#\\\u([0-9a-f]+)#ie"; 
          $replace = "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))"; 
          return preg_replace($search, $replace, $str); 
        }
    }
}