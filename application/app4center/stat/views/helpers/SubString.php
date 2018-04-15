<?php
class Zend_View_Helper_SubString
{
    function subString($text, $start=0, $limit=80) {
        if (function_exists('mb_substr')) {
            $more = (mb_strlen($text,'UTF-8') > $limit) ? TRUE : FALSE;
            if($more) $text = mb_substr($text, $start, $limit, 'UTF-8');
            return $text;
        } elseif (function_exists('iconv_substr')) {
            $more = (iconv_strlen($text,'UTF-8') > $limit) ? TRUE : FALSE;
            if($more) $text = iconv_substr($text, $start, $limit, 'UTF-8');
            return $text;
        } else {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $text, $ar);
            if (count($ar[0])>$limit) {
                $text = join("",array_slice($ar[0],$start,$limit));
            } else {
                $text = join("",array_slice($ar[0],$start));
            }
            return $text;
        }
    }
}
