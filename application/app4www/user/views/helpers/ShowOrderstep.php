<?php
class Zend_View_Helper_ShowOrderstep
{
	function showOrderstep()
	{
        $html=<<<EOD
      $(document).ready(function(){
    $(".js-example-basic-single").select2();

    $("#ORDER_QUOTATION_MODE").change(function () {
        $('#selectgoods').find('li').remove();
        $('#selectgoods').find('a').show();
        cleartongji();
    });

    //获取商品数据
    $("#selectgoods").bind('DOMNodeInserted', function (e) {
        if ($("#selectgoods").find('li').length > 0) {
            $(".selectgoods_a").hide();
        } else {
            $(".selectgoods_a").show();
            cleartongji();
        }
    });

    totalCRN();
    $('#CURRENCY').change(function(){
        totalCRN();
    });
    $('.next').click(function(){
        totalCRN();
    });

    //点击选择商品iterm 包装类型click
    $("select[name='packingType']").change(function () {
        $('#selectgoods').each(function (index) {
            var selected = $("select[name='packingType']").val();
            $("select[name='packingType']").find("option:selected").attr("selected", "selected");
        });
    });

    //判断本单联系人 是否获取数据字典数据
    var ackey = $('#account').val();

    if (ackey == null) {
        $.post("<?php echo $this->BaseUrl();?>/account/orderlist/", function (data) {
            var json;
            if (typeof data === 'object') {
                json = data;
            }
            else {
                json = eval('(' + data + ')');
            }
            var select = "";
            for (var i = 0; i < json.length; i++) {
                var trs = "";
                var text = "";
                trs += "<option value=" + json[i].contactID + ">" + json[i].name + "</option>";
                select += trs;
                text = json[0].name;
            }
            $('#account').html(select);
            $("#vendorContactName").val(text);
        }, "json");
    }

    //通过本单联系人select获取本单联系人NAME
    $('#account').change(function () {
        var VCname = $('#account').find("option:selected").text();
        $("#vendorContactName").val(VCname);
    });

    //获取公司的信息
    $.post("<?php echo $this->BaseUrl();?>/company/orderlist/", function (data2) {
        var json2;
        if (typeof data2 === 'object') {
            json2 = data2;
        }
        else {
            json2 = eval('(' + data2 + ')');
        }
        var trs2 = json2.regdCountryCode;
        var trs3 = json2.crnCode;
        var trs4 = json2.accountName;
        $('#comcity').val(trs2);
        $('#crncode').val(trs3);
        $('#accountName').val(trs4);
    }, "json");

    //获取公司联系人账号Contactname
    $("#accountContactbt").click(function () {
        layer.open({
            type: 2,
            title: false,
            shadeClose: true,
            shade: 0.8,
            area: ['680px', '500px'],
            content: '/user/account/aclist'
        });

    });

    //获取买家联系人账号Contactname
    $("#buyerContactbt").click(function () {
        var toID=$('#buyerid').val();
        layer.open({
            type: 2,
            title: false,
            shadeClose: true,
            shade: 0.8,
            area: ['680px', '500px'],
            content: '/user/buyer/aclist?toID=' + toID //iframe的url
        });

    });

    //获取合作伙伴买家账号
    $("#ddbuyer").click(function () {
        layer.open({
            type: 2,
            title: false,
            shadeClose: true,
            shade: 0.8,
            area: ['680px', '500px'],
            content: '/user/buyer/orderlist' //iframe的url
        });
    });

    $(".goods_active").click(function () {
        var oid="";
        $("#selectgoods").find("li").each(function(){
            oid += $(this).attr('id')+"|";
        });
        layer.open({
            type: 2,
            title: false,
            shadeClose: true,
            shade: 0.8,
            area: ['680px', '500px'],
            content: '/user/goods/orderlist?id='+oid //iframe的url
        });
    });

    //物流服务判断
    wuliufw();
    $(":radio[name='needShipping']").click(function(){
        wuliufw();
    });
    //报关模块判断
    baoguanmk();
    $(":radio[name='isAssignCustomsAgency']").click(function(){
        baoguanmk();
    });
    //金融模块判断
    jinrongmk();
    $(":radio[name='needFinancing']").click(function(){
        jinrongmk();
    });

    //根据价格条款判断是否显示物流服务
    $("#PRICE_TERM").click(function () {
        var isset = $("#PRICE_TERM").val();
        if (isset == "EXW") {
            $('#wuliufw').hide();
        } else {
            $('#wuliufw').show();
        }
    });

    //根据结算方式判断是否显示金融服务
    $("#PAYMENT_TERM").click(function () {
        var isset = $("#PAYMENT_TERM").val();
        if (isset == "T/T") {
            $('#jinrong2').hide();
            $('#jinrong').hide();
        } else {
            $('#jinrong').show();
            $('#jinrong2').show();
        }
    });

    //判断装柜数量
    $("#SHIPPING_SERVICE_TYPE").click(function () {
        var isset = $("#SHIPPING_SERVICE_TYPE").val();
        if (isset == "LCL") {
            $('#zgsliang').hide();
        } else {
            if (isset == "FCL") {
                $('#zgsliang').show();
            } else {
                $('#zgsliang').hide();
            }
        }
    });

    //运输方式判断start
    $("#SHIPPING_METHOD").click(function () {
        var isset = $("#SHIPPING_METHOD").val();
        var lab1 = $("#qiyun").text();
        var lab2 = $("#xiehuo").children().html();
        var lab3 = $("#jiaohuo").children().html();

        if (isset == "SEA") {
            $("#qiyun").html("<?php echo $this->translate('shippost1');?>");/*起运港*/
            $("#xiehuo").html("<?php echo $this->translate('shippost2');?>");/*卸货港*/
            $("#jiaohuo").html("<?php echo $this->translate('shippost3'); ?>");/*交货港*/
        }
        if (isset == "AIR") {
            $("#qiyun").html("<?php echo $this->translate('shippost1'); ?>");/*起运港*/
            $("#xiehuo").html("<?php echo $this->translate('shippost2'); ?>");/*卸货港*/
            $("#jiaohuo").html("<?php echo $this->translate('shippost3'); ?>");/*交货港*/
        }
        if (isset == "EXPRESS" || isset == "LAND") {
            $("#qiyun").html("<?php echo $this->translate('citypost1'); ?>");/*起运城市*/
            $("#xiehuo").html("<?php echo $this->translate('citypost2'); ?>");/*卸货城市*/
            $("#jiaohuo").html("<?php echo $this->translate('citypost3'); ?>");/*交货城市*/
        }
        $('#select2-SHIPPING_PORT1-container').html("");
        $('#select2-SHIPPING_PORT2-container').html("");
        $('#select2-SHIPPING_PORT3-container').html("");

    });



    $("#step2Next").click(function () {
        var view01 = $('#buyer').val();
        $('#view_01').html(view01);
        var view02 = $('#buyerContactname').val();
        $('#view_02').html(view02);
        var view03 = $('#CURRENCY').find("option:selected").text();
        $('#view_03').html(view03);
        var view04 = $('#accountContactName').val();
        $('#view_04').html(view04);
        var view05 = $('#PAYMENT_TERM').find("option:selected").text();
        $('#view_05').html(view05);
        var view06 = $('#PAYMENT_PERIOD').find("option:selected").text();
        $('#view_06').html(view06);
        var view07 = $('input[name="isSelfSupport"]:checked ').val();
        if (view07 == '0') {
            $('#view_07').html("<?php echo $this->translate('siteEX'); ?>");
        } else {
            $('#view_07').html("<?php echo $this->translate('sellersEX'); ?>");
        }
        var view08 = $('textarea[name="buyerOrderRequest"]').val();
        $('#view_08').html(view08);
        var view09 = $('#ORDER_QUOTATION_MODE').find("option:selected").text();
        $('#view_09').html(view09);
        var view10 = $('#PRICE_TERM').find("option:selected").text();
        $('#view_10').html(view10);
        var view11 = $('#ORDER_PACKING_MODE').val();
        var view11text = $('#ORDER_PACKING_MODE').find("option:selected").text();
        $('#view_11').html(view11text);
        if (view11 == 'Full') {
            $('#view_tt01').hide()
        }else {
            $('#view_tt01').show()
        }
        var view12 = $('textarea[name="packingDesc"]').val();
        $('#view_12').html(view12);

        var view13 = $('#SHIPPING_METHOD').find("option:selected").text();
        var view13B = $('#SHIPPING_METHOD').val();
        $('#view_13').html(view13);
        if(view13B=='LAND'||view13B=='EXPRESS'){
            $('#chengshi1').html("
            ShowDictionaryTo
            . $this->translate("citypost1");.
            ');/*起运城市*/
            $('#chengshi2').html('<?php echo $this->translate("citypost2");?>');/*卸货城市*/
            $('#chengshi3').html('<?php echo $this->translate("citypost3");?>');/*交货城市*/
        }

        var view14 = $('#EXPORT_POINTS').find("option:selected").text();
        $('#view_14').html(view14);
//            var shipp1=$('#SHIPPING_PORT1').val();
//            var shipp2=$('#SHIPPING_PORT2').val();
//            var shipp3=$('#SHIPPING_PORT3').val();
        var view15 = $('#select2-SHIPPING_PORT1-container').html();
        var view16 = $('#select2-SHIPPING_PORT2-container').html();
        var view17 = $('#select2-SHIPPING_PORT3-container').html();

        $('#view_15').html(view15);
        $('#view_16').html(view16);
        $('#view_17').html(view17);
        var view18 = $('input[name="deliveryDate"]').val();
        $('#view_18').html(view18);
        var zglx_mod=$('select[name="shippingServiceType"]');
        var view19 = zglx_mod.find("option:selected").text();
        $('#view_19').html(view19);
        if(zglx_mod.val()=='LCL'){
            $('#md_02').hide();
        }else{
            $('#md_02').show();
            var view20 = $('input[name="sizeQuantityMap_20GP"]').val();
            if(view20!='0') {
                $('#view_20').html(view20);
            }else{
                $('.num_view_20').hide();
            }
            var view21 = $('input[name="sizeQuantityMap_40GP"]').val();
            if(view21!='0'){
                $('#view_21').html(view21);
            }else{
                $('.num_view_21').hide();
            }
            var view22 = $('input[name="sizeQuantityMap_40HP"]').val();
            if(view22!='0'){
                $('#view_22').html(view22);
            }else{
                $('.num_view_22').hide();
            }
            var view23 = $('input[name="sizeQuantityMap_45HP"]').val();
            if(view23!='0'){
                $('#view_23').html(view23);
            }else{
                $('.num_view_23').hide();
            }
            var view24 = $('input[name="sizeQuantityMap_20OT"]').val();
            if(view24!='0') {
                $('#view_24').html(view24);
            }else{
                $('.num_view_24').hide();
            }
            var view25 = $('input[name="sizeQuantityMap_40OT"]').val();
            if(view25!='0') {
                $('#view_25').html(view25);
            }else{
                $('.num_view_25').hide();
            }
        }

        var view26=$('input[name="needShipping"]:checked ').val();
        var view27=$('textarea[name="shippingRequest"]').val();
        var ismd_03 = $('#PRICE_TERM').val();
        if(view26=='0'){
            $('#md_03').hide();
        }else{
            if(ismd_03=='EXW'){
                $('#md_03').hide();
            }else{
                $('#view_27').html(view27);
                $('#md_03').show();
            }
        }

        var city1= $('#buyercity').val();
        var city2 = $('#comcity').val();
        var view28=$('input[name="isAssignCustomsAgency"]:checked ').val();
        if(view28=='1'){
            var view30=$('input[name="customsAgencyCode"]').val();
            var view29=$('input[name="customsAgencyName"]').val();
            if(view29==""||view29==null){
                $('#view_30').html(view30);

            } else {
                $('#view_29').html(view29);
            }
            var view31=$('textarea[name="customClearanceRequest"]').val();
            $('#view_31').html(view31);
            if(city1==city2&&city1=="CN"){
                $('#md_06').hide();
                $('#md_05').hide();
            }else{
                $('#md_06').show();
                $('#md_05').show();
            }
        }else{
            $('#md_06').hide();
            $('#md_05').hide();
        }

        var view32=$('input[name="needFinancing"]:checked ').val();
        var view35=$('textarea[name="financingRequest"]').val();
        if(view32=='0'){
            $('#md_07').hide();
        }else{
            $('#view_35').html(view35);
            $('#md_07').show();
        }

        var goodslist = $('#selectgoods').children().clone();
        var goodstotal = $('#totalDATA').children().clone();
        $("#view_goodslist").html(goodslist);
        $("#view_totalgoods").html(goodstotal);
        $('#view_goodslist input').removeAttr("name");
        $('#view_goodslist input').removeAttr("id");
//            $('.select2-container--default').remove();
//            $('.select2-hidden-accessible').removeAttr("class");
        $("#view_goodslist").find('.select2-container--default').remove();
        $("#view_goodslist").find('.select2-hidden-accessible').removeAttr("class");
        $('#view_goodslist input').css('background-color', '#f5f5f5');
        $('#view_goodslist input').css('border', 'none');
        $('#view_goodslist input').attr("disabled", "disabled");
        $('#view_goodslist textarea').removeAttr("name");
        $('#view_goodslist textarea').removeAttr("id");
        $('#view_goodslist select').removeAttr("name");
        $('#view_goodslist select').removeAttr("id");
        $('#view_goodslist select').css('background', 'none');
        $('#view_goodslist select').css('background-color', '#f5f5f5');
        $('#view_goodslist select').css('border', 'none');
        // $('#view_goodslist select').css('-webkit-appearance', 'listitem');
        // $('#view_goodslist select').css('-moz-appearance', 'listitem');
        $('#view_goodslist select').addClass('n_sp4_3_txt');
        $('#view_goodslist select').attr("disabled", "disabled");
        $('#view_goodslist').find(".addb_box").remove();
        $('#view_goodslist').find(".p_delx").remove();
        var view_img = $('.demo').html();
        $('#view_img').html(view_img);
        $('#view_img').find("input").remove();
        $('#view_img').find(".upload-image-list").remove();
        $('#view_img').find(".del_to").remove();
    });
});
EOD;
        echo $html;
	}
}
