
var creating = false, goods = {}, indexImg;

$(function($){
    $("#createBtn").click(function(){
        if (creating) {return false;}
        var param = {}, error,
            rsHtml = '<tr><td colspan="6"><img class="loading" src="/static/commerce/images/loader.gif"></td></tr>',
            rowHtml = '<tr style="display:none;">'+
                          '<td><input type="checkbox" name="chose[]" value="" class="ids_all"></td>'+
                          '<td><img src="" style="width:50px;"></td>'+
                          '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>'+
                          '<td></td>'+
                          '<td><a class="deleteItem" data="" href="javascript:;">[删除]</a></td>'+
                      '</tr>',
            $rsDom = $(rsHtml),
            $rowDom = $(rowHtml);

        param.end_time = $('#end_time').val();
        param.provide_num = parseInt($('#provide_num').val());

        for (var value in param) {
            if ( !param[value]) {
                error = '请输入' + $('#'+value).attr('item');
                break;
            }
            if (param[value] != $('#' + value).val()) {
                error = '请输入正确的' + $('#'+value).attr('item');
                break;
            }
        }
        if (error) {
            generate("error", error);
            return false;
        }
        if (typeof(goods.goods_id) == "undefined") {
            generate("error", "请选择商品");
            return false;
        }
        param.effect     = parseFloat($('#effect').val());
        param.endurance  = parseFloat($('#endurance').val());
        param.moist      = parseFloat($('#moist').val());
        param.absorbance = parseFloat($('#absorbance').val());
        param.apply      = parseInt($('#apply').val());
        param.integral   = parseInt($('#integral').val());
        param.goods_id   = goods.goods_id;
        param.stock_id   = goods.stock_id;
        param.index_img  = $('#index_img').val();
        creating = true;
        $("table.admin_list tr:first").after($rsDom);
        $.post("/b/vmall/goodstry/add", param, function(op){
            if (op.code == 1) {
                $rowDom.attr("id", "tid_"+op.data.id);
                $rowDom.find("td").each(function(i){
                    switch (i) {
                        case 0:
                            $(this).find("input").val(op.id);
                            return true;
                        case 1:
                            $(this).find("img").attr("src", goods.img_url);
                            return true;
                        case 2:
                            $(this).html(goods.goods_name + '<br/>' + goods.stock_name);
                            return true;
                        case 3:
                            $(this).html(param.end_time);
                            return true;
                        case 4:
                            $(this).html(param.integral);
                            return true;
                        case 5:
                            $(this).html(param.effect);
                            return true;
                        case 6:
                            $(this).html(param.endurance);
                            return true;
                        case 7:
                            $(this).html(param.moist);
                            return true;
                        case 8:
                            $(this).html(param.absorbance);
                            return true;
                        case 9:
                            $(this).html(param.provide_num);
                            return true;
                        case 10:
                            $(this).html(param.apply);
                            return true;
                        case 11:
                            if (indexImg) {
                                $(this).html('<img src="'+indexImg+'" style="width:50px;">');
                                indexImg = null;
                            }
                            return true;
                        case 12:
                            $(this).find("a").attr("data", op.id);
                            return true;
                    }
                });
                $rsDom.hide("slow", function(){
                    $('#addTry').modal('hide');
                    $rsDom.remove();
                    $("table.admin_list tr:first").after($rowDom);
                    $rowDom.show("slow");
                });
                generate("success", op.msg);
            } else {
                $rsDom.hide("slow", function(){
                    $rsDom.remove();
                });
                generate("error", op.msg);
            }
            creating = false;
        }, "json");
    });
});