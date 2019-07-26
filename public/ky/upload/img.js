var bizID = '',
    attachType = '',
    bizType = '',
    baseUrl = '',
    uploadUrl = '',
    downloadUrl = '',
    operation = '', // 1:正式, 0:临时
    sid = '';

$(function() {
    webupload_pic();
});

function webupload_pic() {
    $.getScript("../../ky/upload/Public/js/plugins/webuploader/webuploader.js", function() {
        if (!WebUploader.Uploader.support()) {
            alert('您的浏览器不支持上传功能！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        }

        var allMaxSize = 50,
            singleMaxSize = 40;
        var uploader = WebUploader.create({
            multiple: false,
            compress: false,
            auto: true,
            swf: "/ky/upload/Public/js/plugins/webuploader/Uploader.swf",
            // server: uploadUrl,
            pick: {
                id: '.js-add-image',
                innerHTML: ''
            },
            fileNumLimit: 20,   // 上传数量限制
            fileSizeLimit: allMaxSize * 1024 * 1024,            // 限制大小50M，所有被选文件，超出选择不上
            fileSingleSizeLimit: singleMaxSize * 1024 * 1024,   // 限制大小16M，单文件
            duplicate: true,
            //配置生成缩略图的选项
            thumb: {
                width: 120,
                height: 90,
                // 图片质量，只有type为`image/jpeg`的时候才有效。
                quality: 100,
                // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                allowMagnify: false,
                // 是否允许裁剪。
                crop: true,
                // 为空的话则保留原有图片格式。
                // 否则强制转换成指定的类型。
                type: "image/jpeg"
            },
            formData: {
                sid: sid,
                bizID: bizID,
                bizType: bizType,
                type: attachType
            }
        });

        // 当一批文件添加进队列以后触发, 上传时
        uploader.on('fileQueued', function (file) {
            if (operation !== undefined && operation !== '' && operation === '1') {
                uploadUrl = baseUrl + "/doc/uploadAttach.action";
                downloadUrl = baseUrl + "/doc/download.action";
            } else {
                uploadUrl = baseUrl + "/doc/upload.action";
                downloadUrl = baseUrl + "/doc/temporary.action";
            }
            uploader.option('server', uploadUrl);
            uploader.option('formData', {
                sid: sid,
                bizID: bizID,
                bizType: bizType,
                type: attachType
                // name: file['name']
                // file: file
            });

            var fileExt = file.ext;
            if (fileExt === "docx" || fileExt === "wps") {
                fileExt = "word";
            }
            if (fileExt === "xlsx") {
                fileExt = "xls";
            }
            if (fileExt === "pptx") {
                fileExt = "ppt";
            }

            var progressStr = '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 1%; ">1%</div></div>';
            uploader.makeThumb( file, function( error, dataSrc ) {
                if ( !error ) {
                    $(".img-view").append('<div id="' + file['id'] + '" ><img src="' + dataSrc + '" onerror="javascript:this.src=\'/ky/ico/other.png\';"><div class="progressing" >' + progressStr + '</div>');
                } else {
                    $(".img-view").append('<div id="' + file['id'] + '" ><img class="img_common" src="/ky/ico/' + fileExt + '.png" onerror=\'javascript:this.src="/ky/ico/other.png";\'><div class="progressing" >' + progressStr + '</div>');
                }
            });
        });
        // 上传中
        uploader.on('uploadProgress', function(file, percentage) {
            console.log(percentage);
            // 上传进度
            var $progressBar = $("#" + file.id).find(".progress-bar");
            $progressBar.css("width", percentage * 100 + "%");
            $progressBar.html(percentage * 100 + "%");
        });

		// 上传前
        uploader.on('uploadBeforeSend', function(block, data) {

        });

        //上传成功后
        uploader.on('uploadSuccess', function(file, response) {
            if (response.responseCode === 'success') {
                var nid = '',
                    vid = '',
                    middleUrl = '',
                    fullUrl = '',
                    name = file.name,
                    size = file.size,
                    type = file.ext;

                if (operation !== undefined && operation !== '' && operation === '1') {
                    var attachment = response.attachment;
                    downloadUrl = baseUrl + "/doc/download.action?sid=" + sid ;
                    nid = '&nid=' + attachment.attachID;
                    vid = '&vid=' + attachment.verifyID;
                } else {
                    downloadUrl = baseUrl + "/doc/temporary.action?sid=" + sid ;
                    nid = '&nid=' +  response.nid;
                }

                middleUrl = downloadUrl + '&size=MIDDLE&' + nid + vid;
                fullUrl = downloadUrl + nid + vid;
                if (type === "docx" || type === "wps") {
                    type = "word";
                }
                if (type === "xlsx") {
                    type = "xls";
                }
                if (type === "pptx") {
                    type = "ppt";
                }
                $("#" + file.id).remove();
                var CROD = $(".webupload_current").parent().find('#CROD').val();
                var attach_new = "";
                if (CROD === "CRSE") {
                    attach_new = "CRSE";
                } else if (CROD === "ODSE") {
                    attach_new = "ODSE"
                } else {
                    attach_new = attachType;
                }

                if (name !== undefined && name !== '') {
                    if (type !== "jpeg" && type !== "png" && type !== "jpg" && type !== "gif" && type !== "GIF" && type !== "JPG" && type !== "PNG") {
                        $(".webupload_current").parent().parent().find('.img-view').append("<li><img class='img_common' src='/ky/ico/" + type + ".png' data-type='" + type + "' alt='" + name.substr(0, 7) + "' onerror='javascript:this.src=\"/ky/ico/other.png\";'><span class='del_to' >" + name.substr(0, 7) + "..." + "<br><a href=" + fullUrl + " data-type='download' download><i class='fas fa-download'></i></a>&nbsp;&nbsp;&nbsp;<a onclick='delete_pic(this)' data-type='del'><i class='far fa-trash-alt'></i></a></span>" +
                            "<input type='hidden' name='attachNid[]' value=" + nid + "><input type='hidden'  name='attachName[]' value=" + name + "><input type='hidden'  name='attachSize[]' value=" + size + "><input type='hidden'  name='attachType[]' value=" + attach_new + "><input type='hidden'  name='bizType[]' value=" + bizType + "><input type='hidden'  name='attachBizID[]' value=" + bizID + "></li>");
                        if (attach_new === "CRSE" || attach_new === "ODSE") {
                            if ($('#KEY_CRCT').length > 0) {
                                var k = $(this).parent().parent().parent().find('#KEY_CRCT').val();
                                if (k == null && k === "") {
                                    k = 0;
                                }
                                var g = Number(k) + 1;
                                $(this).parent().parent().parent().find('#KEY_CRCT').val(g);
                            }
                            if (typeof isCRCT === 'function') {
                                //存在且是function
                                isCRCT();
                            }
                        }

                        // 上传成功后, 重新初始化fancybox3
                        // $("[data-fancybox-class^='gallery']").fancybox();
                    } else {
                        if (operation !== undefined && operation !== '' && operation === '1') {
                            $(".webupload_current").parent().parent().find('.img-view').append("<li><a href=" + fullUrl + " data-fancybox-class='gallery' data-caption=" + name + " data-fancybox=" + bizID + "><img class='img_common' src=" + middleUrl + " alt='' onerror='javascript:this.src=\"/ky/ico/other.png\";'/></a><span class='del_to' >" + name + "</span>" +
                                "<input type='hidden' name='attachNid[]' value=" + nid + "><input type='hidden'  name='attachName[]' value=" + name + "><input type='hidden'  name='attachSize[]' value=" + size + "><input type='hidden'  name='attachType[]' value=" + attach_new + "><input type='hidden'  name='bizType[]' value=" + bizType + "><input type='hidden'  name='attachBizID[]' value=" + bizID + "></li>");
                        } else {
                            $(".webupload_current").parent().parent().find('.img-view').append("<li><a href=" + fullUrl + " data-fancybox-class='gallery' data-caption=" + name + " data-fancybox=" + bizID + "><img class='img_common' src=" + middleUrl + " alt='' onerror='javascript:this.src=\"/ky/ico/other.png\";'/></a><span class='del_to' ><a onclick='delete_pic(this)'><i class=\"far fa-trash-alt\"></i></a></span>" +
                                "<input type='hidden' name='attachNid[]' value=" + nid + "><input type='hidden'  name='attachName[]' value=" + name + "><input type='hidden'  name='attachSize[]' value=" + size + "><input type='hidden'  name='attachType[]' value=" + attach_new + "><input type='hidden'  name='bizType[]' value=" + bizType + "><input type='hidden'  name='attachBizID[]' value=" + bizID + "></li>");

                        }

                        if (attach_new === "CRSE" || attach_new === "ODSE") {
                            if ($('#KEY_CRCT').length > 0) {
                                var k = $(this).parent().parent().parent().find('#KEY_CRCT').val();
                                if (k == null && k === "") {
                                    k = 0;
                                }
                                var g = Number(k) + 1;
                                $("this").parent().parent().parent().find('#KEY_CRCT').val(g);
                            }
                            if (typeof isCRCT === 'function') {
                                //存在且是function
                                isCRCT();
                            }
                        }
                    }
                }
            } else {

            }
            // var nid = response.nid;
            // var name = response.name;
            // var size = response.size;
            // var type = response.type;
            // var attachTT = response.attachTT;
            // var bizTT = response.bizTT;
            // var bizID = response.bizID;

            // if (type === undefined || type === '') {
            //     type = file.ext;
            // }



        });

        uploader.on('uploadError', function(file, reason) {
            alert("上传失败！请重试。");
        });
        /**
         * 验证文件格式以及文件大小
         */
        uploader.on("error", function (type) {
            if (type === "Q_TYPE_DENIED") {
                layer.msg("请上传JPG、PNG、GIF、BMP格式文件");
            } else if (type === "Q_EXCEED_SIZE_LIMIT" || type === 'F_EXCEED_SIZE') {
                showUploadMsg('文件大小不能超过 ' + singleMaxSize + ' MB');
            } else {
                showUploadMsg('上传出错！请检查后重新上传！错误代码: ' + type);
            }
        });
    });
}

function showUploadMsg(content) {
    layer.open({
        type: 1
        ,title: false //不显示标题栏
        ,closeBtn: false
        ,area: '500px;'
        ,shade: 0.8
        ,id: 'LAY_layDoAuthConfirm' //设定一个id，防止重复弹出
        ,btn: ['确认']
        ,btnAlign: 'c'
        ,moveType: 1 //拖拽模式，0或者1
        ,content: '<div style="padding: 50px; line-height: 22px; background-color: #eee; font-weight: 300;"><h3>验证失败！</h3><br><p>' + content + '</p></div>'
    });
}

function delete_pic(obj) {
    $(obj).parent().parent().remove();
    // isCRCT();
}

function addWebuploadCurrent(obj) {
    $(".webupload_current").removeClass("webupload_current");
    obj.addClass("webupload_current");
    // console.log($(obj).attr("data-biz-id"));
    // console.log(operation);
    // console.log($(obj).children(".webuploader-pick").children("#typeURL").val());

    bizID = $(obj).attr("data-biz-id");
    attachType = $(obj).children(".webuploader-pick").children("#attachType").val();
    bizType = $(obj).children(".webuploader-pick").children("#bizType").val();
    baseUrl = $(obj).children(".webuploader-pick").children("#uploadURL").val();
    operation = $(obj).children(".webuploader-pick").children("#typeURL").val();
    sid = $(obj).children(".webuploader-pick").children("#sid").val();
}

