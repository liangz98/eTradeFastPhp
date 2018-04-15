function chooseAttr(c, s, r, d, cJson, sJson) {
    if (!(this instanceof chooseAttr)) {
        return new chooseAttr(c, s, r, d, cJson, sJson);
    }

    var instance;
    chooseAttr = function () {
        return instance;
    };

    chooseAttr.setterS = function (obj) {
        $(document).unbind('click', chooseAttr.setterH);
        $('.attr-setter,.add-bar').not($(obj)).removeClass('on');
        $(obj).addClass('on');
        var _l = $(obj).position().left, _w = $('#g-attrs').width();
        if (_l <= 105) {
            $(obj).find('.add-bar-con').addClass('bl').removeClass('br');
        }
        else if ((_w - _l) < 200) {
            $(obj).find('.add-bar-con').addClass('br');
        }
        setTimeout(function () {
            $(document).bind('click', chooseAttr.setterH);
        });
        return false;
    };

    chooseAttr.setterH = function () {
        $('.attr-setter,.add-bar').removeClass('on').find('.add-bar-con').removeClass('bl br');
        $(document).unbind('click', chooseAttr.setterH);
    };

    chooseAttr.hc = function () {//thead 选择
        var _val = $(this).parent().parent().attr('fval'), _td = $(this).parents('th').attr('class');
        if ($(this).attr('ato') == 'toall') {
            $('tbody td.' + _td + ' input:text', $('#result-con')).val(_val).blur();
        }
        else {
            $('tbody td.' + _td + ' input:text', $('#result-con')).each(function () {
                if ((/^\s*$/g).test($(this).val())) {
                    $(this).val(_val).blur();
                }
            });
        }
    };

    chooseAttr.bc = function (attr, obj) {
        var _val = $(obj).parent().parent().attr('fval'),
            _td = $(obj).parents('td').attr('class');
        $('tbody tr[' + attr + '=' + $(obj).parents('tr').attr(attr) + '] td.' + _td + ' input:text', $('#result-con')).val(_val).blur();
    };

    chooseAttr.validate = function (obj) {
        var $this = $(obj),
            type = $this.parent().attr('vali'),// 两种验证
            regs = {
                'float': /^\d+(\.\d+)?$/g,
                'string': /^[\u4e00-\u9fa5a-zA-Z0-9]/g,
                'int': /^\d+$/g
            };
        if ((/^\s*$/g).test($this.val())) {//空
            if ($this.attr('show') == 'on') {
                $this.parent().parent().removeClass('error')
            } else {
                $this.parent().parent().removeClass('error').end().next().addClass('unshow');
            }
        } else {//非空
            if (regs[type].test($this.val())) {//验证通过
                var $curInputParent = $this.parent().parent();
                $curInputParent.removeClass('error').end().next().removeClass('unshow').attr('fval', $this.val());
                if (!$this.parents('thead').length) {//存储修改的值 验证符合规则的数据后才能进行保存数据，否则会出现错误数据
                    var _fc = $this.parents('tr').attr('attrc') ? $this.parents('tr').attr('attrc') : 'none',
                        _fs = $this.parents('tr').attr('attrs') ? $this.parents('tr').attr('attrs') : 'none',
                        _fdate = instance.data.json[_fc + '|' + _fs] || {};
                    instance.data.json[_fc + '|' + _fs] = _fdate;
                    _fdate[$this.parents('td').attr('class')] = $this.val();
                }

                if ($curInputParent.has('font').length > 0) { //隐藏掉错误提示
                    $curInputParent.children("font").remove();
                }
            } else {//验证不通过
                if ($this.attr('show') == 'on') {
                    $this.parent().parent().addClass('error');
                } else {
                    $this.parent().parent().addClass('error').end().next().addClass('unshow');
                }
            }
        }

        if ($this.attr("name") == "addWareVO.stocks") {  //检查库存
            checkStock();
        }
    };

    //保存原型属性
    chooseAttr.prototype = this;
    instance = new chooseAttr();
    //重置构造函数指针
    instance.constructor = chooseAttr;

    //隐藏默认销售属性信息
    if ($('#skuAttrBody tr').length > 0) {
        this.isShowDefaultInfo('hide');
    }

    //所有功能
    this.data = {
        "colors": cJson,
        "sizes": sJson,
        "json": d
    };
    this.c = $('#' + c);
    this.s = $('#' + s);
    this.r = $('#' + r);

    this.addEve('color', c);
    this.addEve('size', s);

    this.txtFn();
    return instance;
}

