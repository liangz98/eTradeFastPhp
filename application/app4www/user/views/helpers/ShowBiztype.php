<?php
class Zend_View_Helper_ShowDictionary extends Shop_View_Helper
{

	//查询本地资源文件 单一字典结果
	function ShowDictionary($e,$t,$lang_code){
        //$e  缓存路径-》数据字典代码
        //$lang_code   语言版本
        //$t 查询key
        $cacheM = new Seed_Model_Cache2File();
        $dall = $cacheM->get('datatest_setting');
        $dic=$dall[$e];
        $str=array();
        foreach($dic as $key=>$value){
            $setArr=$value['baseLangList'];
            foreach($setArr as $k1 => $v1){
                if (in_array($lang_code, $v1))
                {
                    if($v1['langCode']==$lang_code){
                        //输出当前语言的name
                        $str[$key]['code']=$value['code'];
                        $str[$key]['name']=$v1['nameText'];
                        //设置缺省
                        if(empty($str[$key]['name'])){
                            if($v1['langCode']=="zh_CN") $str[$key]['name']=$v1['nameText'];
                        }
                    }
                }
            }
        }
        $kval="";
        foreach($str as $k=>$v){
            if($t==$v['code']){
                $kval=$v['name'];
            }
        }
        return  $kval;
	}
}