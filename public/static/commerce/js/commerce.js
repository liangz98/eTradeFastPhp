$(function($) {

    $('div.modal').on("hidden", function() {
        $(this).removeData("modal");
    });

    $('input[name="toggle_button"]').bootstrapSwitch({
        size: 'small',
        onColor: 'primary',
        offColor: 'danger',
        onText: '通过',
        offText: '拒绝',
        onSwitchChange: function(){
            var $this = $(this), id = $this.attr('data'),
                fkey = $this.attr('fkey'),
                baseUrl = window.location.href.replace(/(http:\/\/[a-z.0-9]+\/b\/[a-z]+\/[a-z]+)(\/index)*\/*\?{0,1}.*/, '$1');
            if ( !id || !fkey) {return false;}
            $.post(baseUrl + '/toggle', {id:id, fkey:fkey}, function(op){
                if (op.code == 1) {
                    generate("success", op.msg);
                } else {
                    generate("error", op.msg);
                }
            }, 'json');
        }
    });

    $('.js-copy-link').seedtips({type:'copy', direct:'left'});

    $('a[name="delete_item"]').seedtips({
        type: 'confirm',
        direct: 'left',
        text: '确定删除?',
        onConfirm: function(element) {
            var $element = $(element), id = $element.attr('data'),
                baseUrl = window.location.href.replace(/(http:\/\/[a-z.0-9]+\/b\/[a-z]+\/[a-z]+)(\/index)*\/*\?{0,1}.*/, '$1');
            $element.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            $("input[name='chose[]']").attr("disabled", true);
            $.post(baseUrl + "/delete", {ids:id}, function(op){
                if (op.code == 1) {
                    $element.parents("tr").hide('slow', function(){
                        $element.parents("tr").remove();
                    });
                    generate("success", op.msg);
                } else {
                    $element.next("img.loading").hide("fast", function(){
                        $element.show("fast");
                        $element.next("img.loading").remove();
                    });
                    generate("error", op.msg);
                }
                $("input[name='chose[]']").attr("disabled", false);
            }, 'json');
        }
    });

    $('input[name="delete"][type="button"]').seedtips({
        type: 'confirm',
        direct: 'top',
        text: '确定删除?',
        onConfirm: function() {
            var idList = [], idQuery,
                baseUrl = window.location.href.replace(/(http:\/\/[a-z.0-9]+\/b\/[a-z]+\/[a-z]+)(\/index)*\/*\?{0,1}.*/, '$1');
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
            $("input[name='chose[]']").attr("disabled", true);
            $.post(baseUrl + "/delete", {ids:idQuery}, function(op){
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
                $("input[name='chose[]']").attr("disabled", false);
            }, 'json');
        }
    });

    $('input[name="pass"][type="button"], input[name="deny"][type="button"]').seedtips({
        type: 'confirm',
        direct: 'top',
        text: '确定提交?',
        onConfirm: function(element){
            var action = element.name, idList = [], idQuery = '', $element = $(element),
                is_remove = $element.hasClass('js-remove') ? true : false,
                state = (action === 'pass') ? 1 : 0, fkey = $element.attr('fkey'),
                $chose = $("input[name='chose[]']:checked"),
                baseUrl = window.location.href.replace(/(http:\/\/[a-z0-9.-]+\/b\/[a-z]+\/[a-z]+)(\/index)*\/*\?{0,1}.*/, '$1');
            if ( !fkey) {return false;}
            if ($chose.length === 0) {
                generate("error", "请先选择要提交的记录");
                return false;
            }
            $chose.each(function(){
                var $this = $(this), val = $this.val();
                idList.push(val);
                $this.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
                if (is_remove) {
                    $this.addClass('js-input-remove');
                }
            });
            idQuery = idList.join(',');
            $("input[name='chose[]']").attr("disabled", true);
            $.post(baseUrl + '/setval', {ids:idQuery, state:state, fkey:fkey}, function(op){
                $("img.loading").each(function(){
                    var $this = $(this);
                    $this.hide("fast", function(){
                        if ($this.prev("input").hasClass('js-input-remove') && op.code == 1) {
                            $this.parents("tr").hide('slow', function(){
                                $this.parents("tr").remove();
                            });
                        } else {
                            $this.prev("input").show("fast");
                            $this.remove();
                        }
                    });
                });
                if (op.code == 1) {
                    $("input[name='chose[]']:checked").each(function(){
                        var $this = $(this), id = $this.val(),
                            selector = 'input[name="toggle_button"][data="' + id + '"]';
                        if (typeof op.data.fkey !== 'undefined') {
                            selector += '[fkey="' + fkey + '"]';
                        }
                        $(selector).bootstrapSwitch('state', Boolean(state), true);
                    });
                    generate("success", op.msg);
                } else {
                    generate("error", op.msg);
                }
                $("input[name='chose[]']").attr("disabled", false);
            }, 'json');
        }
    });

    $('a[name="pass"]').seedtips({
        type: 'confirm',
        direct: 'left',
        text: '确定提交?',
        onConfirm: function(element){
            var action = element.name, $element = $(element),
                id = $element.attr('data'), fkey = $element.attr('fkey'),
                state = (action === 'pass') ? 1 : 0,
                baseUrl = window.location.href.replace(/(http:\/\/[a-z0-9.-]+\/b\/[a-z]+\/[a-z]+)(\/index)*\/*\?{0,1}.*/, '$1');
            if ( !fkey) {return false;}
            $element.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            $("input[name='chose[]']").attr("disabled", true);
            $.post(baseUrl + "/setval", {ids:id, state:state, fkey:fkey}, function(op){
                if (op.code == 1) {
                    var show = $element.attr('show');
                    if (show) {
                        $element.next("img.loading").remove();
                        $element.replaceWith(show);
                    } else {
                        $element.parents("tr").hide('slow', function(){
                            $element.parents("tr").remove();
                        });
                    }
                    generate("success", op.msg);
                } else {
                    $element.next("img.loading").hide("fast", function(){
                        $element.show("fast");
                        $element.next("img.loading").remove();
                    });
                    generate("error", op.msg);
                }
                $("input[name='chose[]']").attr("disabled", false);
            }, 'json');
        }
    });

    $('a.js-tip-modify').seedtips({
        type: 'modify',
        direct: 'left',
        onConfirm: function(element){
            var $pop = $('#seed-tip'),
                $element = $(element), value,
                data = $element.attr('data'),
                mid = $element.attr('mid'),
                mtype = $element.attr('mtype'),
                field = $element.attr('field'),
                zero  = $element.attr('zero'),
                error = $element.attr('error'),
                baseUrl = window.location.href.replace(/(http:\/\/[a-z.0-9]+\/b\/[a-z]+\/[a-z]+)(\/index)*\/*\?{0,1}.*/, '$1');
            var placeholder = $pop.find('input.js-url-placeholder').val();
            if ( !error) {error = "请输入正确的值";}
            if ( !field) {field = '';}
            if ( !mtype) {mtype = '';}
            if ( !placeholder) {
                generate("error", "请输入要修改的值");
                return false;
            }
            switch (mtype) {
                case 'float':
                    value = parseFloat(placeholder);
                    break;
                case 'int':
                    value = parseInt(placeholder);
                    break;
                default:
                    value = placeholder;
            }
            if ((zero === 'true' && !value) || value !== placeholder) {
                generate("error", error);
                return false;
            }
            if (data && value == data) {
                generate("success", "修改成功");
                return false;
            }
            $element.hide().after('<img class="loading" src="/static/commerce/images/loader.gif">');
            $.post(baseUrl + "/modify", {mid:mid, mtype:mtype, field:field, data:value}, function(op){
                if (op.code == 1) {
                    if (data) {
                        $element.text(value);
                        $element.attr("data", value);
                    }
                    $element.next("img.loading").hide("fast", function(){
                        $element.show("fast");
                        $element.next("img.loading").remove();
                    });
                    generate("success", op.msg);
                } else {
                    $element.next("img.loading").hide("fast", function(){
                        $element.show("fast");
                        $element.next("img.loading").remove();
                    });
                    generate("error", op.msg);
                }
            }, 'json');
        }
    });

	$(".eidt_help em").click(function(){
		if($(this).parents("span").hasClass("crumb")){
			$(this).parents("span").removeClass("crumb");
		}else{
			$(this).parents("span").addClass("crumb");
		}
	});

});