chooseAttr.prototype = {
    txtFn: function () {
        $('thead .a-s-con a', this.r)
            .bind('click', chooseAttr.hc);
        $('thead input:text', this.r).blur();
    },
    addEve: function (type, id) { //添加颜色和尺码CheckBox选项事件
        var that = this, con = $('#' + id);
        var tempClass = '';
        if (id === 'size-con') {
            tempClass = '.sizeAliasInput';
        } else {
            tempClass = '.colorAliasInput';
        }

        $(tempClass, con).live('blur', function () {
            var _id = $(this).parent().find('input:checkbox').attr('id'), _val = $(this).val() || $(this).attr("init");
            if (type === 'color') {
                $("tr[attrc=" + _id + "] td.td-c .s-color").html(_val);
                // 更新图片管理前面的文案
                $("#picID_" + _id).find(".s-color").html(_val);
            } else {
                $("tr[attrs=" + _id + "] td.td-s").html(_val);
            }

            that.userBlur(type, _id);

            $(this).val(_val);
            if (that.checkAttrAlias($(this))) {
                $(this).css({"color": "", "background-color": ""}); //检查通过，恢复正常样式
            }
        });

        $('input:checkbox', con).live('change',function () {
            if ($(this).attr("checked")) {
                var m = that.data.colors.length, n = that.data.sizes.length; //验证m*n是否超过最大限制
                if (type === 'color') {
                    m = m + 1;
                } else {
                    n = n + 1;
                }

                if (m == 0 || n == 0) {
                    if (m + n > 100) {
                        $(this).attr("checked", false);
                        alert("销售属性最多只能添加100个!");
                        return false;
                    }
                } else {
                    if ((m * n) > 100) {
                        $(this).attr("checked", false);
                        alert("销售属性最多只能添加100个!");
                        return false;
                    }
                }
                that.isShowDefaultInfo('hide');

                $(this).parent().find('span').hide().end().next('input').attr("disabled", false).show();
                that.add(type, $(this).attr('id'));

                checkStock();
            } else {
                $(this).parent().find('span').show().end().next('input').attr("disabled", true).hide();
                that.remove(type, $(this).attr('id'));
            }
        }).filter(':checked').each(function (index) {
                if (type === 'color') { //此处用于过滤用户选中未提交，但刷新页面的问题。
                    if (that.data.colors.length) {
                        if ($(this).attr("checked")) {
                            var delColor_ = $(this).attr("id");
                            var temp = false;
                            for (var i = 0, l = that.data.colors.length; i < l; i++) {
                                var c_ = that.data.colors[i];
                                if (c_['id'] == delColor_) {
                                    temp = true;
                                    break;
                                }
                            }

                            if (!temp) {
                                $(this).attr("checked", false);
                            }
                        }
                    } else {
                        $(this).attr("checked", false);
                    }
                } else {
                    if (that.data.sizes.length) {
                        if ($(this).attr("checked")) {
                            var delSize_ = $(this).attr("id");
                            var temp = false;
                            for (var i = 0, l = that.data.sizes.length; i < l; i++) {
                                var s_ = that.data.sizes[i];
                                if (s_['id'] == delSize_) {
                                    temp = true;
                                    break;
                                }
                            }

                            if (!temp) {
                                $(this).attr("checked", false);
                            }
                        }
                    } else {
                        $(this).attr("checked", false);
                    }
                }
            });
    },
    userBlur: function (type, id) {
        var _data = this.data[type + 's'];
        for (var i = 0 , l = _data.length; i < l; i++) {
            if (_data[i]['id'] == id) {
                _data[i]['txt1'] = $('#' + id).parent().next().val();
                return;
            }
        }
    },
    arrAdd: function (type, id) { //添加SKU属性信息
        var _arr = [],
            _done = false,
            arr = this.data[type + 's'],
            _fid = $('#' + id).attr('fid'),
            _temp = {},
            _flag,
            _isfirst = false;
        for (var i = 0, l = arr.length; i < l; i++) {
            if ((i > 0 ? arr[i - 1]['fid'] : -9999) < _fid && _fid < arr[i]['fid']) {
                _temp = {
                    'fid': _fid,//用户索引
                    'id': id, //checkbox id
                    'txt1': $('#' + id).parent().next().val() //用户输入的val
                };
                _arr.push(_temp);
                _flag = arr[i]['id'];
                if (i == 0) {
                    _isfirst = true
                }
                _done = true;
            }
            _arr.push(arr[i]);
        }
        if (!_done) {
            _temp = {
                'fid': _fid,
                'id': id,
                'txt1': $('#' + id).parent().next().val()
            };
            _arr.push(_temp);
            _flag = false;
        }
        this.data[type + "s"] = _arr;
        return $.extend({}, _temp, {next: _flag, isfirst: _isfirst})
    },
    arrRemove: function (type, id) { //移除SKU属性信息
        var _arr = [], arr = this.data[type + "s"];
        for (var i = 0; i < arr.length; i++) {
            if (arr[i]['id'] != id) {
                _arr.push(arr[i]);
            }
        }
        this.data[type + "s"] = _arr;
    },
    add: function (type, id) { //添加SKU销售属性信息
        var wareJdprice = $('#addWareVO\\.jdPrice').val(); //商品京东价
        var _setter = '&nbsp;<span class="attr-setter unshow">'
            + '<s onclick="chooseAttr.setterS($(this).parent())">&nbsp;</s>'
            + '<b class="a-s-con w2">'
            + '<a ato="attrc" onclick="chooseAttr.bc(\'attrc\',this)" href="javascript:void(0);">应用到同颜色</a>'
            + '<a ato="attrs" onclick="chooseAttr.bc(\'attrs\',this)" href="javascript:void(0);">应用到同尺码</a>'
            + '</b>'
            + '</span>';

        var _setterJdPrice = _setter;//京东价专用批量设置图标
        if (type === 'color') {//添加颜色
            if ($('tr[attrc =' + id + ']').length) {
                return;
            }
            var _temp = this.arrAdd(type, id);
            if (this.data.sizes.length) {//已经有了某尺码
                if (this.data.colors.length > 1) {//有尺码，也有有颜色
                    var _tr = '';
                    for (var i = 0, l = this.data.sizes.length; i < l; i++) {
                        var k = this.data.sizes[i];
                        var skuJdPrice = ((this.data.json[(id + '|' + k['id'])] || {})['td-price'] || ''); //默认将商品京东价赋值给SKU京东价
                        if (skuJdPrice == '' || skuJdPrice == null) {
                            skuJdPrice = wareJdprice;
                        }

                        if (skuJdPrice != '' && skuJdPrice != null) {
                            _setterJdPrice = '&nbsp;<span class="attr-setter" fval="' + skuJdPrice + '">'
                                + '<s onclick="chooseAttr.setterS($(this).parent())">&nbsp;</s>'
                                + '<b class="a-s-con w2">'
                                + '<a ato="attrc" onclick="chooseAttr.bc(\'attrc\',this)" href="javascript:void(0);">应用到同颜色</a>'
                                + '<a ato="attrs" onclick="chooseAttr.bc(\'attrs\',this)" href="javascript:void(0);">应用到同尺码</a>'
                                + '</b>'
                                + '</span>';
                        }

                        var colorAttrData = $('#' + id).attr('value');
                        var sizeAttrData = $('#' + k['id']).attr('value');
                        var priceId = "price_" + colorAttrData + "_" + sizeAttrData;
                        if (i == 0) {
                            _tr += '<tr attrc="' + id + '" attrs="' + k['id'] + '">'
                                + '<td rowspan="' + l + '" class="td-c">' + $('#' + id).next()[0].outerHTML + '<span class="s-color">' + _temp.txt1 + '</span></td>'
                                + '<td class="td-s">' + k.txt1 + '</td>'
                                + '<td class="td-price"><label class="n-label" vali="float"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="12" value="' + skuJdPrice + '" id="' + priceId + '" size="8" name="addWareVO.jdPrices">&#20803;</label>' + _setterJdPrice + '</td>'
                                + '<td class="td-stock"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-stock'] || '') + '" size="8" name="addWareVO.stocks">&#20214;</label>' + _setter + '</td>'
                                + '<td class="td-max"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-max'] || '') + '" size="8" name="addWareVO.maxBuyCounts">&#20214;</label>' + _setter + '</td>'
                                + '<td class="td-sku"><label class="n-label" vali="string"><input onblur="chooseAttr.validate(this)" type="text" maxlength="30" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-sku'] || '') + '" size="16" name="addWareVO.outerIds"></label></td>'
                                + '<td class="td-skuName"><input type="hidden" value="' + colorAttrData + '" name="addWareVO.colorIds" /><input type="hidden" name="addWareVO.sizeIds" value="' + sizeAttrData + '" /><input type="hidden" name="addWareVO.skuIds" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-skuId'] || '') + '" /></td>'
                                + '</tr>';
                        }
                        else {
                            _tr += '<tr attrc="' + id + '" attrs="' + k['id'] + '">'
                                + '<td class="td-s">' + k.txt1 + '</td>'
                                + '<td class="td-price"><label class="n-label" vali="float"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="12" value="' + skuJdPrice + '" id="' + priceId + '" size="8" name="addWareVO.jdPrices">&#20803;</label>' + _setterJdPrice + '</td>'
                                + '<td class="td-stock"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-stock'] || '') + '" size="8" name="addWareVO.stocks">&#20214;</label>' + _setter + '</td>'
                                + '<td class="td-max"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-max'] || '') + '" size="8" name="addWareVO.maxBuyCounts">&#20214;</label>' + _setter + '</td>'
                                + '<td class="td-sku"><label class="n-label" vali="string"><input onblur="chooseAttr.validate(this)" type="text" maxlength="30" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-sku'] || '') + '" size="16" name="addWareVO.outerIds"></label></td>'
                                + '<td class="td-skuName"><input type="hidden" value="' + colorAttrData + '" name="addWareVO.colorIds" /><input type="hidden" name="addWareVO.sizeIds" value="' + sizeAttrData + '" /><input type="hidden" name="addWareVO.skuIds" value="' + ((this.data.json[(id + '|' + k['id'])] || {})['td-skuId'] || '') + '" /></td>'
                                + '</tr>';
                        }
                    }
                    if (_temp.next) {
                        $(_tr).insertBefore($('tbody tr[attrc=' + _temp.next + ']:first', this.r));
                    } else {
                        $(_tr).appendTo($('tbody', this.r));
                    }
                }
                else {//有尺码，无颜色
                    for (var i = 0, l = this.data.sizes.length; i < l; i++) {
                        var j = this.data.sizes[i];
                        if (i == 0) {
                            $('tr[attrs=' + j['id'] + ']').attr('attrc', id).find('.td-c').attr('rowspan', l).html($('#' + id).next()[0].outerHTML + '<span class="s-color">' + _temp.txt1 + '</span>');
                        }
                        else {
                            $('tr[attrs=' + j['id'] + ']').attr('attrc', id);
                            $('.td-c', 'tr[attrs=' + j['id'] + ']').remove();
                        }

                        var colorAttrData = $('#' + id).attr('value');
                        var sizeAttrData = $('#' + j['id']).attr('value');
                        var tColorIds = '<input type="hidden" value="' + colorAttrData + '" name="addWareVO.colorIds" />'; //追加颜色属性值编号
                        $(tColorIds).appendTo($('tr[attrs=' + j['id'] + ']').find('.td-skuName'));

                        $('tr[attrs=' + j['id'] + ']').find('.td-skuName').find('input[name="addWareVO.skuIds"]').val((this.data.json[(id + '|' + j['id'])] || {})['td-skuId'] || ''); //重新赋值SKUID

                        var priceId = "price_" + colorAttrData + "_" + sizeAttrData;
                        $('tr[attrs=' + j['id'] + ']').find('.td-price').find('input[name="addWareVO.jdPrices"]').attr('id', priceId); //重新赋值价格INPUT ID
                    }
                }
            }
            else {//无尺码，不需组合
                var skuJdPrice = ((this.data.json[(id + '|none')] || {})['td-price'] || '');
                if (skuJdPrice == '' || skuJdPrice == null) {
                    skuJdPrice = wareJdprice;
                }

                if (skuJdPrice != '' && skuJdPrice != null) {
                    _setterJdPrice = '&nbsp;<span class="attr-setter" fval="' + skuJdPrice + '">'
                        + '<s onclick="chooseAttr.setterS($(this).parent())">&nbsp;</s>'
                        + '<b class="a-s-con w2">'
                        + '<a ato="attrc" onclick="chooseAttr.bc(\'attrc\',this)" href="javascript:void(0);">应用到同颜色</a>'
                        + '<a ato="attrs" onclick="chooseAttr.bc(\'attrs\',this)" href="javascript:void(0);">应用到同尺码</a>'
                        + '</b>'
                        + '</span>';
                }

                var colorAttrData = $('#' + id).attr('value');
                var priceId = "price_" + colorAttrData;
                var _tr = '<tr attrc="' + id + '">'
                    + '<td class="td-c">' + $('#' + id).next()[0].outerHTML + '<span class="s-color">' + _temp.txt1 + '</span></td>'
                    + '<td class="td-s"></td>'
                    + '<td class="td-price"><label class="n-label" vali="float"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="12" value="' + skuJdPrice + '" id="' + priceId + '" size="8" name="addWareVO.jdPrices">&#20803;</label>' + _setterJdPrice + '</td>'
                    + '<td class="td-stock"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(id + '|none')] || {})['td-stock'] || '') + '" size="8" name="addWareVO.stocks">&#20214;</label>' + _setter + '</td>'
                    + '<td class="td-max"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(id + '|none')] || {})['td-max'] || '') + '" size="8" name="addWareVO.maxBuyCounts">&#20214;</label>' + _setter + '</td>'
                    + '<td class="td-sku"><label class="n-label" vali="string"><input onblur="chooseAttr.validate(this)" type="text" maxlength="30" value="' + ((this.data.json[(id + '|none')] || {})['td-sku'] || '') + '" size="16" name="addWareVO.outerIds"></label></td>'
                    + '<td class="td-skuName"><input type="hidden" value="' + colorAttrData + '" name="addWareVO.colorIds" /><input type="hidden" name="addWareVO.skuIds" value="' + ((this.data.json[(id + '|none')] || {})['td-skuId'] || '') + '" /></td>'
                    + '</tr>';
                if (_temp.next) {
                    $(_tr).insertBefore($('tbody tr[attrc=' + _temp.next + ']', this.r));
                } else {
                    $(_tr).appendTo($('tbody', this.r));
                }
                $(".td-s").hide();
            }
            $(".td-c").show();

            //上传图片控件
            if ($('#picID_' + id).length) {
                $('#picID_' + id).show();
            } else {
                var _upload = '<div id="picID_' + id + '" class="g-imgs"><div class="imgs-top">'
                    + '<div class="g-l">'
                    + '<div class="g-l-t">'
                    + '<label for="">'
                    + $('#' + id).next()[0].outerHTML + '<span class="s-color">' + _temp.txt1 + '</span>'
                    + '</label>'
                    + '</div>'
                    + '</div>'
                    + '<div class="g-m">'
                    + '<ul>'
                    + '<li class="first">'
                    + '<div class="p-img"></div>'
                    + '<div class="ctrl-bar">'
                    + '<a class="to-l" title="左移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="to-r" title="右移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="del" title="删除" href="#picID_' + id + '">&nbsp;</a>'
                    + '</div>'
                    + '</li>'
                    + '<li class="fro">'
                    + '<div class="p-img"></div>'
                    + '<div class="ctrl-bar">'
                    + '<a class="to-l" title="左移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="to-r" title="右移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="del" title="删除" href="#picID_' + id + '">&nbsp;</a>'
                    + '</div>'
                    + '</li>'
                    + '<li class="sid">'
                    + '<div class="p-img"></div>'
                    + '<div class="ctrl-bar">'
                    + '<a class="to-l" title="左移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="to-r" title="右移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="del" title="删除" href="#picID_' + id + '">&nbsp;</a>'
                    + '</div>'
                    + '</li>'
                    + '<li class="sol">'
                    + '<div class="p-img"></div>'
                    + '<div class="ctrl-bar">'
                    + '<a class="to-l" title="左移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="to-r" title="右移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="del" title="删除" href="#picID_' + id + '">&nbsp;</a>'
                    + '</div>'
                    + '</li>'
                    + '<li class="det">'
                    + '<div class="p-img"></div>'
                    + '<div class="ctrl-bar">'
                    + '<a class="to-l" title="左移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="to-r" title="右移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="del" title="删除" href="#picID_' + id + '">&nbsp;</a>'
                    + '</div>'
                    + '</li>'
                    + '<li class="det">'
                    + '<div class="p-img"></div>'
                    + '<div class="ctrl-bar">'
                    + '<a class="to-l" title="左移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="to-r" title="右移" href="javascript:void(0);">&nbsp;</a>'
                    + '<a class="del" title="删除" href="#picID_' + id + '">&nbsp;</a>'
                    + '</div>'
                    + '</li>'
                    + '</ul>'
                    + '</div>'
                    + '<div class="g-r">'
                    + '<button class="fbtn" onclick="return false;">使用商品图片</button>'
                    + '<a class="upload-btn closed" href="#picID_' + id + '">图片上传<i></i></a>'
                    + '</div>'
                    + '</div><div class="cho-imgs"></div></div>'
                if (_temp.next) {
                    $(_upload).insertBefore($('#picID_' + _temp.next)).ctrlImgs();

                } else {
                    $(_upload).appendTo('#pic-con').ctrlImgs();
                }
            }
            if ($('.g-imgs').filter('.open').length) {//如果已经打开编辑,重定位编辑弹层
                $('#uploadbox').css({
                    'top': $(".open .cho-imgs").position().top
                });
            }
            $('.g-imgs').removeClass('last').filter(':visible').last().addClass('last');
            showHideNextRowBtn();

        }//添加颜色结束
        else {//添加尺码
            if ($('tr[attrs =' + id + ']').length) return;
            var _temp = this.arrAdd(type, id);
            if (this.data.colors.length > 0) {//已有颜色
                if (this.data.sizes.length > 1) {//已有尺码
                    for (var i = 0, l = this.data.colors.length; i < l; i++) {
                        var _c = this.data.colors[i];
                        var skuJdPrice = ((this.data.json[(_c['id'] + '|' + id)] || {})['td-price'] || '');  //默认将商品京东价赋值给SKU京东价
                        if (skuJdPrice == '' || skuJdPrice == null) {
                            skuJdPrice = wareJdprice;
                        }

                        if (skuJdPrice != '' && skuJdPrice != null) {
                            _setterJdPrice = '&nbsp;<span class="attr-setter" fval="' + skuJdPrice + '">'
                                + '<s onclick="chooseAttr.setterS($(this).parent())">&nbsp;</s>'
                                + '<b class="a-s-con w2">'
                                + '<a ato="attrc" onclick="chooseAttr.bc(\'attrc\',this)" href="javascript:void(0);">应用到同颜色</a>'
                                + '<a ato="attrs" onclick="chooseAttr.bc(\'attrs\',this)" href="javascript:void(0);">应用到同尺码</a>'
                                + '</b>'
                                + '</span>';
                        }

                        var colorAttrData = $('#' + _c['id']).attr('value');
                        var sizeAttrData = $('#' + id).attr('value');
                        var priceId = "price_" + colorAttrData + "_" + sizeAttrData;
                        if (_temp.next) {//
                            if (_temp.isfirst) {//第一行插入
                                var _tr = '<tr attrc="' + _c['id'] + '" attrs="' + id + '">'
                                    + '<td rowspan="' + this.data.sizes.length + '" class="td-c">' + $('#' + _c['id']).next()[0].outerHTML + '<span class="s-color">' + _c.txt1 + '</span></td>'
                                    + '<td class="td-s">' + _temp.txt1 + '</td>'
                                    + '<td class="td-price"><label class="n-label" vali="float"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="12" value="' + skuJdPrice + '" id="' + priceId + '" size="8" name="addWareVO.jdPrices">&#20803;</label>' + _setterJdPrice + '</td>'
                                    + '<td class="td-stock"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-stock'] || '') + '" size="8" name="addWareVO.stocks">&#20214;</label>' + _setter + '</td>'
                                    + '<td class="td-max"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-max'] || '') + '" size="8" name="addWareVO.maxBuyCounts">&#20214;</label>' + _setter + '</td>'
                                    + '<td class="td-sku"><label class="n-label" vali="string"><input onblur="chooseAttr.validate(this)" type="text" maxlength="30" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-sku'] || '') + '" size="16" name="addWareVO.outerIds"></label></td>'
                                    + '<td class="td-skuName"><input type="hidden" value="' + colorAttrData + '" name="addWareVO.colorIds" /><input type="hidden" name="addWareVO.sizeIds" value="' + sizeAttrData + '" /> <input type="hidden" name="addWareVO.skuIds" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-skuId'] || '') + '" /></td>'
                                    + '</tr>';
                                $("td.td-c", 'tr[attrc=' + _c['id'] + ']').remove();

                                $(_tr).insertBefore($('tr[attrc=' + _c['id'] + ']').first());
                            }
                            else {//中间插入
                                var _tr = '<tr attrc="' + _c['id'] + '" attrs="' + id + '">'
                                    //+'<td rowspan="'+this.data.sizes.length+'" class="td-c">'+$('#'+_c['id']).next()[0].outerHTML+'<span class="s-color">'+_c.txt1+'</span></td>'
                                    + '<td class="td-s">' + _temp.txt1 + '</td>'
                                    + '<td class="td-price"><label class="n-label" vali="float"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="12" value="' + skuJdPrice + '" id="' + priceId + '" size="8" name="addWareVO.jdPrices">&#20803;</label>' + _setterJdPrice + '</td>'
                                    + '<td class="td-stock"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-stock'] || '') + '" size="8" name="addWareVO.stocks">&#20214;</label>' + _setter + '</td>'
                                    + '<td class="td-max"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-max'] || '') + '" size="8" name="addWareVO.maxBuyCounts">&#20214;</label>' + _setter + '</td>'
                                    + '<td class="td-sku"><label class="n-label" vali="string"><input onblur="chooseAttr.validate(this)" type="text" maxlength="30" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-sku'] || '') + '" size="16" name="addWareVO.outerIds"></label></td>'
                                    + '<td class="td-skuName"><input type="hidden" value="' + colorAttrData + '" name="addWareVO.colorIds" /><input type="hidden" name="addWareVO.sizeIds" value="' + sizeAttrData + '" /><input type="hidden" name="addWareVO.skuIds" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-skuId'] || '') + '" /></td>'
                                    + '</tr>';
                                $(_tr).insertBefore($('tr[attrs=' + _temp.next + '][attrc=' + _c['id'] + ']'));
                                $('td:first', 'tr[attrs=' + this.data.sizes[0]['id'] + '][attrc=' + _c['id'] + ']').after('<td class="td-c" rowspan="' + this.data.sizes.length + '">' + $('#' + _c['id']).next()[0].outerHTML + '<span class="s-color">' + _c.txt1 + '</span></td>').remove();
                            }
                        }
                        else {//
                            var _tr = '<tr attrc="' + _c['id'] + '" attrs="' + id + '">'
                                //+'<td rowspan="'+this.data.sizes.length+'" class="td-c">'+$('#'+_c['id']).next()[0].outerHTML+'<span class="s-color">'+_c.txt1+'</span></td>'
                                + '<td class="td-s">' + _temp.txt1 + '</td>'
                                + '<td class="td-price"><label class="n-label" vali="float"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="12" value="' + skuJdPrice + '" id="' + priceId + '" size="8" name="addWareVO.jdPrices">&#20803;</label>' + _setterJdPrice + '</td>'
                                + '<td class="td-stock"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-stock'] || '') + '" size="8" name="addWareVO.stocks">&#20214;</label>' + _setter + '</td>'
                                + '<td class="td-max"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-max'] || '') + '" size="8" name="addWareVO.maxBuyCounts">&#20214;</label>' + _setter + '</td>'
                                + '<td class="td-sku"><label class="n-label" vali="string"><input onblur="chooseAttr.validate(this)" type="text" maxlength="30" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-sku'] || '') + '" size="16" name="addWareVO.outerIds"></label></td>'
                                + '<td class="td-skuName"><input type="hidden" value="' + colorAttrData + '" name="addWareVO.colorIds" /><input type="hidden" name="addWareVO.sizeIds" value="' + sizeAttrData + '" /><input type="hidden" name="addWareVO.skuIds" value="' + ((this.data.json[(_c['id'] + '|' + id)] || {})['td-skuId'] || '') + '" /></td>'
                                + '</tr>';

                            $('tr[attrc=' + _c['id'] + ']:last').after(_tr)
                            $('td:first', 'tr[attrs=' + this.data.sizes[0]['id'] + '][attrc=' + _c['id'] + ']').after('<td class="td-c" rowspan="' + this.data.sizes.length + '">' + $('#' + _c['id']).next()[0].outerHTML + '<span class="s-color">' + _c.txt1 + '</span></td>').remove();
                        }
                    }
                }
                else {//尚无尺码
                    for (var i = 0, l = this.data.colors.length; i < l; i++) {
                        var skuJdPrice = (this.data.json[(_cc + '|' + id)] || {})['td-price'] || ''; //默认将商品京东价赋值给SKU京东价
                        if (skuJdPrice == '' || skuJdPrice == null) {
                            skuJdPrice = wareJdprice;
                        }
                        var _cc = this.data.colors[i]['id'];
                        var colorAttrData = $('#' + _cc).attr('value');
                        var sizeAttrData = $('#' + id).attr('value');
                        var priceId = "price_" + colorAttrData + "_" + sizeAttrData;
                        $('tr[attrc=' + _cc + ']').attr('attrs', id).find('.td-s').html(_temp.txt1);
                        $('.td-price input', $('tr[attrc=' + this.data.colors[i]['id'] + ']')).val(skuJdPrice);
                        $('.td-stock input', $('tr[attrc=' + this.data.colors[i]['id'] + ']')).val((this.data.json[(_cc + '|' + id)] || {})['td-stock'] || '');
                        $('.td-max input', $('tr[attrc=' + this.data.colors[i]['id'] + ']')).val((this.data.json[(_cc + '|' + id)] || {})['td-max'] || '');
                        $('.td-sku input', $('tr[attrc=' + this.data.colors[i]['id'] + ']')).val((this.data.json[(_cc + '|' + id)] || {})['td-sku'] || '');

                        $('.td-skuName', $('tr[attrc=' + this.data.colors[i]['id'] + ']')).find('input[name="addWareVO.skuIds"]').val((this.data.json[(_cc + '|' + id)] || {})['td-skuId'] || ''); //新添加SKUID
                        $('.td-price input', $('tr[attrc=' + this.data.colors[i]['id'] + ']')).attr('id', priceId);
                        var tSizeIds = '<input type="hidden" value="' + sizeAttrData + '" name="addWareVO.sizeIds" />';
                        $(tSizeIds).appendTo($('tr[attrc=' + _cc + ']').find('.td-skuName'));
                    }
                }
            }
            else {//尚无颜色，不需组合
                var skuJdPrice = ((this.data.json[('none|' + id)] || {})['td-price'] || ''); //默认将商品京东价赋值给SKU京东价
                if (skuJdPrice == '' || skuJdPrice == null) {
                    skuJdPrice = wareJdprice;
                }

                if (skuJdPrice != '' && skuJdPrice != null) {
                    _setterJdPrice = '&nbsp;<span class="attr-setter" fval="' + skuJdPrice + '">'
                        + '<s onclick="chooseAttr.setterS($(this).parent())">&nbsp;</s>'
                        + '<b class="a-s-con w2">'
                        + '<a ato="attrc" onclick="chooseAttr.bc(\'attrc\',this)" href="javascript:void(0);">应用到同颜色</a>'
                        + '<a ato="attrs" onclick="chooseAttr.bc(\'attrs\',this)" href="javascript:void(0);">应用到同尺码</a>'
                        + '</b>'
                        + '</span>';
                }

                var sizeAttrData = $('#' + id).attr('value');
                var priceId = "price_" + sizeAttrData;
                var _tr = '<tr attrs="' + id + '">'
                    + '<td class="td-c"></td>'
                    + '<td class="td-s">' + _temp.txt1 + '</td>'
                    + '<td class="td-price"><label class="n-label" vali="float"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="12" value="' + skuJdPrice + '" id="' + priceId + '" size="8" name="addWareVO.jdPrices">&#20803;</label>' + _setterJdPrice + '</td>'
                    + '<td class="td-stock"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[('none|' + id)] || {})['td-stock'] || '') + '" size="8" name="addWareVO.stocks">&#20214;</label>' + _setter + '</td>'
                    + '<td class="td-max"><label class="n-label" vali="int"><input onblur="chooseAttr.validate(this)" class="mr5" type="text" maxlength="9" value="' + ((this.data.json[('none|' + id)] || {})['td-max'] || '') + '" size="8" name="addWareVO.maxBuyCounts">&#20214;</label>' + _setter + '</td>'
                    + '<td class="td-sku"><label class="n-label" vali="string"><input onblur="chooseAttr.validate(this)" type="text" maxlength="30" value="' + ((this.data.json[('none|' + id)] || {})['td-sku'] || '') + '" size="16" name="addWareVO.outerIds"></label></td>'
                    + '<td class="td-skuName"><input type="hidden" name="addWareVO.sizeIds" value="' + sizeAttrData + '" /><input type="hidden" name="addWareVO.skuIds" value="' + ((this.data.json[('none|' + id)] || {})['td-skuId'] || '') + '" /></td>'
                    + '</tr>';
                if (_temp.next) {
                    $(_tr).insertBefore($('tbody tr[attrs=' + _temp.next + ']', this.r));
                } else {
                    $(_tr).appendTo($('tbody', this.r));
                }
                $(".td-c").hide();
            }
            $(".td-s").show();
        }//添加尺码结束
        this.r.show();
    },
    remove: function (type, id) {
        if (type === 'color') {//移除颜色
            if (this.data.colors.length > 1) {//多个颜色，直接移除
                $('tr[attrc=' + id + ']', this.r).remove();
            } else {//单一颜色
                if (this.data.sizes.length) {//单一颜色有尺码，清空尺码单元格
                    for (var i = 0, l = this.data.sizes.length; i < l; i++) {
                        //this.data.sizes[i]
                        var m = this.data.sizes[i]['id'];
                        if ($('.td-c', 'tr[attrs=' + m + ']').length) {//第一行
                            $('.td-c', 'tr[attrs=' + m + ']').after('<td class="td-c"></td>').remove();
                        } else {//非第一行
                            $('<td class="td-c"></td>').prependTo($('tr[attrs=' + m + ']'));
                        }
                        $(".td-c").hide();
                        $('tr[attrs=' + m + ']').removeAttr('attrc');

                        $('tr[attrs=' + m + ']').find('.td-skuName').find('input[name="addWareVO.colorIds"]').remove(); //新增加称除颜色input属性值
                        $('tr[attrs=' + m + ']').find('.td-skuName').find('input[name="addWareVO.skuIds"]').val((this.data.json[('none|' + m)] || {})['td-skuId'] || '');
                        var sizeAttrData = $('#' + m).attr('value');
                        var priceId = "price_" + sizeAttrData;
                        $('tr[attrs=' + m + ']').find('.td-price').find('input[name="addWareVO.jdPrices"]').attr('id', priceId);
                    }
                    ;
                } else {//单一颜色无尺码，直接移除
                    $('tr[attrc=' + id + ']', this.r).remove();
                }
            }

            // 移除该颜色的图片上传行
            var imgsOfColor = $("#picID_" + id);
            if (imgsOfColor.hasClass("open")) { // 如果移除的颜色 图片上传控件是打开的，则应该先关闭它
                imgsOfColor.find(".upload-btn").click();
            }
            imgsOfColor.remove();
            showHideNextRowBtn();
        }
        else {//移除尺码
            if (this.data.colors.length) {//有颜色
                if (this.data.sizes.length > 1) {//有颜色，且多个尺码
                    for (var i = 0, l = this.data.colors.length; i < l; i++) {
                        var y = this.data.colors[i], ytr = $('tr[attrc=' + y['id'] + ']').first();
                        if (ytr.attr('attrs') == id) {
                            $('<td class="td-c" rowspan="' + (this.data.sizes.length - 1) + '">' + $('#' + y['id']).next()[0].outerHTML + '<span class="s-color">' + y['txt1'] + '</span></td>').prependTo(ytr.next());
                            ytr.remove();
                        } else {
                            ytr.find('td:first').attr('rowspan', (this.data.sizes.length - 1));
                            $('tr[attrs=' + id + '][attrc=' + y['id'] + ']', this.r).remove();
                        }
                    }
                }
                else {//有颜色，一个尺码
                    for (var i = 0, l = this.data.colors.length; i < l; i++) {
                        $('tr[attrc=' + this.data.colors[i]['id'] + ']').removeAttr('attrs').find('.td-s').empty();
                        $(".td-s").hide();

                        var tempColorId = this.data.colors[i]['id']; //新增加称除颜色input属性值
                        $('tr[attrc=' + tempColorId + ']').find('.td-skuName').find('input[name="addWareVO.sizeIds"]').remove();
                        $('tr[attrc=' + tempColorId + ']').find('.td-skuName').find('input[name="addWareVO.skuIds"]').val((this.data.json[(tempColorId + '|none')] || {})['td-skuId'] || '');

                        var colorAttrData = $('#' + tempColorId).attr('value');
                        var priceId = "price_" + colorAttrData;
                        $('tr[attrc=' + tempColorId + ']').find('.td-price').find('input[name="addWareVO.jdPrices"]').attr('id', priceId);
                    }
                }
            }
            else {//无颜色
                $('tr[attrs=' + id + ']', this.r).remove();
            }
        }
        this.arrRemove(type, id);
        if ((this.data.colors.length + this.data.sizes.length) <= 0) {
            this.r.hide();

            this.isShowDefaultInfo('show'); //显示默认销售属性信息
        }
        checkStock(); //验证库存
    },
    checkAttrAlias: function (inputer) { //验证销售属性别名
        var v = inputer.val();
        var msg = "";
        if (v.length > 15) {
            msg = "销售属性自定义名称过长，请调整为15字符以内。";
        }
        if (!checkAttributeValue(v)) {
            msg = "暂时只支持! @ # $ % & + - * / \\ _ ( ) = . 16种特殊字符，请重新输入。";
        }
        var repeated = false;
        var inputers = $("");

        if (inputer.hasClass("colorAliasInput")) {
            inputers = $(".colorAliasInput").not(inputer);
        } else if (inputer.hasClass("sizeAliasInput")) {
            inputers = $(".sizeAliasInput").not(inputer);
        }

        inputers.each(function () {
            if ($(this).val() == v) {
                repeated = true;
            }
        });
        if (repeated) {
            msg = "注意：销售属性值名称有重复，请重新进行自定义！";
        }

        if (msg) {
            alert(msg);
            inputer.css({"color": "red", "background-color": "#FFF4D7"});
            setTimeout('$("#' + inputer.attr("id") + '").focus()', 200);
            return false;
        }
        return true;
    },
    isShowDefaultInfo: function (showType) {  //是否显示默认SKU信息
        if (showType === 'show') {  //显示
            $("#wareMaxBuyCount").show().nextAll().show();
            $("#wareOuterId").show().next().show();

            if(isFlashPurchase){
                $("#stockTotal").attr("disabled",false).attr("readonly",true).css("background","#eaeaea");
            }else{
                $("#stockTotal").attr("disabled", false).removeAttr("style").css("background-image", "url(i/input-bg.png) 0 0 no-repeat;").css("border", "1px solid #cbcbcb").val("");//库存量
            }
            $("#defaultMaxBuyCount").attr("disabled", false);//最大购买数量
            $("#defaultOuterId").attr("disabled", false);//商家SKU
        } else {   //隐藏
            $("#attrCheckInfo").parent().hide();
            $("#wareMaxBuyCount").hide().nextAll().hide(); //实物没有此ID，可能会有问题，要注意
            $("#wareOuterId").hide().next().hide();
            $("#stockTotal").attr("disabled", true).css("background", "#eaeaea");
            $("#defaultMaxBuyCount").attr("disabled", true);
            $("#defaultOuterId").attr("disabled", true);
        }
    }
};

