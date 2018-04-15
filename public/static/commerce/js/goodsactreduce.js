$(function($){
    var creating = false, deleting = false, editing = false, goods = {};
    var popDelHtml = '<div class="popover left"><div class="arrow"></div>'+
        '<div class="popover-inner popover-delete"><div class="popover-content text-center"><div class="form-inline">\n'+
            '<span class="help-inline item-delete">确定删除?</span>\n'+
            '<button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n'+
            '<button type="reset" class="btn js-btn-cancel">取消</button>'+
        '</div></div></div></div>';
    var popHtml = '<div class="popover left"><div class="arrow"></div>'+
        '<div class="popover-inner popover-edit"><div class="popover-content"><div class="form-inline"><div class="input-append">'+
            '<input type="text" class="txt js-url-placeholder url-placeholder" value="">'+
            '<button type="button" class="btn js-btn-edit">修改</button>'+
        '</div></div></div></div></div>';
    $('#start_time').datetimepicker({
        language: 'zh-CN',
        pickTime: false
    });
    $('#finish_time').datetimepicker({
        language: 'zh-CN',
        pickTime: false
    });
    $("#goods_id").select2({
        width: '360px',
        dropdownAutoWidth: false,
        minimumInputLength: 0,
        ajax: {
            url: '/b/vmall/goodsactreduce/goodselect',
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term,
                    page: page
                };
            },
            results: function (data, page) {
                return {results:data.rst, more:(page*20<data.total)};
            }
        },
        formatSelection: function(row) {
            goods.img_url = row.img_url;
            goods.goods_name = row.goods_name;
            goods.goods_id = row.goods_id;
            goods.stock_id = row.stock_id;
            goods.stock_name = row.stock_name;
            goods.shop_price = row.shop_price;
            return row.goods_name + (!row.stock_name ? '' : '&nbsp;&nbsp;' + row.text);
        },
        escapeMarkup: function (m) { return m; }
    });
    $("#deleteBtn").click(function(evt){
        var $pop = $(popDelHtml);
        $pop.removeClass('left').addClass('right');
        $pop.find('.js-btn-cancel').click(function(){
            $pop.hide();
        });
        $pop.find('.js-btn-confirm').click(function(){
            $pop.hide();
            if (deleting) {return false;}
            var idList = [], idQuery;
            $("input[name='chose[]']:checked").each(function(){
                var $this = $(this);
                idList.push($this.val());
                $this.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            });
            idQuery = idList.join(',');
            if ( !idQuery) {
                generate("error", "请先选择要删除的记录");
                return false;
            }
            deleting = true;
            $("input[name='chose[]']").attr("disabled", true);
            $.getJSON("/b/vmall/goodsactreduce/delete", {ids:idQuery}, function(op){
                if (op.code == 1) {
                    $("img.loading").each(function(){
                        var $this = $(this);
                        $this.parents("tr").hide('slow', function(){
                            $this.remove();
                        });
                    });
                    generate("success", op.msg);
                } else {
                    $("img.loading").each(function(){
                        var $this = $(this);
                        $this.hide("fast", function(){
                            $this.prev("input").show("fast");
                            $this.remove();
                        });
                    });
                    generate("error", op.msg);
                }
                deleting = false;
                $("input[name='chose[]']").attr("disabled", false);
            });
        });
        $('div[class="popover left"],div[class="popover right"]').remove();
        $('body').append($pop);

        var p = $('div[class="popover right"]'),
            e = $(this).offset(),
            t ={height: $(this).outerHeight(), width: $(this).outerWidth()},
            i ={height: p.outerHeight(), width: p.outerWidth()};
        $pop.css({display: "block", left: e.left + t.width, top: e.top - i.height / 2 + t.height / 2});

        $(document).click(function(e){
            var _con = $('div[class="popover right"]');
            if(!_con.is(e.target) && _con.has(e.target).length === 0){
                _con.hide();
            }
        });
        evt.stopPropagation();
    });
    $("a.deleteItem").live("click", function(evt){
        var $pop = $(popDelHtml);
        var $this = $(this), id = $this.attr("data");
        $pop.find('.js-btn-cancel').click(function(){
            $pop.hide();
        });
        $pop.find('.js-btn-confirm').click(function(){
            $pop.hide();
            deleting = true;
            $("input[name='chose[]']").attr("disabled", true);
            $this.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            $.getJSON("/b/vmall/goodsactreduce/delete", {ids:id}, function(op){
                if (op.code == 1) {
                    $this.parents("tr").hide('slow', function(){
                        $this.remove();
                    });
                    generate("success", op.msg);
                } else {
                    $this.next("img.loading").hide("fast", function(){
                        $this.show("fast");
                        $this.next("img.loading").remove();
                    });
                    generate("error", op.msg);
                }
                deleting = false;
                $("input[name='chose[]']").attr("disabled", false);
            });
        });
        $('div[class="popover left"],div[class="popover right"]').remove();
        $('body').append($pop);

        var p = $('div[class="popover left"]'),
            e = $(this).offset(),
            t ={height: $(this).outerHeight(), width: $(this).outerWidth()},
            i ={height: p.outerHeight(), width: p.outerWidth()};
        $pop.css({display: "block", left: e.left - i.width, top: e.top - i.height / 2 + t.height / 2});

        $(document).click(function(e){
            var _con = $('div[class="popover left"]');
            if(!_con.is(e.target) && _con.has(e.target).length === 0){
                _con.hide();
            }
        });
        evt.stopPropagation();
    });
    $("#createBtn").click(function(){
        if (creating) {return false;}
        var param = {},
            rsHtml = '<tr><td colspan="6"><img class="loading" src="/static/commerce/images/loader.gif"></td></tr>',
            rowHtml = '<tr style="display:none;"><td><input type="checkbox" name="chose[]" value="" class="ids_all"></td>'+
                      '<td><img src="" style="width:50px;"></td><td></td><td><a class="edit-link" href="javascript:;" field="limit_price" data=""></a></td>'+
                      '<td><a class="edit-link" href="javascript:;" field="reduce_value" data=""></a></td><td></td><td></td><td></td>'+
                      '<td><a class="deleteItem" data="" href="javascript:;">删除</a></td></tr>',
            $rsDom = $(rsHtml),
            $rowDom = $(rowHtml);
        param.limit_price = parseFloat($("#limit_price").val());
        param.reduce_value = parseFloat($("#reduce_value").val());
        param.reduce_type = parseInt($("#reduce_type").val());
        param.start_time = $("#start_time input").val();
        param.finish_time = $("#finish_time input").val();
        if (typeof(goods.goods_id) == "undefined") {
            param.goods_id = 0;
            param.stock_id = 0;
        } else {
            param.goods_id = goods.goods_id;
            param.stock_id = goods.stock_id;
        }
        if (isNaN(param.limit_price) || param.limit_price != $("#limit_price").val()) {
            generate("error", "请输入满减价格");
            return false;
        }
        if (isNaN(param.reduce_value) || param.reduce_value != $("#reduce_value").val()) {
            generate("error", "请输入满减数值");
            return false;
        }
        if ( !param.start_time) {
            generate("error", "请输入开始时间");
            return false;
        }
        if ( !param.finish_time) {
            generate("error", "请输入结束时间");
            return false;
        }
        param.limit_price = param.limit_price.toFixed(2);
        if (param.reduce_type == 1) {
            if (param.reduce_value < 0.1 || param.reduce_value > 9.9) {
                generate("error", "折扣范围为 0.1 到 9.9");
                return false;
            }
            param.reduce_value = param.reduce_value.toFixed(1);
        } else {
            if (param.reduce_value < 0.01) {
                generate("error", "优惠减免金额必须大于 0.01 元");
                return false;
            } else if (param.reduce_value >= parseFloat(goods.shop_price)) {
                generate("error", '优惠减免金额 ' + param.reduce_value + ' 不能高于商品价格');
                return false;
            }
            param.reduce_value = param.reduce_value.toFixed(2);
        }
        creating = true;
        $("table.admin_list tr:first").after($rsDom);
        $.post("/b/vmall/goodsactreduce/add", param, function(op){
            if (op.code == 1) {
                $rowDom.attr("id", "reduce_id_"+op.id);
                $rowDom.find("td").each(function(i){
                    switch (i) {
                        case 0:
                            $(this).find("input").val(op.id);
                            return true;
                        case 1:
                            if (typeof(goods.img_url) == "undefined") {
                                $(this).html('');
                            } else {
                                $(this).find("img").attr("src", goods.img_url);
                            }
                            return true;
                        case 2:
                            if (typeof(goods.goods_name) == "undefined") {
                                $(this).html('全场满减');
                            } else {
                                $(this).html(goods.goods_name + '<br/>' + goods.stock_name);
                            }
                            return true;
                        case 3:
                            $(this).find("a").text(param.limit_price).attr("data", param.limit_price);
                            return true;
                        case 4:
                            $(this).find("a").text(param.reduce_value).attr("data", param.reduce_value);
                            return true;
                        case 5:
                            $(this).text(param.reduce_type == 1 ? '折扣' : '直减');
                            return true;
                        case 6:
                            $(this).html(param.start_time);
                            return true;
                        case 7:
                            $(this).html(param.finish_time);
                            return true;
                        case 8:
                            $(this).find("a").attr("data", op.id);
                            return true;
                    }
                });
                $rsDom.hide("slow", function(){
                    $rsDom.remove();
                    $("table.admin_list tr:first").after($rowDom);
                    $rowDom.show("slow");
                });
                $('#limit_price,#reduce_value').val('');
                $('#start_time input,#finish_time input').val('');
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
    $("a.edit-link").live("click", function(evt){
        var $this = $(this),
            $pop = $(popHtml),
            reduce_id = $this.parents('tr').attr('id').replace(/reduce_id_/, ''),
            field = $this.attr('field'),
            data = $this.attr('data');
        $pop.find('input.js-url-placeholder').val(data);
        $pop.find('.js-btn-edit').click(function(){
            $pop.hide();
            if (editing) {return false;}
            var value = parseFloat($pop.find('input.js-url-placeholder').val());
            if (field == "limit_price" && (isNaN(value) || value != $pop.find('input.js-url-placeholder').val())) {
                generate("error", "请输入正确的价格");
                return false;
            }
            if (field == "reduce_value" && ( !value || value != $pop.find('input.js-url-placeholder').val())) {
                generate("error", "请输入正确的数值");
                return false;
            }
            if (value == data) {
                generate("success", "修改成功");
                return false;
            }
            editing = true;
            $this.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            $.getJSON("/b/vmall/goodsactreduce/update", {id:reduce_id, field:field, data:value}, function(op){
                if (op.code == 1) {
                    $this.text(value);
                    $this.attr("data", value);
                    $this.next("img.loading").hide("fast", function(){
                        $this.show("fast");
                        $this.next("img.loading").remove();
                    });
                    generate("success", op.msg);
                } else {
                    $this.next("img.loading").hide("fast", function(){
                        $this.show("fast");
                        $this.next("img.loading").remove();
                    });
                    generate("error", op.msg);
                }
                editing = false;
            });
        });
        $('div[class="popover left"],div[class="popover right"]').remove();
        $('body').append($pop);

        var p = $('div[class="popover left"]'),
            e = $(this).offset(),
            t ={height: $(this).outerHeight(), width: $(this).outerWidth()},
            i ={height: p.outerHeight(), width: p.outerWidth()};
        $pop.css({display: "block", left: e.left - i.width, top: e.top - i.height / 2 + t.height / 2});

        $(document).click(function(e){
            var _con = $('div[class="popover left"]');
            if(!_con.is(e.target) && _con.has(e.target).length === 0){
                _con.hide();
            }
        });
        evt.stopPropagation();
    });
});