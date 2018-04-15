<?php

class Zend_View_Helper_ShowFactoringStatus
{
    function ShowFactoringStatus($type)
    {
        $str='';
        if(empty($str)){
            $str = "待激活";
        }else{
            switch ($type) {
                case 01:
                    $str = "待激活";
                    break;
                case 11:
                    $str = "待放款";
                    break;
                case 12:
                    $str = "代还款";
                    break;
                case 04:
                    $str = "不通过";
                    break;
                case 05:
                    $str = "已完成";
                    break;
                default:
                    $str = "待激活";
            }
        }

        return $str;
    }
}