var checkStock = function () { //多SKU库存校验
    var stockCount = 0;
    $.each($('#result-con').find('input[name="addWareVO.stocks"]'), function (index) {
        var curStock = $(this).val();
        if (/^[1-9][0-9]*$/.test(curStock)) {
            stockCount += parseInt(curStock, 10);
        }

        if(isFlashPurchase){
            $(this).attr("readonly",true).css("background","#eaeaea");
        }
    });
    if (!(stockCount >= 0 && stockCount <= 999999999 && isNumeric(stockCount, "+i"))) {
        jQuery("#stockError").css("display", "block");
    } else {
        jQuery("#stockError").css("display", "none");
    }

    if (stockCount > 0) {
        $("#stockTotal").val(stockCount);
    } else {
        $("#stockTotal").val(0);
    }
}

function uploadImgs(o) {
    this.o = $(o);
}
uploadImgs.prototype = {
    init: function (cookieDomain) {
        var _this = this;
        $('.ftab-t li', this.o).bind('click', function () {
            if ($(this).hasClass("close_btn")) { // 关闭按钮标签被点击, 关闭自己
                $(".g-imgs.open").find('.upload-btn').click();
            } else if ($(this).hasClass("next_r_btn")) { // 设置下一行图片按钮被点击
                var nextRow = $(".g-imgs.open").next();
                if (nextRow.length > 0) {
                    nextRow.find('.upload-btn').click();
                }
            } else { // 普通标签被点击,切换选项卡
                $(this).addClass('curr').siblings().removeClass('curr');
                $($(this).attr('fid')).show().siblings('.ftab-con').hide();

                // 如果有cookie支持, 将选择结果在cookie中保持30天
                if (jQuery.cookie) {
                    jQuery.cookie.json = true;
                    jQuery.cookie("_cut_", {
                        "v": $(this).attr("fid") // cut == Current Upload box Tab
                    }, {
                        expires: 30,
                        path: "/b/",
                        domain: cookieDomain
                    });
                }
            }
        });

        // 如果cookie中有上次的标签选择记录, 则使用上次的选择结果
        if (jQuery.cookie) {
            jQuery.cookie.json = true;
            try {
                var cut = jQuery.cookie("_cut_");
                if (cut) {
                    $(".ftab-t li[fid=" + cut.v + "]").click();
                }
            } catch (e) {
            }
        }
    }
};

