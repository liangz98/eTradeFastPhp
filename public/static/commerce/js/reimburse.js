$(document).ready(function($){
    $('a[name="control"]').toggle(function(){
        var $this = $(this);
        $this.html('<img src="/static/commerce/images/details_close.png"/>');
        $this.parents('tr').next('tr').show('slow');
    },function(){
        var $this = $(this);
        $this.html('<img src="/static/commerce/images/details_open.png"/>');
        $this.parents('tr').next('tr').hide('slow');
    });
    $('.dataTable tr:odd').css('background-color', '#e8f1f6').hover(function(){
        $(this).css('background-color', '#D2FFDB');
    }, function(){
        $(this).css('background-color', '#e8f1f6');
    });
    var popSetHtml = '<div class="popover left"><div class="arrow"></div>'+
        '<div class="popover-inner popover-confirm"><div class="popover-content text-center"><div class="form-inline">\n'+
            '<button type="button" class="btn btn-primary js-btn-confirm">确定</button>\n'+
            '<button type="button" class="btn btn-danger js-btn-deny">取消</button>'+
        '</div></div></div></div>';
    var popHtml = '<div class="popover left"><div class="arrow"></div>'+
        '<div class="popover-inner"><div class="popover-content"><div class="form-inline"><div class="input-append">'+
            '<input type="text" class="txt js-url-placeholder url-placeholder" value="">'+
            '<button type="button" class="btn js-btn-edit">确定</button>'+
        '</div></div></div></div></div>';
    var payHtml = '<div class="radio-append"><label class="radio inline radion-text">退款方式：</label><label class="radio inline">'+
        '<input type="radio" name="payment_way" value="0" checked>全额</label><label class="radio inline">'+
        '<input type="radio" name="payment_way" value="1">部分</label></div>';
    var setStatus = function($this, v, r, p) {
        $this.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
        if (typeof r === "undefined") {r = '';}
        if (typeof p === "undefined") {p = 0;}
        var id = $this.attr("reimburseid"),
            param = {reimburse_id:id, reimburse_status:v, reimburse_result:r, payment_way:p};
        $.post("/b/vmall/orderreimburse/setstatus", param, function(op){
            if (op.code == 1) {
                $this.next("img.loading").hide("fast", function(){
                    $this.removeClass().addClass('btn ' + op.data.class + ' status_' + v).text(op.data.text);
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
        }, 'json');
    };
    $(".status_0,.status_1").live('click', function(evt){
        var $pop = $(popSetHtml);
        var $this = $(this);
        var v = 0, status = $this.hasClass('status_0') ? 0 : 1;
        $pop.find('.js-btn-confirm').text(status === 0 ? '接受申请':'确认退款');
        $pop.find('.js-btn-deny').text(status === 0 ? '拒绝申请':'拒绝退款');
        $pop.find('.js-btn-deny').click(function(){
            $pop.hide();
            if (status === 0) {v = 2;}
            else if (status === 1) {v = 4;}
            else {generate("error", '提交失败');return false;}
            setStatus($this, v);
        });
        $pop.find('.js-btn-confirm').click(function(){
            $pop.hide();
            if (status === 0) {v = 1;}
            else if (status === 1) {v = 3;}
            else {generate("error", '提交失败');return false;}
            setStatus($this, v);
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
    $(".status_3").live('click', function(evt){
        var v = 5, $this = $(this), $pop = $(popHtml);
        $pop.find('.js-btn-edit').text('完成');
        $pop.find('.input-append').after(payHtml);
        $pop.find('.js-btn-edit').click(function(){
            $pop.hide();
            var r = $pop.find('input.js-url-placeholder').val();
            var p = $pop.find('input[name="payment_way"]:checked').val();
            if ( !r) {
                generate("error", "请输入退款结果");
                return false;
            }
            setStatus($this, v, r, p);
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
    $(".edit-btn").click(function(evt){
        var $this = $(this),
            $pop = $(popHtml),
            reimburse_id = $this.parents('table').attr('id').replace(/reimburse_/, ''),
            field = $this.attr('field'),
            data = $this.attr('data');
        $pop.removeClass('left').addClass('right');
        $pop.find('input.js-url-placeholder').val(data);
        $pop.find('.js-btn-edit').click(function(){
            $pop.hide();
            var value = $pop.find('input.js-url-placeholder').val();
            if ( !value) {
                generate("error", "请输入数据");
                return false;
            }
            if (value == data) {
                generate("success", "修改成功");
                return false;
            }
            $this.next('span').hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            $.getJSON("/b/vmall/orderreimburse/update", {id:reimburse_id, field:field, data:value}, function(op){
                if (op.code == 1) {
                    $this.attr("data", value);
                    $this.next('span').text(value);
                    $this.nextAll("img.loading").hide("fast", function(){
                        $this.nextAll("img.loading").remove();
                        $this.next('span').show("fast");
                    });
                    generate("success", op.msg);
                } else {
                    $this.nextAll("img.loading").hide("fast", function(){
                        $this.nextAll("img.loading").remove();
                        $this.next('span').show("fast");
                    });
                    generate("error", op.msg);
                }
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
});