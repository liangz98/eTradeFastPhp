var FeaturesDemoHandlers = {
    swfUploadLoaded: function () {
        FeaturesDemo.start(this); // This refers to the SWFObject because
    },
    fileDialogStart: function () {
        try {
        } catch (ex) {
            this.debug(ex);
        }
    },

    fileQueued: function (file) {
        try {
            var id = file.id;
            var imgSize = file.size;
            imgSize = imgSize / 1024;
            imgSize = Math.round(imgSize * 100) / 100;
            var imgType = file.type;
            imgType = imgType.substring(1, imgType.length);
            imgType = imgType.toUpperCase();//转成大写
            var fileName = file.name;
            fileName = getFileName(fileName);

            var tr= '<tr id='+id+'>'+
                '<td>'+fileName+'</td> '+
                '<td>' + imgSize+'KB</td> '+
                '<td>'+imgType+'</td>'+
                '<td><div class="process" id="process' + file.id + '"><b style="width:2px" ></b><span class="rate">等待上传</span></div></td>'+
                '<td><div style="display:none;"><input type="hidden" id="imgZoneId' + file.id + '" value=""><img id="imgUrl' + file.id + '" src="" ></a></td>'+
                '<td><div><a id="preview' + file.id + '" class="wait">预览 <a id="insert' + file.id + '" class="wait">&nbsp;&nbsp;&nbsp;&nbsp;插入</a><a id="delete' + id + '" class="blue" name="del_Image">&nbsp;&nbsp;&nbsp;&nbsp;删除</a></div></td>'+
                '</tr>';

            $("#filesTable").append(tr);
            $("#div_img_show").show();
            $("#uploadHref").removeClass("btn_type");
            $("#uploadHref").addClass("btn_reelect");
            $("#continueHref").removeClass("btn_type");
            $("#continueHref").addClass("btn_reelect");
            $("#uploadHref").show();
            $("#closeHref").show();
            $("#continueHref").show();

            //删除图片
            $("#delete"+id).click(function () {
                $("#filesTable>tr[id='"+id+"']").remove();
                FeaturesDemo.cancelSelectedFile(file.id);
                var fileNum = $("#filesTable>tr[id]").length;
                if (fileNum == 0) {
                    $("#div_img_show").hide();
                    $("#uploadHref").hide();
                    $("#closeHref").hide();
                    $("#continueHref").hide();
                    $("#div_tip").show();
                }
            });

            //预览图片
            $("#preview"+id).mousemove(function (event) {
                event = event || window.event;
                var imageDfsUrl = jQuery("#imgUrl"+id).attr("src");
                if(imageDfsUrl.length>0){
                    var ei = document.getElementById("enlarge_images");
                    ei.style.display = "block";
                    ei.innerHTML = '<img src="' + imageDfsUrl + '"  width="120" height="120" />';
                    if(event.clientY>240){
                        ei.style.top  = (event.clientY-120) + "px";
                    }else{
                        ei.style.top  = event.clientY+ "px";
                    }
                    ei.style.left = (event.clientX-150) + "px";
                    $(this).removeClass("blue");
                    $(this).addClass("green");
                }
            });

            //退出预览
            $("#preview"+id).mouseout(function () {
                var ei = document.getElementById("enlarge_images");
                ei.innerHTML = "";
                ei.style.display = "none";
                ei.style.top="";
                ei.style.left = "";
                var imageDfsUrl = jQuery("#imgUrl"+id).attr("src");
                if(imageDfsUrl.length>0){
                    $(this).removeClass("green");
                    $(this).addClass("blue");
                }else{
                    $(this).removeClass("green");
                    $(this).addClass("wait");
                }
            });

            //插入图片到编辑器
            $("#insert"+id).click(function(){
                //获取图片的url
                var imageUrl = jQuery("#imgUrl"+id).attr("src");
                //获取图片在图片空间的id
                var imageZoneId= $("#imgZoneId" + id).val();
                //将获取的图片url和图片id以“||”的形式连接
                var imgZoneUrl=imageUrl+"||"+imageZoneId;
                //用来判断点击选择图片空间弹层时
                var descVla = jQuery("#imgZoneType").val();
                if(imageUrl.length>0 && imageZoneId.length>0){
                    if(descVla==1){
                        parent.insertImgUrl(imgZoneUrl);
                    }else{
                        parent.insertImgUrl1(imgZoneUrl);
                    }
                    $(this).removeClass("blue");
                    $(this).addClass("fuchsia");
                }
            });
        } catch (ex) {
            this.debug(ex);
        }
    },

    fileQueueError: function (file, errorCode, message) {
        try {
            switch (errorCode) {
                case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
                    showUploadError(file, "upload.queue.limit.exceeded");
                    break;
                case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                    showUploadError(file, "upload.upload.limit.exceeded");
                    break;
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
        }
    },

    fileDialogComplete: function (numFilesSelected, numFilesQueued) {
        try {
            //未选择图片，需要重置
            if (this.getStats().successful_uploads == this.settings.file_upload_limit) {
                $("#div_tip").hide();
            }else if (this.getStats().files_queued < 1) {
                $("#div_tip").show();
            } else
                $("#div_tip").hide();
        } catch (ex) {
            this.debug(ex);
        }
    },

    uploadStart: function (file) {
        try {
        } catch (ex) {
            this.debug(ex);
            showUploadError(file, "seed.publish.exception");
        }
        return true;
    },

    uploadProgress: function (file, bytesLoaded, totalBytes) {

        try {
            var id = file.id;
            var wd = $("#process" + id + ">b").css("width");
            if (wd == "2px") {
                //由于图片传递到图片服务器速度未知，此处只能模拟进度
                var progressWidth = 0;// 进度条宽度-随机数
                var progressVal = 0;// 进度条百分比数值-随机数
                var i = getRndNum(30, 80);
                progressVal = i;
                progressWidth = Math.floor(progressVal * 185 / 100); //步长取值根据progressVal变化
                $("#process" + id).children().eq(0).attr({"style": "width:" + progressWidth + "px;"});
                $("#process" + id).children().eq(1).text("正在上传");
                var j = 0;
                while (j < 10) {
                    progressVal += 1;
                    progressWidth = Math.floor(progressVal * 185 / 100);
                    $("#process" + id).children().eq(0).attr({"style": "width:" + progressWidth + "px;"});
                    $("#process" + id).children().eq(1).text("正在上传");
                    j++;
                }
            } else {
                var t = $("#process" + id + ">span").text();
                t = t.substring(0, t.length - 1);
                var progressVal = parseInt(t);
                var progressWidth = Math.floor(progressVal * 185 / 100);
                var k = 0;
                while (k < 10) {
                    if (progressVal >= 99) {
                        progressVal = 99;
                        progressWidth = Math.floor(progressVal * 185 / 100);
                        $("#process" + id).children().eq(0).attr({"style": "width:" + progressWidth + "px;"});
                        $("#process" + id).children().eq(1).text("正在上传");
                        break;
                    } else {
                        progressVal += 1;
                        progressWidth = Math.floor(progressVal * 185 / 100);
                        $("#process" + id).children().eq(0).attr({"style": "width:" + progressWidth + "px;"});
                        $("#process" + id).children().eq(1).text("正在上传");
                        k++;
                    }
                }
            }
        } catch (ex) {
            this.debug(ex);
        }
    },

    uploadSuccess: function (file, serverData, receivedResponse) {
        try {
            var id =  file.id;
            serverData = $.parseJSON(serverData);
            if (serverData.success) {
                insertUrl(id,serverData.data);
                FeaturesDemo.SU.startUpload();
                $("#process" + id).children().eq(0).attr({"style": "width:185px;"});
                $("#process" + id).children().eq(1).text("上传成功");
                $("#preview" + id).removeClass("wait");
                $("#preview" + id).addClass("blue");
                $("#insert" + id).removeClass("wait");
                $("#insert" + id).addClass("blue")
            } else {
                FeaturesDemo.SU.startUpload();
                showUploadError(file, serverData.errorCode);
            }
        } catch (ex) {
            this.debug(ex);
            showUploadError(file, "seed.publish.exception");
        }
    },

    uploadError: function (file, errorCode, message) {
        if ( !file) {return false;}
        try {
            if (errorCode == "-280") {
            } else {
                showUploadError(file, "seed.publish.exception");
            }
        } catch (ex) {
            this.debug(ex);
        }
    },

    uploadComplete: function (file) {
        try {
            if (this.getStats().files_queued === 0) {
                $("#continueHref").removeClass("btn_type");
                $("#continueHref").addClass("btn_reelect");
            }
        } catch (ex) {
            this.debug(ex);
        }
    },

    // This custom debug method sends all debug messages to the Firebug console.
    // If debug is enabled it then sends the debug messages
    // to the built in debug console. Only JavaScript message are sent to the
    // Firebug console when debug is disabled (SWFUpload won't send the messages
    // when debug is disabled).
    debug: function (message) {
        try {
            if (window.console && typeof (window.console.error) === "function"
                && typeof (window.console.log) === "function") {
                if (typeof (message) === "object"
                    && typeof (message.name) === "string"
                    && typeof (message.message) === "string") {
                    window.console.error(message);
                } else {
                    window.console.log(message);
                }
            }
        } catch (ex) {
            window.console.log(ex);
        }
        try {
            if (this.settings.debug) {
                this.debugMessage(message);
            }
        } catch (ex1) {
            window.console.log(ex1);
        }
    }
};

function getRndNum(min, max) {
    //产生一个min到max之间的随机整数
    var i = Math.round((max - min + 1) * Math.random() + min);
    if (i > max)
        i = max;
    if (i < min)
        i = min;
    return i;
}


function getFileName(fileName) {
    var index = fileName.lastIndexOf(".");
    if(index>30){
        fileName = fileName.substring(0, 30)+"...";
    }else{
        fileName = fileName.substring(0, index);
    }
    return fileName;
}

function insertUrl(id,result){
    $("#imgUrl" + id).attr("src",(window.pcAppServer!=undefined?window.pcAppServer:"http://" + window.location.host) + "/upload_files" + result.nolImgUrl);
    $("#imgZoneId" + id).val(result.imgId);
}

function showUploadError(file, errorCode) {
    var errorMsg = {
        "upload.file.too.big": "文件过大",
        "upload.invalid.file.type": "类型不符",
        "upload.invalid.size": "尺寸不对",
        "seed.publish.exception": "上传失败",
        "upload.param.name.error": "分类错误",
        "seed.user.nologin": "未登录",
        "upload.queue.limit.exceeded": "超过队列最大长度",
        "upload.upload.limit.exceeded": "上传文件总数超出预设值"
    };
    if(errorCode == "upload.queue.limit.exceeded" || errorCode == "upload.upload.limit.exceeded") {
        var fileNum = $("#filesTable>tr[id]").length;
        if (fileNum == 0) {
            $("#div_img_show").hide();
            $("#uploadHref").hide();
            $("#closeHref").hide();
            $("#continueHref").hide();
            $("#div_tip").show();
        } else {
            $("#div_img_show").show();
            $("#uploadHref").show();
            $("#closeHref").show();
            $("#continueHref").show();
            $("#div_tip").hide();
        }
        alert(errorMsg[errorCode]);
    } else {
        var imgBox = $("#process" + file.id);
        if ($.isEmptyObject(imgBox) || imgBox.size() != 1) {
            alert(errorMsg[errorCode]);
        } else {
            imgBox.children().eq(0).attr({"style": "width:185px;background: none repeat scroll 0 0 #FFBDBB;"});
            if(errorMsg[errorCode].length>6){
                imgBox.children().eq(1).attr({"style": "left:50px;"});
            }
            imgBox.children().eq(1).text(errorMsg[errorCode]);
            $(imgBox.parent()).addClass("failure");
        }
    }
}