function ctrlImgs(o) {
    this.o = $(o);
}
ctrlImgs.prototype = {
    init: function () {
        var _this = this;
        /**
         * 图片框上的hover效果
         */
        $('.g-m li', this.o).hover(function () {
            if ($(".p-img", this).html()) { // 位置上有图
                $(this).addClass('hover');
            } else { // 位置上无图
                if (!$(this).parents(".g-imgs").hasClass("open")) { // 位置无图且当前行未展开时，提示 点击可展开
                    $(this).css({
                        cursor: "pointer"
                    }).attr("title", "点击，展开图片上传");
                }
            }
        }, function () {
            $(this).removeClass('hover');
            $(this).css({
                cursor: "default"
            }).attr("title", "");
        });

        this.o.bind('click', function (e) {
            var _t = $(e.target), _that = this;

            /**
             * 交换两个图片的位置
             * @param sourceImgBox 原图片框
             * @param targetImgBox 目标图片框
             */
            function swapImg(sourceImgBox, targetImgBox) {
                var sourceImgObj = sourceImgBox.html();
                sourceImgBox.html(targetImgBox.html());
                targetImgBox.html(sourceImgObj);

                var sourceLoadError = sourceImgBox.hasClass("imgLoadError");
                var targetLoadError = targetImgBox.hasClass("imgLoadError");
                if (sourceLoadError) {
                    targetImgBox.addClass("imgLoadError");
                } else {
                    targetImgBox.removeClass("imgLoadError");
                }
                if (targetLoadError) {
                    sourceImgBox.addClass("imgLoadError");
                } else {
                    sourceImgBox.removeClass("imgLoadError");
                }
            }

            /**
             * 点击图片框
             */
            if (_t.hasClass("p-img")) {
                // 在当前行展开上传控件
                if (!$(this).hasClass('open')) {
                    $(this).find('.upload-btn').click();
                }
            }

            /**
             * 点击图片上传
             */
            if (_t.hasClass('upload-btn') || _t.parent().hasClass('upload-btn')) {
                // 显示/隐藏 图片上传控件
                toggleUploadBox($(this), true);
                // 正确设置图片空间中图片的选中状态
                letImgsSelected();
            }

            /**
             * 点击左移
             */
            if (_t.hasClass('to-l')) {
                var _prev = _t.parent().parent().prev(); // 前一张图的li
                if (_prev.length) {
                    if (_prev.hasClass("waiting")) {
                        return;
                    }
                    // 移除错误状态(如果有)
                    removeErrorState(_prev);
                    // 交换图片
                    swapImg(_t.parent().prev(), _prev.find('.p-img'));
                }

                // 如果移动完了之后，本位置，已经没有图片了，则将图片操作栏隐藏
                if (_t.parents("li").find(".p-img img").length <= 0) {
                    _t.parents("li").removeClass('hover');
                }
            }

            /**
             * 点击右移
             */
            if (_t.hasClass('to-r')) {
                var _next = _t.parent().parent().next();
                if (_next.length) {
                    if (_next.hasClass("waiting")) {
                        return;
                    }
                    // 移除错误状态(如果有)
                    removeErrorState(_next);
                    // 交换图片
                    swapImg(_t.parent().prev(), _next.find('.p-img'));
                }

                // 如果移动完了之后，本位置，已经没有图片了，则将图片操作栏隐藏
                if (_t.parents("li").find(".p-img img").length <= 0) {
                    _t.parents("li").removeClass('hover');
                }
            }

            /**
             * 点击移除
             */
            if (_t.hasClass('del')) {
                // 移除图片空间中该图片的选中状态
                var url = _t.parent().prev('.p-img').children("img").attr("src");
                $("#imgSpaceImgs").find("img[src='" + url + "']").parent("li").removeClass('sel');
                // 移除图片
                _t.parent().prev('.p-img').removeClass("imgLoadError").empty();
                // 在当前行展开上传控件
                if (!$(this).hasClass('open')) {
                    $(this).find('.upload-btn').click();
                }

                // 如果移动完了之后，本位置，已经没有图片了，则将图片操作栏隐藏
                if (_t.parents("li").find(".p-img img").length <= 0) {
                    _t.parents("li").removeClass('hover');
                }
            }
        });
    }
};
$.fn.extend({
    uploadImgs: function (cookieDomain) {
        this.each(function () {
            var _o = new uploadImgs(this);
            _o.init(cookieDomain);
        });
    },
    ctrlImgs: function () {
        this.each(function () {
            var _o = new ctrlImgs(this);
            _o.init();
        });
    }
});

