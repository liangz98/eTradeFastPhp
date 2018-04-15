<?php
class Zend_View_Helper_ShowHscode extends Shop_View_Helper
{

	//查询本地资源文件 单一字典结果
	function ShowHscode($requestOb,$hscode){
        //$hscode  当前HSCODE值
        $userM = new Kyapi_Controller_Json($this->seed_Setting['KyUrl']);
        $userKY = $userM->listHSCodeApi($requestOb,null,null, $hscode, 0, 0);
        $existData = $userKY->result;
        $existDatt=json_decode($existData);
        $existArr = $this->objectToArray($existDatt);
        $msg="";
        foreach($existArr as $k=>$v){
            $msg=$v['codeName'];
        }

        return  $msg;
	}

}