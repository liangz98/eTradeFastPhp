var j = jQuery;
var prov = "";
var city = "";
var area = "";
j(function() {
    j.get("region/allxml", function(xml) {
    j("body").data("xml", xml);
    j("#province_city_area").append("<select id='_province' name='_province' class='select1'></select>&nbsp;<select id='_city' class='select1'></select><select id='_area' class='select1'></select>");
        j("province", xml).each(function() {
            if (j(this).attr("province") == prov)
                j("#_province").append("<option value=" + j(this).attr("provinceID") + " selected>" + j(this).attr("province") + "</option>");
            else
                j("#_province").append("<option value=" + j(this).attr("provinceID") + ">" + j(this).attr("province") + "</option>");
        }); //初始省
		
		if ( $("#_province").length > 0 ) { 
        	LoadCity(j("#_province").val().substr(0, 3), xml); //初始市 
		}
		
		if ( $("#_city").length > 0 ) { 
        	LoadArea(j("#_city").val().substr(0, 4), xml); //初始区
		}

        j("#_province").change(function() {
            j("#_city").empty();
            var Pvalue = j(this).val().substr(0, 3);
            if (j("province[provinceID^=" + Pvalue + "] City", xml).length == 0) {
                j("#_city").hide();
                j("#_area").remove();
                return;
            } else
                j("#_city").show();
            LoadCity(Pvalue, xml);
            LoadArea(j("#_city").val().substr(0, 4), xml);
        });

        j("#_city").change(function() {
            LoadArea(j(this).val().substr(0, 4), xml);
        });
    }
    );

    function LoadCity(Pvalue, xml) {
        j("#_city").empty();
        j("province[provinceID^=" + Pvalue + "] City", xml).each(function() {
            if (j(this).attr("City") == city)
                j("#_city").append("<option value=" + j(this).attr("CityID") + " selected>" + j(this).attr("City") + "</option>");
            else
                j("#_city").append("<option value=" + j(this).attr("CityID") + ">" + j(this).attr("City") + "</option>");
        });
    }

    function LoadArea(Cvalue, xml) {
        j("#_area").remove();
        j("#_city").after("&nbsp;<select id='_area' class='select1'></select>");
        j("City[CityID^=" + Cvalue + "] Piecearea", xml).each(function() {
            if (j(this).attr("Piecearea") == area)
                j("#_area").append("<option selected>" + j(this).attr("Piecearea") + "</option>");
            else
                j("#_area").append("<option>" + j(this).attr("Piecearea") + "</option>");
        });
    }
});

j.extend({
    Jprov: function(provalue) {
        if (provalue == null)
            return j("#_province option:selected").text();
        else {
            prov = provalue;
            j("#_province option").each(function() {
                if (j(this).text() == provalue) {
                    j(this).attr("selected", true);
                    j("#_province").trigger("change");
                }
            });
        }
    },
    Jcity: function(cityvalue) {
        if (cityvalue == null)
            return j("#_city option:selected").text();
        else {
            city = cityvalue;
            j("#_city option").each(function() {
                if (j(this).text() == cityvalue) {
                    j(this).attr("selected", true);
                    j("#_city").trigger("change");
                }
            });
        }
    },
    Jarea: function(areavalue) {
        if (areavalue == null)
            return j("#_area option:selected").text();
        else {
            area = areavalue;
            j("#_area option").each(function() {
                if (j(this).text() == areavalue) {
                    j(this).attr("selected", true);
                }
            });
        }
    }
});