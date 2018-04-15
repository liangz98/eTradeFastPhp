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
                var div2 = document.getElementById(ffg+"1");
                var div3 = document.getElementById(ffg+"2");

//下面使用each进行遍历
                $.each(json[ffg], function (n, value) {

                    var trs = "";
                    trs += "<option value=" + value.Code + ">" + value.name + "</option>";
                    select += trs;
                });
                $(div1).append(select);
                $(div2).append(select);
                $(div3).append(select);
            });

    }};