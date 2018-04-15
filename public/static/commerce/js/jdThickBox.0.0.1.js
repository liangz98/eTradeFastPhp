(function($){$.extend($.browser,{client:function(){return{width:document.documentElement.clientWidth,height:document.documentElement.clientHeight,bodyWidth:document.body.clientWidth,bodyHeight:document.body.clientHeight};},scroll:function(){return{width:document.documentElement.scrollWidth,height:document.documentElement.scrollHeight,bodyWidth:document.body.scrollWidth,bodyHeight:document.body.scrollHeight,left:document.documentElement.scrollLeft,top:document.documentElement.scrollTop};},screen:function(){return{width:window.screen.width,height:window.screen.height};},isIE6:$.browser.msie&&$.browser.version==6,isMinW:function(val){return Math.min($.browser.client().bodyWidth,$.browser.client().width)<=val;},isMinH:function(val){return $.browser.client().height<=val;}})})(jQuery);(function($){$.fn.jdPosition=function(option){var s=$.extend({mode:null},option||{});switch(s.mode){default:case "center":var ow=$(this).outerWidth(),oh=$(this).outerHeight();var w=$.browser.isMinW(ow),h=$.browser.isMinH(oh);$(this).css({left:(!$.browser.isIE6)?(w?0:Math.max(($.browser.client().width-ow)/2,0)+"px"):(w?0:Math.max($.browser.scroll().left+($.browser.client().width-ow)/2,0)+"px"),top:(!$.browser.isIE6)?(h?0:Math.max(($.browser.client().height-oh)/2,0)+"px"):(($.browser.scroll().top<=$.browser.client().bodyHeight-oh)?(Math.max($.browser.scroll().top+($.browser.client().height-oh)/2,0)+"px"):($.browser.client().bodyHeight-oh)+"px")});break;case "auto":break;case "fixed":break;}}})(jQuery);

(function($) {
    $.fn.jdThickBox = function(option, callback) {
        if (typeof option == "function") {
            callback = option;
            option = {};
        };
        var s = $.extend({
            type: "text",
            source: null,
            scrolling:'no',
            width: null,
            height: null,
            title: null,
            stopinit:false,
            _frame: "",
            _div: "",
            _box: "",
            _con: "",
            _loading: "thickloading",
            close: false,
            _close: "",
            _close_val: "×",
            _titleOn: true,
            _title: ""
        },
        option || {});
        var object = (typeof this != "function") ? $(this) : null;
        var close = function() {
            $(".thickcon").empty();
            $(".thickcon").remove();
            $(".thickframe").add(".thickdiv").hide();
            $(".thickbox").remove();
            $(window).unbind("resize.jdThickBox").unbind("scroll.jdThickBox");
        };
        if (s.close) {
            close();
            return false;
        };
        var reg = function(str) {
            if (str != "") {
                return str.match(/\w+/)
            } else {
                return ""
            };
        };
        var init = function(element) {
            if ($(".thickframe").length == 0 || $(".thickdiv").length == 0) {
                $("<iframe class='thickframe' id='" + reg(s._frame) + "' marginwidth='0' marginheight='0' frameborder='0' scrolling='no'></iframe>").appendTo($(document.body));
                $("<div class='thickdiv' id='" + reg(s._div) + "'></div>").appendTo($(document.body));
            } else {
                $(".thickframe").add(".thickdiv").show();
            };
            $("<div class='thickbox' id='" + reg(s._box) + "'></div>").appendTo($(document.body));
            if (s._titleOn) initTitle(element);
            $("<div class='thickcon' id='" + reg(s._con) + "' style='width:" + s.width + "px;height:" + s.height + "px;'></div>").appendTo($(".thickbox"));
            $(".thickcon").addClass(s._loading);
            reposi();
            initClose();
            if( !s.stopinit) {inputData(element);}
            //$(window).bind("resize.jdThickBox", reposi).bind("scroll.jdThickBox", reposi);
            $(document.body).bind("click.jdThickBox",
            function(e) {
                e = e ? e: window.event;
                var tag = e.srcElement ? e.srcElement: e.target;
                if (tag.className == "thickdiv") {
                    $(this).unbind("click.jdThickBox");
                    close();
                }
            })
        };
        var initTitle = function(element) {
			s.title = (s.title == null&&element) ? element.attr("title") : s.title;
			$("<div class='thicktitle' id='" + reg(s._title) + "' style='width:" + (s.width + 12) + "'><span>" + s.title + "</span></div>").appendTo($(".thickbox"));
            s.title = null;
        };
        var initClose = function() {
            if (s._close != null) {
                $("<a href='#' class='btn_cel' id='" + reg(s._close) + "'>" + s._close_val + "</a>").appendTo($(".thickbox"));
                $(".btn_cel").one("click",
                function() {
                    close();
                    return false;
                });
            }
        };
        var inputData = function(element) {
            s.source = (s.source == null) ? element.attr("href") : s.source;
            switch (s.type) {
            default:
            case "text":
                $(".thickcon").html(s.source);
				$(".thickcon").removeClass(s._loading);
                break;
            case "html":
                $(s.source).clone().appendTo($(".thickcon")).show();
				$(".thickcon").removeClass(s._loading);
                break;
            case "image":
                s._index = (s._index == null) ? object.index(element) : s._index;
                $(".thickcon").append("<img src='" + s.source + "' width='" + s.width + "' height='" + s.height + "'>");
                s.source = null;
				$(".thickcon").removeClass(s._loading);
                break;
            case "ajax":
            case "json":
                callback(s.source, $(".thickcon"),function(){$(".thickcon").removeClass(s._loading);});
                break;
            case "iframe":
                $("<iframe src='" + s.source + "' marginwidth='0' marginheight='0' frameborder='0' scrolling='"+s.scrolling+"' style='width:" + s.width + "px;height:" + s.height + "px;border:0;'></iframe>").appendTo($(".thickcon"));
				$(".thickcon").removeClass(s._loading);
                break;

            };
        };
        var reposi = function() {
            var w1 = $(".thickcon").outerWidth(),
            h1 = (s._titleOn ? $(".thicktitle").outerHeight() : 0) + $(".thickcon").outerHeight();
            $(".thickbox").css({
                width: w1 + "px",
                height: h1 + "px"
            });
            $(".thickbox").jdPosition({
                mode: "center"
            });
            if ($.browser.isIE6) {
                var ow = $(".thickbox").outerWidth(),
                oh = $(".thickbox").outerHeight();
                var w2 = $.browser.isMinW(ow),
                h2 = $.browser.isMinH(oh);
                $(".thickframe").add(".thickdiv").css({
                    width: w2 ? ow: "100%",
                    height: Math.max($.browser.client().height, $.browser.client().bodyHeight) + "px"
                });
            }
        };
        if (object != null) {
            object.click(function() {
                init($(this));
                return false;
            });
        } else {
            init();
        }
    };
    $.jdThickBox = $.fn.jdThickBox;
})(jQuery);

function jdThickBoxclose(){$.jdThickBox({close:true});}
function CloseSeedMsgBox(){$.jdThickBox({close:true});}