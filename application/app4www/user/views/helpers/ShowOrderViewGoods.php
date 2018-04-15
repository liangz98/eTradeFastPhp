<?php
class Zend_View_Helper_ShowOrderViewGoods extends Shop_View_Helper
{
    //订单模块 文档 展示判断
	function showOrderViewGoods($goods)
	{
        //$goods 当前订单商品属性值
       // mb_strimwidth($str, $start, $width, $tail, $encoding)
        //        $str 要截取的字符串
        //$start 从哪个位置开始截取，默认是0
        //$width 要截取的宽度
        //$tail 追加到截取字符串后边的字符串，常用的是 ...
        //$encoding 要使用的编码

       $str="";
       $str.='<a class="view_span" title="'.$goods.'">';
       $str.=$this->strLength($goods)>12?$this->cut_str($goods,11):$goods;
       $str.='</a>';
       return $str;
	}


//    /**
//     *
//     * 中英混合字符串长度判断
//     * @param unknown_type $str
//     * @param unknown_type $charset
//     */
    function strLength($str, $charset = 'utf-8') {
        if ($charset == 'utf-8')
            $str = iconv ( 'utf-8', 'gb2312', $str );
        $num = strlen ( $str );
        $cnNum = 0;
        for($i = 0; $i < $num; $i ++) {
            if (ord ( substr ( $str, $i + 1, 1 ) ) > 127) {
                $cnNum ++;
                $i ++;
            }
        }
        $enNum = $num - ($cnNum * 2);
        $number = ($enNum / 2) + $cnNum;
        return ceil ( $number );
    }
//    /**
//     *
//     * 中英混合的字符串截取
//     * @param unknown_type $sourcestr
//     * @param unknown_type $cutlength
//     */
    function cut_str($sourcestr, $cutlength) {
        $returnstr = '';
        $i = 0;
        $n = 0;
        $str_length = strlen ( $sourcestr ); //字符串的字节数
        while ( ($n < $cutlength) and ($i <= $str_length) ) {
            $temp_str = substr ( $sourcestr, $i, 1 );
            $ascnum = Ord ( $temp_str ); //得到字符串中第$i位字符的ascii码
            if ($ascnum >= 224) //如果ASCII位高与224，
            {
                $returnstr = $returnstr . substr ( $sourcestr, $i, 3 ); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                $i = $i + 3; //实际Byte计为3
                $n ++; //字串长度计1
            } elseif ($ascnum >= 192) //如果ASCII位高与192，
            {
                $returnstr = $returnstr . substr ( $sourcestr, $i, 2 ); //根据UTF-8编码规范，将2个连续的字符计为单个字符
                $i = $i + 2; //实际Byte计为2
                $n ++; //字串长度计1
            } elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
            {
                $returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
                $i = $i + 1; //实际的Byte数仍计1个
                $n ++; //但考虑整体美观，大写字母计成一个高位字符
            } else //其他情况下，包括小写字母和半角标点符号，
            {
                $returnstr = $returnstr . substr ( $sourcestr, $i, 1 );
                $i = $i + 1; //实际的Byte数计1个
                $n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
            }
        }
        if ($str_length > $cutlength) {
            $returnstr = $returnstr . "..."; //超过长度时在尾处加上省略号
        }
        return $returnstr;
    }
}