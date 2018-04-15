<?php

/**
 * @filename String.php
 * @encoding UTF-8
 * @author 落雨 <fallrain, urainame@foxmail.com>
 * @link http://www.gzseed.cn
 * @copyright copyright (c) 2014 萌芽网络
 * @datetime 2014-7-22  14:01:34
 * @version 1.0
 * @Description
 */

Class Tools_String
{
    public static function substr_replace_cn($string, $repalce = '*', $start = 0, $len = 0)
    {
        $count = mb_strlen($string, 'UTF-8'); //此处传入编码，建议使用utf-8。此处编码要与下面mb_substr()所使用的一致
        if (!$count) {
            return $string;
        }
        if ($len == 0) {
            $end = $count;  //传入0则替换到最后
        } else {
            $end = $start + $len;  //传入指定长度则为开始长度+指定长度
        }
        $i = 0;
        $returnString = '';
        while ($i < $count) {  //循环该字符串
            $tmpString = mb_substr($string, $i, 1, 'UTF-8'); // 与mb_strlen编码一致
            if ($start <= $i && $i < $end) {
                $returnString .= $repalce;
            } else {
                $returnString .= $tmpString;
            }
            $i ++;
        }
        return $returnString;
    }
}
