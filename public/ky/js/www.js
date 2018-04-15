$(function(){

    $('#o_tab').find('li').eq(0).addClass('cur');
    $('#order_info').children('div').eq(0).css('display','block');
    var $shrh_titles=$('#o_tab').find('li');
    var $shrh_divs=$('#order_info');
    $shrh_titles.each(function(i,item){
        $(this).on('click',function(){
            $shrh_divs.children('div').css('display','none');
            $shrh_divs.children('div').eq(i).css('display','block');
            $(this).addClass('cur').siblings().removeClass('cur');
        });
    });
    // company/index
    $(".company_icon").hover(function(){
        $(".company_box").show();
    },function(){
        $(".company_box").hide();
    });
    //订单中心选项切换效果
    // $('.list li').click(function(event) {
    //     $('.on').removeClass('on');
    //     $(this).addClass('on');
    // });
    // $('.tab_tab li').click(function(event) {
    //      $('.tab_tab li').removeClass('on');
    //      $(this).addClass('on');
    //      var o_index=$(this).index();
    //      $('.tab_wrap table').hide();
    //      $('.tab_wrap table').eq(o_index).show();
    // });
    //企业表单操作
    // $('.qy_nav li').click(function(event) {
    //     $(this).addClass('cur').siblings().removeClass('cur');
    //     var q_index=$(this).index();
    //     $('.qy_table ').hide();
    //     $('.qy_table ').eq(q_index).show();
    // });
    // $('.qynav_nav li').click(function(event) {
    //     $(this).addClass('cur').siblings().removeClass('cur');
    // });

    // 订单表操作
    $('.order_state222 li,.order_state2 li').click(function(event) {
        $(this).addClass('on').siblings().removeClass('on');
        $('.qy_list_wrap').removeClass('curr');
        $('.qy_list_wrap').eq($(this).index()).addClass('curr');
    });

    //fixed
    // $(window).scroll(function() {
    //         if($(document).scrollTop()>=140){
    //            $('.o_nav').addClass('fixed');
    //         }else{
    //            $('.o_nav').removeClass('fixed');
    //         }
    //     });
    //
   $('.open_off').click(function(event) {
       if(!$(this).hasClass('open')){
            $(this).addClass('open');
       }else{
            $(this).removeClass('open');
       }
       $(this).parents('.img_app_box').find('.h_img_ul').slideToggle(300);
   });

});


function reduce(){
    var goods_number = $("#goods_number").val();
    goods_number--;
    if(goods_number < 1)goods_number=1;
    $("#goods_number").val(goods_number);
}

function increase(){
     var stock_id = parseInt($("#stock_id").val());
     var goods_id = parseInt($("#goods_id").val());
     var goods_number = parseInt($("#goods_number").val());
}

function isRate(ID) {
    var bizID=ID;
    var bizType='OD';
    $.post("/user/system/topview/", {
        'bizID': bizID,
        'bizType': bizType
    }, function (data) {
        var json;
        if (typeof data == 'object') {
            json = data.result;
        }
        else {
            json = eval('(' + data.result + ')');
        }
        var hlv="";
        $.each(json,function(i, n){
            hlv+='<dl>'+n.contraCrn+'-'+n.baseCrn+' '+n.actualRate+'</dl>';
        });
        $('.huilv').html(hlv);
    }, "json");
}

function isCRCT() {
    var status=$('#vendST').val();
    if(status>=3||status<1){
        return false;
    }else{
        var n=0;
        var m=0;
        var s=0;
        var t=0;
        $("#key").find('.download_doc').children("li").each(function(){
            if ($(this).find("input[type='hidden']").val() == "CRCT") {
                n=n+1;

            }
        });
        if(n!=0){
            $(".img-view").children("li").find("input[type='hidden']").each(function () {

                if ($(this).val() == "CRSE") {
                    m=m+1;

                }
            });
        }
        $("#key").find('.download_doc').children("li").each(function(){
            if ($(this).find("input[type='hidden']").val() == "ODTA") {
                s=s+1;

            }
        });
        if(s!=0){
            $(".img-view").children("li").find("input[type='hidden']").each(function () {

                if ($(this).val() == "ODSE") {
                    t=t+1;

                }
            });
        }
        if(s!=0&&n!=0){
           if(m!=0&&t!=0){
                $("#handle").attr("disabled", false);
           }else{
               $("#handle").attr("disabled", true);
           }
        }else  if(s!=0||n!=0){
            if(n!=0){
                if(m!=0){
                    $("#handle").attr("disabled", false);
                }else{
                    $("#handle").attr("disabled", true);
                }
            }else if(s!=0){
                if(t!=0){
                    $("#handle").attr("disabled", false);
                }else{
                    $("#handle").attr("disabled", true);
                }
            }else {

                $("#handle").attr("disabled", true);
            }

        }else {

            $("#handle").attr("disabled", true);
        }
    }
}

function contractSignViewNO($id) {
    layer.msg('电子合同签署企业授权未完成，不能进行网签!', {
        icon: 2,
        time: 4000 //2秒关闭（如果不配置，默认是3秒）
    });
}
function contractSignView($id) {
     var rs_url=window.location.href;
    if($id==null&&$id==""){
        return false;
    }

    $.post('/user/orderxs/sign',{'id':$id,'url': rs_url},function (data) {
        if(data==null||data==""){
            layer.msg('Error,Please try again later!', {
                icon: 2,
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
        /*注意返回为html 格式注意异步回调格式问题*/
        //在这里面打印出合同签署页面
        var html='<div style="padding:50px;">'+data+'</div>';

       layer.open({
            type: 1 //Page层类型
            ,area: ['350px', '300px']
            ,title: 'sign Contract loading...'
            ,shade: 0.6 //遮罩透明度
            ,anim: 1 //0-6的动画形式，-1不开启
            ,content: html
        });
    },'html')
}

function downloadSignView($id,$type) {
    if($id==null&&$id==""){
        return false;
    }
    $.post('/user/orderxs/download',{'id':$id,'type':$type},function (data) {

    if(data.status==1){
        url=data.result;
        window.open(url);//新窗口打开下载链接
    }else{
        layer.msg('Error,Please try again', {
            icon: 2,
            time: 2000 //2秒关闭（如果不配置，默认是3秒）
        });
    }
    },'json')
}

$(document).ready(function(){
    // $('.must').bind('input propertychange', function() {
    //     var val = $(this).val();
    //     if(val==""){
    //       $(this).css('border-color','#DF7E20');
    //     }else{
    //       $(this).css('border-color','#e8e8e8');
    //     }
    // });
});
