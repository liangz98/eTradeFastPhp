<?php
 class Zend_View_Helper_ShowArrList extends Shop_View_Helper
{

	//查询本地资源文件 列表字典结果
	function ShowDictionaryList($files_name,$lang_code,$e,$t){
        //$files_name 缓存文件名称
        //$lang_code   语言版本
        //$e  数据字典代码
        //$t 查询key
        $cacheM = new Seed_Model_Cache2File();
        $dall = $cacheM->get($files_name);
        if(!empty($dall[$e])){
            $dic=$dall[$e];
        }else{
            $dic=$dall;
        }

        $str=array();
        foreach($dic as $key=>$value){
            if($value['baseLangList']){
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
            }else{
                $str=$dic;
            }
        }
        $arroption="";
        $arroption=.'';
        if($t){
        $arroption.= '<select class="js-example-data-array-selected">';
        foreach($str as $k=>$v) {
            if($t==$v['code']){
                $arroption.= '<option selected="selected" value='.$v['code'].'>'.$v['name'].'</option>';
            }

        }
        $arroption.= ' </select>';
        }else{
            $arroption.= '<select class="js-example-data-array-selected">';
            $arroption.= ' </select>';
        }
       return  $arroption;

        $dclist="";
        foreach($str as $k=>$v){
            if(!empty($t)){
            if($t==$v['code']){
                $dclist.= '<option selected="selected" value='.$v['code'].'>'.$v['name'].'</option>';
            }else{
                $dclist.= '<option value='.$v['code'].'>'.$v['name'].'</option>';
            }}
            else{
                if($lang_code=="zh_CN"){
                if($v['code']=="CN"){
                    $dclist.= '<option selected="selected" value='.$v['code'].'>'.$v['name'].'</option>';
                }else{
                    $dclist.= '<option value='.$v['code'].'>'.$v['name'].'</option>';
                }}else{
                    if($v['code']=="US"){
                        $dclist.= '<option selected="selected" value='.$v['code'].'>'.$v['name'].'</option>';
                    }else{
                        $dclist.= '<option value='.$v['code'].'>'.$v['name'].'</option>';
                    }
                }

            }
        }
        return $dclist;
	}
}