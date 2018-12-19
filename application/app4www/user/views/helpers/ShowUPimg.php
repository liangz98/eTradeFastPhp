<?php

class Zend_View_Helper_ShowUPimg extends Shop_View_Helper {
    function ShowUPimg($attArr) {
        //$attArr 服务端返回附件信息数组；
        $str = "";
        if (is_array($attArr) && count($attArr) > 0) {
            foreach ($attArr as $k => $attlist) {
                $ext = "";
                $attachID = "";
                $verifyID = "";
                $name = "";
                $attachType_ = "";
                // $size = "";

                if (is_object($attlist)) {
                    $ext = $attlist->ext;
                    $attachID = $attlist->attachID;
                    $attachType_ = $attlist->attachType;
                    $verifyID = $attlist->verifyID;
                    $name = $attlist->name;
                    // $size = $attlist->size;
                } else if (is_array($attlist)) {
                    $ext = $attlist["ext"];
                    $attachID = $attlist["attachID"];
                    $attachType_ = $attlist["attachType"];
                    $verifyID = $attlist["verifyID"];
                    $name = $attlist["name"];
                    // $size = $attlist["size"];
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

                $downloadURL = $this->view->seed_Setting['KyUrlex'] . '/doc/download.action';

                $str .= '<li style="cursor: pointer;">';
                if ($ext == "jpeg" || $ext == "png" || $ext == "jpg" || $ext == "gif" || $ext == "GIF" || $ext == "JPG" || $ext == "PNG" || $ext == "JPEG") {
                    $str .= '<img width="125px" height="125px" ';
                    $str .= 'data-original="' . $downloadURL . '?sid=' . session_id().'&nid=' . $attachID.'&vid=' . $verifyID.'" ';
                    $str .= 'src="' . $downloadURL . '?sid=' . session_id(). '&nid=' . $attachID . '&vid=' . $verifyID . '&size=MIDDLE" ';
                    $str .= 'name="' . $name . '" alt="' . $attachType_ . '" >';
                } elseif ($ext == "doc" || $ext == "xls" || $ext == "ppt") {
                    $str .= '<img width="125px" height="125px" ';
                    $str .= 'src="/ky/ico/' . strtolower($ext) . '.png" ';
                    $str .= 'name="' . $name . '" alt="' . $attachType_ . '" >';
                } else {
                    $str .= '<img width="125px" height="125px" ';
                    $str .= 'src="/ky/ico/video.png" ';
                    $str .= 'name="' . $name . '" alt="' . $attachType_ . '" >';
                }
                $str .= '</li>';
            }
        }

        $resultImg = '<ul class="showImgView">' . $str . '</ul>';

        return $resultImg;
    }
}
