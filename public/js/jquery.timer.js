/*
* jQuery Time plugin 1.0
*
* Copyright (c) 2013 by teddy
*
* QQ:97081817 MAIL：97081817@qq.com
*
* Blog：http://www.cnblogs.com/mybear/
*/
(function ($) {
    $.fn.extend({
        timer: function (options) {
            var defaults = { Interval: 30, minValue: "00:00", maxValue: "23:59", onSelect: function (date) { } };
            var options = $.extend(defaults, options);
            return this.each(function () {
                new $.timer(this, options);
            });
        },
        hide: function () {
            return this.trigger("hide");
        }
    })
    /*input绑定事件*/
    $.timer = function (input, options) {
        $(input).bind("click", function () {
            InitTimesHTML(input, options);
            $(this).trigger("show");
        }).bind("show", function (event) {
            var offset = $(input).offset();
            $("#times").css({
                top: offset.top + input.offsetHeight,
                left: offset.left
            }).show(false);
        }).bind("hide", function (event) {
            $("#times").css("display", "none");
            $("body").unbind("mousedown");
        })
    }
    /*初始化显示HTML*/
    function InitTimesHTML(input, options) {
        var result = "";
        var times = InitTimes(input, options);

        for (var i = 0; i < times.length; i++) {
            if (times[i] == $(input).val())
                result += "<li style=\"background:#a9e4e9\">" + times[i] + "</li>";
            else
                result += "<li onmouseover=\"this.style.background='#a9e4e9'\" onmouseout=\"this.style.background='#fff'\">" + times[i] + "</li>";
        }
        /*将时间列表加入页面*/
        if ($("#times").length > 0) {
            $("#times").html(result);
        } else {
            $("body").append("<ul id=\"times\" class=\"times\" style=\"display:none\">" + result + "</ul>");
        }

        /*绑定日期选择事件*/
        $("#times li").bind("click", function () {
            var time = $(this).text();
            options.onSelect.call(input, time);
            $(input).trigger("hide").val(time);
        });

        /*实现点击body别的地方自动隐藏弹出窗口*/
        $("#times").hover(function () {
            $("body").unbind("mousedown");
        }, function () {
            $('body').bind('mousedown', function () {
                $("body").unbind("mousedown");
                $(input).trigger("hide");
            })
        });

        $('body').bind('mousedown', function () {
            $("body").unbind("mousedown");
            $(input).trigger("hide");
        })
    }

    /*根据配置的最小和最大时间初始时间数组*/
    function InitTimes(input, options) {
        var times = [];
        var minDate = new Date(1990, 10, 01);
        minDate.setHours(options.minValue.split(":")[0], options.minValue.split(":")[1]);
        if (options.minValue > options.maxValue) {
            var maxDate = new Date(1990, 10, 02);
        } else {
            var maxDate = new Date(1990, 10, 01);
        }
        maxDate.setHours(options.maxValue.split(":")[0], options.maxValue.split(":")[1]);


        while (maxDate > minDate || maxDate.getTime() == minDate.getTime()) {
            var time = (minDate.getHours() > 9 ? minDate.getHours() : "0" + minDate.getHours()) + ":" + (minDate.getMinutes() > 9 ? minDate.getMinutes() : "0" + minDate.getMinutes());
            times.push(time);
            minDate.setMinutes(minDate.getMinutes() + options.Interval);
        }
        return times.sort();
    }
})(jQuery);