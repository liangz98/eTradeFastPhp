
    var bizID=$('#bizID').val();
    var attachType=$('#attachType').val();
    var bizType=$('#bizType').val();
    var urlVal=$('#uploadURL').val();
    var typeVal=$('#typeURL').val();

    $(function() {
        webupload_pic();
    });

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
            fileSingleSizeLimit: maxsize * 1024 * 1024,
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
        uploader.on('fileQueued', function(file) {
            console.log(file);
            var item_progress = "<img style='margin-top: 30px;' id='" + file['id'] + "' src='/ky/images/loading.gif'></li>";
            $(".webupload_current").parent().parent().find('.img-view').prepend(item_progress);

        });
        //上传中
        uploader.on('uploadProgress', function(file, percentage) {
            //var percent = parseFloat(percentage * 100);
            //$("#" + file.id).find('.bar').css({"width": percent + "%"});
            //$("#" + file.id).find(".percent").text(percent + "%");
            //var item_progress = "<div class='progress' id='" + file['id'] + "'><img src='/ky/images/loading.gif'></div></li>";
            //$(".img-view").append(item_progress);

        });

        uploader.on('uploadBeforeSend', function(block, data) {
            data.maxsize = maxsize;
        });
        //上传成功后
        uploader.on('uploadSuccess', function(file, response) {
            var nid=response.nid;
            var name=response.name;
            var size=response.size;
            var type=response.type;
            var attachTT=response.attachTT;
          //  str={"nid":nid,"name":name,"size":size,"attachType":attachType};
            var i="1";
            i++;

            if (type=="docx"||type=="wps"){
                type="doc";
            }
            if (type=="xlsx"){
                type="xls";
            }
            if (type=="pptx"){
                type="ppt";
            }
            $("#" + file.id).remove();
            if (type!="jpeg" && type!="png" && type!="jpg" && type!="gif" && type!="GIF" && type!="JPG" && type!="PNG"){

                $(".webupload_current").parent().parent().find('.img-view').prepend("<li><a href=" + response.doc + " download><img width='125px' height='125px' class='img_common' src='/ky/ico/"+type+".png' alt="+name+"></a><span class='del_to' >"+name+"</span>" +
                    "<input type='hidden' name='attachNid[]' value=" +nid+ "><input type='hidden'  name='attachName[]' value="+name+ "><input type='hidden'  name='attachSize[]' value="+size+ "><input type='hidden'  name='attachType[]' value="+attachTT+ "><input type='hidden'  name='bizType[]' value=''></li>");
            }else{
                $(".webupload_current").parent().parent().find('.img-view').prepend("<li><img width='125px' height='125px' class='img_common' src="  + response.pic + " layer-src=" + response.doc + " alt=''/><span class='del_to' ><a onclick='view_pic(this)'>查看</a>|<a onclick='delete_pic(this)'>删除</a></span>" +
                "<input type='hidden' name='attachNid[]' value=" +nid+ "><input type='hidden'  name='attachName[]' value="+name+ "><input type='hidden'  name='attachSize[]' value="+size+ "><input type='hidden'  name='attachType[]' value="+attachTT+ "><input type='hidden'  name='bizType[]' value=''></li>");
            }

         });

        uploader.on('uploadError', function(file, reason) {
            alert("上传失败！请重试。");
        });
    });
}
 function delete_pic(obj){
    $(obj).parent().parent().remove();

 }
 function view_pic(obj){

     layer.photos({
             type: 1,
             area: '750px',
             title: false,
             offset: '100px',
             shadeClose: true, //点击遮罩关闭
         photos: '.img-view'
         ,shift: 5 //0-6的选择，指定弹出图片动画类型，默认随机
     });
    // $(obj).parent().parent().find('.img_common').addClass('a_par');
    // var srcID= $(obj).parent().parent().find('img').attr("src");
    //  var keyword=srcID.replace("&size=MIDDLE","");
    // $(obj).parent().parent().find('img').attr("layer-src","keyword");
    //  console.log(srcID);
    //
    //
    // layer.open({
    //     type: 1,
    //     area: '750px',
    //     title: false,
    //     offset: '100px',
    //     shadeClose: true, //点击遮罩关闭
    //     content: '\<\img style="max-width:720px;max-height:620px;margin:0 auto;padding:15px;" src='+keyword+'>'
    // });

}

 function addWebuploadCurrent(obj) {
    $(".webupload_current").removeClass("webupload_current");
    obj.addClass("webupload_current");
}

