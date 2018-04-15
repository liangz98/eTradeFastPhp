<?php
class Zend_View_Helper_ShowUPimg extends Shop_View_Helper
{
	function ShowUPimg($attArr)
	{
		//$attArr 服务端返回附件信息数组；
		$str="";
		if(is_array($attArr) && count($attArr)>0){
			foreach($attArr as $k=>$attlist){
				$str.='<img class="table_img"  src='. $this->view->seed_Setting['KyUrlex'];
				$str.='/doc/download.action?sid='.session_id();
				$str.='&nid='.$attlist['attachID'];
				$str.='&vid='.$attlist['verifyID'];
				$str.='&size=MIDDLE name='.$attlist['name'].'alt='.$attlist['attachType'].'>';
			}
		}else{
			$str='<img class="table_img" src="/ky/images/goods_tx.png">';
		}

		return $str;
	}
}