/**
 * 显示/隐藏 “下一行”按钮， 如果有下一行，则显示，无则隐藏。
 */
function showHideNextRowBtn() {
    $("#uploadbox").find(".next_r_btn").css("display", function () {
        return $(".g-imgs.open").next(".g-imgs").length > 0 ? "block" : "none";
    });
}

/**
 * 移除商品图片上的错误状态
 * @param imgBox 图片框
 */
function removeErrorState(imgBox) {
    imgBox.removeClass("error").find(".error-txt").remove();
}

/**
 * 图片空间数据已经load完成，切换到显示状态
 */
function imgSpaceLoaded() {
    $(".ftab-con.loading").hide();
    $(".ftab-con.imgSpaceTab").show();
}

/**
 * 图片空间进入loading状态
 */
function imgSpaceLoading() {
    $(".ftab-con.loading").show();
    $(".ftab-con.imgSpaceTab").hide();
}

/**
 * 加载图片空间数据
 * @param query 数据查询条件
 * @param isInit 是否初始化，如果是初始化将加载类目和初始化分页条
 */
function loadImgSpaceData(query, isInit) {
    query.cateid = isInit ? $(".g-imgs:first").attr('gimgid') : $(".g-imgs.open").attr('gimgid');
    var url = "/upload/imagexhr/initimgspacetab";
    if (!isInit) {
        imgSpaceLoading();
        url = "/upload/imagexhr/loadimgspacedata";
    }
    // ajax获取数据
    jQuery.getJSON(url, query, function (res) {
        if (!isInit) {
            // load完成
            imgSpaceLoaded();
        }
        // 图片分类
        if (res.catalogs) {
            renderCatalogs(res);
        }

        // 当前页图片
        renderImgs(res);

        // cookie信息 给swfUpload使用
        if (res.cookieValue) {
            $("#cookieValue").val(res.cookieValue);
        }
    });
}

