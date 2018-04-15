

function formatRepo(repo) {

    if(repo.loading) return repo.text;
/*    taxRate="'+ repo.taxRate+'" rebateRate="'+ repo.rebateRate+'" pricing="' + repo.pricingUnit+'"*/
    var markup = '<option taxRate="'+ repo.taxRate+'" rebateRate="'+ repo.rebateRate+'" pricing="' + repo.pricingUnit+'" title="' + repo.name+'"  value="' + repo.id + '" >' + repo.id+'('+ repo.name+')</option>';
    return markup;
}

function formatRepoSelection(repo) {
    var tax2=(repo.taxRate)*100;
    var rebate2=(repo.rebateRate)*100;

    $("#taxRate").val(repo.taxRate);
    $("#taxRate2").html(tax2);
    $("#rebateRate").val(repo.rebateRate);
    $("#rebateRate2").html(rebate2);
    $("#legalPricingUnit").val(repo.pricingUnit);
    $("#hscode").val(repo.id);

    if(repo.pricingUnit!=null&&repo.pricingUnit!=""){
    $.post(
        "/user/data/dicto",
        {files:"datatest_setting",e:"PRODUCT_PRICING_UNIT",t:+repo.pricingUnit},
        function (data) {
            var json;
                json = data;
            $("#pricingUnit-name").html(json);
        },"json"
    );
    }

    return repo.id+'('+repo.text+')';

}


$(".js-data-example-ajax").select2({

    ajax: {
        url: "/user/hscode/list",
        dataType: 'json',
        cache:false,
        delay: 250,
        data: function(params) {
            return {
                q: params.term, // search term
                page: params.page
            };
        },
        processResults: function(data, params) {
            params.page = params.page || 1;
            return {
                results: data,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        },
        cache: true
    },
    escapeMarkup: function(markup) {
        return markup;
    }, // let our custom formatter work
    minimumInputLength: 1,
    templateResult: formatRepo, // omitted for brevity, see the source of this page
    templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
});
