<?php

class Zend_View_Helper_ShowUPimg extends Shop_View_Helper {
    function ShowUPimg($attArr) {
        //$attArr 服务端返回附件信息数组；
        $str = "";
        $videoStr = "";
        $hasHide = 0;
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

                if ($k < 7) {
                    $str .= '<li style="cursor: pointer;">';
                } else {
                    $hasHide = 1;
                    $str .= '<li class="imgLi hidden" style="cursor: pointer;">';
                }

                if ($ext == "jpeg" || $ext == "png" || $ext == "jpg" || $ext == "gif" || $ext == "GIF" || $ext == "JPG" || $ext == "PNG" || $ext == "JPEG") {
                    $str .= '<img width="125px" height="125px" ';
                    $str .= 'data-original="' . $downloadURL . '?sid=' . session_id().'&nid=' . $attachID.'&vid=' . $verifyID.'" ';
                    $str .= 'src="' . $downloadURL . '?sid=' . session_id(). '&nid=' . $attachID . '&vid=' . $verifyID . '&size=MIDDLE" ';
                    $str .= 'name="' . $name . '_'.$k.'" alt="' . $attachType_ . '" >';
                } elseif ($ext == "doc" || $ext == "xls" || $ext == "ppt") {
                    $str .= '<img width="125px" height="125px" ';
                    $str .= 'src="/ky/ico/' . strtolower($ext) . '.png" ';
                    $str .= 'name="' . $name . '" alt="' . $attachType_ . '" >';
                } else {
                    $videoStr .= '<li>';
                    $videoStr .= '<video width="480px" height="360px" controls poster="video-background-image" src="' . $downloadURL . '?sid=' . session_id().'&nid=' . $attachID.'&vid=' . $verifyID. '" data-overlay="1" data-title="' . $name . '"></video>';
                    $videoStr .= '</li>';

                    // $str .= '<div class="flowplayer" data-swf="flowplayer.swf" data-ratio="0.4167">';
                    // $str .= '<video>';
                    // $str .= '<source type="video/mp4" src="' . $downloadURL . '?sid=' . session_id().'&nid=' . $attachID.'&vid=' . $verifyID. '">';
                    // $str .= '</video>';
                    // $str .= '</div>';

                }
                $str .= '</li>';
            }
        }

        $resultImg = '<ul class="img-view">' . $str . '</ul>';

        $resultImg .= '<div class="col-sm-12"><ul class="img-view">' . $videoStr . '</ul></div>';

        if ($hasHide) {
            $resultImg .= '<div class="col-sm-3 col-sm-offset-5"><button type="button" class="btn btn-default btn-xs showImgAll" data-type="down">展开全部 <i class="fas fa-chevron-down"></i></button></div>';
        }

        return $resultImg;
    }
}
