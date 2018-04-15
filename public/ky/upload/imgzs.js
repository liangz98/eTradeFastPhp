
var bizID=$('#bizID').val();
var attachType=$('#attachType').val();
var bizType=$('#bizType').val();

    $(function() {
        webupload_pic();
    })

function delete_pic(obj){
    apt=obj;
    var deleobj=$(apt).parent().find("img").html();
    $(deleobj).hide();
    //if (html) {
    //    //删除方法
    //    $(".upload_delete").click(function() {
    //        ZXXFILE.funDeleteFile(files[parseInt($(this).attr("data-index"))]);
    //        return false;
    //    });
    //    //提交按钮显示
    //    $("#fileSubmit").show();
    //} else {
    //    //提交按钮隐藏
    //    $("#fileSubmit").hide();
    //}
    }


function webupload_pic() {
    var maxsize = 5000;
    $.getScript("../../ky/upload/Public/js/plugins/webuploader/webuploader.js", function() {
        if (!WebUploader.Uploader.support()) {
            alert('您的浏览器不支持上传功能！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        }
        var uploader = WebUploader.create({
            multiple: false,
            auto: true,
            swf: "../../ky/upload/Public/js/plugins/webuploader/Uploader.swf",
            server: "../../ky/upload/ajaxzs.php",
            pick: {
                id: '.js-add-image',
                innerHTML: ''
            },
            //accept: {
            //    title: 'intoTypes',
            //    extensions: 'gif,jpg,jpeg,png,rar,zip,doc,xls,docx,xlsx,pdf',
            //    mimeTypes: 'image/jpg,image/jpeg,image/png,.rar,.zip,.doc,.xls,.docx,.xlsx,.pdf'
            //},
            fileSingleSizeLimit: maxsize * 1024 * 1024,
            duplicate: true,
            formData: {
                code: 'identity',
                bizID: bizID,
                attachType:attachType,
                bizType:bizType
            }

        });
        //上传时
        uploader.on('fileQueued', function(file) {
            var item_progress = "<div class='progress' id='" + file['id'] + "'><span class='bar'></span><span class='percent'>0%</span></div></li>";
            $(".img-view").prepend(item_progress);

        });
        //上传中
        uploader.on('uploadProgress', function(file, percentage) {
            //var percent = parseFloat(percentage * 100);
            //$("#" + file.id).find('.bar').css({"width": percent + "%"});
            //$("#" + file.id).find(".percent").text(percent + "%");

        });

        uploader.on('uploadBeforeSend', function(block, data) {
            data.maxsize = maxsize;
        });
        //上传成功后
        uploader.on('uploadSuccess', function(file, response) {
            $("#" + file.id).remove();
            $(".img-view").append("<img width='125px' height='125px' class='img_common' src="  + response.pic + " data-pic=" + response.pic + " alt=''/>")
        });

        uploader.on('uploadError', function(file, reason) {
            alert("上传失败！请重试。");
        });
    });
}

function addWebuploadCurrent(obj) {
    $(".webupload_current").removeClass("webupload_current");
    obj.addClass("webupload_current");
}
