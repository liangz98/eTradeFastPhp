<?php
class Zend_View_Helper_ShowWebuploader1 extends Shop_View_Helper
{
	function ShowWebuploader1($attArr,$bizType,$attachType,$type,$goodsID)
	{
        //$attArr 服务端返回附件信息数组；
        //$goodsID   绑定业务ID->bizID
        //$bizType 业务类型
        //$attachType  附件类型
        //$type  是否临时附件 1 正式  0临时
        $str="";
        $indata="";
        $indata.='<input  type="hidden" id="bizType" value='.$bizType;
        $indata.='><input  type="hidden" id="bizID" value='.$goodsID;
        $indata.='><input  type="hidden" id="attachType" value='.$attachType;
        $indata.='><input  type="hidden" id="uploadURL" value='.$this->view->seed_Setting['KyUrlex'];
        $indata.='><input  type="hidden" id="typeURL" value='.$type;
        $indata.='>';

        if(is_array($attArr) && count($attArr)>0){
            foreach($attArr as $k=>$attlist){
                $str.='<li><img width="125px" height="125px"  layer-src='. $this->view->seed_Setting['KyUrlex'];
                $str.='/doc/download.action?sid='.session_id();
                $str.='&nid='.$attlist['attachID'];
                $str.='&vid='.$attlist['verifyID'];
                $str.=' src='. $this->view->seed_Setting['KyUrlex'];
                $str.='/doc/download.action?sid='.session_id();
                $str.='&nid='.$attlist['attachID'];
                $str.='&vid='.$attlist['verifyID'];
                $str.='&size=MIDDLE name='.$attlist['name'];
                $str.='alt='.$attlist['attachType'].'><span class="del_to"><a onclick="view_pic(this)">查看</a>';
                if($type!='1') {
                    $str.='|<a onclick="delete_pic(this)">删除</a></span>';
                }
                $str.='<input type="hidden" name="attachNid[]" value="'.$attlist['attachID'].'">';
                $str.='<input type="hidden" name="attachVid[]" value="'.$attlist['verifyID'].'">';
                $str.='<input type="hidden" name="attachType[]" value="'.$attlist['attachType'].'">';
                $str.='<input type="hidden" name="attachName[]" value="'.$attlist['name'].'">';
                $str.='<input type="hidden" name="attachSize[]" value="'.$attlist['size'].'"></li>';
            }
        }else{
            $str="";
        }

        $showView="";
        $showView.='<div class="img-read" id="img-read">';
        $showView.='</div><div class="demo"><ul class="img-view">'.$str;
        $showView.='</ul></div><script type="text/javascript" src="/ky/upload/img.js"></script>';
        $showView.= '<script type="text/javascript" src="/ky/layer-v3.1.1/layer.js"></script>';
        $showView.='<link href="/ky/css/img-upload.css" rel="stylesheet">';

        $showimg="";
        $showimg.='<div class="img-read" id="img-read">';
        $showimg.='</div><div class="demo"><ul class="img-view">'.$str;
        $showimg.='</ul><ul class="upload-image-list clearfix"><input id="CROD" type="hidden" value="CRSE"><div class="fileinput-button js-add-image" onclick="addWebuploadCurrent($(this))">'.$indata;
        $showimg.='<span class="cover_words"></span><div class="webuploader-pick">';
        $showimg.='<a class="fileinput-button-icon" href="javascript:;"></a></div></div></ul>';
        $showimg.='</div><script type="text/javascript" src="/ky/upload/img.js"></script>';
        $showimg.= '<script type="text/javascript" src="/ky/layer-v3.1.1/layer.js"></script>';
        $showimg.='<link href="/ky/css/img-upload.css" rel="stylesheet">';
        if($type=='1'){
            return $showView;
        }else{
            return $showimg;
        }
	}
}