/**
 * 刷新图片空间数据
 * @param query 数据查询条件
 */
function refreshImgSpaceData(query) {
    query.cateid = $(".g-imgs.open").attr('gimgid');
    var url = "/upload/imagexhr/loadimgspacedata";
    // Loading
    $(".ftab-con.loading").show().siblings('.ftab-con').hide();
    // ajax获取数据
    jQuery.getJSON(url, query, function (res) {
        $($('.ftab-t .curr').attr('fid')).show().siblings('.ftab-con').hide();
        // 当前页图片
        renderImgs(res);
    });
}

/**
 * 渲染加载回来的图片数据
 * @param res 服务器响应
 */
function renderImgs(res) {
    var imgBox = $("#imgSpaceImgs").empty();
    imgBox.next(".m").empty();
    if (res.page && res.page.totalItem > 0) {
        // 添加图片
        jQuery.each(res.page.detail, function (i, o) {
            imgBox.append('<li onclick="useImg(this);" title="' + o.imgName + '" >' +
                '<img onerror="loadImgError(this)" ' +
                'imgId="' + o.imgId + '" ' +
                'imgUrl="' + o.imgUrl + '" ' +
                'nolImgUrl="' + o.nolImgUrl + '" ' +
                'orlImgUrl="' + o.orlImgurl + '" ' +
                'src="' + "http://" + window.location.host + "/upload_files" + o.imgUrl + '"/>' +
                '</li>');
        });
        // 正确设置图片空间中图片的选中状态
        letImgsSelected();

        // 去掉"没有图片"的提示(如果有) 正确设置分页条
        imgBox.siblings(".no-data-tip").remove().end()
            .next(".m").empty().append('<div class="elfPagination f_r"></div>');
        $(".elfPagination").elfPagination({
            totalPage: res.page.totalPage,
            index: res.page.pageIndex,
            showGoTo: false,
            shortcutSupport: false,
            paged: function (page) {
                searchImg(page);
            }
        });
    } else {
        imgBox.siblings(".no-data-tip").remove().end().after('<div class="no-data-tip">没有找到符合条件的图片</div>');
    }
}

