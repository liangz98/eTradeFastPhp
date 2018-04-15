(function ( $, _, window, document, undefined ) {

    function SkuAtom(atom) {
        this.atom = atom;
        this.structure = '<div class="sku-atom">\n<span data-atom-id="'+this.atom.id+'">'+this.atom.text+'</span>\n<div class="close-modal small js-remove-sku-atom">×</div>\n</div>\n';
        this.init();
    }
    function SkuAtomList(sku) {
        this.sku = sku;
        this.list = [];
        this.class_type = 0;
        this.choosable = [];
        this.maxCount = 0;
        this.customCount = 0;
        this.structure = '<div>\n<div class="js-sku-atom-list sku-atom-list"></div>\n<a href="javascript:;" class="js-add-sku-atom add-sku">+添加</a>\n</div>\n';
        this.init();
    }
    function SkuItem(skuItemList, select2Config) {
        this.editable = true;
        this.skuItemList = skuItemList || [];
        this.choosable = {};
        this.select2Config = select2Config;
        this.structure = '<div class="sku-sub-group">\n<h3 class="sku-group-title">\n<input type="hidden" name="sku_name" value="-1" class="js-sku-name">\n<a class="js-remove-sku-group remove-sku-group">&times;</a>\n</h3>\n<div class="js-sku-atom-container sku-group-cont"></div>\n</div>\n';
        this.init();
    }
    function SkuList() {
        this.list = [];
        this.skuNameList = [];
        this.maxSize = 2;
        this.customMaxSize = this.maxSize;
        this.select2Config = {
            width: 100,
            multiple: false,
            maximumInputLength: 16,
            placeholder: "请选择...",
            createSearchChoice: function(term, data) {
                return 0 === $(data).filter(function() {
                    return 0 === this.text.localeCompare(term);
                }).length ? {id: -1,text: term} : undefined;
            }
        };
        this.structure = '<div class="sku-group">\n<div class="js-sku-list-container"></div>\n<div class="js-sku-group-opts sku-sub-group">\n<h3 class="sku-group-title">\n<button type="button" class="js-add-sku-group btn">添加规格项目</button>\n</h3>\n</div>\n</div>\n';
        this.init();
    }
    function Stock(skuList) {
        this.list = [];
        this.skuList = skuList;
        this.skuData = [];
        this.stockBackup = [];
        this.errorLen = 0;
        this.minPrice = 0.01;
        this.isStockModulShow = false;
        this.structure = '<table class="table-sku-stock"></table>';
        this.thead = '<thead>\n<tr>\n<%=theadHtml %><th class="th-price">市场价格（元）</th>\n<th class="th-price">本店价格（元）</th>\n<th class="th-stock">库存</th>\n</tr>\n</thead>\n';
        this.td = '<td>\n<input type="text" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][stock_market_price]" tag="market_price" class="js-price input-mini" value="<%=market_price %>" />\n</td>\n<td>\n<input type="text" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][stock_shop_price]" tag="shop_price" class="js-price input-mini" value="<%=shop_price %>" />\n</td>\n<td>\n' +
            '<input type="text" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][stock_value]" tag="stock_value" class="js-stock-num input-mini" value="<%=stock_value %>" />' +
            '<input type="hidden" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][order_by]" value="<%=order_by %>" /></td>\n';
        this.init();
    }
    function SkuStockInfo() {
        this.structure = '<div id="sku-info-region" class="goods-info-group"><div class="goods-info-group-inner"><div class="info-group-cont vbox">\n<div class="group-inner">\n<div class="js-goods-sku control-group">\n' +
            '<label class="control-label">商品规格：</label>\n<div id="sku-region" class="controls">\n</div>\n</div>\n<div class="js-goods-stock control-group">\n' +
            '<label class="control-label">商品库存：</label>\n<div id="stock-region" class="controls sku-stock">\n</div>\n</div>\n</div>\n</div>\n</div>\n</div>\n';
        this.init();
    }

    $.extend(SkuAtom.prototype, {
        init: function() {
            this._$ = $(this.structure);
        },
        getQuery: function() {
            return this._$;
        }
    });
    $.extend(SkuAtomList.prototype, {
        init: function() {
            this._$ = $(this.structure);
            var _this = this;
            this._$.find('a.js-add-sku-atom').seedtips({
                type: 'select',
                direct: 'bottom',
                data: _this.initSkuList(),
                extra: {url: window._global.url.www + "/skus/skuleaf", id: _this.sku.id},
                onConfirm: function(selectedData) {
                    _this.chooseSkuAtom(_this, selectedData);
                }
            });
        },
        initSkuList: function() {
            var _skuList = [], _this = this;
            $.each(window._global.skuTree, function(i, sku) {
                if (sku.id === _this.sku.id) {
                    _skuList = sku.list;
                    return false;
                }
            });
            return _skuList;
        },
        resetSkuAtomList: function(sku) {
            this.sku = sku;
            this.list = [];
            this._$.find('div.js-sku-atom-list').empty();
            var _this = this;
            this._$.find('a.js-add-sku-atom').seedtips('destroy').seedtips({
                type: 'select',
                direct: 'bottom',
                data: _this.initSkuList(),
                extra: {url: window._global.url.www + "/skus/skuleaf", id: _this.sku.id},
                onConfirm: function(selectedData) {
                    _this.chooseSkuAtom(_this, selectedData);
                }
            });
            window.skuStockInfo.stockView.updateStockData();
        },
        addAtomItem: function(atom) {
            var atomItem = new SkuAtom(atom),
                $atom = atomItem.getQuery(),
                _thisAtomList = this;
            $atom.find('div.js-remove-sku-atom').click(function(e){
                var $prev = $(this).prev('span'),
                    atom_id = $prev.attr('data-atom-id');
                _thisAtomList.removeAtomItem(atom_id);
                $atom.remove();
                window.skuStockInfo.stockView.updateStockData();
                e.preventDefault();
                e.stopPropagation();
            });
            this.list.push(atomItem);
            this._$.find('div.js-sku-atom-list').append(atomItem.getQuery());
        },
        removeAtomItem: function(atomId) {
            var atom_id = Number(atomId);
            if (atom_id) {
                var _this = this;
                $.each(this.list, function(i, atomItem){
                    if (atomItem.atom.id === atom_id) {
                        _this.list.splice(i, 1);
                        return false;
                    }
                });
            }
        },
        chooseSkuAtom: function(_this, selectedData) {
            if ( !selectedData) {
                return false;
            }
            _.each(selectedData, function(atom) {
                return _this.checkChooseable(atom) ? _this.addAtomItem(atom) : false;
            });
            window.skuStockInfo.stockView.updateStockData();
        },
        checkChooseable: function(atom) {
            return this.checkIsExist(atom) ? false : (this.checkCustomLimit(atom) ? false : true);
        },
        checkIsExist: function(atom) {
            var isExist = false;
            _.each(this.list, function(atomItem) {
                if (atomItem.atom.id == atom.id) {
                    isExist = true;
                    return false;
                }
            });
            if (isExist) {
                generate("error",  "已经添加了相同的规格值：" + atom.text);
                return true;
            }
            return false;
        },
        checkCustomLimit: function(atom) {
            if (this.class_type === 0 || _.isEmpty(this.choosable)) {
                return false;
            }
            var isExist = false;
            $.each(this.choosable, function(i, item) {
                if (item.atom.id == atom.id) {
                    isExist = true;
                    return false;
                }
            });
            if ( !isExist) {
                if (0 === this.maxCount) {
                    generate("error", "该商品不支持『" + atom.text + "』规格，且不支持自定义项。");
                    return true;
                }
                if (this.customCount === this.maxCount) {
                    generate("error", "该规格最多添加个 " + this.maxCount + " 自定义项。");
                    return true;
                }
            }
            return false;
        },
        getQuery: function() {
            return this._$;
        }
    });
    $.extend(SkuItem.prototype, {
        init: function() {
            this.sku = {id:-1, text:''};
            this._$ = $(this.structure);
            this.initSkuName();
        },
        initSkuName: function() {
            return this.editable ? this.initSkuNameSelect2() : false;
        },
        initSkuNameSelect2: function() {
            var _this = this;
            _this._$.find('input.js-sku-name').select2(_this.select2Config).on("select2-opening", function() {
                $('#seed-tip').hide();
            }).on("select2-selecting", function(e) {
                _this.selectSkuName({id:e.choice.id, text:e.choice.text}, e);
            });
        },
        selectSkuName: function(sku, e) {
            var skuItem;
            $.each(this.skuItemList, function(i, sku_item){
                if (sku_item.sku.text == sku.text) {
                    skuItem = sku_item;
                    return false;
                }
            });
            if ( !_.isEmpty(skuItem)) {
                generate("error", "规格名不能相同。");
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            if (-1 == sku.id) {
                this.createSkuName(sku);
            } else {
                this.updateAtomList(sku);
            }
        },
        createSkuName: function(sku) {
            var i = window._global.url.www + "/skus/skutree", skubase = {text: sku.text}, _this = this;
            $.ajax({
                url: i,
                type: "POST",
                dataType: "json",
                timeout: 8000,
                cache: false,
                data: skubase,
                success: function(res) {
                    if (1 === res.code) {
                        _this.onCreateSkuNameSuccess(res.data, skubase);
                    } else {
                        generate("error", res.msg || "新增规格类型失败。");
                    }
                },
                error: function() {},
                complete: function() {}
            });
        },
        onCreateSkuNameSuccess: function(data, skubase) {
            var skuitem = {id: Number(data),text: skubase.text}, skuNameList = [];
            window._global.skuTree.push({id: skuitem.id,text: skuitem.text});
            _.each(window._global.skuTree, function(sku) {
                skuNameList.push({id: sku.id,text: sku.text || sku.name});
            });
            this._$.find('input.js-sku-name').select2({data:skuNameList}).select2("data", skuitem).select2("close");
            this.updateAtomList(skuitem);
        },
        updateAtomList: function(sku) {
            if ( !this.skuAtomList) {
                this.initSkuAtomList(sku);
            }
            this.resetAtomListData(sku);
        },
        initSkuAtomList: function(sku) {
            if ( !sku.id || -1 == sku.id) {
                return false;
            }
            this.skuAtomList = new SkuAtomList(sku);
            this._$.find('.js-sku-atom-container').append(this.skuAtomList.getQuery());
        },
        resetAtomListData: function(sku) {
            this.sku = sku;
            this.skuAtomList.resetSkuAtomList(sku);
        },
        getQuery: function() {
            return this._$;
        }
    });
    $.extend(SkuList.prototype, {
        init: function() {
            this._$ = $(this.structure);
            this.initSkuNameList();
            var _this = this;
            this._$.find('button.js-add-sku-group').click(function(){
                _this.addSkuGroup(_this);
            });
        },
        initSkuNameList: function() {
            var skuTree = window._global.skuTree, _this = this;
            _.each(skuTree, function(sku) {
                _this.skuNameList.push({id: sku.id,text: sku.text || sku.name});
            });
            $.extend(this.select2Config, {data:this.skuNameList});
        },
        addSkuGroup: function(_this, sku) {
            var skuItem = new SkuItem(_this.list, _this.select2Config),
                $sku = skuItem.getQuery();
            $sku.find('a.js-remove-sku-group').click(function(e){
                var skuId = $(this).prev('input').val();
                _this.removeSkuGroup(skuId);
                _this.checkAndReverseUpdate();
                $sku.remove();
                window.skuStockInfo.stockView.rebuildStockData();
                e.preventDefault();
                e.stopPropagation();
            });
            _this._$.find('div.js-sku-list-container').append(skuItem.getQuery());
            if (typeof(sku) === "object") {
                $sku.find('input.js-sku-name').select2('data', {id:sku.id, text:sku.text});
                skuItem.updateAtomList({id:sku.id, text:sku.text});
                _.each(sku.list, function(atom){
                    skuItem.skuAtomList.addAtomItem(atom);
                });
            } else {
                $sku.find('input.js-sku-name').select2('open');
            }
            _this.list.push(skuItem);
            _this.checkAndReverseUpdate();
        },
        removeSkuGroup: function(skuId) {
            var sku_id = Number(skuId);
            if (sku_id) {
                var _this = this;
                $.each(this.list, function(i, skuItem){
                    if (skuItem.sku.id === sku_id) {
                        _this.list.splice(i, 1);
                        return false;
                    }
                });
            }
        },
        checkMaxSize: function() {
            return this.list.length < this.maxSize;
        },
        toggleSkuGroupOpts: function() {
            if (this.checkMaxSize()) {
                this._$.find('div.js-sku-group-opts').show();
            } else {
                this._$.find('div.js-sku-group-opts').hide();
            }
        },
        checkAndReverseUpdate: function() {
            this.toggleSkuGroupOpts();
        },
        checkIsExist: function(sku) {
            var isExist = false;
            $.each(this.list, function(i, skuItem) {
                if (skuItem.sku.id == sku.id) {
                    isExist = true;
                    return false;
                }
            });
            return isExist;
        },
        getQuery: function() {
            return this._$;
        }
    });
    $.extend(Stock.prototype, {
        init: function() {
            this._$ = $(this.structure);
            this.rebuildStockData();
        },
        rebuildStockData: function() {
            this.list = this.generateStockData();
            this.showStockView();
        },
        updateStockData: function() {
            this.list = this.generateStockData();
            this.list = this.recoverBackup(this.list);
            this.showStockView();
        },
        generateStockData: function() {
            var list = [], _this = this;
            _.each(_this.skuList.list, function(skuItem, i){
                if (_.isEmpty(list)) {
                    list = _this.initSkuData(skuItem, i);
                } else {
                    list = _this.appendSkuInfo(list, skuItem, i);
                }
            });
            return list;
        },
        recoverBackup: function(stockList) {
            var backupList = this.stockBackup, keyList = this.getFilterKeys();
            _.each(stockList, function(stock, i){
                var thisPick = _.pick(stock, keyList),
                    backup = _.findWhere(backupList, thisPick);
                if (backup) {
                    stockList[i].stock_value = backup.stock_value;
                    stockList[i].market_price = backup.market_price;
                    stockList[i].shop_price = backup.shop_price;
                    stockList[i].union_price = backup.union_price;
                    stockList[i].min_price = backup.min_price;
                    stockList[i].recommend_price = backup.recommend_price;
                    stockList[i].group_id = backup.group_id;
                    stockList[i].stock_id = backup.stock_id;
                }
            });
            return stockList;
        },
        getFilterKeys: function() {
            var list = [];
            for (var n = 1; n <= this.skuList.list.length; n++) {
                var key = "v" + n + "_id";
                list.push(key);
            }
            return list;
        },
        updateStockBackup: function(stockList) {
            this.stockBackup = stockList;
        },
        initSkuData: function(skuItem, i) {
            var stockList = [];
            _.each(skuItem.skuAtomList.list, function(atomItem){
                var stock = {market_price: "", shop_price: "", union_price: "", min_price: "", recommend_price: "", stock_value: 0, group_id: 0, stock_id: 0},
                    index = i + 1;
                stock['k' + index + '_id'] = skuItem.sku.id;
                stock['k' + index] = skuItem.sku.text;
                stock['v' + index + '_id'] = atomItem.atom.id;
                stock['v' + index] = atomItem.atom.text;
                stockList.push(stock);
            });
            return stockList;
        },
        appendSkuInfo: function(baseList, skuItem, i) {
            if ( !skuItem.skuAtomList || _.isEmpty(skuItem.skuAtomList.list)) {
                return baseList;
            }
            var stockList = [];
            _.each(baseList, function(stock) {
                _.each(skuItem.skuAtomList.list, function(atomItem){
                    var tmp = {};
                    _.each(stock, function(val, key){
                        tmp[key] = val;
                    });
                    var index = i + 1;
                    tmp['k' + index + '_id'] = skuItem.sku.id;
                    tmp['k' + index] = skuItem.sku.text;
                    tmp['v' + index + '_id'] = atomItem.atom.id;
                    tmp['v' + index] = atomItem.atom.text;
                    stockList.push(tmp);
                });
            });
            return stockList;
        },
        showStockView: function() {
            if (0 === this.skuList.list.length) {
                this.isStockModulShow = false;
                this.hideStockModule();
                return false;
            } else {
                this.skuData = this.generateSkuData();
                if (_.isEmpty(this.skuData)) {
                    this.isStockModulShow = false;
                    this.hideStockModule();
                    return false;
                } else {
                    this.renderTable();
                }
            }
        },
        renderTable: function() {
            var $thead = $(this.renderHeader()), $tbody = $(this.renderBody()), _this = this;
            $tbody.find('.js-price').blur(function(e){_this.onAtomPriceBlur(_this, e);});
            $tbody.find('.js-price').keyup(function(e){_this.onAtomPriceInput(_this, e);});
            $tbody.find('.js-stock-num').keyup(function(e){_this.onAtomStockInput(_this, e);});
            _this._$.empty().append($thead).append($tbody);
            _this.validateStockData();
            _this.isStockModulShow = true;
            this.showStockModule();
        },
        renderHeader: function() {
            var theadHtml = '';
            $.each(this.skuList.list, function(i, skuItem){
                if (skuItem.skuAtomList.list.length > 0) {
                    theadHtml += '<th class="ta-c">' + skuItem.sku.text + '</th>\n';
                }
            });
            return this.thead.replace('<%=theadHtml %>', theadHtml);
        },
        renderBody: function() {
            if (_.isEmpty(this.skuData)) {
                return false;
            }
            this.combine = this.calcCombine(this.skuData);
            var rows = this.generateRows();
            return rows;
        },
        generateRows: function() {
            this.TableHtml = '<tbody>';
            this.outputTrTag = false;
            this.trIndex = 0;
            this.printRow(0, this.outputTrTag);
            this.TableHtml += '</tbody>';
            return this.TableHtml;
        },
        generateSkuData: function() {
            var skuData = [];
            _.each(this.skuList.list, function(skuItem){
                var atomList = skuItem.skuAtomList;
                if (atomList && atomList.list && atomList.list.length > 0) {
                    skuData.push(atomList);
                }
            });
            return skuData;
        },
        calcCombine: function(skuData) {
            var tmp = [], len = skuData.length;
            for (var i = 0; i < len; i++) {
                tmp[i] = 1;
                for (var k = i + 1; k < len; k++) {
                    tmp[i] = tmp[i] * skuData[k].list.length;
                }
            }
            return tmp;
        },
        printRow: function(index, tag, prevDep) {
            var skuData = this.skuData, len = skuData.length;
            if (index === len) {
                return false;
            }
            var atomList = skuData[index].list, _this = this;
            _.each(atomList, function(skuAtom, atom_index){
                if ( !tag) {
                    _this.TableHtml += '<tr>';
                    tag = true;
                }
                var group_index = (prevDep === undefined) ? (len === 1 ? 0 : atom_index) : prevDep;
                var tdHtml = '<td data-atom-id="' + skuAtom.atom.id + '" rowspan="' + _this.combine[index] + '">' + skuAtom.atom.text;
                var groupHtml = '';
                if (len === 1) {
                    groupHtml = '<input type="hidden" name="stock_array[<%=group_index %>][group_name]" value="' + skuData[index].sku.text + '"/>' +
                        '<input type="hidden" name="stock_array[<%=group_index %>][sku_id]" value="' + skuData[index].sku.id + '"/>' +
                        '<input type="hidden" name="stock_array[<%=group_index %>][group_id]" value="<%=group_id %>"/>' +
                        '<input type="hidden" name="stock_array[<%=group_index %>][order_by]" value="0"/>' +
                        '<input type="hidden" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][stock_id]" value="<%=stock_id %>"/>' +
                        '<input type="hidden" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][stock_name]" value="' + skuAtom.atom.text + '"/>' +
                        '<input type="hidden" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][sku_id]" value="' + skuAtom.atom.id + '"/>';
                } else if(len === 2) {
                    if (index === 0) {
                        groupHtml = '<input type="hidden" name="stock_array[<%=group_index %>][group_name]" value="' + skuAtom.atom.text + '"/>' +
                            '<input type="hidden" name="stock_array[<%=group_index %>][sku_id]" value="' + skuAtom.atom.id + '"/>' +
                            '<input type="hidden" name="stock_array[<%=group_index %>][group_id]" value="<%=group_id %>"/>' +
                            '<input type="hidden" name="stock_array[<%=group_index %>][order_by]" value="0"/>';
                    } else if (index === 1) {
                        groupHtml = '<input type="hidden" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][stock_id]" value="<%=stock_id %>"/>' +
                            '<input type="hidden" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][stock_name]" value="' + skuAtom.atom.text + '"/>' +
                            '<input type="hidden" name="stock_array[<%=group_index %>][stocks][<%=stock_index %>][sku_id]" value="' + skuAtom.atom.id + '"/>';
                    }
                }
                tdHtml += (groupHtml + '</td>');
                tdHtml = tdHtml.replace(/<%=group_index %>/g, group_index);
                tdHtml = tdHtml.replace(/<%=stock_index %>/g, atom_index);
                _this.TableHtml += tdHtml;
                if (index === len -1) {
                    var stockData = _this.getAtomStockData(_this.trIndex);
                    var tdStockHtml = _this.td,
                        market_price = stockData.market_price ? Number(stockData.market_price).toFixed(2, 10) : '',
                        shop_price = stockData.market_price ? Number(stockData.shop_price).toFixed(2, 10) : '',
                        union_price = stockData.market_price ? Number(stockData.union_price).toFixed(2, 10) : '',
                        min_price = stockData.market_price ? Number(stockData.min_price).toFixed(2, 10) : '',
                        recommend_price = stockData.market_price ? Number(stockData.recommend_price).toFixed(2, 10) : '',
                        group_id = stockData.group_id ? Number(stockData.group_id) : 0,
                        stock_id = stockData.stock_id ? Number(stockData.stock_id) : 0;
                    tdStockHtml = tdStockHtml.replace(/<%=market_price %>/g, market_price);
                    tdStockHtml = tdStockHtml.replace(/<%=shop_price %>/g, shop_price);
                    tdStockHtml = tdStockHtml.replace(/<%=union_price %>/g, union_price);
                    tdStockHtml = tdStockHtml.replace(/<%=min_price %>/g, min_price);
                    tdStockHtml = tdStockHtml.replace(/<%=recommend_price %>/g, recommend_price);
                    tdStockHtml = tdStockHtml.replace(/<%=stock_value %>/g, stockData.stock_value);
                    tdStockHtml = tdStockHtml.replace(/<%=order_by %>/g, _this.trIndex);
                    tdStockHtml = tdStockHtml.replace(/<%=group_index %>/g, group_index);
                    tdStockHtml = tdStockHtml.replace(/<%=stock_index %>/g, atom_index);
                    _this.TableHtml += tdStockHtml + '</tr>';
                    _this.TableHtml = _this.TableHtml.replace(/<%=group_id %>/g, group_id);
                    _this.TableHtml = _this.TableHtml.replace(/<%=stock_id %>/g, stock_id);
                    _this.trIndex += 1;
                    tag = false;
                }
                var key = index + 1;
                _this.printRow(key, tag, atom_index);
            });
        },
        getAtomStockData: function(trIndex) {
            var stockList = this.list, stock = stockList[trIndex];
            if ( !stock) {
                stock = {market_price: "", shop_price: "", union_price: "", min_price: "", recommend_price: "", stock_value: 0};
            }
            return stock;
        },
        reverseUpdateStock: function(e) {
            var $ele = $(e.target), name = $ele.attr('tag'), _this = this;
            name = ('sku_price' === name) ? 'price' : name;
            var val = $.trim($ele.val()), $trList = _this._$.find('tr'),
                index = $trList.index($ele.parents('tr')) -1, stockList = _this.list;
            if ( !stockList[index]) {
                stockList = _this.generateStockData();
            }
            stockList[index][name] = val;
            _this.updateStockBackup(stockList);
            _this.list = stockList;
        },
        onAtomPriceBlur: function(_this, e) {
            var $ele = $(e.target), price = Number($ele.val());
            $ele.val(price.toFixed(2, 10));
            _this.validAtomPrice($ele);
        },
        validAtomPrice: function($ele) {
            var price = $ele.val(), $td = $ele.parents('td'), _this = this,
                $err = $td.find('.error-message'), check = _this.validatePrice(price);
            if (check !== false) {
                if (0 === $err.length) {
                    $err = $('<div class="error-message"></div>');
                    $td.append($err.html(check));
                } else {
                    $err.html(check);
                    $td.addClass('manual-valid-error');
                    _this.errorLen += 1;
                }
            } else {
                $err.remove();
                $td.removeClass("manual-valid-error", function() {
                    _this.errorLen -= 1;
                });
            }
        },
        validAllPrice: function() {
            var _this = this;
            _this._$.find('.js-price').each(function(i, element) {
                var $element = $(element);
                _this.validAtomPrice($element);
            });
        },
        validatePrice: function(price) {
            if ( !price) {
                return "价格不能为空";
            }
            price = Number(price);
            return (isNaN(price) ? "请输入一个数字" : (0.01 > price ? "价格最低为 0.01" : false));
        },
        validateStockData: function() {
            this.errorLen = 0;
            if (this.isStockModulShow) {
                this.validAllPrice();
            }
        },
        onAtomPriceInput: function(_this, e) {
            _this.updatePrice();
            _this.reverseUpdateStock(e);
        },
        onAtomStockInput: function(_this, e) {
            _this.updateTotalStock();
            _this.reverseUpdateStock(e);
        },
        calcPrice: function() {
            var priceList = [], $price = this._$.find('.js-price');
            $price.each(function(i, price){
                var val = $.trim($(price).val());
                if (_.isEmpty(val)) {
                    return false;
                }
                price = Number(price);
                return isNaN(price) ? false : priceList.push(price);
            });
            var minPrice = _.isEmpty(priceList) ? 0 : _.min(priceList);
            return minPrice;
        },
        updatePrice: function() {
            var _this = this, min_price = _this.calcPrice();
            min_price = Number(min_price).toFixed(2, 10) + "";
            _this.minPrice = min_price;
            _this.price = min_price;
        },
        calcTotalStock: function() {
            var total = 0, $stockNum = this._$.find('.js-stock-num');
            $stockNum.each(function(i, stock){
                var val = Number($(stock).val());
                total += val;
            });
            return total;
        },
        updateTotalStock: function() {
            $('input[name="goods_number"]').val(this.calcTotalStock());
        },
        hideStockModule: function() {
            $('div.js-goods-stock').hide();
            $('input[name="market_price"]').prop("readonly", false);
            $('input[name="shop_price"]').prop("readonly", false);
            $('input[name="union_price"]').prop("readonly", false);
            $('input[name="min_price"]').prop("readonly", false);
            $('input[name="recommend_price"]').prop("readonly", false);
            $('input[name="goods_number"]').prop("readonly", false);
            $('input[name="attr_type"]').val(0);
        },
        showStockModule: function() {
            $('div.js-goods-stock').show();
            $('input[name="market_price"]').prop("readonly", true);
            $('input[name="shop_price"]').prop("readonly", true);
            $('input[name="union_price"]').prop("readonly", true);
            $('input[name="min_price"]').prop("readonly", true);
            $('input[name="recommend_price"]').prop("readonly", true);
            $('input[name="goods_number"]').prop("readonly", true);
            $('input[name="attr_type"]').val(1);
        },
        getQuery: function() {
            return this._$;
        }
    });
    $.extend(SkuStockInfo.prototype, {
        init: function() {
            this._$ = $(this.structure);
            this.initSkuView();
            this.initStockView();
        },
        initSkuView: function() {
            this.skuList = new SkuList();
            this._$.find('#sku-region').html(this.skuList.getQuery());
        },
        initStockView: function() {
            this.stockView = new Stock(this.skuList);
            this._$.find('#stock-region').html(this.stockView.getQuery());
            this._$.find('div.js-goods-stock').hide();
        },
        getQuery: function() {
            return this._$;
        }
    });

    $(document).ready(function($){
        window.skuStockInfo = new SkuStockInfo();
        $('#tabs-attrlist').append(window.skuStockInfo.getQuery());
        var skuList = window._global.skuList || [], skuStockList = window._global.skuStockList || [];
        _.each(skuList, function(sku) {
            window.skuStockInfo.skuList.addSkuGroup(window.skuStockInfo.skuList, sku);
        });
        window.skuStockInfo.stockView.list = skuStockList;
        window.skuStockInfo.stockView.stockBackup = skuStockList;
        window.skuStockInfo.stockView.showStockView();
    });
})( jQuery, _, window, document );