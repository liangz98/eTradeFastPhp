<?php

class Zend_View_Helper_ShowWebuploader extends Shop_View_Helper {

    function ShowWebuploader($attArr, $bizType, $attachType, $type, $bizID) {
		//$attArr 服务端返回附件信息数组；
        //$bizID   绑定业务ID->bizID
		//$bizType 业务类型
		//$attachType  附件类型
		//$type  是否临时附件 1 正式  0临时
		$str = "";

        $indata = "";
        $indata .= '<input type="hidden" id="bizType" value="' . $bizType . '" />';
        $indata .= '<input type="hidden" id="bizID" value="' . $bizID . '" />';
        $indata .= '<input type="hidden" id="attachType" value="' . $attachType . '" />';
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
                // if ($type != '1') {
                //     $downloadURL = '/doc/temporary.action';
                // } else {
                //     $downloadURL = '/doc/download.action';
                // }

                $downloadURL = '/doc/download.action';

                if ($ext == "jpeg" || $ext == "png" || $ext == "jpg" || $ext == "gif" || $ext == "GIF" || $ext == "JPG" || $ext == "PNG" || $ext == "JPEG") {
                    $str .= '<li><img width="125px" height="125px"  data-original="' . $this->view->seed_Setting['KyUrlex'];
                    $str .= $downloadURL . '?sid=' . session_id();
                    $str .= '&nid=' . $attachID;
                    $str .= '&vid=' . $verifyID;
                    $str .= '" src="' . $this->view->seed_Setting['KyUrlex'];
                    $str .= $downloadURL . '?sid=' . session_id();
                    $str .= '&nid=' . $attachID;
                    // if ($type == '1') {
                    //     $str .= '&vid=' . $verifyID;
                    // }
                    $str .= '&vid=' . $verifyID;
                    $str .= '&size=MIDDLE" name="' . $name. '" ';
                    $str .= 'alt="' . $name . '">';

                    $str .= '<span class="del_to">';
                    if ($type != '1') {
                        $str .= mb_substr($name,0,7, 'utf-8') . '...'  . '<br>';
                        $str .= '<a onclick="delete_pic(this)"><i class="far fa-trash-alt"></i></a>';
                    } else {
                        $str .= $name;
                    }
                    $str .= '</span>';
                } else {
                    $str .= '<li>';
                    $str .= '<img width="125px" height="125px" src="/ky/ico/' . strtolower($ext) . '.png" alt=' . $attachType_ . ' />';
                    $str .= '<span class="del_to">';
                    if (mb_strlen($name, 'utf-8') > 16) {
                        $str .= mb_substr($name,0,7, 'utf-8') . '...';
                    } else {
                        $str .= $name;
                    }
                    $str .= '<br>';
                    $str .= '<a href="'.$this->view->seed_Setting['KyUrlex'] . '/doc/download.action?sid=' . session_id() . '&nid=' . $attachID. '&vid=' . $verifyID . '" download><i class="fas fa-download"></i></a>&nbsp;&nbsp;&nbsp;';
                    $str .= '<a onclick="delete_pic(this)"><i class="far fa-trash-alt"></i></a>';
                    $str .= '</span>';
                }

                $str .= '<input type="hidden" name="attachNid[]" value="' . $attachID . '" />';
                if ($type == '1') {
                    $str .= '<input type="hidden" name="attachVid[]" value="' . $verifyID . '" />';
                }
                $str .= '<input type="hidden" name="attachType[]" value="' . $attachType_ . '" />';
                $str .= '<input type="hidden" name="attachName[]" value="' . $name . '" />';
                $str .= '<input type="hidden" name="attachSize[]" value="' . $size . '" /><input type="hidden" name="attachBizID[]" value="' . $bizID . '" /></li>';
            }
        } else {
            $str = "";
        }

        $showView = "";
        $showView .= '<div class="img-read" id="img-read">';
        $showView .= '</div><div class="demo"><ul class="img-view">' . $str;
        $showView .= '</ul></div>';
        // $showView .= '<script type="text/javascript" src="/ky/upload/img.js"></script>';
        // $showView .= '<script type="text/javascript" src="/ky/layer-v3.1.1/layer.js"></script>';
        // $showView .= '<link href="/ky/css/img-upload.css" rel="stylesheet">';

        $showimg = "";
        $showimg .= '<div class="img-read" id="img-read"></div>';
        $showimg .= '<div class="demo">';
        $showimg .= '<ul class="img-view">' . $str . '</ul>';

        $showimg .= '<ul class="upload-image-list clearfix">';
        $showimg .= '<div class="fileinput-button js-add-image" onclick="addWebuploadCurrent($(this))" attachType_="'.$attachType.'" data-biz-id="'. $bizID .'">' . $indata;
        $showimg .= '<span class="cover_words"></span>';
        $showimg .= '<div class="webuploader-pick">';
        $showimg .= '<a class="fileinput-button-icon" href="javascript:;"></a>';
        $showimg .= '</div></div></ul>';

        $showimg .= '</div>';
        // $showimg .= '<script type="text/javascript" src="/ky/upload/img.js"></script>';
        // $showimg .= '<link href="/ky/css/img-upload.css" rel="stylesheet">';

        // if ($type == '1') {
        //     return $showView;
        // } else {
        //     return $showimg;
        // }

        return $showimg;
	}
}
