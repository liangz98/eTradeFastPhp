<?php

class Zend_View_Helper_ShowWebuploader extends Shop_View_Helper {

    /**
     * @param $attachmentList // 附件数组
     * @param $bizType // 业务类型
     * @param $attachType // 附件类型
     * @param $operation // 操作类型[1:正式, 0:临时]
     * @param $bizID // 业务ID
     * @return string
     */
    function ShowWebuploader($attachmentList, $bizType, $attachType, $operation, $bizID) {
        $str = "";

        if (empty($operation)) {
            $operation = 0;
        }

        $initData = "";
        $initData .= '<input type="hidden" id="bizType" value="' . $bizType . '" />';
        $initData .= '<input type="hidden" id="bizID" value="' . $bizID . '" />';
        $initData .= '<input type="hidden" id="attachType" value="' . $attachType . '" />';
        $initData .= '<input type="hidden" id="uploadURL" value="' . $this->view->seed_Setting['KyUrlex'] . '" />';
        $initData .= '<input type="hidden" id="typeURL" value="' . $operation . '" />';
        $initData .= '<input type="hidden" id="sid" value="' . session_id() . '" />';

        if (!empty($attachmentList) && is_array($attachmentList) && count($attachmentList) > 0) {
            foreach ($attachmentList as $k => $attachment) {
                $ext = "";
                $attachID = "";
                $verifyID = "";
                $name = "";
                $attachType_ = "";
                $size = "";

                if (is_object($attachment)) {
                    $ext = $attachment->ext;
                    $attachID = $attachment->attachID;
                    $attachType_ = $attachment->attachType;
                    $verifyID = $attachment->verifyID;
                    $name = $attachment->name;
                    $size = $attachment->size;
                } else if (is_array($attachment)) {
                    $ext = $attachment["ext"];
                    $attachID = $attachment["attachID"];
                    $attachType_ = $attachment["attachType"];
                    $verifyID = $attachment["verifyID"];
                    $name = $attachment["name"];
                    $size = $attachment["size"];
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

                $downloadURL = $this->view->seed_Setting['KyUrlex'] . '/doc/download.action';

                $str .= '<li>';

                if ($ext == "jpeg" || $ext == "png" || $ext == "jpg" || $ext == "gif" || $ext == "GIF" || $ext == "JPG" || $ext == "PNG" || $ext == "JPEG") {
                    $str .= '<a class="grouped_elements" href="' . $downloadURL . '?sid=' . session_id() . '&nid=' . $attachID . '&vid=' . $verifyID . '" ';
                    $str .= 'data-fancybox-class="gallery" ';
                    $str .= 'data-fancybox="' . $bizID . '" ';
                    $str .= 'data-caption="' . $name . '"> ';
                    $str .= '<img class="img_common" src="' . $downloadURL . '?sid=' . session_id() . '&nid=' . $attachID . '&vid=' . $verifyID . '&size=MIDDLE" alt="" onerror="javascript:this.src=\'/ky/ico/other.png\';" />';
                    $str .= '</a>';

                    $str .= '<span class="del_to">';
                    if ($operation != '1') {
                        // $str .= mb_substr($name, 0, 7, 'utf-8') . '...' . '<br>';
                        $str .= '<a onclick="delete_pic(this)"><i class="far fa-trash-alt"></i></a>';
                    } else {
                        $str .= $name;
                    }
                    $str .= '</span>';
                } else if ($ext == "doc" || $ext == "xls" || $ext == "ppt" || strtolower($ext) == "pdf") {
                    $pdfUrl = $downloadURL . '?sid=' . session_id() . '&nid=' . $attachID . '&vid=' . $verifyID;

                    $str .= '<img class="img_common" src="/ky/ico/' . strtolower($ext) . '.png" alt=' . $attachType_ . ' data-type="' . strtolower($ext) . '" onclick="initPdfView(\'' . $pdfUrl . '\', this, \'pdf_view\')" onerror="javascript:this.src=\'/ky/ico/other.png\';" />';
                    $str .= '<span class="del_file_to">';
                    if (mb_strlen($name, 'utf-8') > 8) {
                        $str .= mb_substr($name, 0, 7, 'utf-8') . '...';
                    } else {
                        $str .= $name;
                    }
                    $str .= '<br>';
                    $str .= '<a href="' . $this->view->seed_Setting['KyUrlex'] . '/doc/download.action?sid=' . session_id() . '&nid=' . $attachID . '&vid=' . $verifyID . '" data-type="download" download><i class="fas fa-download"></i></a>&nbsp;&nbsp;&nbsp;';
                    if ($operation != '1') {
                        $str .= '<a onclick="delete_pic(this)" data-type="del"><i class="far fa-trash-alt"></i></a>';
                    }
                    $str .= '</span>';
                } else {
                    $attachUrl = $downloadURL . '?sid=' . session_id() . '&nid=' . $attachID . '&vid=' . $verifyID;
                    $str .= '<img class="img_common" src="/ky/ico/' . strtolower($ext) . '.png" alt=' . $attachType_ . ' data-type="' . strtolower($ext) . '" onerror="javascript:this.src=\'/ky/ico/other.png\';" />';
                    $str .= '<span class="del_to">';
                    if (mb_strlen($name, 'utf-8') > 8) {
                        $str .= mb_substr($name, 0, 7, 'utf-8') . '...';
                    } else {
                        $str .= $name;
                    }
                    $str .= '<br>';
                    $str .= '<a href="' . $attachUrl . '" data-type="download" download><i class="fas fa-download"></i></a>&nbsp;&nbsp;&nbsp;';
                    $str .= '<a onclick="delete_pic(this)" data-type="del"><i class="far fa-trash-alt"></i></a>';
                    $str .= '</span>';
                }

                $str .= '<input type="hidden" name="attachNid[]" value="' . $attachID . '" />';
                if ($operation == '1') {
                    $str .= '<input type="hidden" name="attachVid[]" value="' . $verifyID . '" />';
                }
                $str .= '<input type="hidden" name="attachType[]" value="' . $attachType_ . '" />';
                $str .= '<input type="hidden" name="attachName[]" value="' . $name . '" />';
                $str .= '<input type="hidden" name="attachSize[]" value="' . $size . '" /><input type="hidden" name="attachBizID[]" value="' . $bizID . '" /></li>';
            }
        } else {
            $str = "";
        }

        $showImage = "";
        $showImage .= '<div class="img-read" id="img-read"></div>';
        $showImage .= '<div class="demo">';

        $showImage .= '<ul class="img-view">' . $str . '</ul>';
        $showImage .= '<ul class="upload-image-list clearfix">';
        $showImage .= '<div class="fileinput-button js-add-image" onclick="addWebuploadCurrent($(this))" attachType_="' . $attachType . '" data-biz-id="' . $bizID . '">';
        $showImage .= $initData;
        $showImage .= '</div>';
        $showImage .= '</ul>';
        $showImage .= '</div>';

        return $showImage;
    }
}
