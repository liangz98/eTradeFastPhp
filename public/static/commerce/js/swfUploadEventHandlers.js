/* **********************
 处理swfUpload的各种事件
 ********************** */

function swfCreateInstance(option) {
    if (flashDetect()) { // 探测到已经安装了flash插件 则初始化上传按钮和提示
        // 初始化swfUpload组件
        var setting = {
            // File Upload Settings
            upload_url: "/uploadapi/image/cmupload",
            flash_url: "/static/commerce/js/swfUpload/Flash/swfupload.swf",
            file_size_limit: "1024K",
            file_queue_limit: 6,
            file_upload_limit: 6,
            file_types: "*.jpg;*.png;*.jpeg;",
            file_types_description: "图片格式",
            file_post_name: "Filedata",

            // The event handler functions are defined in swfUploadEventHandlers.js
            file_dialog_start_handler: fileDialogStart,
            file_queued_handler: fileQueued,
            file_queue_error_handler: fileQueueError,
            file_dialog_complete_handler: fileDialogComplete,
            upload_start_handler: uploadStart,
            upload_progress_handler: uploadProgress,
            upload_error_handler: uploadError,
            upload_success_handler: uploadSuccess,
            upload_complete_handler: uploadComplete,
            queue_complete_handler: queueComplete,

            // Button Settings
            button_image_url: "/static/commerce/css/i/up_btn.png",
            button_placeholder_id: "swfUploadButton",
            button_width: 87,
            button_height: 31,
            button_text: '',
            button_text_style: '',
            button_text_top_padding: 0,
            button_text_left_padding: 0,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND,
            
            // custom Settings
            custom_settings: {
                cate_name: 'goodsalbum'
            }
        };
        if(typeof option == 'undefined') { option = {}; }
        jQuery.extend(setting, option);
        window.swfUpload = new SWFUpload(setting);
        $(".ifile").append('<span id="uoload_tip" class="upload-tip">一次可选' + setting.file_upload_limit + '张图片哦～</span>');
    } else { // 探测到没有flash支持，给出提示。
        $(".ifile").html('<span class="no-flash-tip">' +
            'Hi，您的浏览器OUT了，它未安装新版的Flash Player，' +
            '<a href="http://get.adobe.com/flashplayer/" target="_blank">去安装>></a>' +
            '</span>');
    }
};

/**
 * 显示图片上传错误
 * @param file 出错的文件引用
 * @param errorCode 错误码
 */
function showUploadError(file, errorCode) {
    var errorMsg = {
        "upload.file.too.big": "文件过大",
        "upload.invalid.file.type": "类型不符",
        "upload.invalid.size": "尺寸不对",
        "seed.publish.exception": "网络不给力",
        "upload.param.name.error": "分类错误",
        "seed.user.nologin": "未登录"
    };
    var imgBox = $('.g-imgs.open .p-img[fileId=' + file.id + ']');
    if ($.isEmptyObject(imgBox) || imgBox.size() != 1) {
        // 没有对应位置的时候 需不需要提示，如何提示
        // alert(errorMsg[errorCode])
    } else {
        imgBox.parent("li").removeClass("waiting").addClass("error") // 移除上传状态
            .find(".error-txt").remove().end() // 移除以前可能存在的错误提示
            .prepend('<div class="error-txt">' + errorMsg[errorCode] + '</div>')
            .find(".progress").remove();
    }
}

/**
 * 记录已经入队的文件文件引用
 * 每个文件入队，push进来
 * 每个文件上传成功，pop出去
 * 如果入队文件数大于空格数，则将队列中所有文件cancelUpload。并清空该队列
 * @type {Array}
 */
var queuedFiles = [];

/**
 * 打开文件选择窗口 不做事情
 */
function fileDialogStart() {
    // 将占位属性清除
    $(".g-imgs.open .p-img").removeAttr("fileId");
}

/**
 * 文件入队事件处理
 * @param file
 */
function fileQueued(file) {
    try {
        // 将已经入队的文件记录下来
        queuedFiles.push(file);

        // 找到本文件将要显示的位置，使用fileId占位
        $('.g-imgs.open .p-img:empty').each(function (i, o) {
            if (!$(this).attr("fileId")) {
                $(this).attr("fileId", file.id);
                return false;
            }
        });
    } catch (ex) {
        this.debug(ex);
    }

}

/**
 * 文件入队错误事件处理
 * @param file
 * @param errorCode
 * @param message
 */
function fileQueueError(file, errorCode, message) {

    if ( !file) {return false;}
    // 找到本文件将要显示的位置，使用fileId占位
    $('.g-imgs.open .p-img:empty').each(function (i, o) {
        if (!$(this).attr("fileId")) {
            $(this).attr("fileId", file.id);
            return false;
        }
    });

    try {
        switch (errorCode) {
            case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                showUploadError(file, "upload.file.too.big");
                break;
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                showUploadError(file, "upload.file.too.big");
                break;
            case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                showUploadError(file, "upload.invalid.file.type");
                break;
            default:
                showUploadError(file, "seed.publish.exception");
                break;
        }
    } catch (ex) {
        this.debug(ex);
        showUploadError(file, "seed.publish.exception");
    }
}

