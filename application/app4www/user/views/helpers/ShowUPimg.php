<?php

class Zend_View_Helper_ShowUPimg extends Shop_View_Helper {
    function ShowUPimg($attArr, $objID) {
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
                    $ext = "word";
                }
                if ($ext == "XLSX") {
                    $ext = "xls";
                }
                if ($ext == "PPTX") {
                    $ext = "ppt";
                }
                if ($ext == "PDF") {
                    $ext = "pdf";
                }

                $downloadURL = $this->view->seed_Setting['KyUrlex'] . '/doc/download.action';

                if ($k < 7) {
                    $str .= '<li>';
                } else {
                    $hasHide = 1;
                    $str .= '<li class="imgLi hidden">';
                }

                if ($ext == "jpeg" || $ext == "png" || $ext == "jpg" || $ext == "gif" || $ext == "GIF" || $ext == "JPG" || $ext == "PNG" || $ext == "JPEG") {
                    //
                    $str .= '<a class="grouped_elements" href="' . $downloadURL . '?sid=' . session_id().'&nid=' . $attachID.'&vid=' . $verifyID.'" ';
                    $str .= 'data-fancybox-class="gallery'.$objID.'" ';
                    $str .= 'data-fancybox="' . $objID . '" ';
                    $str .= 'data-caption="'. $name . '"> ';
                    $str .= '<img class="img_common" src="' . $downloadURL . '?sid=' . session_id(). '&nid=' . $attachID . '&vid=' . $verifyID . '&size=MIDDLE" alt="" />';
                    $str .= '</a>';
                } elseif ($ext == "doc" || $ext == "xls" || $ext == "ppt" || $ext == "pdf") {
                    $pdfUrl = $downloadURL. '?sid=' . session_id() . '&nid=' . $attachID . '&vid=' . $verifyID;

                    $str .= '<input type="hidden" id="contractAttachUrl_'.$attachID.'" value="' . $pdfUrl . '">';
                    $str .= '<img id="' . $attachID . '" class="img_common" src="/ky/ico/' . strtolower($ext) . '.png" alt=' . $attachType_ . ' data-type="' . strtolower($ext) . '" onclick="initPdfView(\'' . $pdfUrl . '\', this, \'show_up_img\')" />';
                    $str .= '<span class="del_to">';
                    if (mb_strlen($name, 'utf-8') > 8) {
                        $str .= mb_substr($name,0,7, 'utf-8') . '...';
                    } else {
                        $str .= $name;
                    }
                } else {
                    $str .= '<a class="grouped_elements" data-fancybox href="#' . $attachID . '" ';
                    $str .= 'data-fancybox-class="gallery'.$objID.'" ';
                    $str .= 'data-fancybox="' . $objID . '" ';
                    $str .= 'data-caption="'. $name . '"> ';
                    // $str .= '<img src="/ky/images/video-circle.png" alt="" style="height: 64px; width: 64px; margin-top: 40px;" />';
                    $str .= '<div style="background-image:url(/ky/images/video-circle.png); background-size: 64px 64px; background-repeat: no-repeat; background-position-x:center; background-position-y:center; height: 90px; width: 120px; float: left;" ></div>';
                    $str .= '</a>';

                    $str .= '<video controls id="' . $attachID . '" style="display:none;">';
                    $str .= '<source src="' . $downloadURL . '?sid=' . session_id().'&nid=' . $attachID . '&vid=' . $verifyID . '" type="video/mp4">';
                    $str .= 'Your browser doesn\'t support HTML5 video tag.';
                    $str .= '</video>';
                }
                $str .= '</li>';
            }
        }

        $classNameStr = '';
        if (!empty($objID)) {
            $classNameStr = $objID;
        }
        $resultImg = '<ul class="img-view imgContainerCls_'.$classNameStr.'" id="local_image_container" data-cls-name="imgContainerCls_' . $classNameStr . '">' . $str . '</ul>';

        if ($hasHide) {
            $resultImg .= '<div class="col-sm-3 col-sm-offset-5"><button type="button" class="btn btn-default btn-xs showImgAll" data-type="down">展开全部 <i class="fas fa-chevron-down"></i></button></div>';
        }

        return $resultImg;
    }
}
