<?php

class Seed_Date
{
    /**
     * 判断是否为闰年
     * 
     * @param int $year 年份，默认为当前年份
     * @return bool
     */
    public static function isLeapYear($year = null)
    {
        $year = $year ? $year : date('Y');
        return ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0);
    }
}