/**
 * 文件选择框关闭
 * @param numFilesSelected
 * @param numFilesQueued
 */
function fileDialogComplete(numFilesSelected, numFilesQueued) {
    try {
        var numSpace = (this.settings.file_upload_limit - $('.g-imgs.open .p-img img').size());
        if (numFilesSelected > 0 && numSpace < numFilesSelected) { // 选了大于空位个数的文件。取消本次上传动作，并提示
            // 将已经入队的文件取消上传
            for (var f in queuedFiles) {
                this.cancelUpload(f.id, false);
            }
            queuedFiles = [];
            // 将占位属性清除
            $(".g-imgs.open .p-img").removeAttr("fileId");
            // 给出提示
            alert("最多只能添加"+this.settings.file_upload_limit+"张图片哦");
        } else {
            // 自动开始上传
            this.startUpload();
        }
    } catch (ex) {
        this.debug(ex);
    }
}

/**
 * 开始上传
 * @param file
 * @returns {boolean}
 */
function uploadStart(file) {
    try {
        // 对文件名称进行编码
        this.addPostParam("name", encodeURIComponent(this.customSettings.cate_name));
        // 兼容firefox 将登陆验证的cookie带上
        this.addPostParam("token", $("#cookieValue").val());
        
        // 显示上传进度条
        var imgBox = $('.g-imgs.open .p-img[fileId=' + file.id + ']');
        imgBox.parent("li")
            .removeClass("error").find(".error-txt").remove().end() // 移除上一次的错误提示
            .addClass("waiting")
            .find(".progress").remove().end() // 移除以前可能存在的进度条
            .append('<div class="progress"><div style="width:0%;" class="per-bar"></div><div class="per-cent">0%</div></div>');
    }
    catch (ex) {
        this.debug(ex);
        showUploadError(file, "seed.publish.exception");
        return false;
    }

    return true;
}

/**
 * 上传过程中
 * @param file
 * @param bytesLoaded
 * @param bytesTotal
 */
function uploadProgress(file, bytesLoaded, bytesTotal) {
    try {
        // 更新上传进度
        var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
        var imgBox = $('.g-imgs.open .p-img[fileId=' + file.id + ']');
        imgBox.parent("li").find(".progress").find(".per-bar").css("width", percent + "%").end()
            .find(".per-cent").text(percent + "%");
    } catch (ex) {
        this.debug(ex);
    }
}

/**
 * 上传成功
 * @param file
 * @param serverData
 */
function uploadSuccess(file, serverData) {
    var imgBox = $('.g-imgs.open .p-img[fileId=' + file.id + ']');
    imgBox.empty();
    try {
        // 删除进度条，显示图片
        serverData = $.parseJSON(serverData);
        if (serverData.success) {
            var img = $('<img imgId="' + serverData.data.imgId + '" ' +
                'imgUrl="' + serverData.data.imgUrl + '" ' +
                'nolImgUrl="' + serverData.data.nolImgUrl + '" ' +
                'orlImgUrl="' + serverData.data.orlImgUrl + '" ' +
                'onerror="loadImgError(this)"' +
                'src="' + "http://" + window.location.host + "/upload_files" + serverData.data.imgUrl + '"/>').hide();
            imgBox.removeAttr("fileId").append(img);
            imgBox.parent("li").find(".progress").fadeOut(600, function () {
                $(this).remove();
            });
            img.fadeIn(1000, function () {
                imgBox.parent("li").removeClass("waiting");
            });
        } else {
            showUploadError(file, serverData.errorCode);
        }
    } catch (ex) {
        this.debug(ex);
        showUploadError(file, "seed.publish.exception");
    }
}

/**
 * 上传错误
 * @param file
 * @param errorCode
 * @param message
 */
function uploadError(file, errorCode, message) {
    if ( !file) {return false;}
    var imgBox = $('.g-imgs.open .p-img[fileId=' + file.id + ']');
    imgBox.empty();
    showUploadError(file, "seed.publish.exception");
}

/**
 * 上传完成
 * @param file
 */
function uploadComplete(file) {
}

/**
 * 整个文件队列上传完成
 * @param numFilesUploaded
 */
function queueComplete(numFilesUploaded) {
    queuedFiles = [];
    $(".g-imgs.open .p-img").removeAttr("fileId");
}

/**
 * 探测浏览器是否支持flash插件，当无法探测时，返回true
 * @returns {boolean}
 */
function flashDetect() {
    if (navigator.mimeTypes.length > 0) {
        return navigator.mimeTypes["application/x-shockwave-flash"] && navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin;
    } else if (window.ActiveXObject) { // IE
        try {
            new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
            return true;
        } catch (oError) {
            return false;
        }
    } else {
        // no way to detect
        return true;
    }
}
