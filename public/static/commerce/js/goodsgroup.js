$(function($){
    $("span.operate-delete").live("click", function(){
        $(this).parents("tr").remove();
    });
    $("#goods-search").select2({
        width: '400px',
        dropdownAutoWidth: false,
        minimumInputLength: 0,
        ajax: {
            url: "/b/vmall/goodsgroup/goodselect2",
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
            var goods_id = (typeof(row.goods_id) !== 'undefined') ? row.goods_id : row.id;
            var stock_id = (typeof(row.stock_id) !== 'undefined') ? row.stock_id : row.id;
            var goods_name = (typeof(row.goods_name) !== 'undefined') ? row.goods_name + ' ' : '';
            var tax_rate = (typeof(row.tax_rate) !== 'undefined') ? row.tax_rate + '' : '';//税率
            var stock_barcode = (typeof(row.tax_rate) !== 'undefined') ? row.stock_barcode + '' : '';//库存条形码
            var shop_price = (typeof(row.shop_price) !== 'undefined') ? row.shop_price + '' : '';//价格
            var html = '';
            var tdHtml = '<td><input type="text" name="sub_goods_number[]" value="1" class="stockNum"/></td>'+
                '<td><input type="text" name="sub_shop_price[]" value="' + shop_price + '" class="stockNum"/></td>'+
                '<td>' + shop_price + '</td>'+
                '<td>'+tax_rate+'</td>'+
                '<td>' + stock_barcode + '</td>'+
                '<td><span class="operate-delete">删除</a></span>';
            html = '<tr><td>' + goods_name + row.text +
                    '<input type="hidden" name="sub_goods_id[]" value="' + goods_id + '"/>' +
                    '<input type="hidden" name="sub_stock_id[]" value="' + stock_id + '"/>' +
                    '<input type="hidden" name="sub_tax_rate[]" value="' + tax_rate + '"/>' + 
                    '<input type="hidden" name="sub_stock_barcode[]" value="' + stock_barcode + '"/>' + 
                    '<input type="hidden" name="sub_cost_price[]" value="' + shop_price + '"/>' + 
                    '</td>' + tdHtml + '</tr>';
            $("#goods-select").append(html);
            return goods_name + row.text; 
        },
        escapeMarkup: function (m) { return m; }
    });
});