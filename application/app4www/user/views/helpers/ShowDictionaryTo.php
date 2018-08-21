<?php

class Zend_View_Helper_ShowDictionaryTo extends Shop_View_Helper {

    //查询本地资源文件 单一字典结果
    function ShowDictionaryTo($files, $lang_code, $e, $t) {
        //$files  缓存文件目录
        //$lang_code   语言版本
        //$e  缓存路径-》数据字典代码
        //$t 查询key
        $cacheM = new Seed_Model_Cache2File();

        if (!isset($t)) {
            return "";
        }
        if (!empty($e)) {
            if ($e == 'SEA') {
                $e = 'SEA_PORT';
                // return $e;
            } else if ($e == 'AIR') {
                $e = 'AIR_PORT';
                // return $e;
            } else if ($e == 'LAND') {
                $e = 'CITY_ISO_CODE';
                // return $e;
            }
        }

        $d_name = 'abc_' . $e;
        $dic = $cacheM->get($d_name);

        $str = array();
        foreach ($dic as $key => $value) {
            if ($value['baseLangList']) {
                $setArr = $value['baseLangList'];
                foreach ($setArr as $k1 => $v1) {
                    if ($v1['langCode'] == $lang_code) {
                        //输出当前语言的name
                        $str[$key]['code'] = $value['code'];
                        $str[$key]['name'] = $v1['nameText'];
                        //设置缺省
                        if (empty($str[$key]['name'])) {
                            if ($v1['langCode'] == "zh_CN")
                                $str[$key]['name'] = $v1['nameText'];
                        }
                    }
                }
            } else {
                $str = $dic;
            }
        }

        foreach ($str as $k => $v) {
            if (isset($t)) {
                if ($t == $v['code']) {
                    $kval = $v['name'];

                }
            } else {
                $kval = $t;
            }
        }
        return $kval;
    }
}
