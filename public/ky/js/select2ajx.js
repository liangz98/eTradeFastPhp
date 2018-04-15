

function formatRepo(repo) {

    if(repo.loading) return repo.text;

    var markup = '<option value="' + repo.id + '">' + repo.name + '</option>';

    return markup;
}

function formatRepoSelection(repo) {
    return repo.name || repo.text;
}


$(".js-data-example-ajax").select2({
    //

    ajax: {

        url: "/user/data/port",
        dataType: 'json',
        cache:false,
        delay: 250,
        data: function(params) {
            var shipping= $('#SHIPPING_METHOD').val();
            if(shipping=="SEA"){
                dictype="SEA_PORT";
            }else if(shipping=="AIR"){
                dictype="AIR_PORT";
            }else {
                dictype="CITY_ISO_CODE";
            }
            return {
                q: params.term, // search term
                type:dictype,
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
