// var bizID = $('#bizID').val();
// var attachType = '';
// var bizType = $('#bizType').val();
// var urlVal = $('#uploadURL').val();
// var typeVal = $('#typeURL').val();

var bizID = '';
var attachType = '';
var bizType = '';
var urlVal = '';
var typeVal = '';

$(function() {
    webupload_pic();
});

function webupload_pic() {
    $.getScript("../../ky/upload/Public/js/plugins/webuploader/webuploader.js", function() {
        if (!WebUploader.Uploader.support()) {
            alert('您的浏览器不支持上传功能！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        }

        // console.log('create:'+ typeVal);
        var uploader = WebUploader.create({
            multiple: false,
            compress: false,
            auto: true,
            swf: "../../ky/upload/Public/js/plugins/webuploader/Uploader.swf",
            server: "../../ky/upload/ajax.php",
            pick: {
                id: '.js-add-image',
                innerHTML: ''
            },
            // accept: {
            //    title: 'Images',
            //    extensions: 'gif,jpg,jpeg,bmp,png',
            //    mimeTypes: 'image/*'
            // },
            //accept: {
            //    title: 'intoTypes',
            //    extensions: 'gif,jpg,jpeg,png,rar,zip,doc,xls,docx,xlsx,pdf',
            //    mimeTypes: 'image/jpg,image/jpeg,image/png,.rar,.zip,.doc,.xls,.docx,.xlsx,.pdf'
            //},
            fileNumLimit: 20,//上传数量限制
            fileSizeLimit: 40960000,//限制上传所有文件大小
            fileSingleSizeLimit: 16777216,
            duplicate: true,
            formData: {
                code: 'identity',
                bizID: bizID,
                attachType:attachType,
                bizType:bizType,
                urlDD:urlVal,
                typeDD:typeVal
            }

        });

        //上传时
        uploader.on('fileQueued', function (file) {
            console.log('fileQueued:' + typeVal);
            uploader.option('formData', {
                code: 'identity',
                bizID: bizID,
                attachType: attachType,
                bizType: bizType,
                urlDD: urlVal,
                typeDD: typeVal
            });
            var item_progress = "<img style='margin-top: 30px;' id='" + file['id'] + "' src='/ky/images/loading.gif'></li>";
            $(".webupload_current").parent().parent().find('.img-view').append(item_progress);
        });
        //上传中
        uploader.on('uploadProgress', function(file, percentage) {
            var $li = $( '#'+file.id ),
                $percent = $li.find('.progress span');
        });

		// 上传前
        uploader.on('uploadBeforeSend', function(block, data) {
			data.attachType = $(".webupload_current").attr("attachType_");
        });


        //上传成功后
        uploader.on('uploadSuccess', function(file, response) {
            var nid = response.nid;
            var name = response.name;
            var size = response.size;
            var type = response.type;
            var attachTT = response.attachTT;
            var bizTT = response.bizTT;
            var bizID = response.bizID;

          //  str={"nid":nid,"name":name,"size":size,"attachType":attachType};
            var i="1";
            i++;

            if (type === undefined || type === '') {
                type = file.ext;
            }
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
                attach_new = attachTT;
            }

            if (type !== "jpeg" && type !== "png" && type !== "jpg" && type !== "gif" && type !== "GIF" && type !== "JPG" && type !== "PNG") {
                $(".webupload_current").parent().parent().find('.img-view').append("<li><img width='125px' height='125px' class='img_common' src='/ky/ico/" + type + ".png' data-type='" + type + "' alt=" + name.substr(0,7) + "><span class='del_to' >" + name.substr(0, 7) + "..." + "<br><a href=" + response.doc + " data-type='download' download><i class='fas fa-download'></i></a>&nbsp;&nbsp;&nbsp;<a onclick='delete_pic(this)' data-type='del'><i class='far fa-trash-alt'></i></a></span>" +
                    "<input type='hidden' name='attachNid[]' value=" + nid + "><input type='hidden'  name='attachName[]' value=" + name + "><input type='hidden'  name='attachSize[]' value=" + size + "><input type='hidden'  name='attachType[]' value=" + attach_new + "><input type='hidden'  name='bizType[]' value=" + bizTT + "><input type='hidden'  name='attachBizID[]' value=" + bizID + "></li>");
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
            } else {
                if (response.uploadType === '1') {
                    $(".webupload_current").parent().parent().find('.img-view').append("<li><a href=" + response.fullPic + " data-fancybox-class='gallery' data-caption=" + name + " data-fancybox=" + bizID + "><img width='125px' height='125px' class='img_common' src="  + response.pic + " alt=''/></a><span class='del_to' >" + name + "</span>" +
                        "<input type='hidden' name='attachNid[]' value=" +nid+ "><input type='hidden'  name='attachName[]' value="+name+ "><input type='hidden'  name='attachSize[]' value="+size+ "><input type='hidden'  name='attachType[]' value="+attach_new+ "><input type='hidden'  name='bizType[]' value="+bizTT+ "><input type='hidden'  name='attachBizID[]' value=" + bizID + "></li>");
                } else {
                    $(".webupload_current").parent().parent().find('.img-view').append("<li><a href=" + response.fullPic + " data-fancybox-class='gallery' data-caption=" + name + " data-fancybox=" + bizID + "><img width='125px' height='125px' class='img_common' src="  + response.pic + " alt=''/></a><span class='del_to' >" + name.substr(0, 7) + "..." + "<br><a onclick='delete_pic(this)'><i class=\"far fa-trash-alt\"></i></a></span>" +
                        "<input type='hidden' name='attachNid[]' value=" +nid+ "><input type='hidden'  name='attachName[]' value="+name+ "><input type='hidden'  name='attachSize[]' value="+size+ "><input type='hidden'  name='attachType[]' value="+attach_new+ "><input type='hidden'  name='bizType[]' value="+bizTT+ "><input type='hidden'  name='attachBizID[]' value=" + bizID + "></li>");
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

            // 上传成功后, 重新初始化fancybox3
            $("[data-fancybox-class^='gallery']").fancybox();
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
                // layer.msg("文件大小不能超过2M");
                showUploadMsg('文件大小不能超过2M');
            } else {
                // layer.msg("上传出错！请检查后重新上传！错误代码"+type);
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
    // console.log(typeVal);
    // console.log($(obj).children(".webuploader-pick").children("#typeURL").val());

    bizID = $(obj).attr("data-biz-id");
    attachType = $(obj).children(".webuploader-pick").children("#attachType").val();
    bizType = $(obj).children(".webuploader-pick").children("#bizType").val();
    urlVal = $(obj).children(".webuploader-pick").children("#uploadURL").val();
    typeVal = $(obj).children(".webuploader-pick").children("#typeURL").val();
}