/**
 * 渲染图片空间的类目下拉框
 * @param res 图片空间数据
 */
function renderCatalogs(res) {
    var catalogSelector = $("#queryImgPara\\.cateId").children("option:gt(0)").remove().end();
    // 递归展示类目层级
    addOption(res.catalogs, 0);

    function addOption(list, deep) {
        deep++;
        jQuery.each(list, function (i, o) {
            var prefix = "";
            for (var d = 1; d < deep; d++) {
                prefix = prefix + "&nbsp;&nbsp;";
            }
            catalogSelector.append('<option value="' + o.cateId + '">' + prefix + o.cateName + '</option>');
            if (o.subList) {
                addOption(o.subList, deep);
            }
        });

    }
}

/**
 * 搜索图片
 * @param page 第几页
 */
function searchImg(page) {
    loadImgSpaceData({
        "query_type": $("#queryImgPara\\.queryType").val(),
        "query_key": encodeURIComponent($("#queryImgPara\\.queryKey").val()),
        "width_limit": $('#width_limit').val(),
        "height_limit": $('#height_limit').val(),
        "page": page
    }, false);
}

/**
 * 初始化商品图片部分界面
 * @param option 设置
 */
function initImgPartOfPage(option) {
    var setting = {
        gImgId: "goodsalbum",
        uploadLimit: 6,
        sizeLimit: '1024K',
        fileTypes: "*.jpg;*.png;*.jpeg;",
        cateExt: "jpg,gif,png",
        widthLimit: 800,
        heightLimit: 800,
        desc: '商品相册'
    };
    if(typeof option == 'undefined') {
        option = {};
    }
    jQuery.extend(setting, option);
    // 初始化HTML
    var liHtml = '';
    for(var i=0;i<setting.uploadLimit;i++) {
        liHtml += '<li' + (i==0?' class="first"':'') +'>' +
            '<div class="p-img"></div>' +
            '<div class="ctrl-bar">' +
                '<a class="to-l" title="左移" href="javascript:void(0);">&nbsp;</a>' +
                '<a class="to-r" title="右移" href="javascript:void(0);">&nbsp;</a>' +
                '<a class="del" title="删除" href="#'+setting.gImgId+'">&nbsp;</a>' +
            '</div>' +
        '</li>';
    }
    var imgHtml = '<div id="'+setting.gImgId+'" class="g-imgs" gImgId="'+setting.gImgId+'" uploadLimit="'+
            setting.uploadLimit+'" fileTypes="'+setting.fileTypes+'" sizeLimit="'+setting.sizeLimit+'" cateExt="'+
            setting.cateExt+'" widthLimit="'+setting.widthLimit+'" heightLimit="'+setting.heightLimit+'">' +
        '<div class="imgs-top">' +
            '<div class="g-l">' +
                '<div class="g-l-t">' +
                    '<label class="necessary-l" for=""><i>*</i>'+setting.desc+'</label>' +
                '</div>' +
            '</div>' +
            '<div class="g-m">' +
                '<ul>' + liHtml + '</ul>' +
            '</div>' +
            '<div class="g-r">' +
                '<a class="upload-btn" href="#'+setting.gImgId+'">图片上传<i></i></a>' +
            '</div>' +
        '</div>' +
        '<div class="cho-imgs"></div>' +
    '</div>';
    $(imgHtml).appendTo('#pic-con').ctrlImgs();

    // (编辑商品时)如果已经有图片，将图片初始化
    $(".imgInput_"+setting.gImgId).each(function (i, o) {
        var wareImg = $(this).val().split(",");
        $("#" + setting.gImgId + " .g-m .p-img").eq(wareImg[0] - 1).empty()
            .append('<img onerror="loadImgError(this)" ' +
                'imgId="' + wareImg[1] + '" ' +
                'imgUrl="' + wareImg[2] + '" ' +
                'nolImgUrl="' + wareImg[3] + '" ' +
                'orlImgUrl="' + wareImg[4] + '" ' +
                'src="' + "http://" + window.location.host + "/upload_files" + wareImg[2] + '">');
    });

    // 上传控件初始化
    $('#uploadbox').uploadImgs(window.location.host);

    // 默认第一行上传控件展开
    //toggleUploadBox($("#picID_zero"), false);

    // 初始化图片空间tab
    loadImgSpaceData({width_limit:setting.widthLimit, height_limit:setting.heightLimit}, true);

    // 绑定图片空间搜索按钮事件
    $("#searchImg").click(function () {
        searchImg(1);
    });
}


