$(function() {
//验证产品列表价格格式
    function yzPrice(obj) {
        var va = obj.value;
        if (va == "") {
            obj.style.cssText = 'border:1px solid red;';
            return false;
        }else{
            obj.style.cssText = 'border:none;';
        }
        var reg = /^([1-9]\d{0,15}|0)(\.\d{1,4})?$/;
        var phoneok = reg.test(va);
        if (!phoneok) {
            obj.style.cssText = 'border:1px solid red;';
            return false;
        }else{
            obj.style.cssText = 'border:none;';
        }
    }

//验证产品列表数量格式
    function yzNum(obj) {
        var va = obj.value;
        if (va == "") {
            obj.style.cssText = 'border:1px solid red;';
            return false;
        }else{
            obj.style.cssText = 'border:none;';
        }
        var reg = /^[0-9]+(.[0-9]{4})?$/;
        var phoneok = reg.test(va);
        if (!phoneok) {
            obj.style.cssText = 'border:1px solid red;';
            return false;
        }else{
            obj.style.cssText = 'border:none;';
        }
    }

//订单产品类判断
    function jisuan() {
        //点击计算 总净重|总毛重|总汇率|
        cleartongji();
        var arge2 = 0;
        $('.jine').each(function () {
            var arge1 = 0;
            if ($(this).val() != '') {
                arge1 = parseFloat($(this).val());
            }
            arge2 += arge1;
        });
        var jz2 = 0;
        $('.jinzhong').each(function () {
            var jz1 = 0;
            if ($(this).val() != '') {
                jz1 = parseFloat($(this).val());
            }
            jz2 += jz1;
        });
        var mz2 = 0;
        $('.maozhong').each(function () {
            var mz1 = 0;
            if ($(this).val() != '') {
                mz1 = parseFloat($(this).val());
            }
            mz2 += mz1;
        });

        //获取汇率
        var huilist = "";
        $('#selectgoods').find("li").each(function (index) {
            var basecode = $(this).find(".crncodelist").text();//商品基准货币
            var contcode = $("#CURRENCY  option:selected").val();//对应目标货币
            $.post("/user/system/orderlist/", {
                'baseCrn': basecode,
                'contraCrn': contcode
            }, function (data) {
                var json;
                if (typeof data === 'object') {
                    json = data.result;
                }
                else {
                    json = eval('(' + data.result + ')');
                }
                var huistr = '<dl>' + basecode + '-' + contcode + '&nbsp;' + json + ';</dl>';
                if (huilist.indexOf(huistr) < 0) {
                    huilist += huistr;
                }
                $('#huilv').html(huilist);
            }, "json");
        });

        $('#zjine').html(arge2);
        $('#zmaozhong').html(mz2);
        $('#zjinzhong').html(jz2);
    }

    function cleartongji() {
        $("#zjinzhong").html("");
        $("#zmaozhong").html("");
        $("#huilv").html("");
        $("#zjine").html("");
    }

    function divchu(obj) {
        /*//    输入PO总价得到 PO单价*/
        var id = obj.id;
        var brandNum = id.lastIndexOf("_");
        var bid = id.substr(brandNum + 1);
        // alert(brandHref);
        var totalP = $('#' + id).val();
        var NUM = $('#quantity_' + bid).val();
        var unitprice = arithDiv(totalP, NUM);
        $('#unitPrice_' + bid).val(unitprice);
        jisuan();
    }

    function divmul(obj) {
        /*//输入PO单价得到 PO总价*/
        var id = obj.id;
        var brandNum = id.lastIndexOf("_");
        var bid = id.substr(brandNum + 1);
        // alert(brandHref);
        var unitP = $('#' + id).val();
        var NUM = $('#quantity_' + bid).val();
        var totalp = arithMul(unitP, NUM);
        $('#totalPrice_' + bid).val(totalp);
        $('#totalPurPrice_' + bid).val(totalp);
        jisuan();
    }

    function PURchu(obj) {
        /*//    输入采购总价得到 采购单价*/
        var id = obj.id;
        var brandNum = id.lastIndexOf("_");
        var bid = id.substr(brandNum + 1);
        var totalP = $('#' + id).val();
        var NUM = $('#quantity_' + bid).val();
        var PUR = arithDiv(totalP, NUM);
        $('#PurPrice_' + bid).val(PUR);
        jisuan();
    }

    function PURmul(obj) {
        /*//    输入采购总价得到 采购单价*/
        var id = obj.id;
        var brandNum = id.lastIndexOf("_");
        var bid = id.substr(brandNum + 1);
        var totalP = $('#' + id).val();
        var NUM = $('#quantity_' + bid).val();
        var PUR = arithMul(totalP, NUM);
        $('#totalPurPrice_' + bid).val(PUR);
        jisuan();
    }

//输入订单商品数量得到 PO总价 以及 商品的件数（每件数量：packingQuantity）
    function divNUM(obj) {
        var id = obj.id;
        var brandNum = id.lastIndexOf("_");
        var bid = id.substr(brandNum + 1);
        var packingQuantity = $('#packingQuantity_' + bid).val();
        // alert(brandHref);
        var NUM = $('#' + id).val();
        var unitP = $('#unitPrice_' + bid).val();
        if (unitP == '0' || unitP == null) {
            var totalP = $('#totalPrice_' + bid).val();
            var unitP2 = arithDiv(totalP, NUM);
            $('#unitPrice_' + bid).val(unitP2);
        } else {
            var totalp = arithMul(unitP, NUM);
            $('#totalPrice_' + bid).val(totalp);
        }
        var purunitP = $('#PurPrice_' + bid).val();
        if (purunitP == '0' || purunitP == null) {
            var PURtotalp2 = $('#totalPurPrice_' + bid).val();
            var purunitP2 = arithDiv(PURtotalp2, NUM);
            $('#PurPrice_' + bid).val(purunitP2);
        } else {
            var PURtotalp = arithMul(purunitP, NUM);
            $('#totalPurPrice_' + bid).val(PURtotalp);
        }

        var tpackage = "";
        var intpkg = "";
        if (packingQuantity == null || packingQuantity == "0") {
            tpackage = 0;
        } else {
            tpackage = arithDiv(NUM, packingQuantity);
            intpkg = Math.ceil(tpackage);
        }
        //   $('#totalpackage_'+bid).val(intpkg); //不计算商品件数

        jisuan();
    }

    function addi() {
        /* 触发事件orderIterm序号*/
        $(".select_NO").html("");
        var i = 1;
        $(".select_NO").each(function () {
            $(this).append(i);
            i++;
        });
    }

    function delSelect(obj) {
        /* 触发事件orderIterm移除*/
        $(obj).parent().remove();
        addi();
        jisuan();
        if ($("#selectgoods").find('li').length > 0) {
            $(".selectgoods_a").hide();
        } else {
            $(".selectgoods_a").show();
            cleartongji();
        }
    }




    function customsname() {
        if ($('[name="isAssignCustomsAgency"]:checked').val() == '1') {
            var Code = $('[name="customsAgencyCode"]').val();
            var Name = $('[name="customsAgencyName"]').val();
            if (Code == '' && Name == '') {
                num1 = num1 + 1;
            }
        }
    }


});