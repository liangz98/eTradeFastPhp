$(function($){
    var buyTypeList = [], goods = {}, qrcodeList = [],
        qrcodeData = {createble:true, limit:5, num:0};
    var popDelHtml = '<div class="popover left"><div class="arrow"></div>'+
        '<div class="popover-inner popover-delete"><div class="popover-content text-center"><div class="form-inline">\n'+
            '<span class="help-inline item-delete">确定删除?</span>\n'+
            '<button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n'+
            '<button type="reset" class="btn js-btn-cancel">取消</button>'+
        '</div></div></div></div>';
    var createHtml = '<form novalidate="" class="form-horizontal poptip-form-create"><div class="control-group"><label class="control-label">购买方式：</label>'+
        '<div class="controls"><select name="buy_type" class="input-medium"<%= editHtml%>><%= optionHtml%></select><%= pHtml%></div></div>'+
        '<div class="control-group"><label class="control-label">店铺编号：</label>'+
        '<div class="controls"><input type="text" name="shop_id" class="input input-mini" value="0">官方店为0</div></div>'+
        '<div class="control-group"><label class="control-label">优惠方式：</label><div class="controls"><label class="radio inline">'+
        '<input type="radio" name="type" value="0"<%= typeCheckSt%><%= editHtml%>>扫码折扣</label>\n<label class="radio inline">'+
        '<input type="radio" name="type" value="1"<%= typeCheckSe%><%= editHtml%>>扫码可减优惠</label></div></div>'+
        '<div class="js-coupon-0<%= typeHideSe%>"><div class="control-group"><label class="control-label">扫码折扣：</label><div class="controls">'+
        '<div class="input-append"><input type="text" name="discount" class="input input-mini js-price" data-fixed="1" value="<%= disHtml%>">'+
        '<span class="add-on">折</span></div></div></div><div class="control-group"><label class="control-label">折后价：</label>'+
        '<div class="controls"><div class="control-action"><%= priceHtml%></div></div></div></div>'+
        '<div class="js-coupon-1<%= typeHideSt%>"><div class="control-group"><label class="control-label">'+
        '扫码优惠：</label><div class="controls"><div class="input-prepend"><span class="add-on">-￥</span><input type="text" '+
        'name="price" class="input input-mini js-price" value="<%= priHtml%>"></div></div></div></div><div class="control-group">'+
        '<div class="controls"><button type="submit" class="btn btn-create-poptip btn-primary js-submit-btn" data-loading-text="保 存...">'+
        '保 存</button><button type="cancel" class="btn js-cancel-btn">取 消</button></div></div></form>';
    var layout = '<div class="poptip-popover popover left"><div class="arrow"></div><div class="popover-inner">'+
        '<div class="popover-content"><div class="poptip-header clearfix"><button type="button" class="close js-close">×</button>'+
        '<div class="poptip-title clearfix">商品二维码 </div></div><div class="poptip-content"><div class="poptip-left-sidebar">'+
        '<div class="poptip-left-lists"></div><a href="javascript:;" class="js-create-poptip poptip-create-button">创建一个商品二维码</a></div>'+
        '<div class="poptip-right-sidebar"><div class="text-center"></div></div></div><div class="poptip-create-content"></div></div></div></div>';
    var item = '<a href="javascript:;" class="js-delete-poptip pull-left btn-delete-poptip">删除</a>';
    var qrcode = '<img class="loading" src="<%= data%>" width="190" height="190"><p><%= description%></p>'+
        '<div class="clearfix poptip-links"><a class="pull-left" href="<%= download%>">下载二维码</a>';
    var editLink = '<a class="pull-right js-edit" href="javascript:;">修改优惠</a></div>';
    var txt = '<p class="text-left"><%= data%></p>';

    $('.js-poptip-btn').click(function(){
        var $layout = $(layout);
        var gid = $(this).attr('gid');
        $layout.find('.poptip-header').addClass('loading');
        $layout.find('.poptip-content').css('visibility', 'hidden');
        $layout.find('.js-close').click(function(){
            $layout.hide();
        });
        $('.poptip-popover').remove();
        $('body').append($layout);

        var p = $('.poptip-popover'),
            e = $(this).offset(),
            i ={height: p.outerHeight(), width: p.outerWidth()};
        $layout.css({display: "block", left: e.left - i.width, top: e.top - 26});
        $.getJSON('/b/vmall/goodsajax/qrcodes',{goods_id:gid}, function(op){
            if (op.code === 0){
                buyTypeList = op.data.buy_types;
                goods = op.data.goods;
                qrcodeList = op.data.qrcodes;
                qrcodeData.creatable = op.data.creatable;
                qrcodeData.limit = op.data.limit;
                qrcodeData.num = op.data.num;
                if (typeof(op.data.qrcodes[0]) === "undefined"){return;}
                var qr = op.data.qrcodes[0];
                var ulHtml = '<ul>', textHtml = '';
                for (var i=0;i<op.data.qrcodes.length;i++){
                    ulHtml += '<li'+(op.data.qrcodes[i].id > 0 ? ' id="gq_id_'+op.data.qrcodes[i].id+'"' : '')+
                        ' class="clearfix'+(i===0?' active':'')+'">'+
                        (op.data.qrcodes[i].deletable ? item : '') + op.data.qrcodes[i].title+'</li>';
                }
                ulHtml += '</ul>';
                if ( !op.data.creatable || op.data.qrcodes.length >= op.data.limit){
                    $('.poptip-popover').find('.js-create-poptip').hide();
                } else {
                    $('.poptip-popover').find('.js-create-poptip').show();
                }
                if (qr.type !== 'error'){
                    var qc = qrcode;
                    if(qr.setting.type !== -1) {qc += editLink;}
                    qc = qc.replace(/<%= data%>/g, qr.data);
                    qc = qc.replace(/<%= description%>/g, qr.description + qr.setting_description);
                    textHtml = qc.replace(/<%= download%>/g, qr.download);
                } else {
                    var qtxt = txt;
                    textHtml = qtxt.replace(/<%= data%>/g, qr.data);
                }
                $('.poptip-popover').find('.poptip-left-lists').append(ulHtml);
                $('.poptip-popover').find('.text-center').append(textHtml);
            } else {
                $('.poptip-popover').find('.poptip-content').html(op.msg);
            }
            $('.poptip-popover').find('.poptip-header').removeClass('loading');
            $('.poptip-popover').find('.poptip-content').css('visibility', 'visible');
        });
    });

    $(document).on('click', '.poptip-left-lists li', function(){
        var index = $('.poptip-left-lists li').index($(this));
        if (typeof(qrcodeList[index]) === "undefined"){return;}
        var qr = qrcodeList[index], textHtml = "";
        if (qr.type !== 'error'){
            var qc = qrcode;
            if(qr.setting.type !== -1) {qc += editLink;}
            qc = qc.replace(/<%= data%>/g, qr.data);
            qc = qc.replace(/<%= description%>/g, qr.description + qr.setting_description);
            textHtml = qc.replace(/<%= download%>/g, qr.download);
        } else {
            var qtxt = txt;
            textHtml = qtxt.replace(/<%= data%>/g, qr.data);
        }
        $(this).siblings("li").removeClass("active");
        $(this).addClass('active');
        $('.poptip-popover .text-center').html(textHtml);
        $(this).find('.js-delete-poptip').show();
    });

    $(document).on('mouseover', '.poptip-left-lists li', function(){
        $(this).find('.js-delete-poptip').show();
    });

    $(document).on('mouseout', '.poptip-left-lists li', function(){
        $(this).find('.js-delete-poptip').hide();
    });

    $(document).on('click', '.js-delete-poptip', function(evt){
        $(this).parent('li').siblings("li").removeClass("active");
        $(this).parent('li').addClass('active');
        var index = $('.poptip-left-lists li').index($(this).parent('li'));
        var qr = qrcodeList[index], textHtml = "";
        if (qr.type !== 'error'){
            var qc = qrcode;
            if(qr.setting.type !== -1) {qc += editLink;}
            qc = qc.replace(/<%= data%>/g, qr.data);
            qc = qc.replace(/<%= description%>/g, qr.description + qr.setting_description);
            textHtml = qc.replace(/<%= download%>/g, qr.download);
        } else {
            var qtxt = txt;
            textHtml = qtxt.replace(/<%= data%>/g, qr.data);
        }
        $('.poptip-popover .text-center').html(textHtml);
        var $pop = $(popDelHtml);
        var _this = this;
        $pop.find('.js-btn-cancel').click(function(){
            $pop.hide();
        });
        $pop.find('.js-btn-confirm').click(function(){
            $pop.hide();
            var gq_id = $(_this).parent('li').attr('id');
            $.getJSON('/b/vmall/goodsajax/deleteqrcode',{id:gq_id.replace('gq_id_', '')}, function(data){
                if (data.code === 0){
                    $(_this).parent('li').siblings("li").removeClass("active");
                    $('.poptip-left-lists li:first').addClass('active');
                    var qr = qrcodeList[0], textHtml = "";
                    if (qr.type !== 'error'){
                        var qc = qrcode;
                        if(qr.setting.type !== -1) {qc += editLink;}
                        qc = qc.replace(/<%= data%>/g, qr.data);
                        qc = qc.replace(/<%= description%>/g, qr.description + qr.setting_description);
                        textHtml = qc.replace(/<%= download%>/g, qr.download);
                    } else {
                        var qtxt = txt;
                        textHtml = qtxt.replace(/<%= data%>/g, qr.data);
                    }
                    $('.poptip-popover .text-center').html(textHtml);
                    $(_this).parent('li').remove();
                    var i = $(this).parent('li').index();
                    qrcodeList.splice(i, 1);
                    if ( !qrcodeData.creatable || qrcodeList.length >= qrcodeData.limit){
                        $('.poptip-popover').find('.js-create-poptip').hide();
                    } else {
                        $('.poptip-popover').find('.js-create-poptip').show();
                    }
                } else {
                    $('.poptip-popover .text-center').html(data.msg);
                }
            });
        });
        $('div[class="popover left"]').remove();
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

    $(document).on('click', '.js-create-poptip', function(){
        var editing = false, buy_type = 1, type = 0, discount = 0, price = 0;
        var editHtml = (editing?' disabled':''), disHtml = (discount>0)?discount:'', priHtml = (price>0)?price:'',
            typeHideSt = (type === 0)?' hide':'', typeHideSe = (type === 1)?' hide':'',
            typeCheckSt = (type === 0)?' checked':'', typeCheckSe = (type === 1)?' checked':'';
        var priceHtml = '￥<span class="js-final-price">'+(parseFloat(goods.price) * (discount > 0 ? discount / 10 : 1)).toFixed(2)+'</span> 元';
        var optionHtml = '';
        var pHtml = '';
        $.each(buyTypeList, function(i, item){
            optionHtml += '<option value="'+item.value+'"'+((buy_type === item.value)?' selected':'')+'>'+item.title+'</option>';
            pHtml += '<p class="poptip-text js-poptip-text-'+item.value+((item.value !== buy_type)?' hide':'')+'">'+item.description+'</p>';
        });
        var ctHtml = createHtml;
        ctHtml = ctHtml.replace(/<%= editHtml%>/g, editHtml);
        ctHtml = ctHtml.replace(/<%= disHtml%>/g, disHtml);
        ctHtml = ctHtml.replace(/<%= priHtml%>/g, priHtml);
        ctHtml = ctHtml.replace(/<%= typeHideSt%>/g, typeHideSt);
        ctHtml = ctHtml.replace(/<%= typeHideSe%>/g, typeHideSe);
        ctHtml = ctHtml.replace(/<%= typeCheckSt%>/g, typeCheckSt);
        ctHtml = ctHtml.replace(/<%= typeCheckSe%>/g, typeCheckSe);
        ctHtml = ctHtml.replace(/<%= priceHtml%>/g, priceHtml);
        ctHtml = ctHtml.replace(/<%= optionHtml%>/g, optionHtml);
        ctHtml = ctHtml.replace(/<%= pHtml%>/g, pHtml);
        var $ctHtml = $(ctHtml);
        $ctHtml.find('select[name="buy_type"]').change(function(){
            var v = $(this).find('option:selected').val();
            $('.poptip-create-content .poptip-text').addClass('hide');
            $('.poptip-create-content .js-poptip-text-'+v).removeClass('hide');
            buy_type = v;
        });
        $ctHtml.find('label[class="radio inline"]:eq(0),input[name="type"]:eq(0)').click(function(){
            $ctHtml.find('.js-coupon-1').addClass('hide');
            $ctHtml.find('.js-coupon-0').removeClass('hide');
        });
        $ctHtml.find('label[class="radio inline"]:eq(1),input[name="type"]:eq(1)').click(function(){
            $ctHtml.find('.js-coupon-0').addClass('hide');
            $ctHtml.find('.js-coupon-1').removeClass('hide');
        });
        $ctHtml.find('input[name="discount"]').blur(function(){
            var _this = $(this);
            _this.parent('div.input-append').next('p.error-message').remove();
            _this.parents('div.control-group').removeClass('error');
            var v = parseFloat(_this.val());
            if (isNaN(v)) {v = 0;}
            _this.val(v.toFixed(1));
            if (v < 0.1 || v > 9.9) {
                _this.parent('div.input-append').after('<p class="help-block error-message">折扣范围为 0.1 到 9.9</p>');
                _this.parents('div.control-group').addClass('error');
            } else {
                $('.js-coupon-0 span.js-final-price').text((parseFloat(goods.price) * v / 10).toFixed(2));
            }
        });
        $ctHtml.find('input[name="price"]').blur(function(){
            var _this = $(this);
            _this.parent('div.input-prepend').next('p.error-message').remove();
            _this.parents('div.control-group').removeClass('error');
            var price = parseFloat(_this.val());
            var msg = '';
            if (isNaN(price)) {
                msg = '优惠减免金额必须是一个数字';
            } else if (price < 0.01) {
                msg = '优惠减免金额必须大于 0.01 元';
            } else if (price >= parseFloat(goods.price)) {
                msg = '优惠减免金额 ' + price + ' 不能高于商品价格';
            }
            if (msg) {
                _this.parent('div.input-prepend').after('<p class="help-block error-message">'+msg+'</p>');
                _this.parents('div.control-group').addClass('error');
            }
        });
        $ctHtml.find('button.js-cancel-btn').click(function(){
            $ctHtml.remove();
            $('.poptip-popover .poptip-content').removeClass('hide');
            return false;
        });
        $ctHtml.find('button.js-submit-btn').click(function(){
            var type = parseInt($ctHtml.find('input[name="type"]:checked').val());
            if (type === 0) {
                var _thisDis = $ctHtml.find('input[name="discount"]');
                _thisDis.parent('div.input-append').next('p.error-message').remove();
                _thisDis.parents('div.control-group').removeClass('error');
                var v = parseFloat(_thisDis.val());
                if (isNaN(v)) {v = 0;}
                 _thisDis.val(v.toFixed(1));
                if (v < 0.1 || v > 9.9) {
                    _thisDis.parent('div.input-append').after('<p class="help-block error-message">折扣范围为 0.1 到 9.9</p>');
                    _thisDis.parents('div.control-group').addClass('error');
                    return false;
                } else {
                    $('.js-coupon-0 span.js-final-price').text((parseFloat(goods.price) * v / 10).toFixed(2));
                }
            } else if (type === 1) {
                var _thisPri = $ctHtml.find('input[name="price"]');
                _thisPri.parent('div.input-prepend').next('p.error-message').remove();
                _thisPri.parents('div.control-group').removeClass('error');
                var v = parseFloat(_thisPri.val());
                if (isNaN(v)) {v = 0;}
                v = v.toFixed(2);
                var msg = '';
                if ( !v) {
                    msg = '优惠减免金额必须是一个数字';
                } else if (v < 0.01) {
                    msg = '优惠减免金额必须大于 0.01 元';
                } else if (v >= parseFloat(goods.price)) {
                    msg = '优惠减免金额 ' + v + ' 不能高于商品价格';
                }
                if (msg) {
                    _thisPri.parent('div.input-prepend').after('<p class="help-block error-message">'+msg+'</p>');
                    _thisPri.parents('div.control-group').addClass('error');
                    return false;
                }
            } else {
                return false;
            }
            
            var _shop_id = $ctHtml.find('input[name="shop_id"]');
            var myshop_id = parseInt(_shop_id.val());
            
            var postData = {goods_id:parseInt(goods.id),shop_id:myshop_id, gq_discount:v, gq_discount_type:type, gq_type:buy_type};
            $.post('/b/vmall/goodsajax/createqrcode', postData, function(op){
                if (op.code === 0) {
                    qrcodeList.push(op.data.qrcode);
                    var liHtml = '<li'+(op.data.qrcode.id > 0 ? ' id="gq_id_'+op.data.qrcode.id+'"' : '')+
                    ' class="clearfix active">'+
                    (op.data.qrcode.deletable ? item : '') + op.data.qrcode.title+'</li>';
                    var textHtml = '';
                    if ( !qrcodeData.creatable || qrcodeList.length >= qrcodeData.limit){
                        $('.poptip-popover').find('.js-create-poptip').hide();
                    } else {
                        $('.poptip-popover').find('.js-create-poptip').show();
                    }
                    if (op.data.qrcode.type !== 'error'){
                        var qc = qrcode;
                        if(op.data.qrcode.setting.type !== -1) {qc += editLink;}
                        qc = qc.replace(/<%= data%>/g, op.data.qrcode.data);
                        qc = qc.replace(/<%= description%>/g, op.data.qrcode.description + op.data.qrcode.setting_description);
                        textHtml = qc.replace(/<%= download%>/g, op.data.qrcode.download);
                    } else {
                        var qtxt = txt;
                        textHtml = qtxt.replace(/<%= data%>/g, op.data.qrcode.data);
                    }
                    $('.poptip-popover').find('.poptip-left-lists ul li').removeClass("active");
                    $('.poptip-popover').find('.poptip-left-lists ul').append(liHtml);
                    $('.poptip-popover').find('.text-center').html(textHtml);
                    $('.poptip-popover .poptip-create-content').html('');
                    $('.poptip-popover .poptip-content').removeClass('hide');
                } else {
                    var type = parseInt($('.poptip-popover input[name="type"]:checked').val());
                    var _groupHand = (type === 0) ? $('.js-coupon-0 .control-group:first') : $('.js-coupon-1 .control-group:first');
                    var _msgHand = (type === 0) ? _groupHand.find('.input-append') : _groupHand.find('.input-prepend');
                    _msgHand.find('p.error-message').remove();
                    _msgHand.append('<p class="help-block error-message">'+op.msg+'</p>');
                    _groupHand.addClass('error');
                }
            }, 'json');
            return false;
        });
        $('.poptip-popover .poptip-content').addClass('hide');
        $('.poptip-popover .poptip-create-content').append($ctHtml);
    });

    $(document).on('click', '.js-edit', function(){
        var i = $('.poptip-left-lists li.active').index();
        var editing = true, buy_type = parseInt(qrcodeList[i].setting.buy_type),
            type = parseInt(qrcodeList[i].setting.type),
            discount = parseFloat(qrcodeList[i].setting.discount),
            price = parseFloat(qrcodeList[i].setting.price),
            gq_id = parseInt(qrcodeList[i].id);
        var editHtml = (editing?' disabled':''), disHtml = (discount>0)?discount:'', priHtml = (price>0)?price:'',
            typeHideSt = (type === 0)?' hide':'', typeHideSe = (type === 1)?' hide':'',
            typeCheckSt = (type === 0)?' checked':'', typeCheckSe = (type === 1)?' checked':'';
        var priceHtml = '￥<span class="js-final-price">'+(parseFloat(goods.price) * (discount > 0 ? discount / 10 : 1)).toFixed(2)+'</span> 元';
        var optionHtml = '';
        var pHtml = '';
        $.each(buyTypeList, function(i, item){
            optionHtml += '<option value="'+item.value+'"'+((buy_type === item.value)?' selected':'')+'>'+item.title+'</option>';
            pHtml += '<p class="poptip-text js-poptip-text-'+item.value+((item.value !== buy_type)?' hide':'')+'">'+item.description+'</p>';
        });
        var ctHtml = createHtml;
        ctHtml = ctHtml.replace(/<%= editHtml%>/g, editHtml);
        ctHtml = ctHtml.replace(/<%= disHtml%>/g, disHtml);
        ctHtml = ctHtml.replace(/<%= priHtml%>/g, priHtml);
        ctHtml = ctHtml.replace(/<%= typeHideSt%>/g, typeHideSt);
        ctHtml = ctHtml.replace(/<%= typeHideSe%>/g, typeHideSe);
        ctHtml = ctHtml.replace(/<%= typeCheckSt%>/g, typeCheckSt);
        ctHtml = ctHtml.replace(/<%= typeCheckSe%>/g, typeCheckSe);
        ctHtml = ctHtml.replace(/<%= priceHtml%>/g, priceHtml);
        ctHtml = ctHtml.replace(/<%= optionHtml%>/g, optionHtml);
        ctHtml = ctHtml.replace(/<%= pHtml%>/g, pHtml);
        var $ctHtml = $(ctHtml);
        $ctHtml.find('.js-coupon-' + (1 - type)).addClass('hide');
        $ctHtml.find('.js-coupon-' + type).removeClass('hide');
        $ctHtml.find('select[name="buy_type"]').change(function(){
            var v = $(this).find('option:selected').val();
            $('.poptip-create-content .poptip-text').addClass('hide');
            $('.poptip-create-content .js-poptip-text-'+v).removeClass('hide');
        });
        $ctHtml.find('label[class="radio inline"]:eq(0),input[name="type"]:eq(0)').click(function(){
            $ctHtml.find('.js-coupon-1').addClass('hide');
            $ctHtml.find('.js-coupon-0').removeClass('hide');
        });
        $ctHtml.find('label[class="radio inline"]:eq(1),input[name="type"]:eq(1)').click(function(){
            $ctHtml.find('.js-coupon-0').addClass('hide');
            $ctHtml.find('.js-coupon-1').removeClass('hide');
        });
        $ctHtml.find('input[name="discount"]').blur(function(){
            var _this = $(this);
            _this.parent('div.input-append').next('p.error-message').remove();
            _this.parents('div.control-group').removeClass('error');
            var v = parseFloat(_this.val());
            if (isNaN(v)) {v = 0;}
            _this.val(v.toFixed(1));
            if (v < 0.1 || v > 9.9) {
                _this.parent('div.input-append').after('<p class="help-block error-message">折扣范围为 0.1 到 9.9</p>');
                _this.parents('div.control-group').addClass('error');
            } else {
                $('.js-coupon-0 span.js-final-price').text((parseFloat(goods.price) * v / 10).toFixed(2));
            }
        });
        $ctHtml.find('input[name="price"]').blur(function(){
            var _this = $(this);
            _this.parent('div.input-prepend').next('p.error-message').remove();
            _this.parents('div.control-group').removeClass('error');
            var price = parseFloat(_this.val());
            var msg = '';
            if (isNaN(price)) {
                msg = '优惠减免金额必须是一个数字';
            } else if (price < 0.01) {
                msg = '优惠减免金额必须大于 0.01 元';
            } else if (price >= parseFloat(goods.price)) {
                msg = '优惠减免金额 ' + price + ' 不能高于商品价格';
            }
            if (msg) {
                _this.parent('div.input-prepend').after('<p class="help-block error-message">'+msg+'</p>');
                _this.parents('div.control-group').addClass('error');
            }
        });
        $ctHtml.find('button.js-cancel-btn').click(function(){
            $ctHtml.remove();
            $('.poptip-popover .poptip-content').removeClass('hide');
            return false;
        });
        $ctHtml.find('button.js-submit-btn').click(function(){
            var type = parseInt($ctHtml.find('input[name="type"]:checked').val());
            if (type === 0) {
                var _thisDis = $ctHtml.find('input[name="discount"]');
                _thisDis.parent('div.input-append').next('p.error-message').remove();
                _thisDis.parents('div.control-group').removeClass('error');
                var v = parseFloat(_thisDis.val());
                if (isNaN(v)) {v = 0;}
                 _thisDis.val(v.toFixed(1));
                if (v < 0.1 || v > 9.9) {
                    _thisDis.parent('div.input-append').after('<p class="help-block error-message">折扣范围为 0.1 到 9.9</p>');
                    _thisDis.parents('div.control-group').addClass('error');
                    return false;
                } else {
                    $('.js-coupon-0 span.js-final-price').text((parseFloat(goods.price) * v / 10).toFixed(2));
                }
            } else if (type === 1) {
                var _thisPri = $ctHtml.find('input[name="price"]');
                _thisPri.parent('div.input-prepend').next('p.error-message').remove();
                _thisPri.parents('div.control-group').removeClass('error');
                var v = parseFloat(_thisPri.val());
                if (isNaN(v)) {v = 0;}
                v = v.toFixed(2);
                var msg = '';
                if ( !v) {
                    msg = '优惠减免金额必须是一个数字';
                } else if (v < 0.01) {
                    msg = '优惠减免金额必须大于 0.01 元';
                } else if (v >= parseFloat(goods.price)) {
                    msg = '优惠减免金额 ' + v + ' 不能高于商品价格';
                }
                if (msg) {
                    _thisPri.parent('div.input-prepend').after('<p class="help-block error-message">'+msg+'</p>');
                    _thisPri.parents('div.control-group').addClass('error');
                    return false;
                }
            } else {
                return false;
            }
            
            $.post('/b/vmall/goodsajax/editqrcode', {goods_id:parseInt(goods.id), gq_id:gq_id, gq_discount:v}, function(op){
                if (op.code === 0) {
                    var i = $('.poptip-left-lists li.active').index();
                    qrcodeList[i] = op.data.qrcode;
                    var textHtml = '';
                    if (op.data.qrcode.type !== 'error'){
                        var qc = qrcode;
                        if(op.data.qrcode.setting.type !== -1) {qc += editLink;}
                        qc = qc.replace(/<%= data%>/g, op.data.qrcode.data);
                        qc = qc.replace(/<%= description%>/g, op.data.qrcode.description + op.data.qrcode.setting_description);
                        textHtml = qc.replace(/<%= download%>/g, op.data.qrcode.download);
                    } else {
                        var qtxt = txt;
                        textHtml = qtxt.replace(/<%= data%>/g, op.data.qrcode.data);
                    }
                    $('.poptip-popover').find('.text-center').html(textHtml);
                    $('.poptip-popover .poptip-create-content').html('');
                    $('.poptip-popover .poptip-content').removeClass('hide');
                } else {
                    var type = parseInt($('.poptip-popover input[name="type"]:checked').val());
                    var _groupHand = (type === 0) ? $('.js-coupon-0 .control-group:first') : $('.js-coupon-1 .control-group:first');
                    var _msgHand = (type === 0) ? _groupHand.find('.input-append') : _groupHand.find('.input-prepend');
                    _msgHand.find('p.error-message').remove();
                    _msgHand.append('<p class="help-block error-message">'+op.msg+'</p>');
                    _groupHand.addClass('error');
                }
            }, 'json');
            return false;
        });
        $('.poptip-popover .poptip-content').addClass('hide');
        $('.poptip-popover .poptip-create-content').append($ctHtml);
    });
});