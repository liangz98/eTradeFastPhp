<?php
class Seed_Lang
{
    protected static $langs=array();

    /**
     * 读取语言包语言，调用方法如：Seed_Lang::get('integral')，可在视图，控制器，模型中直接调用。
     * @param string $lang_name 语言标识
     */
    public static function get($lang_name)
    {
        $langs=self::$langs;
        if(isset($langs[$lang_name])) {
            return $langs[$lang_name];
        }else{
            $fileM = new Seed_Model_Cache2File();
            self::$langs = $langs= $fileM->get('lang');
            if(isset($langs[$lang_name])) {
                return $langs[$lang_name];
            }else{
                return null;
            }
        }
    }

    /**
     * 判断特定的语言标记是否存在
     * @param $lang_name 语言标记
     * @return bool
     */
    public static function isRegistered($lang_name)
    {
        $langs=self::$langs;
        if(isset($langs[$lang_name])) {
            return true;
        }else{
            $fileM = new Seed_Model_Cache2File();
            self::$langs = $langs= $fileM->get('lang');
            if(isset($langs[$lang_name])) {
                return true;
            }else{
                return false;
            }
        }
    }
}