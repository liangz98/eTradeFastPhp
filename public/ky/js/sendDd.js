$(document).ready(function(){
    $(input).onload(function(){
        var inid=$(this).attr("id");
        var datadic=$(this).val();
     if(inid!=null){
         $.post('/ky/one.php',
             {"code":inid, "valueCode": datadic, "lang":"zh_CN"},
             function(data,status){

                 var json;
                 if(typeof data === 'object'){
                     json = data;
                 }
                 else{
                     json = eval('(' + data + ')');
                 }
                 var select = "";
                 var arry=json[inid];
                 var div1 = document.getElementById(ffg);

//下面使用each进行遍历
                 $.each(json[ffg], function (n, value) {

                     var trs = "";
                     trs += "<option value=" + value.valueCode + ">" + value.name + "</option>";
                     select += trs;
                 });
                 $(div1).append(select);
             })
     };
    });
});

function selectDATA(obj){
    var coun=$(obj).val();
    if(coun !== null) {
        return false;
    }else {

        var ffg=$(obj).attr("id");
        var jqxhr=$.post('/ky/go.php',
            {'codes':ffg},

            function(data,status){

                var json;
                if(typeof data === 'object'){
                    json = data;
                }
                else{
                    json = eval('(' + data + ')');
                }
                var select = "";
                var arry=json[ffg];
                var div1 = document.getElementById(ffg);

//下面使用each进行遍历
                $.each(json[ffg], function (n, value) {

                    var trs = "";
                    trs += "<option value=" + value.valueCode + ">" + value.name + "</option>";
                    select += trs;
                });
                $(div1).append(select);
            });

    }};