<?php
class Wechat_Advance_Material{
    public static function getMaterialId($str = 'image'){
        $str = strtolower((string)$str);
        switch ($str) {
            case 'image':
                return '2';
                break;
            case 'voice':
                return '3';
                break;
            case 'video':
                return '15';
                break;
            case 'text':
            default:
                return '1';
                break;
        }
    }
}