/**
 * 设置图片空间分页中图片的选中状态
 */
function letImgsSelected() {
    $("#imgSpaceImgs").find("li").removeClass('sel');
    $(".g-imgs.open").find(".g-m .p-img img").each(function () {
        var url = $(this).attr("src");
        var query = url.replace("http://" + window.location.host + "/upload_files", '');
        $("#imgSpaceImgs").find("img[src='" + url + "']").parent("li").addClass('sel');
        $("#imgSpaceImgs").find("img[nolImgUrl='" + query + "']").parent("li").addClass('sel');
        $("#imgSpaceImgs").find("img[orlImgUrl='" + query + "']").parent("li").addClass('sel');
    });
}

/**
 * 使用图片空间中某一张图片
 * 点击图片空间中的图片时会调用
 * @param o 图片引用
 */
function useImg(o) {
    if ($(o).hasClass('sel')) {
        return;
    }
    var pImgs = $('.g-imgs').filter('.open').find('.p-img').filter(':empty');
    if (pImgs.size() <= 0) {
        alert("最多只能添加"+window.swfUpload.settings.file_upload_limit+"张图片哦");
        return;
    }
    var img = $(o).find("img");
    $(pImgs[0]).parent("li")
        .removeClass("waiting") // 移除上传中状态
        .removeClass("error").find(".error-txt").remove().end().end()// 移除错误
        .html($('<img onerror="loadImgError(this)" imgId="' + img.attr("imgId") +
                '" imgUrl="' + img.attr("imgUrl") +
                '" nolImgUrl="' + img.attr("nolImgUrl") +
                '" orlImgUrl="' + img.attr("orlImgurl") +
                '" src="' + img.attr("src") +
            '">'));
    $(o).addClass('sel');
}

/**
 * 图片加载出错时，显示小joy图片
 * @param img
 */
function loadImgError(img) {
    $(img).hide().parent().addClass("imgLoadError");
}

/**
 * 在某一行 显示/隐藏 图片上传控件
 * @param gImg 某一行图片
 * @param keepInView 是否让展开状态的行保持在视野内
 */
function toggleUploadBox(gImg, keepInView) {
    if (!gImg.hasClass('open')) { // 展开
        gImg.addClass('open').siblings().removeClass('open');
        var _pos = gImg.position();
        $('#uploadbox').css({
            'top': _pos.top + 100 + $('#pic-con').scrollTop(),
            'display': 'block'
        });
        // 设置“下一行”按钮的显示隐藏状态
        showHideNextRowBtn();
        if (keepInView) {
            // 保持打开状态的行在视野内
            var rowId = "#" + gImg.attr("id");
            window.location.href = window.location.href.substring(0, window.location.href.indexOf("#")) + rowId;
        }
        // 设置Upload组件
        if(window.swfUpload) {
            window.swfUpload.settings.file_size_limit = gImg.attr('sizeLimit');
            window.swfUpload.settings.file_queue_limit = gImg.attr('uploadLimit');
            window.swfUpload.settings.file_upload_limit = gImg.attr('uploadLimit');
            window.swfUpload.settings.file_types = gImg.attr('fileTypes');
            window.swfUpload.settings.custom_settings.cate_name = gImg.attr('gImgId');
        }
        // 设置格式说明
        $('.wh').text(gImg.attr('widthLimit')+'*'+gImg.attr('heightLimit'));
        $('.ext').text(gImg.attr('cateExt'));
        $('.sizeLimit').text(gImg.attr('sizeLimit'));
        $('#width_limit').val(gImg.attr('widthLimit'));
        $('#height_limit').val(gImg.attr('heightLimit'));
        $("#uoload_tip").text('一次可选' + gImg.attr('uploadLimit') + '张图片哦～');
        // 刷新图片选择栏
        refreshImgSpaceData({
            "query_type": $("#queryImgPara\\.queryType").val(),
            "query_key": encodeURIComponent($("#queryImgPara\\.queryKey").val()),
            "width_limit": $('#width_limit').val(),
            "height_limit": $('#height_limit').val(),
            "page": 1
        });
    } else { // 关闭
        gImg.removeClass('open').siblings().removeClass('open');
        $('#uploadbox').css({
            'top': -9999,
            'display': 'none'
        });
    }
}