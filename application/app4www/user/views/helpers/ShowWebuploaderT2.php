<?php
class Zend_View_Helper_ShowWebuploaderT2 extends Shop_View_Helper
{
	function ShowWebuploaderT2($attArr,$bizType,$attachType,$type,$goodsID)
	{
		//$attArr 服务端返回附件信息数组；
       //$goodsID   绑定业务ID
		//$bizType 业务类型
		//$attachType  附件类型
		//$type  是否临时附件 1 正式  0临时
		$str="";
		$indata="";
		$indata.='<input  type="hidden" class="bizTypeT2" value='.$bizType;
		$indata.='><input  type="hidden" class="bizIDT2" value='.$goodsID;
		$indata.='><input  type="hidden" class="attachTypeT2" value='.$attachType;
		$indata.='><input  type="hidden" class="uploadURLT2" value='.$this->view->seed_Setting['KyUrlex'];
		$indata.='><input  type="hidden" class="typeURLT2" value='.$type;
		$indata.='>';

		if(is_array($attArr) && count($attArr)>0){
			foreach($attArr as $k=>$attlist){
				$str.='<li><img width="125px" height="125px"  src='. $this->view->seed_Setting['KyUrlex'];
				$str.='/doc/download.action?sid='.session_id();
				$str.='&nid='.$attlist['attachID'];
				$str.='&vid='.$attlist['verifyID'];
				$str.='&size=MIDDLE name='.$attlist['name'];
				$str.='alt='.$attlist['attachType'].'><span class="del_to"><a onclick="view_pic(this)">查看</a>|<a onclick="delete_pic(this)">删除</a></span>';
				$str.='<input type="hidden" name="attachNid[]" value="'.$attlist['attachID'].'">';
				$str.='<input type="hidden" name="attachVid[]" value="'.$attlist['verifyID'].'">';
				$str.='<input type="hidden" name="attachType[]" value="'.$attlist['attachType'].'">';
				$str.='<input type="hidden" name="attachName[]" value="'.$attlist['name'].'">';
				$str.='<input type="hidden" name="attachSize[]" value="'.$attlist['size'].'"></li>';
			}
		}else{
			$str="";
		}


		$showimg="";
		$showimg.='<div class="img-read" id="img-read">';
		$showimg.='</div><div class="demo"><ul class="img-view">'.$str;
		$showimg.='</ul><ul class="upload-image-list clearfix"><div class="fileinput-button js-add-image" onclick="addWebuploadCurrent($(this))">'.$indata;
		$showimg.='<span class="cover_words"></span><div class="webuploader-pick">';
		$showimg.='<a class="fileinput-button-icon" href="javascript:;"></a></div></div></ul>';
		$showimg.='</div><script type="text/javascript" src="/ky/upload/img.js"></script>';
		$showimg.= '<script type="text/javascript" src="/ky/layer-v3.1.1/layer.js"></script>';
		$showimg.='<link href="/ky/css/img-upload.css" rel="stylesheet">';
		return $showimg;
	}
}
