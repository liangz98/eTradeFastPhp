$(function($){
    var creating = false, deleting = false, editing = false, goods = {}, gift = {};
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
            url: '/b/vmall/goodsactgift/goodselect',
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
            return row.goods_name + (!row.stock_name ? '' : '&nbsp;&nbsp;' + row.text);
        },
        escapeMarkup: function (m) { return m; }
    });
    $("#gift_goods_id").select2({
        width: '360px',
        dropdownAutoWidth: false,
        minimumInputLength: 0,
        ajax: {
            url: '/b/vmall/goodsactgift/goodselect',
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
            gift.img_url = row.img_url;
            gift.goods_name = row.goods_name;
            gift.goods_id = row.goods_id;
            gift.stock_id = row.stock_id;
            gift.stock_name = row.stock_name;
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
            $.getJSON("/b/vmall/goodsactgift/delete", {ids:idQuery}, function(op){
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
            $.getJSON("/b/vmall/goodsactgift/delete", {ids:id}, function(op){
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
                      '<td><a class="edit-link" href="javascript:;" field="gift_number" data=""></a></td>'+
                      '<td><a class="deleteItem" data="" href="javascript:;">删除</a></td></tr>',
            $rsDom = $(rsHtml),
            $rowDom = $(rowHtml);
        param.goods_id = goods.goods_id;
        param.stock_id = goods.stock_id;
        param.gift_goods_id = gift.goods_id;
        param.gift_stock_id = gift.stock_id;
        param.limit_price = parseFloat($("#limit_price").val());
        param.gift_number = parseInt($("#gift_number").val());
        param.start_time = $("#start_time input").val();
        param.finish_time = $("#finish_time input").val();
        if (typeof(goods.goods_id) == "undefined") {
            generate("error", "请选择商品");
            return false;
        }
        if (isNaN(param.limit_price) || param.limit_price != $("#limit_price").val()) {
            generate("error", "请输入满送价格");
            return false;
        }
        if ( !param.gift_number || param.gift_number != $("#gift_number").val()) {
            generate("error", "请输入满送数量");
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
        creating = true;
        $("table.admin_list tr:first").after($rsDom);
        $.post("/b/vmall/goodsactgift/add", param, function(op){
            if (op.code == 1) {
                $rowDom.attr("id", "gift_id_"+op.id);
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
                            $(this).find("a").text(param.limit_price).attr("data", param.limit_price);
                            return true;
                        case 4:
                            $(this).find("a").text(param.gift_number).attr("data", param.gift_number);
                            return true;
                        case 5:
                            $(this).find("a").attr("data", op.id);
                            return true;
                    }
                });
                $rsDom.hide("slow", function(){
                    $rsDom.remove();
                    $("table.admin_list tr:first").after($rowDom);
                    $rowDom.show("slow");
                });
                $('#limit_price,#gift_number').val('');
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
            gift_id = $this.parents('tr').attr('id').replace(/gift_id_/, ''),
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
            if (field == "gift_number" && ( !value || value != $pop.find('input.js-url-placeholder').val())) {
                generate("error", "请输入正确的数量");
                return false;
            }
            if (value == data) {
                generate("success", "修改成功");
                return false;
            }
            value = (field == "limit_price" ? value.toFixed(2) : parseInt(value));
            editing = true;
            $this.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            $.getJSON("/b/vmall/goodsactgift/update", {id:gift_id, field:field, data:value}, function(op){
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