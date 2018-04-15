/* Created by jankoatwarpspeed.com */

(function($) {
    $.fn.formToWizard = function(options) {
        options = $.extend({  
            submitButton: "" 
        }, options); 
        
        var element = this;

        var steps = $(element).find("fieldset");
        var count = steps.size();
        var submmitButtonName = "#" + options.submitButton;
        $(submmitButtonName).hide();

        // 2
        $(element).before("<ul id='steps'></ul>");

        steps.each(function(i) {
            $(this).wrap("<div id='step" + i + "'></div>");
            $(this).append("<p id='step" + i + "commands'></p>");

            // 2
            var name = $(this).find("legend").html();
            $("#steps").append("<li id='stepDesc" + i + "'><span>" + name + "</span></li>");

            if (i == 0) {
                createNextButton(i);
                selectStep(i);
            }
            else if (i == count - 1) {
                $("#step" + i).hide();
                createPrevButton(i);
            }
            else {
                $("#step" + i).hide();
                createPrevButton(i);
                createNextButton(i);
            }
        });

        function createPrevButton(i) {
            var stepName = "step" + i;
            $("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Prev' class='prev'>< 上一步</a>");

            $("#" + stepName + "Prev").bind("click", function(e) {
                $("#" + stepName).hide();
                $("#step" + (i - 1)).show();
                $(submmitButtonName).hide();
                selectStep(i - 1);
            });
        }

        function createNextButton(i) {
            var stepName = "step" + i;
            $("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Next' class='next'>下一步 ></a>");
            $("#" + stepName + "Next").bind("click", function(e) {
                var num=0;
                $("#" + stepName).find('.must').each(function() {
                    var text =$.trim($(this).val());
                    if(text == ""){
                        num=num+2;
                        $("input:text").each(function() {
                            var em = '<em class="error" style="margin-left:108px;">请填写此项</em>';
                            if($.trim($(this).val()) == ""){
                                if($(this).parents("p").find("em").length==0){
                                    $(this).parents("p").find("input:last").after(em);
                                }
                            }
                        });
                    }
                });
                /*2选一 必须分开判断是否为空 */
                var num1=0;
                var num2=0;
                if($("#" + stepName).find(".isbaoguanmk").length > 0){
                    if($('[name="isAssignCustomsAgency"]:checked').val()=='1'){
                        var Code=$('[name="customsAgencyCode"]').val();
                        var Name=$('[name="customsAgencyName"]').val();
                        if(Code=='' || Code==null){
                            num1=num1+1;
                        }
                        if(Name=='' || Name==null){
                            num2=num2+1;
                        }
                    } 
                }
                    
                if((num+num1+num2)<=1){
                    $("#" + stepName).hide();
                    $("#step" + (i + 1)).show();
                    if (i + 2 == count)
                    $(submmitButtonName).show();
                    selectStep(i + 1);
                }
            });
        }

        function selectStep(i) {
            $("#steps li").removeClass("current");
            $("#stepDesc" + i).addClass("current");
        }

    }
})(jQuery); 