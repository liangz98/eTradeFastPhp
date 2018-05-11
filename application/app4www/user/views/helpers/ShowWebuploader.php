<?php

class Zend_View_Helper_ShowWebuploader extends Shop_View_Helper {

    function ShowWebuploader($attArr, $bizType, $attachType, $type, $goodsID) {
		//$attArr 服务端返回附件信息数组；
        //$goodsID   绑定业务ID->bizID
		//$bizType 业务类型
		//$attachType  附件类型
		//$type  是否临时附件 1 正式  0临时
		$str = "";

        $indata = "";
        $indata .= '<input type="hidden" id="bizType" value="' . $bizType . '" />';
        $indata .= '<input type="hidden" id="bizID" value="' . $goodsID . '" />';
        // $indata .= '<input type="hidden" id="attachType" value="' . $attachType . '" />';
        $indata .= '<input type="hidden" id="uploadURL" value="' . $this->view->seed_Setting['KyUrlex'] . '" />';
        $indata .= '<input type="hidden" id="typeURL" value="' . $type . '" />';


        if (is_array($attArr) && count($attArr) > 0) {
            foreach ($attArr as $k => $attlist) {
                $ext = "";
                $attachID = "";
                $verifyID = "";
                $name = "";
                $attachType_ = "";
                $size = "";

                if (is_object($attlist)) {
                    $ext = $attlist->ext;
                    $attachID = $attlist->attachID;
                    $attachType_ = $attlist->attachType;
                    $verifyID = $attlist->verifyID;
                    $name = $attlist->name;
                    $size = $attlist->size;
                } else if (is_array($attlist)) {
                    $ext = $attlist["ext"];
                    $attachID = $attlist["attachID"];
                    $attachType_ = $attlist["attachType"];
                    $verifyID = $attlist["verifyID"];
                    $name = $attlist["name"];
                    $size = $attlist["size"];
                }


                if ($ext == "DOCX" || $ext == "WPS") {
                    $ext = "doc";
                }
                if ($ext == "XLSX") {
                    $ext = "xls";
                }
                if ($ext == "PPTX") {
                    $ext = "ppt";
                }
                if ($type != '1') {
                    $downloadurl = '/doc/temporary.action';
                } else {
                    $downloadurl = '/doc/download.action';
                }
                if ($ext == "jpeg" || $ext == "png" || $ext == "jpg" || $ext == "gif" || $ext == "GIF" || $ext == "JPG" || $ext == "PNG" || $ext == "JPEG") {
                    $str .= '<li><img width="125px" height="125px"  layer-src=' . $this->view->seed_Setting['KyUrlex'];
                    $str .= $downloadurl . '?sid=' . session_id();
                    $str .= '&nid=' . $attachID;
                    $str .= '&vid=' . $verifyID;
                    $str .= ' src=' . $this->view->seed_Setting['KyUrlex'];
                    $str .= $downloadurl . '?sid=' . session_id();
                    $str .= '&nid=' . $attachID;
                    if ($type == '1') {
                        $str .= '&vid=' . $verifyID;
                    }
                    $str .= '&size=MIDDLE name=' . $name;
                    $str .= 'alt=' . $attachType_ . '><span class="del_to">' . $name . '<br><a onclick="view_pic(this)">查看</a>';
                    if ($type != '1') {
                        $str .= '|<a onclick="delete_pic(this)">删除</a></span>';
                    }
                } else {
                    $str .= '<li>';
                    $str .= '<img width="125px" height="125px" src="/ky/ico/' . strtolower($ext) . '.png" alt=' . $attachType_ . ' />';
                    $str .= '<span class="del_to">' . $name . '<br>';
                    $str .= '<a href="'.$this->view->seed_Setting['KyUrlex'] . '/doc/download.action?sid=' . session_id() . '&nid=' . $attachID. '&vid=' . $verifyID . '" download>下载</a>&nbsp;&nbsp;';
                    $str .= '<a onclick="delete_pic(this)">删除</a>';
                    $str .= '</span>';
                }

                $str .= '<input type="hidden" name="attachNid[]" value="' . $attachID . '" />';
                if ($type == '1') {
                    $str .= '<input type="hidden" name="attachVid[]" value="' . $verifyID . '" />';
                }
                $str .= '<input type="hidden" name="attachType[]" value="' . $attachType_ . '" />';
                $str .= '<input type="hidden" name="attachName[]" value="' . $name . '" />';
                $str .= '<input type="hidden" name="attachSize[]" value="' . $size . '" /></li>';
            }
        } else {
            $str = "";
        }

        $showView = "";
        $showView .= '<div class="img-read" id="img-read">';
        $showView .= '</div><div class="demo"><ul class="img-view">' . $str;
        $showView .= '</ul></div><script type="text/javascript" src="/ky/upload/img.js"></script>';
        $showView .= '<script type="text/javascript" src="/ky/layer-v3.1.1/layer.js"></script>';
        $showView .= '<link href="/ky/css/img-upload.css" rel="stylesheet">';

        $showimg = "";
        $showimg .= '<div class="img-read" id="img-read"></div>';
        $showimg .= '<div class="demo">';
        $showimg .= '<ul class="img-view">' . $str . '</ul>';

        $showimg .= '<ul class="upload-image-list clearfix">';
        $showimg .= '<div class="fileinput-button js-add-image" onclick="addWebuploadCurrent($(this))" attachType_="'.$attachType.'">' . $indata;
        $showimg .= '<span class="cover_words"></span>';
        $showimg .= '<div class="webuploader-pick">';
        $showimg .= '<a class="fileinput-button-icon" href="javascript:;"></a>';
        $showimg .= '</div></div></ul>';

        $showimg .= '</div>';
        $showimg .= '<script type="text/javascript" src="/ky/upload/img.js"></script>';
        $showimg .= '<link href="/ky/css/img-upload.css" rel="stylesheet">';

        if ($type == '1') {
            return $showView;
        } else {
            return $showimg;
        }
	}
}
