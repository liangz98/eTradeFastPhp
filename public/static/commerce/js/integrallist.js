$(function($){
    var creditHtml = '<div class="poptip-popover popover left"><div class="arrow"></div><div class="popover-inner">'+
        '<div class="popover-content"><div class="poptip-header clearfix"><button type="button" class="close js-close">×</button>'+
        '<div class="poptip-title clearfix">修改积分 </div></div><div class="poptip-create-content">'+
        '<form class="form-horizontal poptip-form-create">'+
        '<div class="control-group"><label class="control-label">积分行为：</label><div class="controls"><label class="radio inline">'+
        '<input type="radio" name="integral_type" value="0" checked>添加积分</label>\n<label class="radio inline">'+
        '<input type="radio" name="integral_type" value="1">减去积分</label></div></div>'+
        '<div class="js-coupon"><div class="control-group"><label class="control-label">积分数值：</label><div class="controls">'+
        '<div class="input-prepend"><span class="add-on">+</span><input type="text" name="integral_value" class="input input-mini js-integral" value="">'+
        '</div></div></div><div class="control-group"><label class="control-label">积分说明：</label>'+
        '<div class="controls"><div class="input-prepend"><textarea name="integral_desc"></textarea></div></div></div></div>'+
        '<div class="control-group"><div class="controls">'+
        '<button type="button" class="btn btn-create-poptip btn-primary js-submit-btn" data-loading-text="保 存...">保 存</button>'+
        '<button type="button" class="btn js-cancel-btn">取 消</button></div></div></form>'+
        '</div></div></div></div>';

    $('a.js-integral-btn').click(function(){
        var $thisBtn = $(this), $ctHtml = $(creditHtml),
            uid = parseInt($thisBtn.attr('uid')),
            iamount = parseInt($thisBtn.attr('iamount'));
        $ctHtml.find('.js-close').click(function(){
            $ctHtml.hide();
        });
        $ctHtml.find('input[name="integral_type"]').click(function(){
            var $this = $(this), iType = $this.val(),
                iFlag = (iType === '1') ? '-' : '+';
            $ctHtml.find('span.add-on').text(iFlag);
        });
        $ctHtml.find('input[name="integral_value"]').blur(function(){
            var $this = $(this), msg = '',
                ival = parseInt($this.val()),
                iType = $ctHtml.find('input[name="integral_type"]:checked').val();
            $this.parent('div.input-prepend').next('p.error-message').remove();
            $this.parents('div.control-group').removeClass('error');
            if (isNaN(ival)) {
                msg = '积分数值必须是一个数字';
            } else if (ival <= 0) {
                msg = '积分数值必须大于零';
            } else if (iType === '1' && ival > iamount) {
                msg = '减去的积分数值 ' + ival + ' 不能高于用户总积分';
            }
            if (msg) {
                $this.parent('div.input-prepend').after('<p class="help-block error-message">'+msg+'</p>');
                $this.parents('div.control-group').addClass('error');
            }
        });
        $ctHtml.find('textarea[name="integral_desc"]').blur(function(){
            var $this = $(this), idesc = $this.val(), msg = '';
            $this.parent('div.input-prepend').next('p.error-message').remove();
            $this.parents('div.control-group').removeClass('error');
            if (!idesc) {
                msg = '请填写积分说明';
            }
            if (msg) {
                $this.parent('div.input-prepend').after('<p class="help-block error-message">'+msg+'</p>');
                $this.parents('div.control-group').addClass('error');
            }
        });
        $ctHtml.find('button.js-cancel-btn').click(function(){
            $ctHtml.hide();
            return false;
        });
        $ctHtml.find('button.js-submit-btn').click(function(){
            var $thisVal = $ctHtml.find('input[name="integral_value"]'),
                $thisDesc = $ctHtml.find('textarea[name="integral_desc"]'),
                ival = parseInt($thisVal.val()), idesc = $thisDesc.val(), vmsg = '', dmsg = '',
                iType = $ctHtml.find('input[name="integral_type"]:checked').val();
            $thisVal.parent('div.input-prepend').next('p.error-message').remove();
            $thisVal.parents('div.control-group').removeClass('error');
            $thisDesc.parent('div.input-prepend').next('p.error-message').remove();
            $thisDesc.parents('div.control-group').removeClass('error');
            if (isNaN(ival)) {
                vmsg = '积分数值必须是一个数字';
            } else if (ival <= 0) {
                vmsg = '积分数值必须大于零';
            } else if (iType === '1' && ival > parseFloat(iamount)) {
                vmsg = '减去的积分数值 ' + ival + ' 不能高于用户总积分';
            }
            if (vmsg) {
                $thisVal.parent('div.input-prepend').after('<p class="help-block error-message">'+vmsg+'</p>');
                $thisVal.parents('div.control-group').addClass('error');
            }
            if (!idesc) {
                dmsg = '请填写积分说明';
            }
            if (dmsg) {
                $thisDesc.parent('div.input-prepend').after('<p class="help-block error-message">'+dmsg+'</p>');
                $thisDesc.parents('div.control-group').addClass('error');
            }
            if (vmsg || dmsg) {
                return false;
            }
            $ctHtml.find('.poptip-header').addClass('loading');
            var postData = {uid:uid, integral_value:ival, integral_desc:idesc, integral_type:iType};
            $.post('/b/vcredit/integraloperator/modify', postData, function(op){
                $ctHtml.find('.poptip-header').removeClass('loading');
                if (op.code === 1) {
                    var new_ival = (iType === '1') ? iamount - ival : iamount + ival;
                    $thisBtn.attr('iamount', new_ival);
                    $thisBtn.parent('td').prev('td').text(new_ival);
                    $ctHtml.hide('fast', function(){
                        $ctHtml.remove();
                    });
                    generate("success", op.msg);
                } else {
                    generate("error", op.msg);
                }
            }, 'json');
            return false;
        });
        $('.poptip-popover').remove();
        $('body').append($ctHtml);

        var p = $('.poptip-popover'),
            e = $(this).offset(),
            i ={height: p.outerHeight(), width: p.outerWidth()};
        $ctHtml.css({display: "block", left: e.left - i.width, top: e.top - 26});
    });
});