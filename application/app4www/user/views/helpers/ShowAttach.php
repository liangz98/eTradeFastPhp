<?php
class Zend_View_Helper_ShowAttach extends Shop_View_Helper
{
	function ShowAttach($attArr,$key)
	{
		//$attArr 服务端返回附件信息数组；
		$str="";
		if(is_array($attArr) && count($attArr)>0){
			foreach($attArr as $k=>$attlist){
//				if($key){
					if($attlist['attachType']==$key)
					{
						$str.='<a  src='. $this->view->seed_Setting['KyUrlex'];
						$str.='/doc/download.action?sid='.session_id();
						$str.='&nid='.$attlist['attachID'];
						$str.='&vid='.$attlist['verifyID'];
						$str.='&size=MIDDLE name='.$attlist['name'].'alt='.$attlist['attachType'].'>';
					}else{
						$str='附件不存在';
					}
//				}else{
//					$str.='<a  src='. $this->view->seed_Setting['KyUrlex'];
//					$str.='/doc/download.action?sid='.session_id();
//					$str.='&nid='.$attlist['attachID'];
//					$str.='&vid='.$attlist['verifyID'];
//					$str.='&size=MIDDLE name='.$attlist['name'].'alt='.$attlist['attachType'].'>';
//				}
			}
		}
			return $str;
		}
	}