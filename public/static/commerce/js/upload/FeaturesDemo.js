// Called instead of the SWFUpload _showUI method
var FeaturesDemo = {
    start : function(swf_upload_instance) {
        FeaturesDemo.SU = swf_upload_instance;
        FeaturesDemo.cacheFields();
        // 给点击上传对象注册单击事件
        FeaturesDemo.startButton.click(function() {
            FeaturesDemo.startSelectedFile();
            $(this).removeClass("btn_reelect");
            $(this).addClass("btn_type");
            $("#continueHref").removeClass("btn_reelect");
            $("#continueHref").addClass("btn_type");
//            $("#selectId").attr("disabled",true);
        });
        // 继续上传按钮
        FeaturesDemo.continueButton.click(function() {
            $(this).hide();
//            $("#imgZoneCateId").hide();
            $("#uploadHref").hide();
            $("#closeHref").hide();
            $("#div_img_show").hide();
            $("#div_tip").show();
        });

    },

    cacheFields : function() {
        if (FeaturesDemo.is_cached) {
            return;
        }
        // 注册点击上传对象
        FeaturesDemo.startButton = $("#uploadHref");
        // 继续上传按钮
        FeaturesDemo.continueButton = $("#continueHref");
        // 取消按钮
        FeaturesDemo.is_cached = true;
    },

    startSelectedFile : function() {
        FeaturesDemo.SU.startUpload();
    },
    stopUpload : function() {
        FeaturesDemo.SU.stopUpload();
    },
    cancelSelectedFile : function(file_id) {
        FeaturesDemo.SU.cancelUpload(file_id, false);
    }

};
