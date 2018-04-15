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
            $.getJSON("/b/vmall/Answer/delete", {ids:id}, function(op){
                if (op.code == 1) {
                    $this.parents("tr").hide('slow', function(){
                        $this.parents("tr").remove();
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
            $.getJSON("/b/Answer/delete", {ids:idQuery}, function(op){
                if (op.code == 1) {
                    $("img.loading").each(function(){
                        var $this = $(this);
                        $this.parents("tr").hide('slow', function(){
                            $this.parents("tr").remove();
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
   
});