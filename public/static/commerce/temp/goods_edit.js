!function(e) {
    "function" == typeof module ? module.exports = e(this.jQuery || require("jquery")) : "function" == typeof define && define.amd ? define("vendor/nprogress", ["jquery", "core/event"], function(t) {
        return e(t)
    }) : this.NProgress = e(this.jQuery)
}(function(e) {
    function t(e, t, i) {
        return t > e ? t : e > i ? i : e
    }
    function i(e) {
        return 100 * (-1 + e)
    }
    function n(e, t, n) {
        var s;
        return s = "translate3d" === o.positionUsing ? {transform: "translate3d(" + i(e) + "%,0,0)"} : "translate" === o.positionUsing ? {transform: "translate(" + i(e) + "%,0)"} : {"margin-left": i(e) + "%"}, s.transition = "all " + t + "ms " + n, s
    }
    var s = {};
    s.version = "0.1.2";
    var o = s.settings = {minimum: .2 * Math.random() + .3,easing: "ease",positionUsing: "",speed: 500,trickle: !0,trickleRate: .1,trickleSpeed: 800,showSpinner: !0,template: '<div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div>'};
    return s.configure = function(t) {
        return e.extend(o, t), this
    }, s.status = null, s.set = function(e) {
        var i = s.isStarted();
        e = t(e, o.minimum, 1), s.status = 1 === e ? null : e;
        var a = s.render(!i), r = a.find('[role="bar"]'), l = o.speed, c = o.easing;
        return a[0].offsetWidth, a.queue(function(t) {
            "" === o.positionUsing && (o.positionUsing = s.getPositioningCSS()), r.css(n(e, l, c)), 1 === e ? (a.css({transition: "none",opacity: 1}), a[0].offsetWidth, setTimeout(function() {
                a.css({transition: "all " + l + "ms linear",opacity: 0}), setTimeout(function() {
                    s.remove(), t()
                }, l)
            }, l)) : setTimeout(t, l)
        }), this
    }, s.isStarted = function() {
        return "number" == typeof s.status
    }, s.start = function() {
        s.status || s.set(0);
        var e = function() {
            setTimeout(function() {
                s.status && (s.trickle(), e())
            }, o.trickleSpeed)
        };
        return o.trickle && e(), this
    }, s.done = function(e) {
        return e || s.status ? (window.NC && window.NC.trigger("finish"), s.inc(.3 + .5 * Math.random()).set(1)) : this
    }, s.inc = function(e) {
        var i = s.status;
        return i ? ("number" != typeof e && (e = (1 - i) * t(Math.random() * i, .1, .95)), i = t(i + e, 0, .994), s.set(i)) : s.start()
    }, s.trickle = function() {
        return s.inc(Math.random() * o.trickleRate)
    }, s.render = function(t) {
        if (s.isRendered())
            return e("#nprogress");
        e("html").addClass("nprogress-busy");
        var n = e("<div id='nprogress'>").html(o.template), a = t ? "-100" : i(s.status || 0);
        return n.find('[role="bar"]').css({transition: "all 0 linear",transform: "translate3d(" + a + "%,0,0)"}), o.showSpinner || n.find('[role="spinner"]').remove(), n.appendTo(document.body), n
    }, s.remove = function() {
        e("html").removeClass("nprogress-busy"), e("#nprogress").remove()
    }, s.isRendered = function() {
        return e("#nprogress").length > 0
    }, s.getPositioningCSS = function() {
        var e = document.body.style, t = "WebkitTransform" in e ? "Webkit" : "MozTransform" in e ? "Moz" : "msTransform" in e ? "ms" : "OTransform" in e ? "O" : "";
        return t + "Perspective" in e ? "translate3d" : t + "Transform" in e ? "translate" : "margin"
    }, s
}), define("components/pop/base", ["backbone"], function(e) {
    var t = e.View.extend({className: "popover left",initialize: function(e) {
            this.callback = e.callback, this.target = e.target, this.trigger = e.trigger, e.notAutoHide || this.autoHide()
        },render: function() {
            return this.$el.html(this.template({})), this.onRender && this.onRender(), this
        },autoHide: function() {
            var e = this;
            $(document).on("click", function(t) {
                e.isShow() && 0 === e.$el.has(t.target).length && t.target !== e.trigger[0] && e.hide()
            })
        },setCallback: function(e) {
            this.callback = e
        },setTarget: function(e) {
            this.target = e
        },setTrigger: function(e) {
            this.trigger = e
        },isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },hide: function() {
            return this.$el.hide(), this.$el
        },show: function() {
            return this.$el.show(), this.$el
        },toggle: function() {
            return this.$el.toggle(), this.$e
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : -1 !== t.indexOf("top") ? {of: e.target,my: "center bottom",at: "center top",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        },triggerCallback: function() {
            this.callback.call(this), this.hide()
        }});
    return t
}), define("text!components/pop/templates/link.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-link">\n    <div class="popover-content">\n        <div class="form-inline">\n            <input type="text" class="link-placeholder js-link-placeholder" placeholder="<%= content %>">\n            <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定"> 确定</button>\n            <button type="reset" class="btn js-btn-cancel">取消</button>\n        </div>\n    </div>\n</div>\n'
}), define("components/pop/link", ["backbone", "components/pop/base", "text!components/pop/templates/link.html", "core/utils"], function(e, t, i, n) {
    return t.extend({template: _.template(i),className: "popover popover-link-wrap bottom",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keydown .js-link-placeholder": function(e) {
                e.keyCode === n.keyCode.ENTER && this.triggerCallback()
            }},initialize: function(e) {
            this.content = e.content || "链接地址：http://example.com", this.callback = e.callback, this.target = e.target, this.trigger = e.trigger, e.notAutoHide || this.autoHide()
        },render: function() {
            return this.$el.html(this.template({content: this.content})), this
        },positioning: function() {
            this.$el.show().position({of: this.target,my: "center top+5",at: "center bottom",collision: "none"}), this.$(".js-link-placeholder").focus()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.content || "链接地址：http://example.com";
            this.$(".js-link-placeholder").attr("placeholder", s), this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.clearInput(), this.show()
        },triggerCallback: function() {
            var e = n.urlCheck(this.$(".js-link-placeholder").val());
            this.callback.call(this, e), this.hide()
        },clearInput: function() {
            this.$(".js-link-placeholder").val("")
        }})
}), define("text!components/pop/templates/confirm.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-confirm">\n    <% if (options.className.indexOf(\'popover-confirm-block\') > 0) { %>\n        <div class="popover-content">\n            <div class="form-inline">\n                <div class="js-confirm-text custom-template">\n                    <%= options.data || \'\' %>\n                </div>\n                <div class="js-confirm-btn-action btn-action">\n                    <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n                    <button type="reset" class="btn js-btn-cancel">取消</button>\n                </div>\n            </div>\n        </div>\n    <% } else { %>\n        <div class="popover-content text-center">\n            <div class="form-inline">\n                <span class="js-confirm-text help-inline">\n                    <%= options.data || \'\' %>\n                </span>\n                <span class="js-confirm-btn-action">\n                    <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n                    <button type="reset" class="btn js-btn-cancel">取消</button>\n                </span>\n            </div>\n        </div>\n    <% } %>\n</div>\n'
}), define("components/pop/confirm", ["require", "backbone", "components/pop/base", "text!components/pop/templates/confirm.html", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/confirm.html");
        e("core/utils")
    }
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },initialize: function(e) {
            $(".popover-confirm").parents(".popover").remove(), t.prototype.initialize.call(this, e)
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},render: function() {
            return this.$el.html(this.template({options: this.options})), this
        },isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : -1 !== t.indexOf("top") ? {of: e.target,my: "center bottom",at: "center top",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover right";
            this.$(".js-confirm-text").html(e.data), this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        },triggerCallback: function() {
            this.callback.call(this), this.hide()
        }})
}), define("text!components/pop/templates/delete.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-delete">\n    <div class="popover-content text-center">\n        <div class="form-inline">\n            <span class="help-inline item-delete">确定删除?</span>\n            <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n            <button type="reset" class="btn js-btn-cancel">取消</button>\n        </div>\n    </div>\n</div>\n'
}), define("components/pop/delete", ["require", "backbone", "components/pop/base", "text!components/pop/templates/delete.html", "core/utils", "underscore"], function(e) {
    var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/delete.html"), n = e("core/utils"), s = e("underscore");
    return t.extend({template: s.template(i),className: function() {
            return this.options.className
        },initialize: function(e) {
            t.prototype.initialize.call(this, e)
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), n.focus(e.$(".js-btn-confirm")), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover left";
            this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        },triggerCallback: function(e) {
            if ("keydown" === e.type) {
                if (e.keyCode !== n.keyCode.ENTER)
                    return
            } else if ("click" !== e.type)
                return;
            this.callback.call(this), this.hide()
        }})
}), define("components/pop/delete_multiple", ["backbone", "components/pop/base", "text!components/pop/templates/delete.html", "core/utils"], function(e, t, i) {
    return t.extend({template: _.template(i),className: "popover right",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },positioning: function() {
            var e = this.target.offset(), t = {height: this.target.outerHeight(),width: this.target.outerWidth()}, i = {height: this.$el.outerHeight(),width: this.$el.outerWidth()};
            this.$el.css({display: "block",left: e.left + 80,top: e.top - i.height / 2 + t.height / 2})
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        },triggerCallback: function() {
            this.callback.call(this), this.hide()
        }})
}), define("text!components/pop/templates/rule.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-rule">\n	<div class="popover-content">\n        <div class="form-horizontal">\n            <div class="control-group">\n                <label class="control-label"><em class="required">*</em>规则名：</label>\n                <div class="controls">\n                    <input placeholder="关键词最多支持15个字" class="js-txt" type="text" maxlength="15" class="span3 txt" value="<%-name || \'\' %>">\n                </div>\n            </div>\n            <div class="control-group">\n                <label class="control-label">应用于：</label>\n                <div class="controls">\n                    <p class="static-value">粉丝发私信的时候</p>\n                </div>\n            </div>\n            <div class="form-actions">\n                <button class="js-btn-confirm btn btn-primary">确定</button>\n                <button class="js-btn-cancel btn">取消</button>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("components/pop/rule", ["backbone", "components/pop/base", "text!components/pop/templates/rule.html", "core/utils"], function(e, t, i, n) {
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keypress .js-txt": "enterSave"},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e.el.className = e.options.className, e.txt = e.$el.find(".js-txt"), e
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: e.target,my: "center top",at: "center bottom",collision: "none"}), e.txt.focus()
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className;
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },enterSave: function(e) {
            return e.which === n.keyCode.ENTER ? (this.triggerCallback(), !1) : void 0
        },triggerCallback: function() {
            var e = this, t = e.txt.val();
            return n.isEmpty(t) ? (e.txt.focus(), !1) : (e.setData({name: t}), e.callback.call(e, e.data), void e.hide())
        }})
}), define("text!components/pop/templates/keyword.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-keyword">\n	<div class="popover-content">\n        <div class="form-horizontal">\n            <div class="control-group">\n                <label class="control-label"><em class="required">*</em>关键词：</label>\n                <div class="controls">\n                    <input placeholder="关键词最多支持15个字" class="js-txt" type="text" maxlength="15" class="span3 txt" value="<%-text || \'\' %>">\n                    <span class="help-block err-message" style="font-size: 12px; color: red"></span>\n\n                    <% if(weixin){ %>\n                        <span class="js-emotion input-emotion-btn"></span>\n                        <div class="emotion-wrapper">\n                            <ul class="emotion-container clearfix"></ul>\n                        </div>\n                    <% } %>\n\n                </div>\n            </div>\n            <div class="control-group">\n                <label class="control-label"><em class="required">*</em>规则：</label>\n                <div class="controls">\n                    <% if (freeze) { %>\n                    <span class="control-action">模糊匹配</span>\n                    <% } %>\n                    <div class="keyword-type-group <% if (freeze) { %>hide<% } %>" >\n                        <% if(showP) { %>\n                        <label class="radio inline">\n                            <input name="keyword_type" type="radio" <% if (type === 1) { %>checked<% } %> value="1" /> 全匹配\n                        </label>\n                        <% } %>\n                        <label class="radio inline">\n                            <input name="keyword_type" type="radio" <% if (type === 2) { %>checked<% } %> value="2" /> 模糊\n                        </label>\n                        <% if(showQ) { %>\n                        <label class="radio inline">\n                            <input name="keyword_type" type="radio" <% if (type === 3) { %>checked<% } %> value="3" /> 前缀\n                        </label>\n                        <% } %>\n                    </div>\n                </div>\n            </div>\n            <div class="form-actions">\n                <button class="js-btn-confirm btn btn-primary">确定</button>\n                <button class="js-btn-cancel btn">取消</button>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("components/weixin_emotion/datas/default", ["require"], function() {
    var e = [{key: "[微笑]",val: "01"}, {key: "[撇嘴]",val: "02"}, {key: "[色]",val: "03"}, {key: "[发呆]",val: "04"}, {key: "[得意]",val: "05"}, {key: "[流泪]",val: "06"}, {key: "[害羞]",val: "07"}, {key: "[闭嘴]",val: "08"}, {key: "[睡]",val: "09"}, {key: "[大哭]",val: "10"}, {key: "[尴尬]",val: "11"}, {key: "[发怒]",val: "12"}, {key: "[调皮]",val: "13"}, {key: "[呲牙]",val: "14"}, {key: "[惊讶]",val: "15"}, {key: "[难过]",val: "16"}, {key: "[酷]",val: "17"}, {key: "[冷汗]",val: "18"}, {key: "[抓狂]",val: "19"}, {key: "[吐]",val: "20"}, {key: "[偷笑]",val: "21"}, {key: "[愉快]",val: "22"}, {key: "[白眼]",val: "23"}, {key: "[傲慢]",val: "24"}, {key: "[饥饿]",val: "25"}, {key: "[困]",val: "26"}, {key: "[惊恐]",val: "27"}, {key: "[流汗]",val: "28"}, {key: "[憨笑]",val: "29"}, {key: "[悠闲]",val: "30"}, {key: "[奋斗]",val: "31"}, {key: "[咒骂]",val: "32"}, {key: "[疑问]",val: "33"}, {key: "[嘘]",val: "34"}, {key: "[晕]",val: "35"}, {key: "[疯了]",val: "36"}, {key: "[衰]",val: "37"}, {key: "[骷髅]",val: "38"}, {key: "[敲打]",val: "39"}, {key: "[再见]",val: "40"}, {key: "[擦汗]",val: "41"}, {key: "[抠鼻]",val: "42"}, {key: "[鼓掌]",val: "43"}, {key: "[糗大了]",val: "44"}, {key: "[坏笑]",val: "45"}, {key: "[左哼哼]",val: "46"}, {key: "[右哼哼]",val: "47"}, {key: "[哈欠]",val: "48"}, {key: "[鄙视]",val: "49"}, {key: "[委屈]",val: "50"}, {key: "[快哭了]",val: "51"}, {key: "[阴险]",val: "52"}, {key: "[亲亲]",val: "53"}, {key: "[吓]",val: "54"}, {key: "[可怜]",val: "55"}, {key: "[菜刀]",val: "56"}, {key: "[西瓜]",val: "57"}, {key: "[啤酒]",val: "58"}, {key: "[篮球]",val: "59"}, {key: "[乒乓]",val: "60"}, {key: "[咖啡]",val: "61"}, {key: "[饭]",val: "62"}, {key: "[猪头]",val: "63"}, {key: "[玫瑰]",val: "64"}, {key: "[凋谢]",val: "65"}, {key: "[嘴唇]",val: "66"}, {key: "[爱心]",val: "67"}, {key: "[心碎]",val: "68"}, {key: "[蛋糕]",val: "69"}, {key: "[闪电]",val: "70"}, {key: "[炸弹]",val: "71"}, {key: "[刀]",val: "72"}, {key: "[足球]",val: "73"}, {key: "[瓢虫]",val: "74"}, {key: "[便便]",val: "75"}, {key: "[月亮]",val: "76"}, {key: "[太阳]",val: "77"}, {key: "[礼物]",val: "78"}, {key: "[拥抱]",val: "79"}, {key: "[强]",val: "80"}, {key: "[弱]",val: "81"}, {key: "[握手]",val: "82"}, {key: "[胜利]",val: "83"}, {key: "[抱拳]",val: "84"}, {key: "[勾引]",val: "85"}, {key: "[拳头]",val: "86"}, {key: "[差劲]",val: "87"}, {key: "[爱你]",val: "88"}, {key: "[NO]",val: "89"}, {key: "[OK]",val: "90"}, {key: "[爱情]",val: "91"}, {key: "[飞吻]",val: "92"}, {key: "[跳跳]",val: "93"}, {key: "[发抖]",val: "94"}, {key: "[怄火]",val: "95"}, {key: "[转圈]",val: "96"}, {key: "[磕头]",val: "97"}, {key: "[回头]",val: "98"}, {key: "[跳绳]",val: "99"}, {key: "[投降]",val: "100"}, {key: "[激动]",val: "101"}, {key: "[乱舞]",val: "102"}, {key: "[献吻]",val: "103"}, {key: "[左太极]",val: "104"}, {key: "[右太极]",val: "105"}], t = [{key: "/::)",val: "01"}, {key: "/::~",val: "02"}, {key: "/::B",val: "03"}, {key: "/::|",val: "04"}, {key: "/:8-)",val: "05"}, {key: "/::<",val: "06"}, {key: "/::$",val: "07"}, {key: "/::X",val: "08"}, {key: "/::Z",val: "09"}, {key: "/::'(",val: "10"}, {key: "/::-|",val: "11"}, {key: "/::@",val: "12"}, {key: "/::P",val: "13"}, {key: "/::D",val: "14"}, {key: "/::O",val: "15"}, {key: "/::(",val: "16"}, {key: "/::+",val: "17"}, {key: "/:--b",val: "18"}, {key: "/::Q",val: "19"}, {key: "/::T",val: "20"}, {key: "/:,@P",val: "21"}, {key: "/:,@-D",val: "22"}, {key: "/::d",val: "23"}, {key: "/:,@o",val: "24"}, {key: "/::g",val: "25"}, {key: "/:|-)",val: "26"}, {key: "/::!",val: "27"}, {key: "/::L",val: "28"}, {key: "/::>",val: "29"}, {key: "/::,@",val: "30"}, {key: "/:,@f",val: "31"}, {key: "/::-S",val: "32"}, {key: "/:?",val: "33"}, {key: "/:,@x",val: "34"}, {key: "/:,@@",val: "35"}, {key: "/::8",val: "36"}, {key: "/:,@!",val: "37"}, {key: "/:!!!",val: "38"}, {key: "/:xx",val: "39"}, {key: "/:bye",val: "40"}, {key: "/:wipe",val: "41"}, {key: "/:dig",val: "42"}, {key: "/:handclap",val: "43"}, {key: "/:&-(",val: "44"}, {key: "/:B-)",val: "45"}, {key: "/:<@",val: "46"}, {key: "/:@>",val: "47"}, {key: "/::-O",val: "48"}, {key: "/:>-|",val: "49"}, {key: "/:P-(",val: "50"}, {key: "/::'|",val: "51"}, {key: "/:X-)",val: "52"}, {key: "/::*",val: "53"}, {key: "/:@x",val: "54"}, {key: "/:8*",val: "55"}, {key: "/:pd",val: "56"}, {key: "/:<W>",val: "57"}, {key: "/:beer",val: "58"}, {key: "/:basketb",val: "59"}, {key: "/:oo",val: "60"}, {key: "/:coffee",val: "61"}, {key: "/:eat",val: "62"}, {key: "/:pig",val: "63"}, {key: "/:rose",val: "64"}, {key: "/:fade",val: "65"}, {key: "/:showlove",val: "66"}, {key: "/:heart",val: "67"}, {key: "/:break",val: "68"}, {key: "/:cake",val: "69"}, {key: "/:li",val: "70"}, {key: "/:bome",val: "71"}, {key: "/:kn",val: "72"}, {key: "/:footb",val: "73"}, {key: "/:ladybug",val: "74"}, {key: "/:shit",val: "75"}, {key: "/:moon",val: "76"}, {key: "/:sun",val: "77"}, {key: "/:gift",val: "78"}, {key: "/:hug",val: "79"}, {key: "/:strong",val: "80"}, {key: "/:weak",val: "81"}, {key: "/:share",val: "82"}, {key: "/:v",val: "83"}, {key: "/:@)",val: "84"}, {key: "/:jj",val: "85"}, {key: "/:@@",val: "86"}, {key: "/:bad",val: "87"}, {key: "/:lvu",val: "88"}, {key: "/:no",val: "89"}, {key: "/:ok",val: "90"}, {key: "/:love",val: "91"}, {key: "/:<L>",val: "92"}, {key: "/:jump",val: "93"}, {key: "/:shake",val: "94"}, {key: "/:<O>",val: "95"}, {key: "/:circle",val: "96"}, {key: "/:kotow",val: "97"}, {key: "/:turn",val: "98"}, {key: "/:skip",val: "99"}, {key: "/:oY",val: "100"}, {key: "/:#-0",val: "101"}, {key: "/:hiphot",val: "102"}, {key: "/:kiss",val: "103"}, {key: "/:<&",val: "104"}, {key: "/:&>",val: "105"}];
    return [e, t]
}), define("components/weixin_emotion/models/emotion", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = (e("core/utils"), t.Model.extend({defaults: {},initialize: function() {
        }}));
    return i
}), define("components/weixin_emotion/collections/emotion_list", ["require", "backbone", "components/weixin_emotion/models/emotion", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/weixin_emotion/models/emotion"), n = (e("core/utils"), t.Collection.extend({model: i,initialize: function(e) {
            var t = this;
            t.options = e, t.listenTo(t, "selected:emotion", t.selectEmotion)
        },selectEmotion: function() {
        }}));
    return n
}), define("text!components/weixin_emotion/templates/emotion.html", [], function() {
    return '<img src="<%=icon %>" alt="<%=value %>" title="<%=value %>">\n'
}), define("components/weixin_emotion/views/emotion", ["require", "backbone", "text!components/weixin_emotion/templates/emotion.html", "components/weixin_emotion/models/emotion", "core/utils"], function(e) {
    var t = e("backbone"), i = e("text!components/weixin_emotion/templates/emotion.html"), n = (e("components/weixin_emotion/models/emotion"), e("core/utils"), t.View.extend({tagName: "li",template: _.template(i),events: {click: "selectEmotion"},initialize: function() {
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.model.toJSON())), e
        },selectEmotion: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.model.collection.trigger("selected:emotion", t.model)
        }}));
    return n
}), define("components/weixin_emotion/views/emotion_list", ["require", "backbone", "components/weixin_emotion/views/emotion", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/weixin_emotion/views/emotion"), n = (e("core/utils"), t.View.extend({initialize: function() {
            var e = this;
            e.listenTo(e.collection, "add", e.addOne), e.listenTo(e.collection, "reset", e.addAll), e.listenTo(e.collection, "all", e.render)
        },render: function() {
            var e = this;
            return e
        },addOne: function(e) {
            var t = this, n = new i({model: e});
            t.$el.append(n.render().el)
        },addAll: function() {
            var e = this;
            e.$el.empty(), e.collection.each(this.addOne, this)
        }}));
    return n
}), define("components/weixin_emotion/com", ["require", "backbone", "components/weixin_emotion/datas/default", "components/weixin_emotion/collections/emotion_list", "components/weixin_emotion/views/emotion_list", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/weixin_emotion/datas/default"), n = e("components/weixin_emotion/collections/emotion_list"), s = e("components/weixin_emotion/views/emotion_list"), o = (e("core/utils"), t.View.extend({events: {click: "stopPropagation"},initialize: function(e) {
            var t = this;
            null == e.kind && (e.kind = 0), t.config = e, t.render()
        },render: function() {
            var e = this;
            e.emotionList = new n([]), e.container = e.$el.find(".emotion-container"), e.emotionListView = new s({el: e.container,collection: e.emotionList});
            var t = _global.url.v1_static + "/assets/ueditor/build/dialogs/emotion/images/qq/", o = i[e.config.kind], a = o.map(function(e) {
                return {value: e.key,icon: t + e.val + ".gif"}
            });
            return e.emotionList.reset(a), e.listenTo(e.emotionList, "selected:emotion", e.selectEmotion), e
        },selectEmotion: function(e) {
            var t = this;
            console.warn(e), t.trigger("selected:emotion", e.get("value")), t.hide()
        },show: function() {
            var e = this;
            e.$el.show().position({of: e.config.target,my: "center bottom",at: "center top"})
        },hide: function() {
            this.$el.hide()
        },toggle: function() {
            var e = this, t = e.$el.is(":hidden");
            t ? e.show() : e.hide()
        },stopPropagation: function(e) {
            e.stopPropagation()
        }}));
    return o
}), define("components/pop/keyword", ["require", "backbone", "components/pop/base", "text!components/pop/templates/keyword.html", "components/weixin_emotion/com", "core/utils"], function(e) {
    var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/keyword.html"), n = e("components/weixin_emotion/com"), s = e("core/utils");
    return t.extend({template: _.template(i),className: "popover bottom",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keypress .js-txt": "enterSave"},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data, void 0 == e.data.weixin && (this.data.weixin = !1), void 0 === e.data.showP && (this.data.showP = !0), void 0 == e.data.showQ && (this.data.showQ = !0);
            var i = this;
            this.$el.delegate(".js-emotion", "click", function() {
                i.wxEmotion.toggle()
            })
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e.txt = e.$el.find(".js-txt"), e.keywordType = e.$el.find(".keyword-type-group"), this.wxEmotion = new n({target: this.$(".js-emotion"),el: this.$(".emotion-wrapper"),kind: 1}), this.listenTo(this.wxEmotion, "selected:emotion", this.insertText), e
        },insertText: function(e) {
            var t = this.$(".js-txt");
            t.val(t.val() + e)
        },getMode: function() {
            var e = this;
            return Number(e.keywordType.find(":checked").val())
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: e.target,my: "center top",at: "center bottom",collision: "none"}), e.txt.focus()
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },enterSave: function(e) {
            return e.which === s.keyCode.ENTER ? (this.triggerCallback(), !1) : void 0
        },triggerCallback: function() {
            var e = this, t = $.trim(e.txt.val()), i = e.getMode();
            return s.isEmpty(t) ? (e.$(".err-message").text("关键词不能为空"), e.txt.focus(), !1) : (e.$(".err-message").text(""), 1 !== i && 2 !== i && 3 !== i ? !1 : (e.setData({text: t,type: i}), e.callback.call(e, e.data), void e.hide()))
        }})
}), define("text!components/pop/templates/reply.html", [], function() {
    return '<div class="arrow"></div>\n<a href="javascript:;" class="close--circle js-close">&times;</a>\n<div class="popover-inner popover-reply">\n    <div class="popover-content">\n        <div class="form-horizontal">\n            <div class="wb-sender">\n                <div class="wb-sender__inner">\n                    <div class="wb-sender__input">\n                    </div>\n                    <div class="wb-sender__actions in-editor">\n                        <button class="js-btn-confirm btn btn-primary">确定</button>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("components/wb_editor_v2/models/editor", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = (e("core/utils"), t.Model.extend({defaults: function() {
            return {charLimit: 300,picLimit: 9,topic: "在这里输入你想要说的话题",hasSign: !1,supportModules: {emotion: "Emotion",hyperlink: "Hyperlink",picture: "Picture",articles: "Articles",feature: "Feature",goods: "Goods",homepage: "Homepage",shortlink: "Shortlink",image: "Image",usercenter: "Usercenter"},mode: "top-action",modules: ["emotion", "hyperlink"],data: {type: "text",pics: [],content: ""}}
        }}));
    return i
}), define("text!components/modal/templates/modal.html", [], function() {
    return '<% if (type === \'goods\') { %>\n<div class="modal fade hide js-goods-modal">\n    <div class="modal-header">\n        <a class="close js-news-modal-dismiss" data-dismiss="modal">×</a>\n        <!-- 顶部tab -->\n        <ul class="module-nav modal-tab">\n\n        </ul>\n    </div>\n    <div class="modal-body">\n        <div class="tab-content">\n\n        </div>\n    </div>\n    <div class="modal-footer clearfix">\n        <div style="display:none;" class="js-confirm-choose pull-left">\n            <input type="button" class="btn btn-primary" value="确定使用">\n        </div>\n        <div class="pagenavi">\n\n        </div>\n    </div>\n</div>\n<% } else { %>\n<div class="modal fade hide js-modal">\n    <div class="modal-header">\n        <a class="close js-news-modal-dismiss" data-dismiss="modal">×</a>\n        <!-- 顶部tab -->\n        <ul class="module-nav modal-tab">\n\n        </ul>\n    </div>\n    <div class="modal-body">\n        <div class="tab-content">\n\n        </div>\n    </div>\n    <div class="modal-footer">\n        <div style="display:none;" class="js-confirm-choose pull-left">\n            <input type="button" class="btn btn-primary" value="确定使用">\n        </div>\n        <div class="pagenavi">\n\n        </div>\n    </div>\n</div>\n<% } %>\n'
}), define("text!components/modal/templates/modal_link.html", [], function() {
    return '    <a href="<%= link %>" target="_blank" class="new_window"><%= text %></a>\n    <% if (!isLast) { %>-<% } %>'
}), define("text!components/modal/templates/modal_pane.html", [], function() {
    return '<% if (type !== \'image\') { %>\n<table class="table">\n    <% if(type === \'activity\' || type === "survey") { %>\n    <colgroup>\n        <col class="modal-col-title">\n        <col class="modal-col-time">\n        <col class="modal-col-action">\n    </colgroup>\n    <% } else { %>\n    <colgroup>\n        <col class="modal-col-title">\n        <col class="modal-col-time" span="2">\n        <col class="modal-col-action">\n    </colgroup>\n    <% } %>\n    <!-- 表格头部 -->\n    <thead>\n\n    </thead>\n    <!-- 表格数据区 -->\n    <tbody>\n\n    </tbody>\n</table>\n<% } else { %>\n<div class="module-header"></div>\n<ul class="module-body clearfix"></ul>\n<% } %>\n'
}), define("text!components/modal/templates/modal_row.html", [], function() {
    return '<% if (type === \'image\') { %>\n    <div class="js-choose" title="<%= attachment_title %>">\n        <p class="image-size"><%= attachment_title.slice(0, 5) %><br><%= (size / 1000).toFixed(1) %> KB</p>\n        <img src="<%= window._global.url.imgqn %>/<%= thumb_url %>" width="60" height="60" />\n    </div>\n    <label class="multi-select-container hide">\n        <input type="checkbox" class="multi-select js-multi-select">\n    </label>\n<% } else if (type === \'guang_activity\' || type === \'activity\' || type === \'survey\' || type === \'grab\' || type === \'guaguale\') { %>\n    <td class="title">\n        <div class="td-cont">\n            <a target="_blank" class="new_window" href="<%= link %>"><%= title %></a>\n        </div>\n    </td>\n    <td class="time">\n        <div class="td-cont">\n            <span><%= start_time %></span>\n        </div>\n    </td>\n    <td class="time">\n        <div class="td-cont">\n            <span><%= end_time %></span>\n        </div>\n    </td>\n    <td class="opts">\n        <div class="td-cont">\n            <button class="btn js-choose" href="#" data-id="<%= data_id %>" data-url="<%= data_url %>" data-page-type="<%= type %>" data-cover-attachment-id="<%= data_cover_attachment_id %>" data-cover-attachment-url="<%= data_cover_attachment_url %>" data-title="<%= data_title %>" data-alias="<%= data_alias %>">选取</button>\n        </div>\n    </td>\n<% } else if (type === \'news\' || type === \'multiNews\' || type === \'articles\') { %>\n    <td class="title">\n        <div class="td-cont">\n        <% if (news.length > 1) { %>\n            <div class="ng ng-multiple">\n            <% for (var index in news) { %>\n                <% if (news[index] && news.hasOwnProperty(index)) { %>\n                    <% if (news[index].title.indexOf(\'　　点此查看更多\') !== -1) { %>\n                        <div class="ng-item">\n                            <div class="ng-title">\n                                <a href="javascript:;" target="_blank" class="new_window" title=""><%=news[index] %></a>\n                            </div>\n                        </div>\n                    <% } else { %>\n                    <div class="ng-item">\n                        <span class="label label-success">图文<%=Number(index) + 1%></span>\n                        <div class="ng-title">\n                            <a href="<%= news[index] && news[index].url %>" class="new_window" title="<%=news[index].title %>">\n                                <%=news[index].title %>\n                            </a>\n                        </div>\n                    </div>\n                    <% } %>\n                    <% } %>\n            <% } %>\n            </div>\n        <% } else { %>\n            <div class="ng ng-single">\n                <div class="ng-item">\n                    <div class="ng-title">\n                        <a href="<%= news[0] && news[0].url %>" target="_blank" class="new-window" title="<%= news[0] &&news[0].title %>"><%= news[0] && news[0].title %></a>\n                    </div>\n                </div>\n                <div class="ng-item view-more">\n                    <a href="<%= news[0] && news[0].url %>" class="clearfix new-window">\n                        <span class="pull-left">阅读全文</span>\n                        <span class="pull-right">&gt;</span>\n                    </a>\n                </div>\n            </div>\n        <% } %>\n        </div>\n    </td>\n\n    <td class="time">\n        <div class="td-cont">\n            <span><%= time %></span>\n        </div>\n    </td>\n    <td class="opts">\n        <div class="td-cont">\n            <button class="btn js-choose" href="#" data-id="<%= data_id %>" data-url="<%= data_url %>" data-page-type="<%= type %>" data-cover-attachment-id="<%= data_cover_attachment_id %>" data-cover-attachment-url="<%= data_cover_attachment_url %>" data-title="<%= data_title %>" data-alias="<%= data_alias %>">选取</button>\n        </div>\n    </td>\n<% } else if (type === \'goods\') { %>\n    <td class="title">\n        <div class="td-cont">\n            <a target="_blank" class="new_window" href="<%= link %>" data-cover-attachment-url="<%= data_cover_attachment_url %>"><%= title %></a>\n        </div>\n    </td>\n\n    <td class="time">\n        <div class="td-cont">\n            <span><%= time %></span>\n        </div>\n    </td>\n    <td class="opts">\n        <div class="td-cont">\n            <button class="btn js-choose" href="javascript:void(0);">选取</button>\n        </div>\n    </td>\n<% } else {%>\n    <td class="title">\n        <div class="td-cont">\n            <a target="_blank" class="new_window" href="<%= link %>"><%= title %></a>\n        </div>\n    </td>\n\n    <td class="time">\n        <div class="td-cont">\n            <span><%= time %></span>\n        </div>\n    </td>\n    <td class="opts">\n        <div class="td-cont">\n            <button class="btn js-choose" href="#" data-id="<%= data_id %>" data-url="<%= data_url %>" data-page-type="<%= type %>" data-cover-attachment-id="<%= data_cover_attachment_id %>" data-cover-attachment-url="<%= data_cover_attachment_url %>" data-title="<%= data_title %>" data-alias="<%= data_alias %>">选取</button>\n        </div>\n    </td>\n<% } %>\n'
}), define("text!components/modal/templates/modal_tab.html", [], function() {
    return '<a href="#js-module-<%= type %>" data-type="<%= type %>" class="js-modal-tab"><%= tab %></a><% if (!isLast) { %> | <% } %>\n'
}), define("text!components/modal/templates/modal_thead.html", [], function() {
    return '    <% if (type === \'image\') { %>\n    <p class="help-inline">点击图片即可选中 <a class="js-update" href="javascript:void(0);">刷新</a></p>\n    <form class="form-search search-box">\n        <div class="input-append">\n            <input class="input-small js-modal-search-input" type="text"/>\n			<a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" data-action-type="search">搜</a>\n        </div>\n    </form>\n    <% } else if (type === \'guang_activity\' || type === \'activity\' || type === \'survey\' || type === \'grab\' || type === \'guaguale\') { %>\n    <tr>\n        <th class="title">\n            <div class="td-cont">\n                <span><%= title %></span> <a class="js-update" href="javascript:void(0);">刷新</a>\n            </div>\n        </th>\n        <th class="time">\n            <span></span>\n        </th>\n        <th class="time">\n            <div class="td-cont">\n                <span>有效时间</span>\n            </div>\n        </th>\n        <th class="opts">\n            <div class="td-cont">\n                <form class="form-search">\n                    <div class="input-append">\n                        <input class="input-small js-modal-search-input" type="text"/><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" data-action-type="search">搜</a>\n                    </div>\n                </form>\n            </div>\n        </th>\n    </tr>\n    <% } else {%>\n    <tr>\n        <th class="title">\n            <div class="td-cont">\n                <span><%= title %></span> <a class="js-update" href="javascript:void(0);">刷新</a>\n            </div>\n        </th>\n        <th class="time">\n            <div class="td-cont">\n                <span><%= time %></span>\n            </div>\n        </th>\n        <th class="opts">\n            <div class="td-cont">\n                <form class="form-search">\n                    <div class="input-append">\n                        <input class="input-small js-modal-search-input" type="text"/><a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" data-action-type="search">搜</a>\n                    </div>\n                </form>\n            </div>\n        </th>\n    </tr>\n    <% } %>\n'
}), define("text!components/modal/templates/modal_dropdown.html", [], function() {
    return '    <a href="javascript:void(0);" data-toggle="dropdown" data-hover="dropdown" data-delay="200">+新建营销活动<b class="caret"></b></a>\n    <ul class="dropdown-menu">\n        <li><a href="/v2/apps/cards#create" target="_blank">刮刮乐</a></li>\n        <li><a href="/v2/apps/wheel#create" target="_blank">幸运大抽奖</a></li>\n        <li><a href="/v2/apps/zodiac#create" target="_blank">生肖翻翻看</a></li>\n        <li><a href="/activity/add?activity_type=6" target="_blank">随便逛一逛</a></li>\n    </ul>\n'
}), define("text!components/modal/templates/modal_static.html", [], function() {
    return '<div class="get-web-img">\n    <form class="form-horizontal" action="<%= window._global.url.img %>/download" method="post">\n        <div class="control-group">\n            <label class="control-label">网络图片：</label>\n            <div class="controls">\n                <input type="text" name="attachment_url" class="get-web-img-input js-web-img-input" placeholder="请贴入网络图片地址">\n                <a href="javascript:;" class="btn js-preview-img">提取</a>\n            </div>\n            <div class="controls preview-container js-download-img">\n            </div>\n        </div>\n    </form>\n</div>\n<div class="upload-local-img">\n    <div class="form-horizontal">\n        <div class="control-group">\n            <label class="control-label">本地图片：</label>\n            <div class="controls preview-container js-upload-img">\n            </div>\n            <div class="controls">\n                <div class="fileinput-button">\n                    <a href="javascript:;" data-toggle-text="重新选择..." class="control-action">添加图片...</a>\n                    <input class="js-fileupload fileupload" type="file" name="upload_file[]" data-url="<%= window._global.url.img + \'/uploadmultiple?format=json\' %>" multiple>\n                </div>\n                <p class="help-desc">最大支持 1 MB 的图片( jpg / gif / png )</p>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("text!components/modal/templates/modal_static_footer.html", [], function() {
    return '<div class="form-action">\n    <button type="button" class="btn btn-primary js-confirm-upload-image" data-loading-text="正在上传...">确定使用</button>\n    <a href="javascript:void(0);" data-dismiss="modal" class="btn btn-cancel">取消</a>\n</div>\n'
}), function(e) {
    "function" == typeof define && define.amd ? define("fileupload", ["jquery", "jqueryui"], e) : e(window.jQuery)
}(function(e) {
    e.support.fileInput = !(new RegExp("(Android (1\\.[0156]|2\\.[01]))|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)|(w(eb)?OSBrowser)|(webOS)|(Kindle/(1\\.0|2\\.[05]|3\\.0))").test(window.navigator.userAgent) || e('<input type="file">').prop("disabled")), e.support.xhrFileUpload = !(!window.XMLHttpRequestUpload || !window.FileReader), e.support.xhrFormDataFileUpload = !!window.FormData, e.support.blobSlice = window.Blob && (Blob.prototype.slice || Blob.prototype.webkitSlice || Blob.prototype.mozSlice), e.widget("blueimp.fileupload", {options: {dropZone: e(document),pasteZone: e(document),fileInput: void 0,replaceFileInput: !0,paramName: void 0,singleFileUploads: !0,limitMultiFileUploads: void 0,sequentialUploads: !1,limitConcurrentUploads: void 0,forceIframeTransport: !1,redirect: void 0,redirectParamName: void 0,postMessage: void 0,multipart: !0,maxChunkSize: void 0,uploadedBytes: void 0,recalculateProgress: !0,progressInterval: 100,bitrateInterval: 500,autoUpload: !0,messages: {uploadedBytes: "Uploaded bytes exceed file size"},i18n: function(t, i) {
                return t = this.messages[t] || t.toString(), i && e.each(i, function(e, i) {
                    t = t.replace("{" + e + "}", i)
                }), t
            },formData: function(e) {
                return e.serializeArray()
            },add: function(t, i) {
                (i.autoUpload || i.autoUpload !== !1 && e(this).fileupload("option", "autoUpload")) && i.process().done(function() {
                    i.submit()
                })
            },processData: !1,contentType: !1,cache: !1},_specialOptions: ["fileInput", "dropZone", "pasteZone", "multipart", "forceIframeTransport"],_blobSlice: e.support.blobSlice && function() {
            var e = this.slice || this.webkitSlice || this.mozSlice;
            return e.apply(this, arguments)
        },_BitrateTimer: function() {
            this.timestamp = Date.now ? Date.now() : (new Date).getTime(), this.loaded = 0, this.bitrate = 0, this.getBitrate = function(e, t, i) {
                var n = e - this.timestamp;
                return (!this.bitrate || !i || n > i) && (this.bitrate = (t - this.loaded) * (1e3 / n) * 8, this.loaded = t, this.timestamp = e), this.bitrate
            }
        },_isXHRUpload: function(t) {
            return !t.forceIframeTransport && (!t.multipart && e.support.xhrFileUpload || e.support.xhrFormDataFileUpload)
        },_getFormData: function(t) {
            var i;
            return "function" == typeof t.formData ? t.formData(t.form) : e.isArray(t.formData) ? t.formData : "object" === e.type(t.formData) ? (i = [], e.each(t.formData, function(e, t) {
                i.push({name: e,value: t})
            }), i) : []
        },_getTotal: function(t) {
            var i = 0;
            return e.each(t, function(e, t) {
                i += t.size || 1
            }), i
        },_initProgressObject: function(t) {
            var i = {loaded: 0,total: 0,bitrate: 0};
            t._progress ? e.extend(t._progress, i) : t._progress = i
        },_initResponseObject: function(e) {
            var t;
            if (e._response)
                for (t in e._response)
                    e._response.hasOwnProperty(t) && delete e._response[t];
            else
                e._response = {}
        },_onProgress: function(e, t) {
            if (e.lengthComputable) {
                var i, n = Date.now ? Date.now() : (new Date).getTime();
                if (t._time && t.progressInterval && n - t._time < t.progressInterval && e.loaded !== e.total)
                    return;
                t._time = n, i = Math.floor(e.loaded / e.total * (t.chunkSize || t._progress.total)) + (t.uploadedBytes || 0), this._progress.loaded += i - t._progress.loaded, this._progress.bitrate = this._bitrateTimer.getBitrate(n, this._progress.loaded, t.bitrateInterval), t._progress.loaded = t.loaded = i, t._progress.bitrate = t.bitrate = t._bitrateTimer.getBitrate(n, i, t.bitrateInterval), this._trigger("progress", e, t), this._trigger("progressall", e, this._progress)
            }
        },_initProgressListener: function(t) {
            var i = this, n = t.xhr ? t.xhr() : e.ajaxSettings.xhr();
            n.upload && (e(n.upload).bind("progress", function(e) {
                var n = e.originalEvent;
                e.lengthComputable = n.lengthComputable, e.loaded = n.loaded, e.total = n.total, i._onProgress(e, t)
            }), t.xhr = function() {
                return n
            })
        },_isInstanceOf: function(e, t) {
            return Object.prototype.toString.call(t) === "[object " + e + "]"
        },_initXHRData: function(t) {
            var i, n = this, s = t.files[0], o = t.multipart || !e.support.xhrFileUpload, a = t.paramName[0];
            t.headers = t.headers || {}, t.contentRange && (t.headers["Content-Range"] = t.contentRange), o && !t.blob && this._isInstanceOf("File", s) || (t.headers["Content-Disposition"] = 'attachment; filename="' + encodeURI(s.name) + '"'), o ? e.support.xhrFormDataFileUpload && (t.postMessage ? (i = this._getFormData(t), t.blob ? i.push({name: a,value: t.blob}) : e.each(t.files, function(e, n) {
                i.push({name: t.paramName[e] || a,value: n})
            })) : (n._isInstanceOf("FormData", t.formData) ? i = t.formData : (i = new FormData, e.each(this._getFormData(t), function(e, t) {
                i.append(t.name, t.value)
            })), t.blob ? i.append(a, t.blob, s.name) : e.each(t.files, function(e, s) {
                (n._isInstanceOf("File", s) || n._isInstanceOf("Blob", s)) && i.append(t.paramName[e] || a, s, s.name)
            })), t.data = i) : (t.contentType = s.type, t.data = t.blob || s), t.blob = null
        },_initIframeSettings: function(t) {
            var i = e("<a></a>").prop("href", t.url).prop("host");
            t.dataType = "iframe " + (t.dataType || ""), t.formData = this._getFormData(t), t.redirect && i && i !== location.host && t.formData.push({name: t.redirectParamName || "redirect",value: t.redirect})
        },_initDataSettings: function(e) {
            this._isXHRUpload(e) ? (this._chunkedUpload(e, !0) || (e.data || this._initXHRData(e), this._initProgressListener(e)), e.postMessage && (e.dataType = "postmessage " + (e.dataType || ""))) : this._initIframeSettings(e)
        },_getParamName: function(t) {
            var i = e(t.fileInput), n = t.paramName;
            return n ? e.isArray(n) || (n = [n]) : (n = [], i.each(function() {
                for (var t = e(this), i = t.prop("name") || "files[]", s = (t.prop("files") || [1]).length; s; )
                    n.push(i), s -= 1
            }), n.length || (n = [i.prop("name") || "files[]"])), n
        },_initFormSettings: function(t) {
            t.form && t.form.length || (t.form = e(t.fileInput.prop("form")), t.form.length || (t.form = e(this.options.fileInput.prop("form")))), t.paramName = this._getParamName(t), t.url || (t.url = t.form.prop("action") || location.href), t.type = (t.type || t.form.prop("method") || "").toUpperCase(), "POST" !== t.type && "PUT" !== t.type && "PATCH" !== t.type && (t.type = "POST"), t.formAcceptCharset || (t.formAcceptCharset = t.form.attr("accept-charset"))
        },_getAJAXSettings: function(t) {
            var i = e.extend({}, this.options, t);
            return this._initFormSettings(i), this._initDataSettings(i), i
        },_getDeferredState: function(e) {
            return e.state ? e.state() : e.isResolved() ? "resolved" : e.isRejected() ? "rejected" : "pending"
        },_enhancePromise: function(e) {
            return e.success = e.done, e.error = e.fail, e.complete = e.always, e
        },_getXHRPromise: function(t, i, n) {
            var s = e.Deferred(), o = s.promise();
            return i = i || this.options.context || o, t === !0 ? s.resolveWith(i, n) : t === !1 && s.rejectWith(i, n), o.abort = s.promise, this._enhancePromise(o)
        },_addConvenienceMethods: function(t, i) {
            var n = this, s = function(t) {
                return e.Deferred().resolveWith(n, [t]).promise()
            };
            i.process = function(e, t) {
                return (e || t) && (i._processQueue = this._processQueue = (this._processQueue || s(this)).pipe(e, t)), this._processQueue || s(this)
            }, i.submit = function() {
                return "pending" !== this.state() && (i.jqXHR = this.jqXHR = n._trigger("submit", t, this) !== !1 && n._onSend(t, this)), this.jqXHR || n._getXHRPromise()
            }, i.abort = function() {
                return this.jqXHR ? this.jqXHR.abort() : n._getXHRPromise()
            }, i.state = function() {
                return this.jqXHR ? n._getDeferredState(this.jqXHR) : this._processQueue ? n._getDeferredState(this._processQueue) : void 0
            }, i.progress = function() {
                return this._progress
            }, i.response = function() {
                return this._response
            }
        },_getUploadedBytes: function(e) {
            var t = e.getResponseHeader("Range"), i = t && t.split("-"), n = i && i.length > 1 && parseInt(i[1], 10);
            return n && n + 1
        },_chunkedUpload: function(t, i) {
            t.uploadedBytes = t.uploadedBytes || 0;
            var n, s, o = this, a = t.files[0], r = a.size, l = t.uploadedBytes, c = t.maxChunkSize || r, d = this._blobSlice, u = e.Deferred(), p = u.promise();
            return this._isXHRUpload(t) && d && (l || r > c) && !t.data ? i ? !0 : l >= r ? (a.error = t.i18n("uploadedBytes"), this._getXHRPromise(!1, t.context, [null, "error", a.error])) : (s = function() {
                var i = e.extend({}, t), p = i._progress.loaded;
                i.blob = d.call(a, l, l + c, a.type), i.chunkSize = i.blob.size, i.contentRange = "bytes " + l + "-" + (l + i.chunkSize - 1) + "/" + r, o._initXHRData(i), o._initProgressListener(i), n = (o._trigger("chunksend", null, i) !== !1 && e.ajax(i) || o._getXHRPromise(!1, i.context)).done(function(n, a, c) {
                    l = o._getUploadedBytes(c) || l + i.chunkSize, p + i.chunkSize - i._progress.loaded && o._onProgress(e.Event("progress", {lengthComputable: !0,loaded: l - i.uploadedBytes,total: l - i.uploadedBytes}), i), t.uploadedBytes = i.uploadedBytes = l, i.result = n, i.textStatus = a, i.jqXHR = c, o._trigger("chunkdone", null, i), o._trigger("chunkalways", null, i), r > l ? s() : u.resolveWith(i.context, [n, a, c])
                }).fail(function(e, t, n) {
                    i.jqXHR = e, i.textStatus = t, i.errorThrown = n, o._trigger("chunkfail", null, i), o._trigger("chunkalways", null, i), u.rejectWith(i.context, [e, t, n])
                })
            }, this._enhancePromise(p), p.abort = function() {
                return n.abort()
            }, s(), p) : !1
        },_beforeSend: function(e, t) {
            0 === this._active && (this._trigger("start"), this._bitrateTimer = new this._BitrateTimer, this._progress.loaded = this._progress.total = 0, this._progress.bitrate = 0), this._initResponseObject(t), this._initProgressObject(t), t._progress.loaded = t.loaded = t.uploadedBytes || 0, t._progress.total = t.total = this._getTotal(t.files) || 1, t._progress.bitrate = t.bitrate = 0, this._active += 1, this._progress.loaded += t.loaded, this._progress.total += t.total
        },_onDone: function(t, i, n, s) {
            var o = s._progress.total, a = s._response;
            s._progress.loaded < o && this._onProgress(e.Event("progress", {lengthComputable: !0,loaded: o,total: o}), s), a.result = s.result = t, a.textStatus = s.textStatus = i, a.jqXHR = s.jqXHR = n, this._trigger("done", null, s)
        },_onFail: function(e, t, i, n) {
            var s = n._response;
            n.recalculateProgress && (this._progress.loaded -= n._progress.loaded, this._progress.total -= n._progress.total), s.jqXHR = n.jqXHR = e, s.textStatus = n.textStatus = t, s.errorThrown = n.errorThrown = i, this._trigger("fail", null, n)
        },_onAlways: function(e, t, i, n) {
            this._trigger("always", null, n)
        },_onSend: function(t, i) {
            i.submit || this._addConvenienceMethods(t, i);
            var n, s, o, a, r = this, l = r._getAJAXSettings(i), c = function() {
                return r._sending += 1, l._bitrateTimer = new r._BitrateTimer, n = n || ((s || r._trigger("send", t, l) === !1) && r._getXHRPromise(!1, l.context, s) || r._chunkedUpload(l) || e.ajax(l)).done(function(e, t, i) {
                    r._onDone(e, t, i, l)
                }).fail(function(e, t, i) {
                    r._onFail(e, t, i, l)
                }).always(function(e, t, i) {
                    if (r._onAlways(e, t, i, l), r._sending -= 1, r._active -= 1, l.limitConcurrentUploads && l.limitConcurrentUploads > r._sending)
                        for (var n = r._slots.shift(); n; ) {
                            if ("pending" === r._getDeferredState(n)) {
                                n.resolve();
                                break
                            }
                            n = r._slots.shift()
                        }
                    0 === r._active && r._trigger("stop")
                })
            };
            return this._beforeSend(t, l), this.options.sequentialUploads || this.options.limitConcurrentUploads && this.options.limitConcurrentUploads <= this._sending ? (this.options.limitConcurrentUploads > 1 ? (o = e.Deferred(), this._slots.push(o), a = o.pipe(c)) : (this._sequence = this._sequence.pipe(c, c), a = this._sequence), a.abort = function() {
                return s = [void 0, "abort", "abort"], n ? n.abort() : (o && o.rejectWith(l.context, s), c())
            }, this._enhancePromise(a)) : c()
        },_onAdd: function(t, i) {
            var n, s, o, a, r = this, l = !0, c = e.extend({}, this.options, i), d = c.limitMultiFileUploads, u = this._getParamName(c);
            if ((c.singleFileUploads || d) && this._isXHRUpload(c))
                if (!c.singleFileUploads && d)
                    for (o = [], n = [], a = 0; a < i.files.length; a += d)
                        o.push(i.files.slice(a, a + d)), s = u.slice(a, a + d), s.length || (s = u), n.push(s);
                else
                    n = u;
            else
                o = [i.files], n = [u];
            return i.originalFiles = i.files, e.each(o || i.files, function(s, a) {
                var c = e.extend({}, i);
                return c.files = o ? a : [a], c.paramName = n[s], r._initResponseObject(c), r._initProgressObject(c), r._addConvenienceMethods(t, c), l = r._trigger("add", t, c)
            }), l
        },_replaceFileInput: function(t) {
            var i = t.clone(!0);
            e("<form></form>").append(i)[0].reset(), t.after(i).detach(), e.cleanData(t.unbind("remove")), this.options.fileInput = this.options.fileInput.map(function(e, n) {
                return n === t[0] ? i[0] : n
            }), t[0] === this.element[0] && (this.element = i)
        },_handleFileTreeEntry: function(t, i) {
            var n, s = this, o = e.Deferred(), a = function(e) {
                e && !e.entry && (e.entry = t), o.resolve([e])
            };
            return i = i || "", t.isFile ? t._file ? (t._file.relativePath = i, o.resolve(t._file)) : t.file(function(e) {
                e.relativePath = i, o.resolve(e)
            }, a) : t.isDirectory ? (n = t.createReader(), n.readEntries(function(e) {
                s._handleFileTreeEntries(e, i + t.name + "/").done(function(e) {
                    o.resolve(e)
                }).fail(a)
            }, a)) : o.resolve([]), o.promise()
        },_handleFileTreeEntries: function(t, i) {
            var n = this;
            return e.when.apply(e, e.map(t, function(e) {
                return n._handleFileTreeEntry(e, i)
            })).pipe(function() {
                return Array.prototype.concat.apply([], arguments)
            })
        },_getDroppedFiles: function(t) {
            t = t || {};
            var i = t.items;
            return i && i.length && (i[0].webkitGetAsEntry || i[0].getAsEntry) ? this._handleFileTreeEntries(e.map(i, function(e) {
                var t;
                return e.webkitGetAsEntry ? (t = e.webkitGetAsEntry(), t && (t._file = e.getAsFile()), t) : e.getAsEntry()
            })) : e.Deferred().resolve(e.makeArray(t.files)).promise()
        },_getSingleFileInputFiles: function(t) {
            t = e(t);
            var i, n, s = t.prop("webkitEntries") || t.prop("entries");
            if (s && s.length)
                return this._handleFileTreeEntries(s);
            if (i = e.makeArray(t.prop("files")), i.length)
                void 0 === i[0].name && i[0].fileName && e.each(i, function(e, t) {
                    t.name = t.fileName, t.size = t.fileSize
                });
            else {
                if (n = t.prop("value"), !n)
                    return e.Deferred().resolve([]).promise();
                i = [{name: n.replace(/^.*\\/, "")}]
            }
            return e.Deferred().resolve(i).promise()
        },_getFileInputFiles: function(t) {
            return t instanceof e && 1 !== t.length ? e.when.apply(e, e.map(t, this._getSingleFileInputFiles)).pipe(function() {
                return Array.prototype.concat.apply([], arguments)
            }) : this._getSingleFileInputFiles(t)
        },_onChange: function(t) {
            var i = this, n = {fileInput: e(t.target),form: e(t.target.form)};
            this._getFileInputFiles(n.fileInput).always(function(e) {
                n.files = e, i.options.replaceFileInput && i._replaceFileInput(n.fileInput), i._trigger("change", t, n) !== !1 && i._onAdd(t, n)
            })
        },_onPaste: function(t) {
            var i = t.originalEvent && t.originalEvent.clipboardData && t.originalEvent.clipboardData.items, n = {files: []};
            return i && i.length && (e.each(i, function(e, t) {
                var i = t.getAsFile && t.getAsFile();
                i && n.files.push(i)
            }), this._trigger("paste", t, n) === !1 || this._onAdd(t, n) === !1) ? !1 : void 0
        },_onDrop: function(e) {
            e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
            var t = this, i = e.dataTransfer, n = {};
            i && i.files && i.files.length && (e.preventDefault(), this._getDroppedFiles(i).always(function(i) {
                n.files = i, t._trigger("drop", e, n) !== !1 && t._onAdd(e, n)
            }))
        },_onDragOver: function(t) {
            t.dataTransfer = t.originalEvent && t.originalEvent.dataTransfer;
            var i = t.dataTransfer;
            if (i) {
                if (this._trigger("dragover", t) === !1)
                    return !1;
                -1 !== e.inArray("Files", i.types) && (i.dropEffect = "copy", t.preventDefault())
            }
        },_initEventHandlers: function() {
            this._isXHRUpload(this.options) && (this._on(this.options.dropZone, {dragover: this._onDragOver,drop: this._onDrop}), this._on(this.options.pasteZone, {paste: this._onPaste})), e.support.fileInput && this._on(this.options.fileInput, {change: this._onChange})
        },_destroyEventHandlers: function() {
            this._off(this.options.dropZone, "dragover drop"), this._off(this.options.pasteZone, "paste"), this._off(this.options.fileInput, "change")
        },_setOption: function(t, i) {
            var n = -1 !== e.inArray(t, this._specialOptions);
            n && this._destroyEventHandlers(), this._super(t, i), n && (this._initSpecialOptions(), this._initEventHandlers())
        },_initSpecialOptions: function() {
            var t = this.options;
            void 0 === t.fileInput ? t.fileInput = this.element.is('input[type="file"]') ? this.element : this.element.find('input[type="file"]') : t.fileInput instanceof e || (t.fileInput = e(t.fileInput)), t.dropZone instanceof e || (t.dropZone = e(t.dropZone)), t.pasteZone instanceof e || (t.pasteZone = e(t.pasteZone))
        },_getRegExp: function(e) {
            var t = e.split("/"), i = t.pop();
            return t.shift(), new RegExp(t.join("/"), i)
        },_isRegExpOption: function(t, i) {
            return "url" !== t && "string" === e.type(i) && /^\/.*\/[igm]{0,3}$/.test(i)
        },_initDataAttributes: function() {
            var t = this, i = this.options;
            e.each(e(this.element[0].cloneNode(!1)).data(), function(e, n) {
                t._isRegExpOption(e, n) && (n = t._getRegExp(n)), i[e] = n
            })
        },_create: function() {
            this._initDataAttributes(), this._initSpecialOptions(), this._slots = [], this._sequence = this._getXHRPromise(!0), this._sending = this._active = 0, this._initProgressObject(this), this._initEventHandlers()
        },active: function() {
            return this._active
        },progress: function() {
            return this._progress
        },add: function(t) {
            var i = this;
            t && !this.options.disabled && (t.fileInput && !t.files ? this._getFileInputFiles(t.fileInput).always(function(e) {
                t.files = e, i._onAdd(null, t)
            }) : (t.files = e.makeArray(t.files), this._onAdd(null, t)))
        },send: function(t) {
            if (t && !this.options.disabled) {
                if (t.fileInput && !t.files) {
                    var i, n, s = this, o = e.Deferred(), a = o.promise();
                    return a.abort = function() {
                        return n = !0, i ? i.abort() : (o.reject(null, "abort", "abort"), a)
                    }, this._getFileInputFiles(t.fileInput).always(function(e) {
                        if (!n) {
                            if (!e.length)
                                return void o.reject();
                            t.files = e, i = s._onSend(null, t).then(function(e, t, i) {
                                o.resolve(e, t, i)
                            }, function(e, t, i) {
                                o.reject(e, t, i)
                            })
                        }
                    }), this._enhancePromise(a)
                }
                if (t.files = e.makeArray(t.files), t.files.length)
                    return this._onSend(null, t)
            }
            return this._getXHRPromise(!1, t && t.context)
        }})
}), function(e) {
    "function" == typeof define && define.amd ? define("fileupload_process", ["jquery", "fileupload"], e) : e(window.jQuery)
}(function(e) {
    var t = e.blueimp.fileupload.prototype.options.add;
    e.widget("blueimp.fileupload", e.blueimp.fileupload, {options: {processQueue: [],add: function(i, n) {
                var s = e(this);
                n.process(function() {
                    return s.fileupload("process", n)
                }), t.call(this, i, n)
            }},processActions: {},_processFile: function(t) {
            var i = this, n = e.Deferred().resolveWith(i, [t]), s = n.promise();
            return this._trigger("process", null, t), e.each(t.processQueue, function(e, t) {
                var n = function(e) {
                    return i.processActions[t.action].call(i, e, t)
                };
                s = s.pipe(n, t.always && n)
            }), s.done(function() {
                i._trigger("processdone", null, t), i._trigger("processalways", null, t)
            }).fail(function() {
                i._trigger("processfail", null, t), i._trigger("processalways", null, t)
            }), s
        },_transformProcessQueue: function(t) {
            var i = [];
            e.each(t.processQueue, function() {
                var n = {}, s = this.action, o = this.prefix === !0 ? s : this.prefix;
                e.each(this, function(i, s) {
                    n[i] = "string" === e.type(s) && "@" === s.charAt(0) ? t[s.slice(1) || (o ? o + i.charAt(0).toUpperCase() + i.slice(1) : i)] : s
                }), i.push(n)
            }), t.processQueue = i
        },processing: function() {
            return this._processing
        },process: function(t) {
            var i = this, n = e.extend({}, this.options, t);
            return n.processQueue && n.processQueue.length && (this._transformProcessQueue(n), 0 === this._processing && this._trigger("processstart"), e.each(t.files, function(t) {
                var s = t ? e.extend({}, n) : n, o = function() {
                    return i._processFile(s)
                };
                s.index = t, i._processing += 1, i._processingQueue = i._processingQueue.pipe(o, o).always(function() {
                    i._processing -= 1, 0 === i._processing && i._trigger("processstop")
                })
            })), this._processingQueue
        },_create: function() {
            this._super(), this._processing = 0, this._processingQueue = e.Deferred().resolveWith(this).promise()
        }})
}), function(e) {
    "function" == typeof define && define.amd ? define("fileupload_validate", ["jquery", "fileupload_process"], e) : e(window.jQuery)
}(function(e) {
    e.blueimp.fileupload.prototype.options.processQueue.push({action: "validate",always: !0,acceptFileTypes: "@",maxFileSize: "@",minFileSize: "@",maxNumberOfFiles: "@",disabled: "@disableValidation"}), e.widget("blueimp.fileupload", e.blueimp.fileupload, {options: {getNumberOfFiles: e.noop,messages: {maxNumberOfFiles: "Maximum number of files exceeded",acceptFileTypes: "File type not allowed",maxFileSize: "File is too large",minFileSize: "File is too small"}},processActions: {validate: function(t, i) {
                if (i.disabled)
                    return t;
                var n = e.Deferred(), s = this.options, o = t.files[t.index];
                return "number" === e.type(i.maxNumberOfFiles) && (s.getNumberOfFiles() || 0) + t.files.length > i.maxNumberOfFiles ? o.error = s.i18n("maxNumberOfFiles") : !i.acceptFileTypes || i.acceptFileTypes.test(o.type) || i.acceptFileTypes.test(o.name) ? i.maxFileSize && o.size > i.maxFileSize ? o.error = s.i18n("maxFileSize") : "number" === e.type(o.size) && o.size < i.minFileSize ? o.error = s.i18n("minFileSize") : delete o.error : o.error = s.i18n("acceptFileTypes"), o.error || t.files.error ? (t.files.error = !0, n.rejectWith(this, [t])) : n.resolveWith(this, [t]), n.promise()
            }}})
}), define("components/modal/modal", ["backbone", "jqueryui", "text!components/modal/templates/modal.html", "text!components/modal/templates/modal_link.html", "text!components/modal/templates/modal_pane.html", "text!components/modal/templates/modal_row.html", "text!components/modal/templates/modal_tab.html", "text!components/modal/templates/modal_thead.html", "text!components/modal/templates/modal_dropdown.html", "text!components/modal/templates/modal_static.html", "text!components/modal/templates/modal_static_footer.html", "core/utils", "fileupload_validate"], function(e, t, i, n, s, o, a, r, l, c, d, u) {
    $.ajaxSetup({cache: !1});
    var p = e.Model.extend({defaults: {tab: "",isLink: !1,type: "",text: "",link: "",groupID: null,isDropdown: !1}}), h = e.Collection.extend({model: p}), m = e.View.extend({tagName: "li",className: function() {
            return this.model.get("isLink") ? "link-group link-group-" + this.model.get("groupID") : this.model.get("isDropdown") ? "link-group dropdown link-group-" + this.model.get("groupID") : void 0
        },template: _.template(a),linkTemplate: _.template(n),dropdownTemplate: _.template(l),render: function() {
            var e = this.model.toJSON(), t = this.model.get("groupID");
            return _.isNumber(this.model.get("groupID")) && _.isEqual(_.last(this.model.collection.where({groupID: t})), this.model) ? _.extend(e, {isLast: !0}) : _.extend(e, {isLast: !1}), this.model.collection.where({isLink: !1,isDropdown: !1}).length === this.model.collection.length && this.model.collection.indexOf(this.model) === this.model.collection.length - 1 && _.extend(e, {isLast: !0}), this.$el.html(this.model.get("isLink") ? this.linkTemplate(e) : this.model.get("isDropdown") ? this.dropdownTemplate(e) : this.template(e)), this.options.hide && this.$el.hide(), this
        }}), f = e.Model.extend({defaults: {title: "",time: "",type: ""}}), g = e.View.extend({tagName: "tr",template: _.template(r),render: function() {
            return this.$el.html(this.template(this.model.toJSON())), this
        }}), v = e.Model.extend({}), b = e.View.extend({tagName: function() {
            return "image" === this.model.get("type") ? "li" : "tr"
        },template: _.template(o),events: {"click .js-choose": function(e) {
                if (this.parent.multiChoose)
                    this.toggle();
                else {
                    var t = this.model.attributes;
                    "guaguale" == t.type && (t.type = "activity", t._real_type = "guaguale"), "wheel" == t.type && (t.type = "activity", t._real_type = "wheel"), "zodiac" == t.type && (t.type = "activity", t._real_type = "zodiac"), M.chooseItemCallback(t)
                }
                e.stopPropagation()
            },"click .js-multi-select": "toggleImage"},initialize: function(e) {
            this.parent = e.parent;
            var t = this.model.get("time"), i = t.split(" ");
            this.model.set("time", i.join("<br>"))
        },toggleImage: function() {
            this.toggle(!0)
        },toggle: function(e) {
            var t = this.$(".js-choose");
            t.toggleClass("btn-primary"), t.hasClass("btn-primary") ? (t.data("view", this), e || t.html("取消")) : e || t.html("选取"), this.toggleConfirm()
        },toggleConfirm: function() {
            this.parent.$(".js-choose.btn-primary").length > 0 ? this.parent.$(".js-confirm-choose").show() : this.parent.$(".js-confirm-choose").hide()
        },render: function() {
            return this.$el.html(this.template(this.model.toJSON())), "image" === this.model.get("type") && this.parent.multiChoose ? this.$(".multi-select-container").show() : this.$(".multi-select-container").hide(), this
        }}), y = e.Model.extend({defaults: {type: "",data: [],pageNavi: ""},getType: function(e) {
            var t = e.slice(1).split("-"), i = this.get("type");
            return i === t[t.length - 1]
        },fetch: function(e, t, i) {
            var n = this, s = this.get("type"), o = M.url[s];
            _.isUndefined(o) || ("cards" != s && (o += o.indexOf("?") >= 0 ? "&v=2" : "?v=2"), _.isUndefined(e) || (o += "&keyword=" + e), _.isUndefined(t) || (o += "&p=" + t), window._global.imageSize && "image" === s && (o += "&size=" + window._global.imageSize), $.getJSON(o, function(e) {
                var t = e.data;
                if ("0" == e.errcode || 0 == e.code) {
                    var o = t.data_list, a = t.page_view, r = t.data_type;
                    n.set({type: s,data: o,pageNavi: a,dataType: r})
                } else
                    u.errorNotify(e.errmsg);
                _.isFunction(i) && i()
            }))
        }}), w = e.Collection.extend({model: y,fetch: function(e, t) {
            var i = this, n = M.url[e];
            if (_.isUndefined(n))
                return void i.add({type: e}, {callback: t});
            "cards" != e && (n += n.indexOf("?") >= 0 ? "&v=2" : "?v=2"), window._global.imageSize && "image" === e && (n += "&size=" + window._global.imageSize);
            var s;
            _.isFunction(t) ? s = t : i.reset(), $.getJSON(n, function(t) {
                var n = t.data;
                if ("0" == t.errcode || 0 == t.code) {
                    var o = n.data_list, a = n.page_view, r = n.data_type;
                    i.add({type: e,data: o,pageNavi: a,dataType: r}, {callback: s})
                } else
                    u.errorNotify(t.errmsg)
            })
        }}), k = e.View.extend({tagName: "div",id: function() {
            return "js-module-" + this.model.get("type")
        },className: function() {
            return "tab-pane module-" + this.model.get("type")
        },template: _.template(s),events: {"click .js-modal-search": function(e) {
                {
                    var t = this, i = t.search.val() || void 0;
                    t.model.get("type"), $(e.target)
                }
                t.model.getType(t.parent.tab.find("li.active a").attr("href")) && ("image" === t.model.get("type") ? t.$(".module-header").addClass("loading") : t.parent.loading(), t.model.fetch(i, void 0, function() {
                    "image" === t.model.get("type") ? t.$(".module-header").removeClass("loading") : t.parent.done()
                }))
            },"keydown .js-modal-search-input": function(e) {
                13 === e.keyCode && (this.$(".js-modal-search").trigger("click"), e.preventDefault())
            },"click .js-update": "update"},initialize: function(e) {
            e = e || {}, this.parent = e.parent;
            var t = this;
            this.parent.$(".pagenavi").on("click", function(e) {
                var i = $(e.target), n = i.data("page-num");
                e.preventDefault(), i.hasClass("js-confirm-upload-image") || i.hasClass("btn-cancel") || e.stopPropagation(), i.hasClass("fetch_page") && !i.hasClass("active") && t.model.getType(t.parent.tab.find("li.active a").attr("href")) && t.searchKeyword(n)
            }), this.parent.$(".pagenavi").on("keydown", "a.active", function(e) {
                e.keyCode === u.keyCode.ENTER && t.model.getType(t.parent.tab.find("li.active a").attr("href")) && (t.searchKeyword(Number(e.target.innerText)), e.preventDefault())
            }), this.parent.tabLink.on("active:tab", function(e) {
                var i = e.target, n = i.getAttribute("href");
                n && t.model.getType(n) && t.renderPageNavi()
            }), "image" === this.model.get("type") && this.parent.$el.on("show", function() {
                t.renderRow()
            }), this.listenTo(this.model, "change:data", this.renderRow), this.listenTo(this.model, "change:pageNavi", this.renderPageNavi)
        },update: function() {
            var e = this;
            "image" === e.model.get("type") ? e.$(".module-header").addClass("loading") : e.parent.loading(), this.model.fetch(void 0, void 0, function() {
                "image" === e.model.get("type") ? e.$(".module-header").removeClass("loading") : e.parent.done()
            })
        },searchKeyword: function(e) {
            var t = this;
            isNaN(e) && (e = 1);
            var i = t.search.val() || void 0;
            "image" === t.model.get("type") ? t.$(".module-header").addClass("loading") : t.parent.loading(), t.model.fetch(i, e, function() {
                "image" === t.model.get("type") ? t.$(".module-header").removeClass("loading") : t.parent.done()
            })
        },render: function() {
            var e = this;
            this.modelData = this.model.toJSON(), this.$el.html(this.template(this.modelData)), this.thead = new f({title: "标题",time: "创建时间",type: e.model.get("type")}), this.renderThead(), this.renderRow(), this.search = this.$(".js-modal-search-input");
            var t = this.parent.tab.find("li.active a");
            return t.length > 0 && this.model.getType(t.attr("href")) && this.renderPageNavi(this.modelData.pageNavi), this
        },renderThead: function() {
            var e, t = this;
            e = this.$("image" === this.model.get("type") ? ".module-header" : "thead"), e.empty();
            var i = new g({el: e,model: t.thead});
            i.render()
        },renderRow: function() {
            var e, t = this, i = this.model.get("data"), n = this.model.get("type"), s = this.model.get("dataType");
            e = t.$("image" === n ? ".module-body" : "tbody"), e.empty(), _.each(i, function(o) {
                var a;
                "news" === n ? (i = [], _.each(o.news_list, function(e) {
                    i.push(e.title)
                }), a = i.join("\\n")) : a = o.title;
                var r = new b({model: new v({title: o.title || "",time: o.created_time || "",link: o.url || "",data_url: o.url || "",data_cover_attachment_id: o.cover_attachment_id || "",data_cover_attachment_url: o.cover_attachment_url || "",data_title: a || "",data_alias: o.alias || "",data_price: o.price || "",data_buy_url: o.buy_url || "",data_type: s || "",width: o.width || "",height: o.height || "",type: n || "",data_id: o.id || o._id || "",start_time: o.valid_start_time || o.start_time || "",end_time: o.valid_end_time || o.end_time || "",news: o.news_list || "",attachment_url: o.attachment_url || "",attachment_title: o.attachment_title || "",attachment_id: o.attachment_id || "",thumb_url: o.thumb_url || "",multiChoose: t.parent.multiChoose || !1,id: o.id || o._id || "",image_url: o.image_url || "",size: o.attachment_size || ""}),parent: t.parent});
                e.append(r.render().el)
            }), "image" === this.model.get("type") ? this.$(".module-header").removeClass("loading") : this.parent.done()
        },renderPageNavi: function() {
            var e = this.model.get("pageNavi");
            this.parent.$(".pagenavi").html(e), this.parent.done()
        }}), x = e.View.extend({tagName: "div",id: function() {
            return "js-module-" + this.model.get("type")
        },className: function() {
            return "tab-pane module-" + this.model.get("type")
        },events: {"click .js-preview-img": "previewImage"},template: _.template(c),footerTemplate: _.template(d),initialize: function(e) {
            var t = this;
            e = e || {}, this.parent = e.parent, this.parent.tabLink.on("active:tab", function(e) {
                var i = e.target, n = i.getAttribute("href");
                n && t.model.getType(n) && t.renderFooter()
            }), this.parent.$el.on("click", function(e) {
                if (e.target === $(".js-confirm-upload-image")[0]) {
                    $(e.target).button("loading");
                    var i = t.downloadImage();
                    if (i)
                        return;
                    var n = $(".js-fileupload");
                    t.uploadFiles ? n.fileupload("send", {files: t.uploadFiles}).success(function(e) {
                        var i = [], n = [];
                        if (_.each(e, function(e, t) {
                            "success" === e.status ? n.push(e.success_msg) : i.push({index: t + 1,msg: e.failed_msg})
                        }), n.length > 1 ? M.chooseItemCallback(t.parent.multiChoose ? n : n[0]) : 1 === n.length && M.chooseItemCallback(n[0]), i.length > 0) {
                            var s = _.reduce(i, function(e, t) {
                                return "size" === t.msg.upload_file ? e + "第" + t.index + " 张图片大于 1MB 上传失败；" : e + "第" + t.index + " 张图片上传失败（请联系客服）；"
                            }, "");
                            u.errorNotify(s)
                        }
                        t.clearDownload()
                    }) : (u.errorNotify("至少选择一张图片。"), t.clearDownload())
                }
            })
        },render: function() {
            return this.$el.html(this.template(this.model.attributes)), this
        },renderFooter: function() {
            this.parent.$(".pagenavi").html(this.footerTemplate(this.model.attributes)), this.uploadImage()
        },uploadImage: function() {
            var e = this;
            if (!e.initUploadImage) {
                e.initUploadImage = !0;
                var t = $(".js-fileupload");
                t.fileupload({dataType: "json",add: function() {
                    },xhrFields: {withCredentials: !0}}).fileupload("option", {formData: {media_type: "image",v: "2",mp_id: window._global.kdt_id},maxFileSize: 1e6,acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i}).on("change", function(t) {
                    var i = t.target.files.length;
                    if (0 !== i) {
                        if (i > 10)
                            return void u.errorNotify("一次只能选择 10 张图片。");
                        var n = $(".js-upload-img");
                        n.empty(), _.each(t.target.files, function(e) {
                            var t = new FileReader;
                            t.onload = function(e) {
                                n.append($("<img>").attr("src", e.target.result))
                            }, t.readAsDataURL(e)
                        }), e.uploadFiles = t.target.files
                    }
                })
            }
        },previewImage: function() {
            var e = $(".js-web-img-input").val();
            if ("" === $.trim(e))
                return void $(".js-web-img-input").focus();
            this.model.set("src", e);
            var t = this.$(".js-download-img"), i = $("<img>").on("load", function() {
                t.removeClass("loading")
            }).attr("src", e);
            t.html(i).addClass("loading")
        },downloadImage: function() {
            var e = this;
            if ("" != $.trim(this.model.get("src"))) {
                var t = {attachment_url: this.model.get("src"),media_type: "image",v: 2,mp_id: window._global.kdt_id};
                return $.post(window._global.url.img + "/download?format=json", t, function(e) {
                    e.success && M.chooseItemCallback(e.success)
                }, "json").always(function() {
                    e.clearDownload()
                }).fail(function() {
                    u.errorNotify("网络出现错误啦。")
                }), !0
            }
        },clearDownload: function() {
            this.$(".js-download-img").html(""), this.$(".js-upload-img").html(""), this.$(".js-web-img-input").val(""), this.uploadFiles = null, this.model.set("src", "");
            var e = $(".js-confirm-upload-image");
            e.button("reset")
        }}), C = e.View.extend({initialize: function(e) {
            e = e || {}, this.type = e.type, this.multiChoose = e.multiChoose || !1, this.tab = this.$(".modal-tab"), this.tabContent = this.$(".tab-content"), this.modalBody = this.$(".modal-body"), this.listenTo(T[e.type], "add", this.addTabs), this.listenTo(j[e.type], "add", this.addPanes), this.listenTo(j[e.type], "reset", this.reset), T[e.type].add(e.list), this.tabList = e.tab;
            var t = this, i = this.tabLink = this.tab.find(".js-modal-tab"), n = function(e) {
                var n = $(e.target), s = i.index(n);
                t.tab.find(".link-group").css({display: "none"}), t.tab.find(".link-group-" + s).css({display: "inline-block"}), t.tabContent.find("#" + n.attr("href").substring(1)).length > 0 && n.tab("show").trigger("active:tab"), e.preventDefault(), e.stopPropagation()
            };
            i.on("click", n), i.one("click", function(e) {
                var i = $(e.target), n = i.data("type");
                t.loading(), j[t.options.type] && j[t.options.type].fetch(n, function() {
                    i.tab("show").trigger("active:tab")
                })
            }), this.$el.on("click", ".js-confirm-choose .btn", function() {
                var e = [];
                t.$(".js-choose.btn-primary").each(function(t, i) {
                    e.push($(i).data("view").model.attributes)
                }), M.chooseItemCallback(e)
            }), this.on("hide", function() {
                "image" === t.type && this.$(".js-multi-select:checked").prop("checked", !1), t.$(".js-choose.btn-primary").each(function(e, i) {
                    "image" === t.type ? $(i).data("view").toggle(!0) : $(i).data("view").toggle()
                })
            }), this.on("show", function() {
                for (var e = !0, t = 0; t < i.length; t++) {
                    var n = $(i[t]);
                    if (n.parent("li").hasClass("active")) {
                        n.trigger("click"), e = !1;
                        break
                    }
                }
                e && i.first().trigger("click")
            })
        },addTabs: function(e) {
            var t = {model: e};
            !_.isUndefined(this.options.hide) && "" !== e.type && _.indexOf(this.options.hide, e.get("type")) >= 0 && _.extend(t, {hide: !0});
            var i = new m(t);
            this.tab.append(i.render().$el)
        },addPanes: function(e, t, i) {
            var n, s = this, o = _.isUndefined(M.url[e.get("type")]);
            n = o ? new x({model: e,parent: s}) : new k({model: e,parent: s}), this.tabContent.append(n.render().$el), _.isFunction(i.callback) && i.callback(), this.done()
        },reset: function() {
            this.tabContent.empty()
        },update: function() {
            var e = this;
            this.loading(), _.each(this.tabList, function(t) {
                j[e.type] && j[e.type].fetch(t, !0)
            })
        },loading: function() {
            this.modalBody.addClass("loading")
        },done: function() {
            this.modalBody.removeClass("loading"), this.$(".js-confirm-choose").hide()
        },fetchAll: function() {
            var e = this;
            _.each(this.options.tab, function(t) {
                j[e.options.type] && j[e.options.type].fetch(t)
            })
        }}), j = {}, T = {}, S = e.Model.extend({defaults: {type: "other"}}), D = e.View.extend({template: _.template(i),render: function() {
            return $(this.template(this.model.attributes)).appendTo("body")
        }}), M = {}, N = {};
    return {initialize: function(e) {
            if (N[e.type])
                return _.isArray(e.hide) && e.hide.length > 0 ? N[e.type].find(".modal-tab li:not(.link-group)>a").each(function(t, i) {
                    var n = $.trim($(i).attr("href").replace("#js-module-", ""));
                    e.hide.indexOf(n) >= 0 && $(i).parent("li").hide()
                }) : N[e.type].find(".modal-tab li:not(.link-group)").removeAttr("style"), N[e.type].app.multiChoose = e.multiChoose ? !0 : !1, N[e.type];
            _.isUndefined(M.url) && (M.url = e.url || {goods: window._global.url.www + "/showcase/goods/shortList.json",topic: window._global.url.v1 + "/topic/list/get",category: window._global.url.www + "/showcase/category/shortList.json",component: window._global.url.www + "/showcase/component/shortList.json",feature_category: window._global.url.www + "/showcase/category/shortList.json",survey: window._global.url.www + "/apps/vote/selectList.json",image: window._global.url.www + "/showcase/attachment/alert.json?media_type=1",article: window._global.url.v1 + "/article/list/get",tag: window._global.url.www + "/showcase/tag/shortList.json",goods_tag: window._global.url.www + "/showcase/tag/shortList.json",f_category: window._global.url.www + "/showcase/category/shortList.json",tag_feature: window._global.url.www + "/showcase/tag/shortList.json",tag_feature_2: window._global.url.www + "/showcase/feature/shortList.json",news: window._global.url.v1 + "/news/list/get",activity: window._global.url.v1 + "/activity/list/modal",guaguale: window._global.url.www + "/apps/cards/shortlist.json",wheel: window._global.url.www + "/apps/wheel/shortlist.json",zodiac: window._global.url.www + "/apps/zodiac/shortlist.json",grab: window._global.url.www + "/apps/grab/shortList.json",guang_activity: window._global.url.v1 + "/activity/list/modal?is_guangyiguang=1",feature: window._global.url.www + "/showcase/feature/shortList.json",articles: window._global.url.www + "/sinaweibo/articles/atricleselectionlist.json"}, _.each(M.url, function(e, t) {
                j[t] = new w, T[t] = new h
            })), M.chooseItemCallback = function() {
                e.chooseItemCallback.apply(i, arguments), i.modal("hide"), s.trigger("hide")
            } || function() {
            };
            var t = new D({model: new S({type: e.type})}), i = t.render();
            i.view = t, i.on("show", function(e) {
                var t = $(e.target);
                t.hasClass("modal") && s.trigger("show")
            }), i.setChooseItemCallback = function(e) {
                return M.chooseItemCallback = null, M.chooseItemCallback = function() {
                    e.apply(i, arguments), i.modal("hide"), s.trigger("hide")
                }, this
            };
            var n;
            switch (e.type) {
                case "news":
                    n = {list: [{tab: "图文素材",type: "news"}, {link: "/news/list",text: "图文素材管理",isLink: !0,groupID: 0}],tab: ["news"],type: "news"};
                    break;
                case "articles":
                    n = {list: [{tab: "新浪微博图文素材",type: "articles"}, {link: window._global.url.www + "/sinaweibo/articles",text: "新浪微博图文素材管理",isLink: !0,groupID: 0}],tab: ["articles"],type: "articles"};
                    break;
                case "tag":
                    n = {list: [{tab: "商品标签",type: "goods_tag"}, {link: "/v2/showcase/tag",text: "分类管理",isLink: !0,groupID: 0}],tab: ["goods_tag"],type: "goods_tag"};
                    break;
                case "tag_feature":
                    n = {list: [{tab: "商品标签",type: "tag_feature"}, {tab: "微杂志",type: "tag_feature_2"}, {link: "/v2/showcase/tag",text: "分类管理",isLink: !0,groupID: 0}, {link: "/v2/showcase/feature#create",text: "新建微杂志",isLink: !0,groupID: 1}, {link: "/v2/showcase/feature#list&is_display=0",text: "草稿管理",isLink: !0,groupID: 1}],tab: ["tag_feature", "tag_feature_2"],type: "tag_feature"};
                    break;
                case "feature":
                    n = {list: [{tab: "微杂志",type: "feature"}, {tab: "微杂志分类",type: "category"}, {link: "/v2/showcase/feature#create",text: "新建微杂志",isLink: !0,groupID: 0}, {link: "/v2/showcase/feature#list&is_display=0",text: "草稿管理",isLink: !0,groupID: 0}, {link: "/v2/showcase/category",text: "分类管理",isLink: !0,groupID: 1}],tab: ["feature", "category"],type: "feature"};
                    break;
                case "category":
                    n = {list: [{tab: "微杂志分类",type: "f_category"}, {link: "/v2/showcase/category",text: "分类管理",isLink: !0,groupID: 0}],tab: ["f_category"],type: "f_category"};
                    break;
                case "feature_category":
                    n = {list: [{tab: "微杂志分类",type: "feature_category"}, {link: "/v2/showcase/category",text: "分类管理",isLink: !0,groupID: 0}],tab: ["feature_category"],type: "feature_category"};
                    break;
                case "activity":
                    n = {list: [{tab: "在有效期内的活动",type: "activity"}, {tab: "刮刮乐",type: "guaguale"}, {tab: "幸运大抽奖",type: "wheel"}, {tab: "翻翻看",type: "zodiac"}, {link: "/activity/list?activity_type=1",text: "新建营销活动",groupID: 0,isDropdown: !0}, {link: "/v2/apps/cards#create",text: "新建刮刮乐",groupID: 1,isLink: !0}, {link: "/v2/apps/wheel#create",text: "新建幸运大抽奖",groupID: 2,isLink: !0}, {link: "/v2/apps/zodiac#create",text: "新建翻翻看",groupID: 3,isLink: !0}],tab: ["activity"],type: "activity"};
                    break;
                case "grab":
                    n = {list: [{tab: "抢楼活动",type: "grab"}, {link: _global.url.www + "/apps/grab/create",text: "新建抢楼活动",groupID: 0,isLink: !0}],tab: ["grab"],type: "grab"};
                    break;
                case "guang_activity":
                    n = {list: [{tab: "在有效期内的活动",type: "guang_activity"}, {tab: "刮刮乐",type: "guaguale"}, {tab: "幸运大抽奖",type: "wheel"}, {tab: "翻翻看",type: "zodiac"}, {link: "/activity/list?activity_type=1",text: "新建营销活动",groupID: 0,isDropdown: !0}, {link: "/v2/apps/cards#create",text: "新建刮刮乐",groupID: 1,isDropdown: !0}, {link: "/v2/apps/wheel#create",text: "新建幸运大抽奖",groupID: 2,isDropdown: !0}, {link: "/v2/apps/zodiac#create",text: "新建翻翻看",groupID: 3,isDropdown: !0}],tab: ["guang_activity"],type: "guang_activity"};
                    break;
                case "goods":
                    n = {list: [{tab: "已上架商品",type: "goods"}, {tab: "商品标签",type: "tag"}, {link: "/v2/showcase/goods/edit",text: "新建商品",isLink: !0,groupID: 0}, {link: "/v2/showcase/goods#list&is_display=0",text: "草稿管理",isLink: !0,groupID: 0}, {link: "/v2/showcase/tag",text: "分类管理",isLink: !0,groupID: 1}],tab: ["goods", "tag"],type: "goods"}, $.widget.bridge("uitooltip", $.ui.tooltip), $(document).uitooltip({items: ".js-goods-modal #js-module-goods tbody .title a",position: {my: "top+20",at: "center",collision: "none"},content: function() {
                            var e = $(this), t = e.data("cover-attachment-url");
                            return '<div class="arrow"></div><div class="loading" style="width: 200px;height: 200px;background-color: #fff;"><img class="picture-tooltip" style="height: 200px;" width="200" height="200" src="' + t + '"/></div>'
                        }});
                    break;
                case "survey":
                    n = {list: [{tab: "投票调查",type: "survey"}, {link: "/v2/apps/vote#create",text: "新建投票调查",groupID: 0,isLink: !0}],tab: ["survey"],type: "survey"};
                    break;
                case "component":
                    n = {list: [{tab: "自定义页面模块",type: "component"}, {link: "/v2/showcase/component#create",text: "新建自定义页面模块",groupID: 0,isLink: !0}],tab: ["component"],type: "component"};
                    break;
                case "image":
                    n = {list: [{tab: "用过的图片",type: "image"}, {tab: "新图片",type: "uploadImage"}],tab: ["image", "uploadImage"],type: "image"};
                    break;
                default:
                    n = e.config
            }
            _.extend(n, {modal: i,hide: e.hide || [],multiChoose: e.multiChoose || !1}), e.size && (window._global.imageSize = e.size || !1);
            var s = new C(_.extend({}, {el: i}, n));
            return i.app = s, N[e.type] = i, i
        }}
}), function(e, t) {
    if ("object" == typeof exports) {
        var i = require("underscore"), n = require("backbone");
        module.exports = t(i, n)
    } else
        "function" == typeof define && define.amd && define("backbone.babysitter", ["underscore", "backbone"], t)
}(this, function(e, t) {
    "option strict";
    return t.ChildViewContainer = function(e, t) {
        var i = function(e) {
            this._views = {}, this._indexByModel = {}, this._indexByCustom = {}, this._updateLength(), t.each(e, this.add, this)
        };
        t.extend(i.prototype, {add: function(e, t) {
                var i = e.cid;
                return this._views[i] = e, e.model && (this._indexByModel[e.model.cid] = i), t && (this._indexByCustom[t] = i), this._updateLength(), this
            },findByModel: function(e) {
                return this.findByModelCid(e.cid)
            },findByModelCid: function(e) {
                var t = this._indexByModel[e];
                return this.findByCid(t)
            },findByCustom: function(e) {
                var t = this._indexByCustom[e];
                return this.findByCid(t)
            },findByIndex: function(e) {
                return t.values(this._views)[e]
            },findByCid: function(e) {
                return this._views[e]
            },remove: function(e) {
                var i = e.cid;
                return e.model && delete this._indexByModel[e.model.cid], t.any(this._indexByCustom, function(e, t) {
                    return e === i ? (delete this._indexByCustom[t], !0) : void 0
                }, this), delete this._views[i], this._updateLength(), this
            },call: function(e) {
                this.apply(e, t.tail(arguments))
            },apply: function(e, i) {
                t.each(this._views, function(n) {
                    t.isFunction(n[e]) && n[e].apply(n, i || [])
                })
            },_updateLength: function() {
                this.length = t.size(this._views)
            }});
        var n = ["forEach", "each", "map", "find", "detect", "filter", "select", "reject", "every", "all", "some", "any", "include", "contains", "invoke", "toArray", "first", "initial", "rest", "last", "without", "isEmpty", "pluck"];
        return t.each(n, function(e) {
            i.prototype[e] = function() {
                var i = t.values(this._views), n = [i].concat(t.toArray(arguments));
                return t[e].apply(t, n)
            }
        }), i
    }(t, e), t.ChildViewContainer
}), function(e, t) {
    if ("object" == typeof exports) {
        var i = require("underscore"), n = require("backbone"), s = require("backbone.wreqr"), o = require("backbone.babysitter");
        module.exports = t(i, n, s, o)
    } else
        "function" == typeof define && define.amd && define("marionette", ["underscore", "backbone", "backbone.wreqr", "backbone.babysitter"], t)
}(this, function(e, t) {
    !function(e, t, i) {
        function n(e) {
            return a.call(e)
        }
        function s(e, t) {
            var i = new Error(e);
            throw i.name = t || "Error", i
        }
        var o = {};
        t.Marionette = o, o.$ = t.$;
        var a = Array.prototype.slice;
        return o.extend = t.Model.extend, o.getOption = function(e, t) {
            if (e && t) {
                var i;
                return i = e.options && t in e.options && void 0 !== e.options[t] ? e.options[t] : e[t]
            }
        }, o.normalizeMethods = function(e) {
            var t, n = {};
            return i.each(e, function(e, s) {
                t = e, i.isFunction(t) || (t = this[t]), t && (n[s] = t)
            }, this), n
        }, o.triggerMethod = function() {
            function e(e, t, i) {
                return i.toUpperCase()
            }
            var t = /(^|:)(\w)/gi, n = function(n) {
                var s = "on" + n.replace(t, e), o = this[s];
                return i.isFunction(this.trigger) && this.trigger.apply(this, arguments), i.isFunction(o) ? o.apply(this, i.tail(arguments)) : void 0
            };
            return n
        }(), o.MonitorDOMRefresh = function(e) {
            function t(e) {
                e._isShown = !0, s(e)
            }
            function n(e) {
                e._isRendered = !0, s(e)
            }
            function s(e) {
                e._isShown && e._isRendered && o(e) && i.isFunction(e.triggerMethod) && e.triggerMethod("dom:refresh")
            }
            function o(t) {
                return e.contains(t.el)
            }
            return function(e) {
                e.listenTo(e, "show", function() {
                    t(e)
                }), e.listenTo(e, "render", function() {
                    n(e)
                })
            }
        }(document.documentElement), function(e) {
            function t(e, t, n, o) {
                var a = o.split(/\s+/);
                i.each(a, function(i) {
                    var o = e[i];
                    o || s("Method '" + i + "' was configured as an event handler, but does not exist."), e.listenTo(t, n, o, e)
                })
            }
            function n(e, t, i, n) {
                e.listenTo(t, i, n, e)
            }
            function o(e, t, n, s) {
                var o = s.split(/\s+/);
                i.each(o, function(i) {
                    var s = e[i];
                    e.stopListening(t, n, s, e)
                })
            }
            function a(e, t, i, n) {
                e.stopListening(t, i, n, e)
            }
            function r(e, t, n, s, o) {
                t && n && (i.isFunction(n) && (n = n.call(e)), i.each(n, function(n, a) {
                    i.isFunction(n) ? s(e, t, a, n) : o(e, t, a, n)
                }))
            }
            e.bindEntityEvents = function(e, i, s) {
                r(e, i, s, n, t)
            }, e.unbindEntityEvents = function(e, t, i) {
                r(e, t, i, a, o)
            }
        }(o), o.Callbacks = function() {
            this._deferred = o.$.Deferred(), this._callbacks = []
        }, i.extend(o.Callbacks.prototype, {add: function(e, t) {
                this._callbacks.push({cb: e,ctx: t}), this._deferred.done(function(i, n) {
                    t && (i = t), e.call(i, n)
                })
            },run: function(e, t) {
                this._deferred.resolve(t, e)
            },reset: function() {
                var e = this._callbacks;
                this._deferred = o.$.Deferred(), this._callbacks = [], i.each(e, function(e) {
                    this.add(e.cb, e.ctx)
                }, this)
            }}), o.Controller = function(e) {
            this.triggerMethod = o.triggerMethod, this.options = e || {}, i.isFunction(this.initialize) && this.initialize(this.options)
        }, o.Controller.extend = o.extend, i.extend(o.Controller.prototype, t.Events, {close: function() {
                this.stopListening(), this.triggerMethod("close"), this.unbind()
            }}), o.Region = function(e) {
            if (this.options = e || {}, this.el = o.getOption(this, "el"), !this.el) {
                var t = new Error("An 'el' must be specified for a region.");
                throw t.name = "NoElError", t
            }
            if (this.initialize) {
                var i = Array.prototype.slice.apply(arguments);
                this.initialize.apply(this, i)
            }
        }, i.extend(o.Region, {buildRegion: function(e, t) {
                var n = "string" == typeof e, s = "string" == typeof e.selector, o = "undefined" == typeof e.regionType, a = "function" == typeof e;
                if (!a && !n && !s)
                    throw new Error("Region must be specified as a Region type, a selector string or an object with selector property");
                var r, l;
                n && (r = e), e.selector && (r = e.selector, delete e.selector), a && (l = e), !a && o && (l = t), e.regionType && (l = e.regionType, delete e.regionType), (n || a) && (e = {}), e.el = r;
                var c = new l(e);
                return e.parentEl && (c.getEl = function(t) {
                    var n = e.parentEl;
                    return i.isFunction(n) && (n = n()), n.find(t)
                }), c
            }}), i.extend(o.Region.prototype, t.Events, {show: function(e) {
                this.ensureEl();
                var t = e.isClosed || i.isUndefined(e.$el), n = e !== this.currentView;
                n && this.close(), e.render(), (n || t) && this.open(e), this.currentView = e, o.triggerMethod.call(this, "show", e), o.triggerMethod.call(e, "show")
            },ensureEl: function() {
                this.$el && 0 !== this.$el.length || (this.$el = this.getEl(this.el))
            },getEl: function(e) {
                return o.$(e)
            },open: function(e) {
                this.$el.empty().append(e.el)
            },close: function() {
                var e = this.currentView;
                e && !e.isClosed && (e.close ? e.close() : e.remove && e.remove(), o.triggerMethod.call(this, "close", e), delete this.currentView)
            },attachView: function(e) {
                this.currentView = e
            },reset: function() {
                this.close(), delete this.$el
            }}), o.Region.extend = o.extend, o.RegionManager = function(e) {
            var t = e.Controller.extend({constructor: function(t) {
                    this._regions = {}, e.Controller.prototype.constructor.call(this, t)
                },addRegions: function(e, t) {
                    var n = {};
                    return i.each(e, function(e, s) {
                        "string" == typeof e && (e = {selector: e}), e.selector && (e = i.defaults({}, e, t));
                        var o = this.addRegion(s, e);
                        n[s] = o
                    }, this), n
                },addRegion: function(t, n) {
                    var s, o = i.isObject(n), a = i.isString(n), r = !!n.selector;
                    return s = a || o && r ? e.Region.buildRegion(n, e.Region) : i.isFunction(n) ? e.Region.buildRegion(n, e.Region) : n, this._store(t, s), this.triggerMethod("region:add", t, s), s
                },get: function(e) {
                    return this._regions[e]
                },removeRegion: function(e) {
                    var t = this._regions[e];
                    this._remove(e, t)
                },removeRegions: function() {
                    i.each(this._regions, function(e, t) {
                        this._remove(t, e)
                    }, this)
                },closeRegions: function() {
                    i.each(this._regions, function(e) {
                        e.close()
                    }, this)
                },close: function() {
                    this.removeRegions();
                    var t = Array.prototype.slice.call(arguments);
                    e.Controller.prototype.close.apply(this, t)
                },_store: function(e, t) {
                    this._regions[e] = t, this._setLength()
                },_remove: function(e, t) {
                    t.close(), delete this._regions[e], this._setLength(), this.triggerMethod("region:remove", e, t)
                },_setLength: function() {
                    this.length = i.size(this._regions)
                }}), n = ["forEach", "each", "map", "find", "detect", "filter", "select", "reject", "every", "all", "some", "any", "include", "contains", "invoke", "toArray", "first", "initial", "rest", "last", "without", "isEmpty", "pluck"];
            return i.each(n, function(e) {
                t.prototype[e] = function() {
                    var t = i.values(this._regions), n = [t].concat(i.toArray(arguments));
                    return i[e].apply(i, n)
                }
            }), t
        }(o), o.TemplateCache = function(e) {
            this.templateId = e
        }, i.extend(o.TemplateCache, {templateCaches: {},get: function(e) {
                var t = this.templateCaches[e];
                return t || (t = new o.TemplateCache(e), this.templateCaches[e] = t), t.load()
            },clear: function() {
                var e, t = n(arguments), i = t.length;
                if (i > 0)
                    for (e = 0; i > e; e++)
                        delete this.templateCaches[t[e]];
                else
                    this.templateCaches = {}
            }}), i.extend(o.TemplateCache.prototype, {load: function() {
                if (this.compiledTemplate)
                    return this.compiledTemplate;
                var e = this.loadTemplate(this.templateId);
                return this.compiledTemplate = this.compileTemplate(e), this.compiledTemplate
            },loadTemplate: function(e) {
                var t = o.$(e).html();
                return t && 0 !== t.length || s("Could not find template: '" + e + "'", "NoTemplateError"), t
            },compileTemplate: function(e) {
                return i.template(e)
            }}), o.Renderer = {render: function(e, t) {
                if (!e) {
                    var i = new Error("Cannot render the template since it's false, null or undefined.");
                    throw i.name = "TemplateNotFoundError", i
                }
                var n;
                return (n = "function" == typeof e ? e : o.TemplateCache.get(e))(t)
            }}, o.View = t.View.extend({constructor: function(e) {
                i.bindAll(this, "render");
                var n = Array.prototype.slice.apply(arguments);
                this.options = i.extend({}, i.result(this, "options"), i.isFunction(e) ? e.call(this) : e), this.events = this.normalizeUIKeys(i.result(this, "events")), t.View.prototype.constructor.apply(this, n), o.MonitorDOMRefresh(this), this.listenTo(this, "show", this.onShowCalled, this)
            },triggerMethod: o.triggerMethod,normalizeMethods: o.normalizeMethods,getTemplate: function() {
                return o.getOption(this, "template")
            },mixinTemplateHelpers: function(e) {
                e = e || {};
                var t = o.getOption(this, "templateHelpers");
                return i.isFunction(t) && (t = t.call(this)), i.extend(e, t)
            },normalizeUIKeys: function(e) {
                return "undefined" != typeof e ? (i.each(i.keys(e), function(t) {
                    var i = t.split("@ui.");
                    2 === i.length && (e[i[0] + this.ui[i[1]]] = e[t], delete e[t])
                }, this), e) : void 0
            },configureTriggers: function() {
                if (this.triggers) {
                    var e = {}, t = this.normalizeUIKeys(i.result(this, "triggers"));
                    return i.each(t, function(t, n) {
                        var s = i.isObject(t), o = s ? t.event : t;
                        e[n] = function(e) {
                            if (e) {
                                var i = e.preventDefault, n = e.stopPropagation, a = s ? t.preventDefault : i, r = s ? t.stopPropagation : n;
                                a && i && i.apply(e), r && n && n.apply(e)
                            }
                            var l = {view: this,model: this.model,collection: this.collection};
                            this.triggerMethod(o, l)
                        }
                    }, this), e
                }
            },delegateEvents: function(e) {
                this._delegateDOMEvents(e), o.bindEntityEvents(this, this.model, o.getOption(this, "modelEvents")), o.bindEntityEvents(this, this.collection, o.getOption(this, "collectionEvents"))
            },_delegateDOMEvents: function(e) {
                e = e || this.events, i.isFunction(e) && (e = e.call(this));
                var n = {}, s = this.configureTriggers();
                i.extend(n, e, s), t.View.prototype.delegateEvents.call(this, n)
            },undelegateEvents: function() {
                var e = Array.prototype.slice.call(arguments);
                t.View.prototype.undelegateEvents.apply(this, e), o.unbindEntityEvents(this, this.model, o.getOption(this, "modelEvents")), o.unbindEntityEvents(this, this.collection, o.getOption(this, "collectionEvents"))
            },onShowCalled: function() {
            },close: function() {
                if (!this.isClosed) {
                    var e = this.triggerMethod("before:close");
                    e !== !1 && (this.isClosed = !0, this.triggerMethod("close"), this.unbindUIElements(), this.remove())
                }
            },bindUIElements: function() {
                if (this.ui) {
                    this._uiBindings || (this._uiBindings = this.ui);
                    var e = i.result(this, "_uiBindings");
                    this.ui = {}, i.each(i.keys(e), function(t) {
                        var i = e[t];
                        this.ui[t] = this.$(i)
                    }, this)
                }
            },unbindUIElements: function() {
                this.ui && this._uiBindings && (i.each(this.ui, function(e, t) {
                    delete this.ui[t]
                }, this), this.ui = this._uiBindings, delete this._uiBindings)
            }}), o.ItemView = o.View.extend({constructor: function() {
                o.View.prototype.constructor.apply(this, n(arguments))
            },serializeData: function() {
                var e = {};
                return this.model ? e = this.model.toJSON() : this.collection && (e = {items: this.collection.toJSON()}), e
            },render: function() {
                this.isClosed = !1, this.triggerMethod("before:render", this), this.triggerMethod("item:before:render", this);
                var e = this.serializeData();
                e = this.mixinTemplateHelpers(e);
                var t = this.getTemplate(), i = o.Renderer.render(t, e);
                return this.$el.html(i), this.bindUIElements(), this.triggerMethod("render", this), this.triggerMethod("item:rendered", this), this
            },close: function() {
                this.isClosed || (this.triggerMethod("item:before:close"), o.View.prototype.close.apply(this, n(arguments)), this.triggerMethod("item:closed"))
            }}), o.CollectionView = o.View.extend({itemViewEventPrefix: "itemview",constructor: function() {
                this._initChildViewStorage(), o.View.prototype.constructor.apply(this, n(arguments)), this._initialEvents(), this.initRenderBuffer()
            },initRenderBuffer: function() {
                this.elBuffer = document.createDocumentFragment(), this._bufferedChildren = []
            },startBuffering: function() {
                this.initRenderBuffer(), this.isBuffering = !0
            },endBuffering: function() {
                this.isBuffering = !1, this.appendBuffer(this, this.elBuffer), this._triggerShowBufferedChildren(), this.initRenderBuffer()
            },_triggerShowBufferedChildren: function() {
                this._isShown && (i.each(this._bufferedChildren, function(e) {
                    o.triggerMethod.call(e, "show")
                }), this._bufferedChildren = [])
            },_initialEvents: function() {
                this.collection && (this.listenTo(this.collection, "add", this.addChildView, this), this.listenTo(this.collection, "remove", this.removeItemView, this), this.listenTo(this.collection, "reset", this.render, this))
            },addChildView: function(e) {
                this.closeEmptyView();
                var t = this.getItemView(e), i = this.collection.indexOf(e);
                this.addItemView(e, t, i)
            },onShowCalled: function() {
                this.children.each(function(e) {
                    o.triggerMethod.call(e, "show")
                })
            },triggerBeforeRender: function() {
                this.triggerMethod("before:render", this), this.triggerMethod("collection:before:render", this)
            },triggerRendered: function() {
                this.triggerMethod("render", this), this.triggerMethod("collection:rendered", this)
            },render: function() {
                return this.isClosed = !1, this.triggerBeforeRender(), this._renderChildren(), this.triggerRendered(), this
            },_renderChildren: function() {
                this.startBuffering(), this.closeEmptyView(), this.closeChildren(), this.isEmpty(this.collection) ? this.showEmptyView() : this.showCollection(), this.endBuffering()
            },showCollection: function() {
                var e;
                this.collection.each(function(t, i) {
                    e = this.getItemView(t), this.addItemView(t, e, i)
                }, this)
            },showEmptyView: function() {
                var e = this.getEmptyView();
                if (e && !this._showingEmptyView) {
                    this._showingEmptyView = !0;
                    var i = new t.Model;
                    this.addItemView(i, e, 0)
                }
            },closeEmptyView: function() {
                this._showingEmptyView && (this.closeChildren(), delete this._showingEmptyView)
            },getEmptyView: function() {
                return o.getOption(this, "emptyView")
            },getItemView: function() {
                var e = o.getOption(this, "itemView");
                return e || s("An `itemView` must be specified", "NoItemViewError"), e
            },addItemView: function(e, t, n) {
                var s = o.getOption(this, "itemViewOptions");
                i.isFunction(s) && (s = s.call(this, e, n));
                var a = this.buildItemView(e, t, s);
                return this.addChildViewEventForwarding(a), this.triggerMethod("before:item:added", a), this.children.add(a), this.renderItemView(a, n), this._isShown && !this.isBuffering && o.triggerMethod.call(a, "show"), this.triggerMethod("after:item:added", a), a
            },addChildViewEventForwarding: function(e) {
                var t = o.getOption(this, "itemViewEventPrefix");
                this.listenTo(e, "all", function() {
                    var s = n(arguments), a = s[0], r = this.normalizeMethods(this.getItemEvents());
                    s[0] = t + ":" + a, s.splice(1, 0, e), "undefined" != typeof r && i.isFunction(r[a]) && r[a].apply(this, s), o.triggerMethod.apply(this, s)
                }, this)
            },getItemEvents: function() {
                return i.isFunction(this.itemEvents) ? this.itemEvents.call(this) : this.itemEvents
            },renderItemView: function(e, t) {
                e.render(), this.appendHtml(this, e, t)
            },buildItemView: function(e, t, n) {
                var s = i.extend({model: e}, n);
                return new t(s)
            },removeItemView: function(e) {
                var t = this.children.findByModel(e);
                this.removeChildView(t), this.checkEmpty()
            },removeChildView: function(e) {
                e && (this.stopListening(e), e.close ? e.close() : e.remove && e.remove(), this.children.remove(e)), this.triggerMethod("item:removed", e)
            },isEmpty: function() {
                return !this.collection || 0 === this.collection.length
            },checkEmpty: function() {
                this.isEmpty(this.collection) && this.showEmptyView()
            },appendBuffer: function(e, t) {
                e.$el.append(t)
            },appendHtml: function(e, t) {
                e.isBuffering ? (e.elBuffer.appendChild(t.el), e._bufferedChildren.push(t)) : e.$el.append(t.el)
            },_initChildViewStorage: function() {
                this.children = new t.ChildViewContainer
            },close: function() {
                this.isClosed || (this.triggerMethod("collection:before:close"), this.closeChildren(), this.triggerMethod("collection:closed"), o.View.prototype.close.apply(this, n(arguments)))
            },closeChildren: function() {
                this.children.each(function(e) {
                    this.removeChildView(e)
                }, this), this.checkEmpty()
            }}), o.CompositeView = o.CollectionView.extend({constructor: function() {
                o.CollectionView.prototype.constructor.apply(this, n(arguments))
            },_initialEvents: function() {
                this.once("render", function() {
                    this.collection && (this.listenTo(this.collection, "add", this.addChildView, this), this.listenTo(this.collection, "remove", this.removeItemView, this), this.listenTo(this.collection, "reset", this._renderChildren, this))
                })
            },getItemView: function() {
                var e = o.getOption(this, "itemView") || this.constructor;
                return e || s("An `itemView` must be specified", "NoItemViewError"), e
            },serializeData: function() {
                var e = {};
                return this.model && (e = this.model.toJSON()), e
            },render: function() {
                this.isRendered = !0, this.isClosed = !1, this.resetItemViewContainer(), this.triggerBeforeRender();
                var e = this.renderModel();
                return this.$el.html(e), this.bindUIElements(), this.triggerMethod("composite:model:rendered"), this._renderChildren(), this.triggerMethod("composite:rendered"), this.triggerRendered(), this
            },_renderChildren: function() {
                this.isRendered && (this.triggerMethod("composite:collection:before:render"), o.CollectionView.prototype._renderChildren.call(this), this.triggerMethod("composite:collection:rendered"))
            },renderModel: function() {
                var e = {};
                e = this.serializeData(), e = this.mixinTemplateHelpers(e);
                var t = this.getTemplate();
                return o.Renderer.render(t, e)
            },appendBuffer: function(e, t) {
                var i = this.getItemViewContainer(e);
                i.append(t)
            },appendHtml: function(e, t) {
                if (e.isBuffering)
                    e.elBuffer.appendChild(t.el), e._bufferedChildren.push(t);
                else {
                    var i = this.getItemViewContainer(e);
                    i.append(t.el)
                }
            },getItemViewContainer: function(e) {
                if ("$itemViewContainer" in e)
                    return e.$itemViewContainer;
                var t, n = o.getOption(e, "itemViewContainer");
                if (n) {
                    var a = i.isFunction(n) ? n.call(this) : n;
                    t = e.$(a), t.length <= 0 && s("The specified `itemViewContainer` was not found: " + e.itemViewContainer, "ItemViewContainerMissingError")
                } else
                    t = e.$el;
                return e.$itemViewContainer = t, t
            },resetItemViewContainer: function() {
                this.$itemViewContainer && delete this.$itemViewContainer
            }}), o.Layout = o.ItemView.extend({regionType: o.Region,constructor: function(e) {
                e = e || {}, this._firstRender = !0, this._initializeRegions(e), o.ItemView.prototype.constructor.call(this, e)
            },render: function() {
                this.isClosed && this._initializeRegions(), this._firstRender ? this._firstRender = !1 : this.isClosed || this._reInitializeRegions();
                var e = Array.prototype.slice.apply(arguments), t = o.ItemView.prototype.render.apply(this, e);
                return t
            },close: function() {
                if (!this.isClosed) {
                    this.regionManager.close();
                    var e = Array.prototype.slice.apply(arguments);
                    o.ItemView.prototype.close.apply(this, e)
                }
            },addRegion: function(e, t) {
                var i = {};
                return i[e] = t, this._buildRegions(i)[e]
            },addRegions: function(e) {
                return this.regions = i.extend({}, this.regions, e), this._buildRegions(e)
            },removeRegion: function(e) {
                return delete this.regions[e], this.regionManager.removeRegion(e)
            },_buildRegions: function(e) {
                var t = this, i = {regionType: o.getOption(this, "regionType"),parentEl: function() {
                        return t.$el
                    }};
                return this.regionManager.addRegions(e, i)
            },_initializeRegions: function(e) {
                var t;
                this._initRegionManager(), t = i.isFunction(this.regions) ? this.regions(e) : this.regions || {}, this.addRegions(t)
            },_reInitializeRegions: function() {
                this.regionManager.closeRegions(), this.regionManager.each(function(e) {
                    e.reset()
                })
            },_initRegionManager: function() {
                this.regionManager = new o.RegionManager, this.listenTo(this.regionManager, "region:add", function(e, t) {
                    this[e] = t, this.trigger("region:add", e, t)
                }), this.listenTo(this.regionManager, "region:remove", function(e, t) {
                    delete this[e], this.trigger("region:remove", e, t)
                })
            }}), o.AppRouter = t.Router.extend({constructor: function(e) {
                t.Router.prototype.constructor.apply(this, n(arguments)), this.options = e || {};
                var i = o.getOption(this, "appRoutes"), s = this._getController();
                this.processAppRoutes(s, i)
            },appRoute: function(e, t) {
                var i = this._getController();
                this._addAppRoute(i, e, t)
            },processAppRoutes: function(e, t) {
                if (t) {
                    var n = i.keys(t).reverse();
                    i.each(n, function(i) {
                        this._addAppRoute(e, i, t[i])
                    }, this)
                }
            },_getController: function() {
                return o.getOption(this, "controller")
            },_addAppRoute: function(e, t, n) {
                var s = e[n];
                if (!s)
                    throw new Error("Method '" + n + "' was not found on the controller");
                this.route(t, n, i.bind(s, e))
            }}), o.Application = function(e) {
            this._initRegionManager(), this._initCallbacks = new o.Callbacks, this.vent = new t.Wreqr.EventAggregator, this.commands = new t.Wreqr.Commands, this.reqres = new t.Wreqr.RequestResponse, this.submodules = {}, i.extend(this, e), this.triggerMethod = o.triggerMethod
        }, i.extend(o.Application.prototype, t.Events, {execute: function() {
                var e = Array.prototype.slice.apply(arguments);
                this.commands.execute.apply(this.commands, e)
            },request: function() {
                var e = Array.prototype.slice.apply(arguments);
                return this.reqres.request.apply(this.reqres, e)
            },addInitializer: function(e) {
                this._initCallbacks.add(e)
            },start: function(e) {
                this.triggerMethod("initialize:before", e), this._initCallbacks.run(e, this), this.triggerMethod("initialize:after", e), this.triggerMethod("start", e)
            },addRegions: function(e) {
                return this._regionManager.addRegions(e)
            },closeRegions: function() {
                this._regionManager.closeRegions()
            },removeRegion: function(e) {
                this._regionManager.removeRegion(e)
            },getRegion: function(e) {
                return this._regionManager.get(e)
            },module: function(e, t) {
                var i = o.Module;
                t && (i = t.moduleClass || i);
                var s = n(arguments);
                return s.unshift(this), i.create.apply(i, s)
            },_initRegionManager: function() {
                this._regionManager = new o.RegionManager, this.listenTo(this._regionManager, "region:add", function(e, t) {
                    this[e] = t
                }), this.listenTo(this._regionManager, "region:remove", function(e) {
                    delete this[e]
                })
            }}), o.Application.extend = o.extend, o.Module = function(e, t, n) {
            this.moduleName = e, this.options = i.extend({}, this.options, n), this.initialize = n.initialize || this.initialize, this.submodules = {}, this._setupInitializersAndFinalizers(), this.app = t, this.startWithParent = !0, this.triggerMethod = o.triggerMethod, i.isFunction(this.initialize) && this.initialize(this.options, e, t)
        }, o.Module.extend = o.extend, i.extend(o.Module.prototype, t.Events, {initialize: function() {
            },addInitializer: function(e) {
                this._initializerCallbacks.add(e)
            },addFinalizer: function(e) {
                this._finalizerCallbacks.add(e)
            },start: function(e) {
                this._isInitialized || (i.each(this.submodules, function(t) {
                    t.startWithParent && t.start(e)
                }), this.triggerMethod("before:start", e), this._initializerCallbacks.run(e, this), this._isInitialized = !0, this.triggerMethod("start", e))
            },stop: function() {
                this._isInitialized && (this._isInitialized = !1, o.triggerMethod.call(this, "before:stop"), i.each(this.submodules, function(e) {
                    e.stop()
                }), this._finalizerCallbacks.run(void 0, this), this._initializerCallbacks.reset(), this._finalizerCallbacks.reset(), o.triggerMethod.call(this, "stop"))
            },addDefinition: function(e, t) {
                this._runModuleDefinition(e, t)
            },_runModuleDefinition: function(e, n) {
                if (e) {
                    var s = i.flatten([this, this.app, t, o, o.$, i, n]);
                    e.apply(this, s)
                }
            },_setupInitializersAndFinalizers: function() {
                this._initializerCallbacks = new o.Callbacks, this._finalizerCallbacks = new o.Callbacks
            }}), i.extend(o.Module, {create: function(e, t, s) {
                var o = e, a = n(arguments);
                a.splice(0, 3), t = t.split(".");
                var r = t.length, l = [];
                return l[r - 1] = s, i.each(t, function(t, i) {
                    var n = o;
                    o = this._getModule(n, t, e, s), this._addModuleDefinition(n, o, l[i], a)
                }, this), o
            },_getModule: function(e, t, n, s) {
                var a = o.Module, r = i.extend({}, s);
                s && (a = s.moduleClass || a);
                var l = e[t];
                return l || (l = new a(t, n, r), e[t] = l, e.submodules[t] = l), l
            },_addModuleDefinition: function(e, t, n, s) {
                var o, a;
                i.isFunction(n) ? (o = n, a = !0) : i.isObject(n) ? (o = n.define, a = "undefined" != typeof n.startWithParent ? n.startWithParent : !0) : a = !0, o && t.addDefinition(o, s), t.startWithParent = t.startWithParent && a, t.startWithParent && !t.startWithParentIsConfigured && (t.startWithParentIsConfigured = !0, e.addInitializer(function(e) {
                    t.startWithParent && t.start(e)
                }))
            }}), o
    }(this, t, e);
    return t.Marionette
}), define("text!components/image/templates/image_list.html", [], function() {
    return '<div class="modal-header">\n    <a class="close" data-dismiss="modal">×</a>\n    <!-- 顶部tab -->\n    <ul class="module-nav modal-tab">\n        <li class="active"><a href="javascript:;" data-pane="image" class="js-modal-tab">用过的图片</a> | </li>\n        <li><a href="javascript:;" data-pane="upload" class="js-modal-tab">新图片</a></li>\n    </ul>\n</div>\n<div class="tab-pane js-tab-pane-image">\n    <div class="modal-body module-image">\n        <div class="module-header module-loading">\n            <p class="help-inline">点击图片即可选中 <a class="js-update-image-list" href="javascript:void(0);">刷新</a></p>\n            <form class="form-search search-box">\n                <div class="input-append">\n                    <input class="input-small js-modal-search-input" type="text"/>\n                    <a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" data-action-type="search">搜</a>\n                </div>\n            </form>\n        </div>\n        <ul class="module-body clearfix">\n\n        </ul>\n    </div>\n    <div class="modal-footer">\n        <div class="modal-action pull-left">\n            <input type="button" class="btn btn-primary js-choose-image hide" value="确定使用">\n        </div>\n        <div class="pagenavi js-pagenavi"></div>\n    </div>\n</div>\n<div class="tab-pane js-tab-pane-upload hide">\n    <div class="modal-body">\n        <div class="get-web-img js-get-web-img"></div>\n        <div class="upload-local-img js-upload-local-img"></div>\n    </div>\n    <div class="modal-footer">\n        <div class="modal-action pull-right">\n            <input type="button" class="btn btn-primary js-upload-image" data-loading-text="上传中..." value="确定上传">\n        </div>\n    </div>\n</div>\n\n'
}), define("text!components/image/templates/image.html", [], function() {
    return '<div class="js-choose" title="<%= attachment_title %>">\n    <p class="image-size"><%= attachment_title.slice(0, 5) %><br><%= (attachment_size / 1000).toFixed(1) %> KB</p>\n    <img src="<%= utils.fullfillImage(thumb_url) %>" width="60" height="60" />\n    <div class="selected-style"><i class="icon-ok"></i></div>\n</div>\n'
}), define("components/image/views/image_view", ["require", "marionette", "text!components/image/templates/image.html", "core/utils"], function(e) {
    var t = e("marionette"), i = e("text!components/image/templates/image.html"), n = e("core/utils");
    return t.ItemView.extend({template: _.template(i),tagName: "li",events: {click: "select"},collectionEvents: {clear: "clear"},className: function() {
            return this.model.collection.isSelected(this.model) ? "selected" : ""
        },select: function() {
            this.model.collection.select(this.model), this.$el.toggleClass("selected"), 0 == this.model.collection.multiChoose && this.model.collection.trigger("image:choose:success")
        },clear: function() {
            this.$el.removeClass("selected")
        },templateHelpers: function() {
            return {utils: n}
        }})
}), define("components/image/views/image_list_view", ["require", "marionette", "text!components/image/templates/image_list.html", "components/image/views/image_view", "core/utils"], function(e) {
    var t = e("marionette"), i = e("text!components/image/templates/image_list.html"), n = e("components/image/views/image_view"), s = e("core/utils");
    return t.CompositeView.extend({className: "modal fade hide",ui: {pagenavi: ".js-pagenavi",chooseButton: ".js-choose-image",image: ".js-tab-pane-image",upload: ".js-tab-pane-upload",pane: ".tab-pane",uploadButton: ".js-upload-image",searchInput: ".js-modal-search-input"},events: {"click .fetch_page:not(.active)": "fetchPage","keydown .fetch_page.active": "fetchPage","click .js-choose-image": "chooseImage","click .js-update-image-list": "update","click .js-modal-tab": "tab","click .js-upload-image": "uploadImage","click .js-modal-search": "update","keydown .js-modal-search-input": "search"},template: _.template(i),itemView: n,itemViewContainer: ".module-body",collectionEvents: {sync: "renderPagenavi",select: "toggleChooseButton"},initialize: function(e) {
            this.opt = e.options, this.update();
            var t = this;
            this.listenTo(window.NC, "image:upload:always", function() {
                t.ui.uploadButton.button("reset")
            }), this.listenTo(this.collection, "image:choose:success", this.chooseImage)
        },tab: function(e) {
            var t = $(e.target), i = t.parent();
            i.addClass("active"), i.siblings("li").removeClass("active");
            var n = t.data("pane");
            this.ui.pane.addClass("hide"), this.ui[n].removeClass("hide")
        },fetchPage: function(e) {
            e.stopPropagation();
            var t = $(e.target);
            return "keydown" == e.type && e.keyCode !== s.keyCode.ENTER ? !0 : (this.collection._pageNumber = "keydown" == e.type ? +t.html() : t.data("page-num"), e.preventDefault(), void this._fetch())
        },update: function() {
            this._fetch()
        },search: function(e) {
            e.stopPropagation(), e.keyCode == s.keyCode.ENTER && (this._fetch(), e.preventDefault())
        },_fetch: function() {
            if (this.opt.onlyUpload !== !0) {
                this.$el.addClass("image-loading");
                var e = "";
                _.isFunction(this.ui.searchInput.val) && (e = this.ui.searchInput.val());
                var t = s.addParameter(this.collection.url, {p: this.collection._pageNumber,keyword: e}), i = this;
                this.collection.fetch({url: t,success: function() {
                        i.$el.removeClass("image-loading")
                    }})
            }
        },chooseImage: function() {
            var e = this.collection.getSelectedImages();
            window.NC.trigger("image:choose:success", e, {upload: !1}), this.clearAllSelected()
        },clearAllSelected: function() {
            this.collection.clearAllSelected(), this.$("li.selected").removeClass("selected")
        },renderPagenavi: function() {
            this.ui.pagenavi.html(this.collection.pagenavi)
        },toggleChooseButton: function(e) {
            e > 0 ? this.ui.chooseButton.removeClass("hide") : this.ui.chooseButton.addClass("hide")
        },uploadImage: function() {
            this.ui.uploadButton.button("loading"), window.NC.trigger("image:upload")
        }})
}), define("components/image/models/image", ["require", "backbone"], function(e) {
    var t = e("backbone");
    return t.Model.extend({})
}), define("components/image/collections/images", ["require", "backbone", "components/image/models/image", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/image/models/image"), n = e("core/utils");
    return t.Collection.extend({url: _global.url.www + "/showcase/attachment/alert.json?media_type=1&v=2",model: i,_pageNumber: 1,parse: function(e) {
            return 0 == e.errcode ? (this.pagenavi = e.data.page_view, e.data.data_list) : void n.errorNotify(e.errmsg || "出错啦。")
        },_selectedModel: [],_idAttribute: "attachment_id",getSelectedImages: function() {
            return this._selectedModel
        },select: function(e) {
            var t = this, i = {};
            i[this._idAttribute] = e.get(this._idAttribute);
            var n = _.where(this._selectedModel, i);
            n.length > 0 ? _.each(n, function(e) {
                t._selectedModel.splice(t._selectedModel.indexOf(e), 1)
            }) : this._selectedModel.push(e.attributes), this.trigger("select", this._selectedModel.length, e)
        },isSelected: function(e) {
            var t = {};
            t[this._idAttribute] = e.get(this._idAttribute);
            var i = _.where(this._selectedModel, t);
            return i.length > 0 ? !0 : !1
        },clearAllSelected: function() {
            this._selectedModel.length = 0, this.trigger("clear"), this.trigger("select", 0)
        }})
}), define("text!components/image/templates/image_download.html", [], function() {
    return '<div class="control-group">\n    <label class="control-label">网络图片：</label>\n    <div class="controls">\n        <input type="text" name="attachment_url" class="get-web-img-input js-web-img-input" placeholder="请贴入网络图片地址">\n        <input type="button" class="btn js-preview-img" data-loading-text="提取..." value="提取" / >\n    </div>\n    <div class="controls preview-container js-preview-download-img">\n    </div>\n</div>'
}), define("components/image/views/image_download", ["require", "marionette", "text!components/image/templates/image_download.html"], function(e) {
    var t = e("marionette"), i = e("text!components/image/templates/image_download.html");
    return t.ItemView.extend({tagName: "form",className: "form-horizontal",template: _.template(i),ui: {input: ".js-web-img-input",preview: ".js-preview-download-img"},events: {"click .js-preview-img": "previewImage"},previewImage: function(e) {
            var t = $(e.target), i = this, n = this.ui.input.val();
            if ("" === $.trim(n))
                return void this.ui.input.focus();
            t.button("loading"), this.model.set("src", n);
            var s = this.ui.preview, o = $("<img>").on("load", function() {
                s.removeClass("loading")
            }).attr("src", n);
            s.html(o).addClass("loading"), this.model.sync({always: function() {
                    t.button("reset")
                },success: function() {
                    i.clearDownload()
                }})
        },clearDownload: function() {
            this.ui.preview.empty(), this.ui.input.val("")
        }})
}), define("components/image/models/image_download", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = e("core/utils");
    return t.Model.extend({sync: function(e) {
            var t = this;
            if ("" != $.trim(this.get("src"))) {
                var n = {attachment_url: this.get("src"),media_type: "image",v: 2,mp_id: window._global.kdt_id};
                return $.post(window._global.url.img + "/download?format=json", n, function(t) {
                    return t.success ? (window.NC.trigger("image:download:success", [t.success]), void (e && _.isFunction(e.success) && e.success(t.success))) : void i.errorNotify("出错啦，请重试。")
                }, "json").always(function() {
                    e && _.isFunction(e.always) && e.always(), t.clearDownload()
                }).fail(function() {
                    i.errorNotify("网络出现错误啦。")
                }), !0
            }
        },clearDownload: function() {
            this.set("src", "")
        }})
}), define("text!components/image/templates/image_upload_item.html", [], function() {
    return '<img src="<%= src %>">\n<a href="javascript:;" class="close-modal small js-remove-image">×</a>'
}), define("components/image/views/image_upload_item", ["require", "marionette", "text!components/image/templates/image_upload_item.html"], function(e) {
    var t = e("marionette"), i = e("text!components/image/templates/image_upload_item.html");
    return t.ItemView.extend({template: _.template(i),tagName: "li",className: "upload-preview-img",events: {"click .js-remove-image": "removeImage"},removeImage: function() {
            this.model.destroy(), window.NC.trigger("image:number:change")
        }})
}), define("text!components/image/templates/image_upload.html", [], function() {
    return '<div class="control-group">\n    <label class="control-label">本地图片：</label>\n    <div class="controls preview-container js-preview-upload-img">\n    </div>\n    <div class="controls">\n        <div class="control-action">\n            <ul class="js-upload-image-list upload-image-list clearfix">\n                <li class="fileinput-button">\n                        <a class="fileinput-button-icon" href="javascript:;">+</a>\n                        <input class="js-fileupload-input fileupload" type="file" name="upload_file[]" data-url="http://img.koudaitong.com/uploadmultiple?format=json" multiple>\n                </li>\n            </ul>\n            <p class="help-desc">最大支持 <span class="js-max-size">1 MB</span> 的图片( jpg / gif / png )，不能选中大于 <span class="js-max-size">1 MB</span> 的图片</p>\n        </div>\n    </div>\n</div>'
}), define("components/image/views/image_upload", ["require", "marionette", "components/image/views/image_upload_item", "text!components/image/templates/image_upload.html"], function(e) {
    var t = e("marionette"), i = e("components/image/views/image_upload_item"), n = e("text!components/image/templates/image_upload.html");
    return t.CompositeView.extend({tagName: "form",className: "form-horizontal",template: _.template(n),itemView: i,itemViewContainer: ".js-upload-image-list",events: {"change .js-fileupload-input": "selectFile"},initialize: function() {
            this.listenTo(window.NC, "image:upload", this.upload)
        },appendBuffer: function(e, t) {
            e.$(".fileinput-button").before(t)
        },appendHtml: function(e, t) {
            e.isBuffering ? (e.elBuffer.appendChild(t.el), e._bufferedChildren.push(t)) : e.$(".fileinput-button").before(t.el)
        },selectFile: function(e) {
            var t = e.target.files, i = this;
            _.each(t, function(e) {
                var t = e.size / 1024;
                t > i.collection.maxSize || e.name.match(/(\.|\/)(gif|jpe?g|png)$/i) && i.previewAndAdd(e)
            }), $(e.target).val("")
        },previewAndAdd: function(e) {
            var t = new FileReader, i = this;
            t.onload = function(t) {
                i.collection.add({src: t.target.result,file: e}), window.NC.trigger("image:number:change")
            }, t.readAsDataURL(e)
        },upload: function() {
            this.collection.sync({success: function() {
                    this.render()
                }.bind(this)})
        }})
}), function(e) {
    "function" == typeof define && define.amd ? define("cookie", ["jquery"], e) : e(jQuery)
}(function(e) {
    function t(e) {
        return r.raw ? e : encodeURIComponent(e)
    }
    function i(e) {
        return r.raw ? e : decodeURIComponent(e)
    }
    function n(e) {
        return t(r.json ? JSON.stringify(e) : String(e))
    }
    function s(e) {
        0 === e.indexOf('"') && (e = e.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
        try {
            return e = decodeURIComponent(e.replace(a, " ")), r.json ? JSON.parse(e) : e
        } catch (t) {
        }
    }
    function o(t, i) {
        var n = r.raw ? t : s(t);
        return e.isFunction(i) ? i(n) : n
    }
    var a = /\+/g, r = e.cookie = function(s, a, l) {
        if (void 0 !== a && !e.isFunction(a)) {
            if (l = e.extend({}, r.defaults, l), "number" == typeof l.expires) {
                var c = l.expires, d = l.expires = new Date;
                d.setDate(d.getDate() + c)
            }
            return document.cookie = [t(s), "=", n(a), l.expires ? "; expires=" + l.expires.toUTCString() : "", l.path ? "; path=" + l.path : "", l.domain ? "; domain=" + l.domain : "", l.secure ? "; secure" : ""].join("")
        }
        for (var u = s ? void 0 : {}, p = document.cookie ? document.cookie.split("; ") : [], h = 0, m = p.length; m > h; h++) {
            var f = p[h].split("="), g = i(f.shift()), v = f.join("=");
            if (s && s === g) {
                u = o(v, a);
                break
            }
            s || void 0 === (v = o(v)) || (u[g] = v)
        }
        return u
    };
    r.defaults = {}, e.removeCookie = function(t, i) {
        return void 0 === e.cookie(t) ? !1 : (e.cookie(t, "", e.extend({}, i, {expires: -1})), !e.cookie(t))
    }
}), define("components/image/models/image_upload", ["require", "backbone"], function(e) {
    var t = e("backbone");
    return t.Model.extend({})
}), define("components/image/collections/image_upload", ["require", "cookie", "backbone", "components/image/models/image_upload", "core/utils"], function(e) {
    var t = (e("cookie"), e("backbone")), i = e("components/image/models/image_upload"), n = e("core/utils");
    return t.Collection.extend({model: i,url: "http://up.qiniu.com",uploadURL: _global.url.www + "/common/qiniu/upToken.json",eventPrefix: "image",sync: function(e) {
            var t = this;
            if (e = e || {}, this.length <= 0)
                return void window.NC.trigger(t.eventPrefix + ":upload:always");
            var i;
            $.post(this.uploadURL, {Scope: this.private ? _global.js.qn_private : _global.js.qn_public,mp_id: $.cookie("kdt_id")}).success(function(s) {
                n.parse(s, {success: function(n) {
                        i = n.uptoken, t.upload(i, e)
                    },fail: function() {
                        window.NC.trigger(t.eventPrefix + ":upload:always"), n.errorNotify("获取token失败，请重试。")
                    }})
            }).fail(function() {
                window.NC.trigger(t.eventPrefix + ":upload:always"), n.errorNotify("获取token失败，请重试。")
            })
        },upload: function(e, t) {
            var i = this, s = [];
            this.each(function(o) {
                var a = new $.Deferred, r = new FormData;
                r.append("token", e), r.append("file", o.get("file"));
                var l = o.get("file").name.split("."), c = "";
                l.length > 1 && (c = "." + l[l.length - 1]), r.append("x:ext", c), $.ajax({url: this.url,type: "post",data: r,dataType: "json",processData: !1,contentType: !1}).success(function(e) {
                    n.parse(e, {success: function(e) {
                            window.NC.trigger(i.eventPrefix + ":upload:success", [e]), i.clearDownload(o), _.isFunction(t.success) && t.success(), a.resolve()
                        },fail: function() {
                            a.reject()
                        }})
                }).fail(function() {
                    a.reject()
                }).always(function() {
                    window.NC.trigger(i.eventPrefix + ":upload:always")
                }), s.push(a)
            }.bind(this)), $.when.apply(void 0, s).promise().done(function() {
                n.successNotify("上传成功")
            }).fail(function() {
                n.errorNotify("上传失败，请重试")
            })
        },clearDownload: function(e) {
            e.destroy()
        }})
}), define("components/image/app", ["require", "core/event", "marionette", "components/image/views/image_list_view", "components/image/collections/images", "components/image/views/image_download", "components/image/models/image_download", "components/image/views/image_upload", "components/image/collections/image_upload"], function(e) {
    window.NC = e("core/event");
    var t = e("marionette"), i = e("components/image/views/image_list_view"), n = e("components/image/collections/images"), s = e("components/image/views/image_download"), o = e("components/image/models/image_download");
    ImageUploadView = e("components/image/views/image_upload"), ImageUploadCollection = e("components/image/collections/image_upload");
    var a = new t.Application;
    a.on("initialize:before", function() {
        $("body").append('<div class="js-image-app-container"></div>'), a.addRegions({container: ".js-image-app-container",imageUpload: ".js-upload-local-img",imageDownload: ".js-get-web-img"})
    });
    var r = new n, l = new o, c = new ImageUploadCollection;
    a.addInitializer(function() {
        a.container.show(new i({collection: r,options: a._options})), a._options.hideDownload || a.imageDownload.show(new s({model: l})), a.imageUpload.show(new ImageUploadView({collection: c}))
    }), a.on("show:image", function() {
        a.container.$el.find(".modal").modal("show");
        var e = a._options.tabIndex, t = a._options.onlyUpload, i = a.container.$el.find(".js-modal-tab");
        void 0 != e && i.eq(e).click();
        var n, s = a._options.maxSize;
        void 0 != s && (n = s % 1024 == 0 ? s / 1024 + " MB" : s + " KB", a.imageUpload.$el.find(".js-max-size").html(n)), t === !0 && (a.container.$el.find(".module-nav li").eq(0).hide(), i.eq(1).click())
    });
    var d = function(e, t) {
        a._options && _.isFunction(a._options.callback) && a._options.callback(e, t), a.container.$el.find(".modal").modal("hide")
    };
    return window.NC.on("image:upload:success", d), window.NC.on("image:download:success", d), window.NC.on("image:choose:success", d), window.NC.on("image:number:change", function() {
        c.multiUpload !== !0 && a.imageUpload.$el.find(".fileinput-button").toggle(0 === c.length)
    }), {initialize: function(e) {
            a._options = e, this._initialized || (a.start(), this._initialized = !0), r.multiChoose = e.multiChoose === !1 ? !1 : !0, c.multiUpload = e.multiUpload === !1 ? !1 : !0, c.uploadURL = e.uploadURL ? e.uploadURL : _global.url.www + "/common/qiniu/upToken.json", c.private = e.private ? !0 : !1, void 0 == e.maxSize ? e.maxSize = c.maxSize = 1024 : c.maxSize = e.maxSize, a.trigger("show:image")
        }}
}), define("text!components/wb_editor_v2/templates/editor.html", [], function() {
    return '<% if(mode !== \'bottom-action\') { %>\n<div class="misc top clearfix">\n    <div class="content-actions clearfix">\n    <% for(var idx = 0, len = modules.length; idx < len; idx++) { %>\n\n        <% if (modules[idx] === \'emotion\') { %>\n        <div class="editor-module insert-emotion">\n            <a class="js-open-emotion" data-action-type="emotion" href="javascript:;">表情</a>\n            <div class="emotion-wrapper">\n                <ul class="emotion-container clearfix"></ul>\n            </div>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'hyperlink\') { %>\n        <div class="editor-module insert-hyperlink">\n            <a class="js-open-hyperlink" data-action-type="hyperlink" href="javascript:;">插入链接</a>\n            <div class="hyperlink-wrapper"></div>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'articles\') { %>\n        <div class="editor-module insert-article">\n            <a class="js-open-articles" data-action-type="articles" href="javascript:;">选择图文</a>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'feature\') { %>\n        <div class="editor-module insert-feature">\n            <a class="js-open-feature" data-action-type="feature" data-complex-mode="true" href="javascript:;">微杂志及分类</a>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'goods\') { %>\n        <div class="editor-module insert-goods">\n            <a class="js-open-goods" data-action-type="goods" data-complex-mode="true" href="javascript:;">商品及分类</a>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'homepage\') { %>\n        <div class="editor-module insert-homepage">\n            <a class="js-open-homepage" data-action-type="homepage" data-complex-mode="true" href="javascript:;">店铺主页</a>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'usercenter\') { %>\n        <div class="editor-module insert-usercenter">\n            <a class="js-open-usercenter" data-action-type="usercenter" data-complex-mode="true" href="javascript:;">会员主页</a>\n        </div>\n        <% } %>\n\n\n        <% if (modules[idx] === \'picture\') { %>\n        <div class="editor-module insert-picture">\n            <a class="js-open-picture" data-action-type="picture" href="javascript:;">图片</a>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'image\') { %>\n        <div class="editor-module insert-image">\n            <a class="js-open-image" data-action-type="image" href="javascript:;">图片</a>\n        </div>\n        <% } %>\n\n        \n        <% if (modules[idx] === \'shortlink\') { %>\n        <div class="editor-module insert-shortlink">\n            <div class="js-reply-menu dropup hover dropdown--right add-reply-menu">\n                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">\n                    <span class="dropdown-current">插入链接</span>\n                    <i class="caret"></i>\n                </a>\n                <ul class="dropdown-menu">\n                    <li><a data-link-type="Feature" class="js-open-shortlink" href="javascript:;">微杂志及分类</a></li>\n                    <li><a data-link-type="Goods" class="js-open-shortlink" href="javascript:;">商品及分类</a></li>\n                    <li><a data-link-type="Homepage" class="js-open-shortlink" href="javascript:;">店铺主页</a></li>\n                </ul>\n            </div>\n        </div>\n        <% } %>\n\n    <% } %>\n    </div>\n</div>\n<% } %>\n\n<div class="content-wrapper">\n    <textarea class="js-txta" cols="50" rows="4"></textarea>\n    <div class="js-picture-container picture-container"></div>\n    <div class="complex-backdrop"><div class="js-complex-content complex-content"></div></div>\n</div>\n\n<% if(mode === \'bottom-action\') { %>\n<div class="misc clearfix">\n    <div class="content-actions clearfix">\n    <% for(var idx = 0, len = modules.length; idx < len; idx++) { %>\n\n        <% if (modules[idx] === \'emotion\') { %>\n        <div class="editor-module insert-emotion">\n            <a class="js-open-emotion" data-action-type="emotion" href="javascript:;">表情</a>\n            <div class="emotion-wrapper">\n                <ul class="emotion-container clearfix"></ul>\n            </div>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'hyperlink\') { %>\n        <div class="editor-module insert-hyperlink">\n            <a class="js-open-hyperlink" data-action-type="hyperlink" href="javascript:;">插入链接</a>\n            <div class="hyperlink-wrapper"></div>\n        </div>\n        <% } %>\n\n        <% if (modules[idx] === \'image\') { %>\n        <div class="editor-module insert-image">\n            <a class="js-open-image" data-action-type="image" href="javascript:;">图片</a>\n        </div>\n        <% } %>\n        \n        <% if (modules[idx] === \'articles\') { %>\n        <div class="editor-module insert-articles">\n            <a class="js-open-articles" data-action-type="articles" href="javascript:;">选择图文</a>\n        </div>\n        <% } %>\n\n        <% if (thumb != true) { %>\n\n            <% if (modules[idx] === \'feature\') { %>\n            <div class="editor-module insert-feature">\n                <a class="js-open-feature" data-action-type="feature" data-complex-mode="true" href="javascript:;">微杂志及分类</a>\n            </div>\n            <% } %>\n\n            <% if (modules[idx] === \'goods\') { %>\n            <div class="editor-module insert-goods">\n                <a class="js-open-goods" data-action-type="goods" data-complex-mode="true" href="javascript:;">商品及分类</a>\n            </div>\n            <% } %>\n\n            <% if (modules[idx] === \'homepage\') { %>\n            <div class="editor-module insert-homepage">\n                <a class="js-open-homepage" data-action-type="homepage" data-complex-mode="true" href="javascript:;">店铺主页</a>\n            </div>\n            <% } %>\n\n            <% if (modules[idx] === \'usercenter\') { %>\n            <div class="editor-module insert-usercenter">\n                <a class="js-open-usercenter" data-action-type="usercenter" data-complex-mode="true" href="javascript:;">会员主页</a>\n            </div>\n            <% } %>\n\n            <% if (modules[idx] === \'picture\') { %>\n            <div class="editor-module insert-picture">\n                <a class="js-open-picture" data-action-type="picture" href="javascript:;">图片</a>\n            </div>\n            <% } %>\n        <% } %>\n\n        \n        <% if (modules[idx] === \'shortlink\') { %>\n        <div class="editor-module insert-shortlink">\n            <div class="js-reply-menu dropup hover dropdown--right add-reply-menu">\n                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">\n                    <span class="dropdown-current">插入链接</span>\n                    <i class="caret"></i>\n                </a>\n                <ul class="dropdown-menu">\n                    <li><a data-link-type="Feature" class="js-open-shortlink" href="javascript:;">微杂志及分类</a></li>\n                    <li><a data-link-type="Goods" class="js-open-shortlink" href="javascript:;">商品及分类</a></li>\n                    <li><a data-link-type="Homepage" class="js-open-shortlink" href="javascript:;">店铺主页</a></li>\n                </ul>\n            </div>\n        </div>\n        <% } %>\n    <% } %>\n\n        <% if (thumb === true) { %>\n            <div class="editor-module">\n                <div class="js-reply-menu dropup hover dropdown--right add-reply-menu">\n                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">\n                        <span class="dropdown-current">其它</span>\n                        <i class="caret"></i>\n                    </a>\n                    <ul class="dropdown-menu">\n                    <% for(var idx = 0, len = modules.length; idx < len; idx++) { %>\n                        <% if (modules[idx] === \'feature\') { %>\n                        <li><a class="js-open-feature" data-action-type="feature" data-complex-mode="true" href="javascript:;">微杂志及分类</a></li>\n                        <% } %>\n                        <% if (modules[idx] === \'goods\') { %>\n                        <li><a class="js-open-goods" data-action-type="goods" data-complex-mode="true" href="javascript:;">商品及分类</a></li>\n                        <% } %>\n                        <% if (modules[idx] === \'homepage\') { %>\n                        <li><a class="js-open-homepage" data-action-type="homepage" data-complex-mode="true" href="javascript:;">店铺主页</a></li>\n                        <% } %>\n                        <% if (modules[idx] === \'usercenter\') { %>\n                        <li><a class="js-open-usercenter" data-action-type="usercenter" data-complex-mode="true" href="javascript:;">会员主页</a></li>\n                        <% } %>\n                    <% } %>\n                    </ul>\n                </div>\n            </div>\n        <% } %>\n\n        <div class="word-counter pull-right">还能输入 <i><%=charLimit %></i> 个字</div>\n    </div>\n</div>\n<% } else { %>\n<div class="misc clearfix">\n    <div class="content-actions clearfix">\n        <div class="word-counter pull-right">还能输入 <i><%=charLimit %></i> 个字</div>\n    </div>\n</div>\n<% } %>\n\n'
}), define("components/wb_editor_v2/models/data", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = e("core/utils"), n = t.Model.extend({defaults: function() {
            return {type: "text",pics: [],content: ""}
        },i18n: {text: "文本",picture: "图片",articles: "图文",feature: "微杂志",category: "微杂志分类",goods: "商品",tag: "商品标签",homepage: "商家主页",usercenter: "会员主页"},initialize: function(e, t) {
            var i = this, n = t.modules || [], s = "text," + n.join(",");
            s = s.replace("goods", "goods,tag").replace("feature", "feature,category"), i.modules = s.split(",")
        },validate: function(e) {
            var t = this, n = !0, s = e.type, o = t.modules;
            return o && !_(o).contains(s) ? (n = "此编辑器不支持插入“" + t.i18n[s] + "”类型的内容。", i.errorNotify(n), n) : void 0
        }});
    return n
}), define("components/wb_editor_v2/models/picture", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = e("core/utils"), n = t.Model.extend({idAttribute: "_id",defaults: {attachment_title: "",attachment_url: "",attachment_url_full: ""},initialize: function() {
            var e = this, t = i.fullfillImgqn(e.get("attachment_url"));
            e.set({attachment_url_full: t}, {slient: !0}), console.warn(e.toJSON())
        }});
    return n
}), define("components/wb_editor_v2/collections/picture_list", ["require", "backbone", "components/wb_editor_v2/models/picture", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/wb_editor_v2/models/picture"), n = e("core/utils"), s = t.Collection.extend({model: i,initialize: function(e) {
            var t = this;
            t.maxSize = e.maxSize || 9
        },getPics: function() {
            var e = this;
            return e.pluck("attachment_url")
        },checkMaxsize: function() {
            var e = this, t = e.length < e.maxSize, i = "最多支持 " + e.maxSize + " 张图片。";
            return t || (console.log(i), n.errorNotify(i)), t
        }});
    return s
}), define("text!components/wb_editor_v2/templates/picture.html", [], function() {
    return '<!-- <div class="picture-wrapper"> -->\n    <a href="javascript:;" class="close--circle js-delete-picture">&times;</a>\n    <a title="<%=attachment_title %>" class="picture loading" target="_blank" href="<%=attachment_url_full %>"><img src="<%=attachment_url_full %>!100x100.jpg" alt="" /></a>\n<!-- </div> -->\n'
}), define("components/wb_editor_v2/views/picture", ["require", "backbone", "text!components/wb_editor_v2/templates/picture.html", "core/utils"], function(e) {
    var t = e("backbone"), i = e("text!components/wb_editor_v2/templates/picture.html"), n = (e("core/utils"), t.View.extend({template: _.template(i),tagName: "div",className: "picture-wrapper",events: {"click .js-delete-picture": "deletePicture"},initialize: function() {
            var e = this;
            e.listenTo(e.model, "change", e.render), e.listenTo(e.model, "destroy", e.remove)
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.model.toJSON())), this.$("img").load(function() {
                $(this).parents(".picture").removeClass("loading").off("load")
            }), e
        },deletePicture: function() {
            var e = this, t = e.model.collection;
            e.model.destroy(), t.trigger("update:picture_list")
        }}));
    return n
}), define("components/wb_emotion/datas/default", ["require"], function() {
    var e = [{phrase: "[草泥马]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7a/shenshou_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7a/shenshou_thumb.gif",value: "[草泥马]",picid: ""}, {phrase: "[神马]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/60/horse2_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/60/horse2_thumb.gif",value: "[神马]",picid: ""}, {phrase: "[浮云]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/fuyun_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/fuyun_thumb.gif",value: "[浮云]",picid: ""}, {phrase: "[给力]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c9/geili_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c9/geili_thumb.gif",value: "[给力]",picid: ""}, {phrase: "[围观]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f2/wg_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f2/wg_thumb.gif",value: "[围观]",picid: ""}, {phrase: "[威武]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/vw_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/vw_thumb.gif",value: "[威武]",picid: ""}, {phrase: "[熊猫]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/panda_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/panda_thumb.gif",value: "[熊猫]",picid: ""}, {phrase: "[兔子]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/81/rabbit_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/81/rabbit_thumb.gif",value: "[兔子]",picid: ""}, {phrase: "[奥特曼]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/otm_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/otm_thumb.gif",value: "[奥特曼]",picid: ""}, {phrase: "[囧]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/15/j_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/15/j_thumb.gif",value: "[囧]",picid: ""}, {phrase: "[互粉]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/89/hufen_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/89/hufen_thumb.gif",value: "[互粉]",picid: ""}, {phrase: "[礼物]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c4/liwu_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c4/liwu_thumb.gif",value: "[礼物]",picid: ""}, {phrase: "[呵呵]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ac/smilea_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ac/smilea_thumb.gif",value: "[呵呵]",picid: ""}, {phrase: "[嘻嘻]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0b/tootha_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0b/tootha_thumb.gif",value: "[嘻嘻]",picid: ""}, {phrase: "[哈哈]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6a/laugh.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6a/laugh.gif",value: "[哈哈]",picid: ""}, {phrase: "[可爱]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/14/tza_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/14/tza_thumb.gif",value: "[可爱]",picid: ""}, {phrase: "[可怜]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/kl_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/kl_thumb.gif",value: "[可怜]",picid: ""}, {phrase: "[挖鼻屎]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a0/kbsa_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a0/kbsa_thumb.gif",value: "[挖鼻屎]",picid: ""}, {phrase: "[吃惊]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f4/cj_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f4/cj_thumb.gif",value: "[吃惊]",picid: ""}, {phrase: "[害羞]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/shamea_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/shamea_thumb.gif",value: "[害羞]",picid: ""}, {phrase: "[挤眼]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c3/zy_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c3/zy_thumb.gif",value: "[挤眼]",picid: ""}, {phrase: "[闭嘴]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/29/bz_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/29/bz_thumb.gif",value: "[闭嘴]",picid: ""}, {phrase: "[鄙视]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/71/bs2_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/71/bs2_thumb.gif",value: "[鄙视]",picid: ""}, {phrase: "[爱你]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/lovea_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/lovea_thumb.gif",value: "[爱你]",picid: ""}, {phrase: "[泪]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9d/sada_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9d/sada_thumb.gif",value: "[泪]",picid: ""}, {phrase: "[偷笑]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/19/heia_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/19/heia_thumb.gif",value: "[偷笑]",picid: ""}, {phrase: "[亲亲]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/8f/qq_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/8f/qq_thumb.gif",value: "[亲亲]",picid: ""}, {phrase: "[生病]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/b6/sb_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/b6/sb_thumb.gif",value: "[生病]",picid: ""}, {phrase: "[太开心]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/mb_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/mb_thumb.gif",value: "[太开心]",picid: ""}, {phrase: "[懒得理你]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/17/ldln_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/17/ldln_thumb.gif",value: "[懒得理你]",picid: ""}, {phrase: "[右哼哼]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/98/yhh_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/98/yhh_thumb.gif",value: "[右哼哼]",picid: ""}, {phrase: "[左哼哼]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/zhh_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/zhh_thumb.gif",value: "[左哼哼]",picid: ""}, {phrase: "[嘘]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a6/x_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a6/x_thumb.gif",value: "[嘘]",picid: ""}, {phrase: "[衰]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/cry.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/cry.gif",value: "[衰]",picid: ""}, {phrase: "[委屈]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/73/wq_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/73/wq_thumb.gif",value: "[委屈]",picid: ""}, {phrase: "[吐]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9e/t_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9e/t_thumb.gif",value: "[吐]",picid: ""}, {phrase: "[打哈欠]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f3/k_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f3/k_thumb.gif",value: "[打哈欠]",picid: ""}, {phrase: "[抱抱]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/27/bba_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/27/bba_thumb.gif",value: "[抱抱]",picid: ""}, {phrase: "[怒]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7c/angrya_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7c/angrya_thumb.gif",value: "[怒]",picid: ""}, {phrase: "[疑问]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/5c/yw_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/5c/yw_thumb.gif",value: "[疑问]",picid: ""}, {phrase: "[馋嘴]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a5/cza_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a5/cza_thumb.gif",value: "[馋嘴]",picid: ""}, {phrase: "[拜拜]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/88_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/88_thumb.gif",value: "[拜拜]",picid: ""}, {phrase: "[思考]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/e9/sk_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/e9/sk_thumb.gif",value: "[思考]",picid: ""}, {phrase: "[汗]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/24/sweata_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/24/sweata_thumb.gif",value: "[汗]",picid: ""}, {phrase: "[困]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7f/sleepya_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7f/sleepya_thumb.gif",value: "[困]",picid: ""}, {phrase: "[睡觉]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6b/sleepa_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6b/sleepa_thumb.gif",value: "[睡觉]",picid: ""}, {phrase: "[钱]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/90/money_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/90/money_thumb.gif",value: "[钱]",picid: ""}, {phrase: "[失望]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0c/sw_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0c/sw_thumb.gif",value: "[失望]",picid: ""}, {phrase: "[酷]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/cool_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/cool_thumb.gif",value: "[酷]",picid: ""}, {phrase: "[花心]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/8c/hsa_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/8c/hsa_thumb.gif",value: "[花心]",picid: ""}, {phrase: "[哼]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/49/hatea_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/49/hatea_thumb.gif",value: "[哼]",picid: ""}, {phrase: "[鼓掌]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/36/gza_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/36/gza_thumb.gif",value: "[鼓掌]",picid: ""}, {phrase: "[晕]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/dizzya_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/dizzya_thumb.gif",value: "[晕]",picid: ""}, {phrase: "[悲伤]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1a/bs_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1a/bs_thumb.gif",value: "[悲伤]",picid: ""}, {phrase: "[抓狂]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/62/crazya_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/62/crazya_thumb.gif",value: "[抓狂]",picid: ""}, {phrase: "[黑线]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/91/h_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/91/h_thumb.gif",value: "[黑线]",picid: ""}, {phrase: "[阴险]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/yx_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/yx_thumb.gif",value: "[阴险]",picid: ""}, {phrase: "[怒骂]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/89/nm_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/89/nm_thumb.gif",value: "[怒骂]",picid: ""}, {phrase: "[心]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/hearta_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/hearta_thumb.gif",value: "[心]",picid: ""}, {phrase: "[伤心]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ea/unheart.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ea/unheart.gif",value: "[伤心]",picid: ""}, {phrase: "[猪头]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/pig.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/pig.gif",value: "[猪头]",picid: ""}, {phrase: "[ok]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d6/ok_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d6/ok_thumb.gif",value: "[ok]",picid: ""}, {phrase: "[耶]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/ye_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/ye_thumb.gif",value: "[耶]",picid: ""}, {phrase: "[good]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/good_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/good_thumb.gif",value: "[good]",picid: ""}, {phrase: "[不要]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c7/no_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c7/no_thumb.gif",value: "[不要]",picid: ""}, {phrase: "[赞]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d0/z2_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d0/z2_thumb.gif",value: "[赞]",picid: ""}, {phrase: "[来]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/come_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/come_thumb.gif",value: "[来]",picid: ""}, {phrase: "[弱]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/sad_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/sad_thumb.gif",value: "[弱]",picid: ""}, {phrase: "[蜡烛]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/91/lazu_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/91/lazu_thumb.gif",value: "[蜡烛]",picid: ""}, {phrase: "[钟]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d3/clock_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d3/clock_thumb.gif",value: "[钟]",picid: ""}, {phrase: "[话筒]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1b/m_org.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1b/m_thumb.gif",value: "[话筒]",picid: ""}, {phrase: "[蛋糕]",type: "face",url: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6a/cake.gif",hot: !1,common: !0,category: "",icon: "http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6a/cake.gif",value: "[蛋糕]",picid: ""}];
    return e
}), define("components/wb_emotion/models/emotion", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = (e("core/utils"), t.Model.extend({defaults: {},initialize: function() {
        }}));
    return i
}), define("components/wb_emotion/collections/emotion_list", ["require", "backbone", "components/wb_emotion/models/emotion", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/wb_emotion/models/emotion"), n = (e("core/utils"), t.Collection.extend({model: i,initialize: function(e) {
            var t = this;
            t.options = e, t.listenTo(t, "selected:emotion", t.selectEmotion)
        },selectEmotion: function() {
        }}));
    return n
}), define("text!components/wb_emotion/templates/emotion.html", [], function() {
    return '<img src="<%=icon %>" alt="<%=value %>" title="<%=value %>">\n'
}), define("components/wb_emotion/views/emotion", ["require", "backbone", "text!components/wb_emotion/templates/emotion.html", "components/wb_emotion/models/emotion", "core/utils"], function(e) {
    var t = e("backbone"), i = e("text!components/wb_emotion/templates/emotion.html"), n = (e("components/wb_emotion/models/emotion"), e("core/utils"), t.View.extend({tagName: "li",template: _.template(i),events: {click: "selectEmotion"},initialize: function() {
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.model.toJSON())), e
        },selectEmotion: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.model.collection.trigger("selected:emotion", t.model)
        }}));
    return n
}), define("components/wb_emotion/views/emotion_list", ["require", "backbone", "components/wb_emotion/views/emotion", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/wb_emotion/views/emotion"), n = (e("core/utils"), t.View.extend({initialize: function() {
            var e = this;
            e.listenTo(e.collection, "add", e.addOne), e.listenTo(e.collection, "reset", e.addAll), e.listenTo(e.collection, "all", e.render)
        },render: function() {
            var e = this;
            return e
        },addOne: function(e) {
            var t = this, n = new i({model: e});
            t.$el.append(n.render().el)
        },addAll: function() {
            var e = this;
            e.$el.empty(), e.collection.each(this.addOne, this)
        }}));
    return n
}), define("components/wb_emotion/com", ["require", "backbone", "components/wb_emotion/datas/default", "components/wb_emotion/collections/emotion_list", "components/wb_emotion/views/emotion_list", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/wb_emotion/datas/default"), n = e("components/wb_emotion/collections/emotion_list"), s = e("components/wb_emotion/views/emotion_list"), o = (e("core/utils"), t.View.extend({events: {click: "stopPropagation"},initialize: function(e) {
            var t = this;
            t.config = e, t.render()
        },render: function() {
            var e = this;
            return e.emotionList = new n([]), e.container = e.$el.find(".emotion-container"), e.emotionListView = new s({el: e.container,collection: e.emotionList}), e.emotionList.reset(i), e.listenTo(e.emotionList, "selected:emotion", e.selectEmotion), e
        },selectEmotion: function(e) {
            var t = this;
            console.warn(e), t.trigger("selected:emotion", e.get("value")), t.hide()
        },show: function() {
            var e = this;
            e.$el.show().position({of: e.config.target,my: "center bottom",at: "center top"})
        },hide: function() {
            this.$el.hide()
        },toggle: function() {
            var e = this, t = e.$el.is(":hidden");
            t ? e.show() : e.hide()
        },stopPropagation: function(e) {
            e.stopPropagation()
        }}));
    return o
}), define("text!components/wb_hyperlink/templates/hyperlink.html", [], function() {
    return '<input class="js-txt input-large txt" type="text" placeholder="http://" />\n<button class="js-btn-hyperlink btn btn-primary">确定</button>\n'
}), define("components/wb_hyperlink/com", ["require", "backbone", "text!components/wb_hyperlink/templates/hyperlink.html", "core/utils"], function(e) {
    var t = e("backbone"), i = e("text!components/wb_hyperlink/templates/hyperlink.html"), n = e("core/utils"), s = t.View.extend({template: _.template(i),events: {"click .js-btn-hyperlink": "saveHyperlink","keypress .js-txt": "enterSave",click: "stopPropagation"},initialize: function(e) {
            var t = this;
            t.config = e, t.render()
        },render: function() {
            var e = this;
            return e.$el.html(e.template({})), e.txt = e.$el.find(".js-txt"), e
        },saveHyperlink: function(e) {
            e && e.preventDefault();
            var t = this, i = t.txt.val();
            return i = $.trim(i), "" === i ? (t.txt.focus(), !1) : (i = n.urlCheck(i), i = " " + i + " ", console.warn(i), t.trigger("save:hyperlink", i), t.txt.val(""), void t.hide())
        },enterSave: function() {
            var e = this;
            return event.which === n.keyCode.ENTER ? (e.saveHyperlink(), !1) : void 0
        },show: function() {
            var e = this;
            e.$el.show().position({of: e.config.target,my: "center bottom",at: "center top"}), e.txt.focus()
        },hide: function() {
            this.$el.hide()
        },toggle: function() {
            var e = this, t = e.$el.is(":hidden");
            t ? e.show() : e.hide()
        },stopPropagation: function(e) {
            e.stopPropagation()
        }});
    return s
}), define("components/homepage/com", ["require", "jquery", "underscore", "core/utils"], function(e) {
    var t = e("jquery"), i = e("underscore"), n = e("core/utils"), s = function() {
    };
    return s.prototype.get = function(e) {
        var s = window._global.url.www + "/showcase/homepage/detail.json";
        t.ajax({url: s,type: "GET",dataType: "json",timeout: 3e4}).done(function(t) {
            if (0 === t.code) {
                if (i.isFunction(e)) {
                    var s = t.data, o = window._global.url.wap + "/showcase/homepage?alias=" + t.data.alias;
                    e({id: s.id,alias: s.alias,type: "homepage",title: s.data[0].title,link: o,data_url: o})
                }
            } else
                n.errorNotify(t.msg)
        }).fail(function() {
            console.error("ERROR: fetch homepage link error."), n.errorNotify("出错鸟，获取店铺首页链接失败。")
        })
    }, s
}), define("components/usercenter/com", ["require", "jquery", "underscore", "core/utils"], function(e) {
    var t = e("jquery"), i = e("underscore"), n = e("core/utils"), s = function() {
    };
    return s.prototype.get = function(e) {
        var s = window._global.url.www + "/showcase/usercenter/detail.json";
        t.ajax({url: s,type: "GET",dataType: "json",timeout: 3e4}).done(function(t) {
            if (0 === t.code) {
                if (i.isFunction(e)) {
                    var s = t.data, o = window._global.url.wap + "/showcase/usercenter?alias=" + t.data.alias;
                    e({id: s.id,alias: s.alias,type: "usercenter",title: s.data[0].title,link: o,data_url: o})
                }
            } else
                n.errorNotify(t.msg)
        }).fail(function() {
            console.error("ERROR: fetch usercenter link error."), n.errorNotify("出错鸟，获取店铺首页链接失败。")
        })
    }, s
}), define("text!components/wb_editor_v2/templates/complex.html", [], function() {
    return '<% if (type === \'feature\') { %>\n<!-- 微杂志 -->\n<div class="ng ng-single">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">微杂志</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item view-more">\n        <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n            <span class="pull-left">阅读全文</span>\n            <span class="pull-right">&gt;</span>\n        </a>\n    </div>\n</div>\n\n<% } else if (type === \'category\') { %>\n<!-- 微杂志分类 -->\n<div class="ng ng-multiple-mini">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">微杂志分类</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n</div>\n\n<% } else if (type === \'goods\') { %>\n<!-- 商品 -->\n<div class="ng ng-single">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">商 品</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item view-more">\n        <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n            <span class="pull-left">阅读全文</span>\n            <span class="pull-right">&gt;</span>\n        </a>\n    </div>\n</div>\n\n<% } else if (type === \'tag\') { %>\n<!-- 商品标签 -->\n<div class="ng ng-multiple-mini">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">分 类</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n</div>\n\n<% } else if (type === \'articles\') { %>\n<!-- 图文 -->\n    <% if(content.reply && content.reply.length > 1) { %>\n        <div class="ng ng-multiple-mini">\n            <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n            <div class="ng-item">\n                <span class="label label-success">多条图文</span>\n                <div class="ng-title">\n                    <a href="<%=content.reply[0].link %>" target="_blank" class="new-window" title="<%-content.reply[0].title %>"><%=content.reply[0].title %></a>\n                </div>\n            </div>\n            <% for(var idx = 1, len = content.reply.length; idx < len; idx++) { %>\n            <div class="ng-item"></div>\n            <% } %>\n        </div>\n    <% } else { %>\n        <div class="ng ng-single">\n            <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n            <div class="ng-item">\n                <span class="label label-success">图 文</span>\n                <div class="ng-title">\n                    <a href="<%=content.reply[0].link %>" target="_blank" class="new-window" title="<%-content.reply[0].title %>"><%=content.reply[0].title %></a>\n                </div>\n            </div>\n            <div class="ng-item view-more">\n                <a href="<%=content.reply[0].link %>" target="_blank" class="clearfix new-window">\n                    <span class="pull-left">阅读全文</span>\n                    <span class="pull-right">&gt;</span>\n                </a>\n            </div>\n        </div>\n    <% } %>\n\n<% } else if (type === \'homepage\') { %>\n    <!-- 店铺主页 -->\n    <div class="ng ng-single">\n        <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n        <div class="ng-item">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="商家主页"><span class="label label-success">商家主页</span></a>\n        </div>\n        <div class="ng-item view-more">\n            <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n                <span class="pull-left">阅读全文</span>\n                <span class="pull-right">&gt;</span>\n            </a>\n        </div>\n    </div>\n<% } else if (type === \'usercenter\') { %>\n    <div class="ng ng-single">\n        <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n        <div class="ng-item">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="会员主页"><span class="label label-success">会员主页</span></a>\n        </div>\n        <div class="ng-item view-more">\n            <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n                <span class="pull-left">阅读全文</span>\n                <span class="pull-right">&gt;</span>\n            </a>\n        </div>\n    </div>\n<% } else if (type === \'image\') { %>\n    <div class="ng ng-single ng-image">\n        <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n        <a title="<%=content.reply.title %>" class="picture" target="_blank" href="<%=content.reply.link %>"><img src="<%=content.reply.link %>!100x100.jpg" alt="" /></a>\n    </div>\n<% } %>\n'
}), function(e, t) {
    function i(e, t) {
        return null === e ? "null" === t : void 0 === e ? "undefined" === t : e.is && e instanceof l ? "element" === t : Object.prototype.toString.call(e).toLowerCase().indexOf(t) > 7
    }
    function n(e) {
        var t, s, o, a, r, l, c, p, h;
        if (e instanceof n)
            return e;
        for (i(e, "array") || (e = String(e).replace(/\s/g, "").toLowerCase().match(/(?:\+,|[^,])+/g)), t = 0, s = e.length; s > t; ++t) {
            for (i(e[t], "array") || (e[t] = String(e[t]).match(/(?:\+\/|[^\/])+/g)), l = [], o = e[t].length; o--; ) {
                for (c = e[t][o], r = {jwertyCombo: String(c),shiftKey: !1,ctrlKey: !1,altKey: !1,metaKey: !1}, i(c, "array") || (c = String(c).toLowerCase().match(/(?:(?:[^\+])+|\+\+|^\+$)/g)), a = c.length; a--; )
                    "++" === c[a] && (c[a] = "+"), c[a] in u.mods ? r[d[u.mods[c[a]]]] = !0 : c[a] in u.keys ? r.keyCode = u.keys[c[a]] : p = c[a].match(/^\[([^-]+\-?[^-]*)-([^-]+\-?[^-]*)\]$/);
                if (i(r.keyCode, "undefined"))
                    if (p && p[1] in u.keys && p[2] in u.keys) {
                        for (p[2] = u.keys[p[2]], p[1] = u.keys[p[1]], h = p[1]; h < p[2]; ++h)
                            l.push({altKey: r.altKey,shiftKey: r.shiftKey,metaKey: r.metaKey,ctrlKey: r.ctrlKey,keyCode: h,jwertyCombo: String(c)});
                        r.keyCode = h
                    } else
                        r.keyCode = 0;
                l.push(r)
            }
            this[t] = l
        }
        return this.length = t, this
    }
    var s, o, a, r = e.document, l = e.jQuery || e.Zepto || e.ender || r, c = "keydown";
    l === r ? (s = function(e, t) {
        return e ? l.querySelector(e, t || l) : l
    }, o = function(e, t) {
        e.addEventListener(c, t, !1)
    }, a = function(e, t) {
        var i, n = r.createEvent("Event");
        n.initEvent(c, !0, !0);
        for (i in t)
            n[i] = t[i];
        return (e || l).dispatchEvent(n)
    }) : (s = function(e, t) {
        return l(e || r, t)
    }, o = function(e, t) {
        l(e).bind(c + ".jwerty", t)
    }, a = function(e, t) {
        l(e || r).trigger(l.Event(c, t))
    });
    for (var d = {16: "shiftKey",17: "ctrlKey",18: "altKey",91: "metaKey"}, u = {mods: {"⇧": 16,shift: 16,"⌃": 17,ctrl: 17,"⌥": 18,alt: 18,option: 18,"⌘": 91,meta: 91,cmd: 91,"super": 91,win: 91},keys: {"⌫": 8,backspace: 8,"⇥": 9,"⇆": 9,tab: 9,"↩": 13,"return": 13,enter: 13,"⌅": 13,pause: 19,"pause-break": 19,"⇪": 20,caps: 20,"caps-lock": 20,"⎋": 27,escape: 27,esc: 27,space: 32,"↖": 33,pgup: 33,"page-up": 33,"↘": 34,pgdown: 34,"page-down": 34,"⇟": 35,end: 35,"⇞": 36,home: 36,ins: 45,insert: 45,del: 46,"delete": 46,"←": 37,left: 37,"arrow-left": 37,"↑": 38,up: 38,"arrow-up": 38,"→": 39,right: 39,"arrow-right": 39,"↓": 40,down: 40,"arrow-down": 40,"*": 106,star: 106,asterisk: 106,multiply: 106,"+": 107,plus: 107,"-": 109,subtract: 109,"num-.": 110,"num-period": 110,"num-dot": 110,"num-full-stop": 110,"num-delete": 110,";": 186,semicolon: 186,"=": 187,equals: 187,",": 188,comma: 188,".": 190,period: 190,"full-stop": 190,"/": 191,slash: 191,"forward-slash": 191,"`": 192,tick: 192,"back-quote": 192,"[": 219,"open-bracket": 219,"\\": 220,"back-slash": 220,"]": 221,"close-bracket": 221,"'": 222,quote: 222,apostraphe: 222}}, p = 47, h = 0; ++p < 106; )
        u.keys[h] = p, u.keys["num-" + h] = p + 48, ++h;
    for (p = 111, h = 1; ++p < 136; )
        u.keys["f" + h] = p, ++h;
    for (p = 64; ++p < 91; )
        u.keys[String.fromCharCode(p).toLowerCase()] = p;
    var m = t.jwerty = {event: function(e, t, s) {
            if (i(t, "boolean")) {
                var o = t;
                t = function() {
                    return o
                }
            }
            e = new n(e);
            var a, r, l = 0, c = e.length - 1;
            return function(i) {
                return (r = m.is(e, i, l)) ? c > l ? void ++l : (a = t.call(s || this, i, r), a === !1 && i.preventDefault(), void (l = 0)) : void (l = m.is(e, i) ? 1 : 0)
            }
        },is: function(e, t, i) {
            e = new n(e), i = i || 0, e = e[i], t = t.originalEvent || t;
            for (var s = e.length, o = !1; s--; ) {
                o = e[s].jwertyCombo;
                for (var a in e[s])
                    "jwertyCombo" !== a && t[a] != e[s][a] && (o = !1);
                if (o !== !1)
                    return o
            }
            return o
        },key: function(t, n, a, r, l) {
            var c = i(a, "element") || i(a, "string") ? a : r, d = c === a ? e : a, u = c === a ? r : l;
            o(i(c, "element") ? c : s(c, u), m.event(t, n, d))
        },fire: function(e, t, o, r) {
            e = new n(e);
            var l = i(o, "number") ? o : r;
            a(i(t, "element") ? t : s(t, o), e[l || 0][0])
        },KEYS: u}
}(this, "undefined" != typeof module && module.exports ? module.exports : this), define("jwerty", ["jquery"], function() {
}), define("components/wb_editor_v2/views/editor", ["require", "jquery", "backbone", "components/modal/modal", "components/image/app", "text!components/wb_editor_v2/templates/editor.html", "components/wb_editor_v2/models/data", "components/wb_editor_v2/collections/picture_list", "components/wb_editor_v2/views/picture", "components/wb_emotion/com", "components/wb_hyperlink/com", "components/homepage/com", "components/usercenter/com", "text!components/wb_editor_v2/templates/complex.html", "core/utils", "jwerty"], function(e) {
    var t = e("jquery"), i = e("backbone"), n = e("components/modal/modal"), s = e("components/image/app"), o = e("text!components/wb_editor_v2/templates/editor.html"), a = e("components/wb_editor_v2/models/data"), r = e("components/wb_editor_v2/collections/picture_list"), l = e("components/wb_editor_v2/views/picture"), c = e("components/wb_emotion/com"), d = e("components/wb_hyperlink/com"), u = e("components/homepage/com"), p = e("components/usercenter/com"), h = e("text!components/wb_editor_v2/templates/complex.html"), m = e("core/utils");
    e("jwerty");
    var f = i.View.extend({template: _.template(o),complexTemplate: _.template(h),events: {"click .js-delete-complex": "deleteComplex","keyup .js-txta": "textUx",click: "hideMiscPops"},initialize: function(e) {
            var t = this;
            t.config = {}, t.setConfig(e), t.hideModules = [], t.listenTo(t, "update:counter", t.updateCounter), t.render(), t.init(), t.renderContent()
        },setConfig: function(e) {
            var t = this;
            _(t.config).extend(e);
            var i = t.model, n = i.get("data");
            t.charLimit = i.get("charLimit"), t.picLimit = i.get("picLimit"), t.hasSign = i.get("hasSign"), t.sign = "", t.hasSign && "text" === n.type && (t.sign = n.content)
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.model.toJSON())), e.$txta = e.$el.find(".js-txta"), e.txta = e.$txta[0], e.counter = e.$el.find(".word-counter i"), e.pictureContainer = e.$el.find(".js-picture-container"), e.complexBackdrop = e.$el.find(".complex-backdrop"), e.complexContent = e.$el.find(".js-complex-content"), e.focusTxta(), e
        },init: function() {
            var e = this;
            e.initModules(), e.initEditorData(), e.initShortcuts()
        },renderContent: function() {
            var e = this, t = e.getContent(), i = t.type;
            "text" === i ? (e.switchToText(), e.setText(t.content), e.renderPicture(t.pics)) : e.renderComplex(t)
        },initEditorData: function() {
            var e = this, t = e.model;
            e.editorData = new a(t.get("data"), {modules: t.get("modules")}), e.listenTo(e.editorData, "change", e.renderContent)
        },initShortcuts: function() {
            var e = this;
            e.$txta.bind("keydown", jwerty.event("ctrl+enter", function() {
                e.trigger("ctrl+enter:txta")
            }))
        },initModules: function() {
            var e, t, i = this, n = i.model.get("modules"), s = i.model.get("supportModules"), o = {};
            _.each(n, function(n) {
                _.has(s, n) && (e = s[n], t = i["init" + e], _.isFunction(t) && t.apply(i), o["click .js-open-" + n] = "toggle" + e)
            }), _(i.events).extend(o)
        },textUx: function() {
            var e = this, t = e.$txta.val();
            e.hasRemain(t), e.updateData({type: "text",content: t}, {silent: !0})
        },hasRemain: function(e) {
            var t = this, i = m.wbLength(e), n = t.charLimit - i;
            return t.trigger("update:counter", n), n >= 0 ? !0 : !1
        },updateData: function(e, t) {
            var i = this, n = i.model.get("data");
            _(n).extend(e);
            var s = {validate: !0};
            _(s).extend(t), i.editorData.set(n, s)
        },initEmotion: function() {
            var e = this;
            e.emotionTrigger = e.$el.find(".js-open-emotion"), e.emotionWrapper = e.$el.find(".emotion-wrapper")
        },_initEmotion: function() {
            var e = this;
            e.wbEmotion = new c({target: e.emotionTrigger,el: e.emotionWrapper}), e.hideModules.push(e.wbEmotion), e.listenTo(e.wbEmotion, "selected:emotion", e.insertText)
        },initHyperlink: function() {
            var e = this;
            e.hyperlinkTrigger = e.$el.find(".js-open-hyperlink"), e.hyperlinkWrapper = e.$el.find(".hyperlink-wrapper"), e.wbHyperlink = new d({target: e.hyperlinkTrigger,el: e.hyperlinkWrapper}), e.hideModules.push(e.wbHyperlink), e.listenTo(e.wbHyperlink, "save:hyperlink", e.insertText)
        },initArticles: function() {
        },initHomepage: function() {
            var e = this;
            e.homepage = new u, e.usercenter = new p
        },initPicture: function() {
            var e = this, t = e.pictureList = new r([]);
            e.listenTo(t, "add", e.addOnePicture), e.listenTo(t, "reset", e.addAllPicture), e.listenTo(t, "update:picture_list", e.updatePics)
        },setText: function(e) {
            var t = this;
            t.$txta.val(e).trigger("keyup")
        },insertText: function(e) {
            var t = this;
            console.log("insertText", e), m.insertText(t.txta, e), t.$txta.trigger("keyup")
        },updateCounter: function(e) {
            var t = this;
            t.counter.html(e), t.counter.toggleClass("error", 0 > e)
        },toggleHyperlink: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.switchToText(), t.hideMiscPops(), t.wbHyperlink.toggle()
        },toggleEmotion: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.wbEmotion || t._initEmotion(), t.switchToText(), t.hideMiscPops(), t.wbEmotion.toggle()
        },toggleArticles: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.articleModal = n.initialize({type: "articles"}).setChooseItemCallback(function(e) {
                console.warn("------------- choosed article: ", e), t.insertComplex({type: "articles",content: {_id: e.id,reply: e.news}})
            }), t.articleModal.modal("show")
        },togglePicture: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.pictureModal = s.initialize({multiChoose: !1,callback: function(e) {
                    e = e[0], console.warn("------------- choosed picture: ", e), t.switchToText(), t.insertPicture(e)
                }})
        },toggleImage: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.imageModal = n.initialize({type: "image"}).setChooseItemCallback(function(e) {
                t.insertComplex({type: e.type,content: {_id: e.id,reply: {title: e.attachment_title,link: m.fullfillImgqn(e.attachment_url)}}})
            }), t.imageModal.modal("show")
        },toggleShortlink: function(e) {
            var i = this, n = t(e.target), s = n.data("link-type");
            i.homepage || i.initHomepage(), i["toggle" + s]()
        },toggleFeature: function(e) {
            var i = this, s = !1;
            e && (s = t(e.target).data("complex-mode")), i.featureModal = n.initialize({type: "feature"}).setChooseItemCallback(function(e) {
                console.warn("------------- choosed feature: ", e), s ? i.insertComplex({type: e.type,content: {_id: e.id,reply: {title: e.title,link: e.data_url}}}) : (i.switchToText(), i.insertText(" " + e.data_url + " "))
            }), i.featureModal.modal("show")
        },toggleGoods: function(e) {
            var i = this, s = !1;
            e && (s = t(e.target).data("complex-mode")), i.goodsModal = n.initialize({type: "goods"}).setChooseItemCallback(function(e) {
                console.warn("------------- choosed goods: ", e), s ? i.insertComplex({type: e.type,content: {_id: e.id,reply: {title: e.title,link: e.data_url}}}) : (i.switchToText(), i.insertText(" " + e.data_url + " "))
            }), i.goodsModal.modal("show")
        },toggleHomepage: function(e) {
            var i = this, n = !1;
            return e && (n = t(e.target).data("complex-mode")), "homepage" === i.model.get("data").type ? !1 : void i.homepage.get(function(e) {
                n ? i.insertComplex({type: e.type,content: {_id: e.id,reply: {title: e.title,link: e.data_url}}}) : (i.switchToText(), i.insertText(" " + e.data_url + " "))
            })
        },toggleUsercenter: function() {
            var e = this, i = !1;
            return event && (i = t(event.target).data("complex-mode")), "usercenter" === e.model.get("data").type ? !1 : void e.usercenter.get(function(t) {
                i ? e.insertComplex({type: t.type,content: {_id: t.id,reply: {title: t.title,link: t.data_url}}}) : (e.switchToText(), e.insertText(" " + t.data_url + " "))
            })
        },insertComplex: function(e) {
            var t = this;
            t.renderComplex(e), t.updateData(e), t.$txta.focus()
        },renderComplex: function(e) {
            var t = this;
            t.showBackdrop();
            var i = t.complexTemplate(e);
            t.complexContent.empty(), t.complexContent.append(i)
        },insertPicture: function(e) {
            var t = this;
            t.pictureList && (t.picLimit > 1 ? t.pictureList.checkMaxsize() && t.pictureList.add(e) : t.pictureList.reset(e), t.pictureList.trigger("update:picture_list"))
        },addOnePicture: function(e) {
            var t = this, i = new l({model: e});
            t.pictureContainer.append(i.render().el)
        },addAllPicture: function() {
            var e = this;
            e.pictureContainer.empty(), e.pictureList.each(e.addOnePicture, e)
        },updatePics: function() {
            var e = this, t = e.pictureList.getPics();
            e.updateData({pics: t}, {silent: !0})
        },renderPicture: function(e) {
            for (var t = this, i = 0, n = e.length; n > i; i++)
                e[i] = {attachment_url: e[i]};
            t.insertPicture(e)
        },existedComplex: function() {
            var e = this, t = e.editorData.toJSON();
            "text" !== t.type && e.renderComplex(t)
        },showBackdrop: function() {
            this.complexBackdrop.show()
        },hideBackdrop: function() {
            this.complexBackdrop.hide()
        },focusTxta: function() {
            var e = this;
            e.$txta.focus()
        },clearTxta: function() {
            var e = this;
            e.$txta.val(e.hasSign ? e.sign : ""), e.resetData()
        },clearPicture: function() {
            var e = this;
            e.pictureList && e.pictureList.reset([])
        },resetData: function() {
            var e = this;
            e.updateData({type: "text",content: e.sign || "",pics: []})
        },deleteComplex: function() {
            var e = this;
            e.complexContent.empty(), e.hideBackdrop(), e.textUx(), e.focusTxta()
        },switchToText: function() {
            var e = this;
            e.deleteComplex()
        },hideMiscPops: function() {
            var e = this;
            _(e.hideModules).each(function(e) {
                e.hide()
            })
        },clear: function() {
            var e = this;
            e.deleteComplex(), e.clearTxta(), e.clearPicture()
        },getContent: function() {
            var e = this, t = e.editorData.toJSON();
            return "text" === t.type && m.wbLength(t.content) > e.charLimit && (e.flashAnimate(), e.focusTxta()), t
        },flashAnimate: function() {
            var e = this;
            e.$txta.addClass("animated flashError"), window.clearTimeout(e.flashTimer), e.flashTimer = window.setTimeout(function() {
                e.$txta.removeClass("flashError")
            }, 2e3)
        }});
    return f
}), define("components/wb_editor_v2/com", ["require", "backbone", "components/wb_editor_v2/models/editor", "components/wb_editor_v2/views/editor", "components/wb_emotion/com", "components/wb_hyperlink/com", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/wb_editor_v2/models/editor"), n = e("components/wb_editor_v2/views/editor"), s = (e("components/wb_emotion/com"), e("components/wb_hyperlink/com"), e("core/utils"), t.View.extend({initialize: function(e) {
            var t = this;
            null == e.config.thumb && (e.config.thumb = !1);
            var s = t.config = e.config, o = t.editor = new i(s);
            t.editorView = new n({el: t.el,model: o}), t.listenTo(t.editorView, "ctrl+enter:txta", t.shortcuts)
        },render: function() {
            var e = this;
            return e
        },focus: function() {
            var e = this;
            e.editorView.focusTxta()
        },shortcuts: function() {
            var e = this;
            e.trigger("ctrl+enter:txta")
        },updateContent: function(e) {
            var t = this;
            t.editorView.updateData(e)
        },insertText: function(e) {
            var t = this;
            t.editorView.insertText(e)
        },setContent: function() {
        },getContent: function() {
            var e = this, t = e.editorView.getContent();
            return t
        },clear: function() {
            var e = this;
            e.editorView.clear()
        }}));
    return s
}), define("components/pop/reply", ["require", "backbone", "components/pop/base", "text!components/pop/templates/reply.html", "components/wb_editor_v2/com", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/reply.html"), n = e("components/wb_editor_v2/com");
        e("core/utils")
    }
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-close": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            var i = this;
            t.prototype.initialize.call(i, e), i.data = e.data
        },render: function() {
            var e = this, t = e.data;
            return e.$el.html(e.template(t)), e.wbEditorEle = e.$el.find(".wb-sender__input"), e.wbEditor = new n({config: {modules: ["emotion", "hyperlink", "articles", "feature", "goods", "homepage"],data: t},el: e.wbEditorEle}), e
        },positioning: function() {
            var e = this, t = e.options.className;
            e.el.className = t, e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center"} : {of: e.target,my: "left center",at: "right center"}), e.wbEditor.focus()
        },setData: function(e) {
            var t = this;
            t.wbEditor.updateContent(e)
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className;
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },triggerCallback: function() {
            var e = this, t = e.wbEditor.getContent();
            return t.content ? (e.callback.call(e, t), e.hide(), void e.wbEditor.clear()) : (e.wbEditor.focus(), !1)
        }})
}), define("text!components/pop/templates/normal_reply.html", [], function() {
    return '<div class="arrow"></div>\n<a href="javascript:;" class="close--circle js-close">&times;</a>\n<div class="popover-inner popover-normal-reply">\n    <div class="popover-content">\n        <div class="form-horizontal">\n            <h4>一般信息：</h4>\n            <div class="wb-sender">\n                <div class="wb-sender__inner">\n                    <div class="wb-sender__input">\n                    </div>\n                    <div class="wb-sender__actions">\n                        <button class="js-btn-confirm btn btn-primary">确定</button>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("components/pop/normal_reply", ["require", "backbone", "components/pop/base", "text!components/pop/templates/normal_reply.html", "components/wb_editor_v2/com", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/normal_reply.html"), n = e("components/wb_editor_v2/com");
        e("core/utils")
    }
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-close": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e.wbEditorEle = e.$el.find(".wb-sender__input"), e.wbEditor = new n({config: {modules: ["emotion", "hyperlink"],data: {type: "text",content: e.data.content}},el: e.wbEditorEle}), e
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center"} : {of: e.target,my: "left center",at: "right center"}), e.el.className = e.options.className
        },setData: function(e) {
            var t = this, i = {data: {type: e.type,content: e.content}};
            t.wbEditor.updateContent(i)
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className;
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },triggerCallback: function() {
            var e = this, t = e.wbEditor.getContent();
            return t.content ? (e.callback.call(e, t), e.hide(), void e.wbEditor.clear()) : (e.wbEditor.focus(), !1)
        }})
}), define("text!components/pop/templates/url.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner">\n    <div class="popover-content">\n        <div class="form-inline">\n            <div class="input-append">\n                <input type="text" class="txt js-url-placeholder url-placeholder" readonly value="<%= url %>">\n                <button type="button" class="btn js-btn-copy" data-clipboard-text="<%= url %>">复制</button>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), function() {
    function e(e) {
        return e.replace(/,/g, ".").replace(/[^0-9\.]/g, "")
    }
    function t(t) {
        return parseFloat(e(t)) >= 10
    }
    var i, n = {bridge: null,version: "0.0.0",disabled: null,outdated: null,ready: null}, s = {}, o = 0, a = {}, r = 0, l = {}, c = null, d = null, u = function() {
        var e, t, i, n, s = "ZeroClipboard.swf";
        if (document.currentScript && (n = document.currentScript.src))
            ;
        else {
            var o = document.getElementsByTagName("script");
            if ("readyState" in o[0])
                for (e = o.length; e-- && ("interactive" !== o[e].readyState || !(n = o[e].src)); )
                    ;
            else if ("loading" === document.readyState)
                n = o[o.length - 1].src;
            else {
                for (e = o.length; e--; ) {
                    if (i = o[e].src, !i) {
                        t = null;
                        break
                    }
                    if (i = i.split("#")[0].split("?")[0], i = i.slice(0, i.lastIndexOf("/") + 1), null == t)
                        t = i;
                    else if (t !== i) {
                        t = null;
                        break
                    }
                }
                null !== t && (n = t)
            }
        }
        return n && (n = n.split("#")[0].split("?")[0], s = n.slice(0, n.lastIndexOf("/") + 1) + s), s
    }(), p = function() {
        var e = /\-([a-z])/g, t = function(e, t) {
            return t.toUpperCase()
        };
        return function(i) {
            return i.replace(e, t)
        }
    }(), h = function(e, t) {
        var i, n, s;
        return window.getComputedStyle ? i = window.getComputedStyle(e, null).getPropertyValue(t) : (n = p(t), i = e.currentStyle ? e.currentStyle[n] : e.style[n]), "cursor" !== t || i && "auto" !== i || (s = e.tagName.toLowerCase(), "a" !== s) ? i : "pointer"
    }, m = function(e) {
        e || (e = window.event);
        var t;
        this !== window ? t = this : e.target ? t = e.target : e.srcElement && (t = e.srcElement), z.activate(t)
    }, f = function(e, t, i) {
        e && 1 === e.nodeType && (e.addEventListener ? e.addEventListener(t, i, !1) : e.attachEvent && e.attachEvent("on" + t, i))
    }, g = function(e, t, i) {
        e && 1 === e.nodeType && (e.removeEventListener ? e.removeEventListener(t, i, !1) : e.detachEvent && e.detachEvent("on" + t, i))
    }, v = function(e, t) {
        if (!e || 1 !== e.nodeType)
            return e;
        if (e.classList)
            return e.classList.contains(t) || e.classList.add(t), e;
        if (t && "string" == typeof t) {
            var i = (t || "").split(/\s+/);
            if (1 === e.nodeType)
                if (e.className) {
                    for (var n = " " + e.className + " ", s = e.className, o = 0, a = i.length; a > o; o++)
                        n.indexOf(" " + i[o] + " ") < 0 && (s += " " + i[o]);
                    e.className = s.replace(/^\s+|\s+$/g, "")
                } else
                    e.className = t
        }
        return e
    }, _ = function(e, t) {
        if (!e || 1 !== e.nodeType)
            return e;
        if (e.classList)
            return e.classList.contains(t) && e.classList.remove(t), e;
        if (t && "string" == typeof t || void 0 === t) {
            var i = (t || "").split(/\s+/);
            if (1 === e.nodeType && e.className)
                if (t) {
                    for (var n = (" " + e.className + " ").replace(/[\n\t]/g, " "), s = 0, o = i.length; o > s; s++)
                        n = n.replace(" " + i[s] + " ", " ");
                    e.className = n.replace(/^\s+|\s+$/g, "")
                } else
                    e.className = ""
        }
        return e
    }, b = function() {
        var e, t, i, n = 1;
        return "function" == typeof document.body.getBoundingClientRect && (e = document.body.getBoundingClientRect(), t = e.right - e.left, i = document.body.offsetWidth, n = Math.round(t / i * 100) / 100), n
    }, y = function(e, t) {
        var i = {left: 0,top: 0,width: 0,height: 0,zIndex: T(t) - 1};
        if (e.getBoundingClientRect) {
            var n, s, o, a = e.getBoundingClientRect();
            "pageXOffset" in window && "pageYOffset" in window ? (n = window.pageXOffset, s = window.pageYOffset) : (o = b(), n = Math.round(document.documentElement.scrollLeft / o), s = Math.round(document.documentElement.scrollTop / o));
            var r = document.documentElement.clientLeft || 0, l = document.documentElement.clientTop || 0;
            i.left = a.left + n - r, i.top = a.top + s - l, i.width = "width" in a ? a.width : a.right - a.left, i.height = "height" in a ? a.height : a.bottom - a.top
        }
        return i
    }, w = function(e, t) {
        var i = null == t || t && t.cacheBust === !0 && t.useNoCache === !0;
        return i ? (-1 === e.indexOf("?") ? "?" : "&") + "noCache=" + (new Date).getTime() : ""
    }, k = function(e) {
        var t, i, n, s = [], o = [], a = [];
        if (e.trustedOrigins && ("string" == typeof e.trustedOrigins ? o.push(e.trustedOrigins) : "object" == typeof e.trustedOrigins && "length" in e.trustedOrigins && (o = o.concat(e.trustedOrigins))), e.trustedDomains && ("string" == typeof e.trustedDomains ? o.push(e.trustedDomains) : "object" == typeof e.trustedDomains && "length" in e.trustedDomains && (o = o.concat(e.trustedDomains))), o.length)
            for (t = 0, i = o.length; i > t; t++)
                if (o.hasOwnProperty(t) && o[t] && "string" == typeof o[t]) {
                    if (n = D(o[t]), !n)
                        continue;
                    if ("*" === n) {
                        a = [n];
                        break
                    }
                    a.push.apply(a, [n, "//" + n, window.location.protocol + "//" + n])
                }
        return a.length && s.push("trustedOrigins=" + encodeURIComponent(a.join(","))), "string" == typeof e.jsModuleId && e.jsModuleId && s.push("jsModuleId=" + encodeURIComponent(e.jsModuleId)), s.join("&")
    }, x = function(e, t, i) {
        if ("function" == typeof t.indexOf)
            return t.indexOf(e, i);
        var n, s = t.length;
        for ("undefined" == typeof i ? i = 0 : 0 > i && (i = s + i), n = i; s > n; n++)
            if (t.hasOwnProperty(n) && t[n] === e)
                return n;
        return -1
    }, C = function(e) {
        if ("string" == typeof e)
            throw new TypeError("ZeroClipboard doesn't accept query strings.");
        return e.length ? e : [e]
    }, j = function(e, t, i, n) {
        n ? window.setTimeout(function() {
            e.apply(t, i)
        }, 0) : e.apply(t, i)
    }, T = function(e) {
        var t, i;
        return e && ("number" == typeof e && e > 0 ? t = e : "string" == typeof e && (i = parseInt(e, 10)) && !isNaN(i) && i > 0 && (t = i)), t || ("number" == typeof R.zIndex && R.zIndex > 0 ? t = R.zIndex : "string" == typeof R.zIndex && (i = parseInt(R.zIndex, 10)) && !isNaN(i) && i > 0 && (t = i)), t || 0
    }, S = function(e, t) {
        if (e && t !== !1 && "undefined" != typeof console && console && (console.warn || console.log)) {
            var i = "`" + e + "` is deprecated. See docs for more info:\n    https://github.com/zeroclipboard/zeroclipboard/blob/master/docs/instructions.md#deprecations";
            console.warn ? console.warn(i) : console.log(i)
        }
    }, $ = function() {
        var e, t, i, n, s, o, a = arguments[0] || {};
        for (e = 1, t = arguments.length; t > e; e++)
            if (null != (i = arguments[e]))
                for (n in i)
                    if (i.hasOwnProperty(n)) {
                        if (s = a[n], o = i[n], a === o)
                            continue;
                        void 0 !== o && (a[n] = o)
                    }
        return a
    }, D = function(e) {
        if (null == e || "" === e)
            return null;
        if (e = e.replace(/^\s+|\s+$/g, ""), "" === e)
            return null;
        var t = e.indexOf("//");
        e = -1 === t ? e : e.slice(t + 2);
        var i = e.indexOf("/");
        return e = -1 === i ? e : -1 === t || 0 === i ? null : e.slice(0, i), e && ".swf" === e.slice(-4).toLowerCase() ? null : e || null
    }, M = function() {
        var e = function(e, t) {
            var i, n, s;
            if (null != e && "*" !== t[0] && ("string" == typeof e && (e = [e]), "object" == typeof e && "length" in e))
                for (i = 0, n = e.length; n > i; i++)
                    if (e.hasOwnProperty(i) && (s = D(e[i]))) {
                        if ("*" === s) {
                            t.length = 0, t.push("*");
                            break
                        }
                        -1 === x(s, t) && t.push(s)
                    }
        }, t = {always: "always",samedomain: "sameDomain",never: "never"};
        return function(i, n) {
            var s, o = n.allowScriptAccess;
            if ("string" == typeof o && (s = o.toLowerCase()) && /^always|samedomain|never$/.test(s))
                return t[s];
            var a = D(n.moviePath);
            null === a && (a = i);
            var r = [];
            e(n.trustedOrigins, r), e(n.trustedDomains, r);
            var l = r.length;
            if (l > 0) {
                if (1 === l && "*" === r[0])
                    return "always";
                if (-1 !== x(i, r))
                    return 1 === l && i === a ? "sameDomain" : "always"
            }
            return "never"
        }
    }(), N = function(e) {
        if (null == e)
            return [];
        if (Object.keys)
            return Object.keys(e);
        var t = [];
        for (var i in e)
            e.hasOwnProperty(i) && t.push(i);
        return t
    }, E = function(e) {
        if (e)
            for (var t in e)
                e.hasOwnProperty(t) && delete e[t];
        return e
    }, I = function() {
        var e = !1;
        if ("boolean" == typeof n.disabled)
            e = n.disabled === !1;
        else {
            if ("function" == typeof ActiveXObject)
                try {
                    new ActiveXObject("ShockwaveFlash.ShockwaveFlash") && (e = !0)
                } catch (t) {
                }
            !e && navigator.mimeTypes["application/x-shockwave-flash"] && (e = !0)
        }
        return e
    }, z = function(e, t) {
        return this instanceof z ? (this.id = "" + o++, a[this.id] = {instance: this,elements: [],handlers: {}}, e && this.clip(e), "undefined" != typeof t && (S("new ZeroClipboard(elements, options)", R.debug), z.config(t)), this.options = z.config(), "boolean" != typeof n.disabled && (n.disabled = !I()), void (n.disabled === !1 && n.outdated !== !0 && null === n.bridge && (n.outdated = !1, n.ready = !1, P()))) : new z(e, t)
    };
    z.prototype.setText = function(e) {
        return e && "" !== e && (s["text/plain"] = e, n.ready === !0 && n.bridge && n.bridge.setText(e)), this
    }, z.prototype.setSize = function(e, t) {
        return n.ready === !0 && n.bridge && n.bridge.setSize(e, t), this
    };
    var O = function(e) {
        n.ready === !0 && n.bridge && n.bridge.setHandCursor(e)
    };
    z.prototype.destroy = function() {
        this.unclip(), this.off(), delete a[this.id]
    };
    var A = function() {
        var e, t, i, n = [], s = N(a);
        for (e = 0, t = s.length; t > e; e++)
            i = a[s[e]].instance, i && i instanceof z && n.push(i);
        return n
    };
    z.version = "1.3.2";
    var R = {swfPath: u,trustedDomains: window.location.host ? [window.location.host] : [],cacheBust: !0,forceHandCursor: !1,zIndex: 999999999,debug: !0,title: null,autoActivate: !0};
    z.config = function(e) {
        "object" == typeof e && null !== e && $(R, e);
        {
            if ("string" != typeof e || !e) {
                var t = {};
                for (var i in R)
                    R.hasOwnProperty(i) && (t[i] = "object" == typeof R[i] && null !== R[i] ? "length" in R[i] ? R[i].slice(0) : $({}, R[i]) : R[i]);
                return t
            }
            if (R.hasOwnProperty(e))
                return R[e]
        }
    }, z.destroy = function() {
        z.deactivate();
        for (var e in a)
            if (a.hasOwnProperty(e) && a[e]) {
                var t = a[e].instance;
                t && "function" == typeof t.destroy && t.destroy()
            }
        var i = q(n.bridge);
        i && i.parentNode && (i.parentNode.removeChild(i), n.ready = null, n.bridge = null)
    }, z.activate = function(e) {
        i && (_(i, R.hoverClass), _(i, R.activeClass)), i = e, v(e, R.hoverClass), V();
        var t = R.title || e.getAttribute("title");
        if (t) {
            var s = q(n.bridge);
            s && s.setAttribute("title", t)
        }
        var o = R.forceHandCursor === !0 || "pointer" === h(e, "cursor");
        O(o)
    }, z.deactivate = function() {
        var e = q(n.bridge);
        e && (e.style.left = "0px", e.style.top = "-9999px", e.removeAttribute("title")), i && (_(i, R.hoverClass), _(i, R.activeClass), i = null)
    };
    var P = function() {
        var e, t, i = document.getElementById("global-zeroclipboard-html-bridge");
        if (!i) {
            var s = z.config();
            s.jsModuleId = "string" == typeof c && c || "string" == typeof d && d || null;
            var o = M(window.location.host, R), a = k(s), r = R.moviePath + w(R.moviePath, R), l = '      <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="global-zeroclipboard-flash-bridge" width="100%" height="100%">         <param name="movie" value="' + r + '"/>         <param name="allowScriptAccess" value="' + o + '"/>         <param name="scale" value="exactfit"/>         <param name="loop" value="false"/>         <param name="menu" value="false"/>         <param name="quality" value="best" />         <param name="bgcolor" value="#ffffff"/>         <param name="wmode" value="transparent"/>         <param name="flashvars" value="' + a + '"/>         <embed src="' + r + '"           loop="false" menu="false"           quality="best" bgcolor="#ffffff"           width="100%" height="100%"           name="global-zeroclipboard-flash-bridge"           allowScriptAccess="' + o + '"           allowFullScreen="false"           type="application/x-shockwave-flash"           wmode="transparent"           pluginspage="http://www.macromedia.com/go/getflashplayer"           flashvars="' + a + '"           scale="exactfit">         </embed>       </object>';
            i = document.createElement("div"), i.id = "global-zeroclipboard-html-bridge", i.setAttribute("class", "global-zeroclipboard-container"), i.style.position = "absolute", i.style.left = "0px", i.style.top = "-9999px", i.style.width = "15px", i.style.height = "15px", i.style.zIndex = "" + T(R.zIndex), document.body.appendChild(i), i.innerHTML = l
        }
        e = document["global-zeroclipboard-flash-bridge"], e && (t = e.length) && (e = e[t - 1]), n.bridge = e || i.children[0].lastElementChild
    }, q = function(e) {
        for (var t = /^OBJECT|EMBED$/, i = e && e.parentNode; i && t.test(i.nodeName) && i.parentNode; )
            i = i.parentNode;
        return i || null
    }, V = function() {
        if (i) {
            var e = y(i, R.zIndex), t = q(n.bridge);
            t && (t.style.top = e.top + "px", t.style.left = e.left + "px", t.style.width = e.width + "px", t.style.height = e.height + "px", t.style.zIndex = e.zIndex + 1), n.ready === !0 && n.bridge && n.bridge.setSize(e.width, e.height)
        }
        return this
    };
    z.prototype.on = function(e, t) {
        var i, s, o, r = {}, l = a[this.id] && a[this.id].handlers;
        if ("string" == typeof e && e)
            o = e.toLowerCase().split(/\s+/);
        else if ("object" == typeof e && e && "undefined" == typeof t)
            for (i in e)
                e.hasOwnProperty(i) && "string" == typeof i && i && "function" == typeof e[i] && this.on(i, e[i]);
        if (o && o.length) {
            for (i = 0, s = o.length; s > i; i++)
                e = o[i].replace(/^on/, ""), r[e] = !0, l[e] || (l[e] = []), l[e].push(t);
            r.noflash && n.disabled && F.call(this, "noflash", {}), r.wrongflash && n.outdated && F.call(this, "wrongflash", {flashVersion: n.version}), r.load && n.ready && F.call(this, "load", {flashVersion: n.version})
        }
        return this
    }, z.prototype.off = function(e, t) {
        var i, n, s, o, r, l = a[this.id] && a[this.id].handlers;
        if (0 === arguments.length)
            o = N(l);
        else if ("string" == typeof e && e)
            o = e.split(/\s+/);
        else if ("object" == typeof e && e && "undefined" == typeof t)
            for (i in e)
                e.hasOwnProperty(i) && "string" == typeof i && i && "function" == typeof e[i] && this.off(i, e[i]);
        if (o && o.length)
            for (i = 0, n = o.length; n > i; i++)
                if (e = o[i].toLowerCase().replace(/^on/, ""), r = l[e], r && r.length)
                    if (t)
                        for (s = x(t, r); -1 !== s; )
                            r.splice(s, 1), s = x(t, r, s);
                    else
                        l[e].length = 0;
        return this
    }, z.prototype.handlers = function(e) {
        var t, i = null, n = a[this.id] && a[this.id].handlers;
        if (n) {
            if ("string" == typeof e && e)
                return n[e] ? n[e].slice(0) : null;
            i = {};
            for (t in n)
                n.hasOwnProperty(t) && n[t] && (i[t] = n[t].slice(0))
        }
        return i
    };
    var L = function(e, t, i, n) {
        var s = a[this.id] && a[this.id].handlers[e];
        if (s && s.length) {
            var o, r, l, c = t || this;
            for (o = 0, r = s.length; r > o; o++)
                l = s[o], t = c, "string" == typeof l && "function" == typeof window[l] && (l = window[l]), "object" == typeof l && l && "function" == typeof l.handleEvent && (t = l, l = l.handleEvent), "function" == typeof l && j(l, t, i, n)
        }
        return this
    };
    z.prototype.clip = function(e) {
        e = C(e);
        for (var t = 0; t < e.length; t++)
            if (e.hasOwnProperty(t) && e[t] && 1 === e[t].nodeType) {
                e[t].zcClippingId ? -1 === x(this.id, l[e[t].zcClippingId]) && l[e[t].zcClippingId].push(this.id) : (e[t].zcClippingId = "zcClippingId_" + r++, l[e[t].zcClippingId] = [this.id], R.autoActivate === !0 && f(e[t], "mouseover", m));
                var i = a[this.id].elements;
                -1 === x(e[t], i) && i.push(e[t])
            }
        return this
    }, z.prototype.unclip = function(e) {
        var t = a[this.id];
        if (t) {
            var i, n = t.elements;
            e = "undefined" == typeof e ? n.slice(0) : C(e);
            for (var s = e.length; s--; )
                if (e.hasOwnProperty(s) && e[s] && 1 === e[s].nodeType) {
                    for (i = 0; -1 !== (i = x(e[s], n, i)); )
                        n.splice(i, 1);
                    var o = l[e[s].zcClippingId];
                    if (o) {
                        for (i = 0; -1 !== (i = x(this.id, o, i)); )
                            o.splice(i, 1);
                        0 === o.length && (R.autoActivate === !0 && g(e[s], "mouseover", m), delete e[s].zcClippingId)
                    }
                }
        }
        return this
    }, z.prototype.elements = function() {
        var e = a[this.id];
        return e && e.elements ? e.elements.slice(0) : []
    };
    var B = function(e) {
        var t, i, n, s, o, r = [];
        if (e && 1 === e.nodeType && (t = e.zcClippingId) && l.hasOwnProperty(t) && (i = l[t], i && i.length))
            for (n = 0, s = i.length; s > n; n++)
                o = a[i[n]].instance, o && o instanceof z && r.push(o);
        return r
    };
    R.hoverClass = "zeroclipboard-is-hover", R.activeClass = "zeroclipboard-is-active", R.trustedOrigins = null, R.allowScriptAccess = null, R.useNoCache = !0, R.moviePath = "ZeroClipboard.swf", z.detectFlashSupport = function() {
        return S("ZeroClipboard.detectFlashSupport", R.debug), I()
    }, z.dispatch = function(e, t) {
        if ("string" == typeof e && e) {
            var n = e.toLowerCase().replace(/^on/, "");
            if (n)
                for (var s = i ? B(i) : A(), o = 0, a = s.length; a > o; o++)
                    F.call(s[o], n, t)
        }
    }, z.prototype.setHandCursor = function(e) {
        return S("ZeroClipboard.prototype.setHandCursor", R.debug), e = "boolean" == typeof e ? e : !!e, O(e), R.forceHandCursor = e, this
    }, z.prototype.reposition = function() {
        return S("ZeroClipboard.prototype.reposition", R.debug), V()
    }, z.prototype.receiveEvent = function(e, t) {
        if (S("ZeroClipboard.prototype.receiveEvent", R.debug), "string" == typeof e && e) {
            var i = e.toLowerCase().replace(/^on/, "");
            i && F.call(this, i, t)
        }
    }, z.prototype.setCurrent = function(e) {
        return S("ZeroClipboard.prototype.setCurrent", R.debug), z.activate(e), this
    }, z.prototype.resetBridge = function() {
        return S("ZeroClipboard.prototype.resetBridge", R.debug), z.deactivate(), this
    }, z.prototype.setTitle = function(e) {
        if (S("ZeroClipboard.prototype.setTitle", R.debug), e = e || R.title || i && i.getAttribute("title")) {
            var t = q(n.bridge);
            t && t.setAttribute("title", e)
        }
        return this
    }, z.setDefaults = function(e) {
        S("ZeroClipboard.setDefaults", R.debug), z.config(e)
    }, z.prototype.addEventListener = function(e, t) {
        return S("ZeroClipboard.prototype.addEventListener", R.debug), this.on(e, t)
    }, z.prototype.removeEventListener = function(e, t) {
        return S("ZeroClipboard.prototype.removeEventListener", R.debug), this.off(e, t)
    }, z.prototype.ready = function() {
        return S("ZeroClipboard.prototype.ready", R.debug), n.ready === !0
    };
    var F = function(o, a) {
        o = o.toLowerCase().replace(/^on/, "");
        var r = a && a.flashVersion && e(a.flashVersion) || null, l = i, c = !0;
        switch (o) {
            case "load":
                if (r) {
                    if (!t(r))
                        return void F.call(this, "onWrongFlash", {flashVersion: r});
                    n.outdated = !1, n.ready = !0, n.version = r
                }
                break;
            case "wrongflash":
                r && !t(r) && (n.outdated = !0, n.ready = !1, n.version = r);
                break;
            case "mouseover":
                v(l, R.hoverClass);
                break;
            case "mouseout":
                R.autoActivate === !0 && z.deactivate();
                break;
            case "mousedown":
                v(l, R.activeClass);
                break;
            case "mouseup":
                _(l, R.activeClass);
                break;
            case "datarequested":
                var d = l.getAttribute("data-clipboard-target"), u = d ? document.getElementById(d) : null;
                if (u) {
                    var p = u.value || u.textContent || u.innerText;
                    p && this.setText(p)
                } else {
                    var h = l.getAttribute("data-clipboard-text");
                    h && this.setText(h)
                }
                c = !1;
                break;
            case "complete":
                E(s)
        }
        var m = l, f = [this, a];
        return L.call(this, o, m, f, c)
    };
    "function" == typeof define && define.amd ? define("clipboard", ["require", "exports", "module"], function(e, t, i) {
        return c = i && i.id || null, z
    }) : "object" == typeof module && module && "object" == typeof module.exports && module.exports ? (d = module.id || null, module.exports = z) : window.ZeroClipboard = z
}(), define("components/pop/url", ["backbone", "components/pop/base", "text!components/pop/templates/url.html", "core/utils", "clipboard"], function(e, t, i, n, s) {
    return t.extend({template: _.template(i),className: "popover left",events: {"click .js-btn-copy": "copy"},initialize: function(e) {
            t.prototype.initialize.call(this, e), s.config({moviePath: window._global.url.static + "/vendor/plugin/ZeroClipboard.swf",trustedDomains: ["*"],allowScriptAccess: "always"}), this.data = e.data
        },render: function() {
            var e = this;
            this.$el.html(this.template({url: this.data}));
            var t = new s(this.$(".js-btn-copy"));
            return t.on("load", function(i) {
                i.on("complete", function() {
                    n.successNotify("复制成功"), e.hide(), t.off("load")
                })
            }), this
        },positioning: function() {
            var e = this.target.offset(), t = {height: this.target.outerHeight(),width: this.target.outerWidth()}, i = {height: this.$el.outerHeight(),width: this.$el.outerWidth()};
            this.$el.css({display: "block",left: e.left - i.width,top: e.top - i.height / 2 + t.height / 2})
        },setData: function(e) {
            this.data = e, this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.clearInput(), this.show()
        },copy: function() {
            this.hide()
        },clearInput: function() {
            this.$(".js-link-placeholder").val("")
        }})
}), define("text!components/pop/templates/text.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-text">\n    <div class="popover-content">\n        <form class="form-horizontal">\n            <div class="control-group">\n                <label class="control-label">请设置模块名称：</label>\n                <div class="controls">\n                    <input type="text" class="text-placeholder js-text-placeholder">\n                </div>\n            </div>\n            <div class="form-actions">\n                <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定"> 确定</button>\n                <button type="reset" class="btn js-btn-cancel">取消</button>\n            </div>\n        </form>\n    </div>\n</div>\n'
}), define("components/pop/text", ["backbone", "components/pop/base", "text!components/pop/templates/text.html", "core/utils", "jqueryui"], function(e, t, i, n) {
    return t.extend({template: _.template(i),className: "popover top",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keydown .js-text-placeholder": function(e) {
                e.keyCode === n.keyCode.ENTER && this.triggerCallback()
            }},positioning: function() {
            this.$el.show().position({of: this.target,my: "center bottom-5",at: "center top",collision: "none"}), this.$(".js-text-placeholder").focus()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.clearInput(), this.show()
        },triggerCallback: function() {
            this.callback($.trim(this.$(".js-text-placeholder").val())), this.hide()
        },clearInput: function() {
            this.$(".js-text-placeholder").val("")
        }})
}), define("text!components/pop/templates/rename.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-rename">\n    <div class="popover-content">\n        <div class="form-horizontal">\n            <div class="control-group">\n                <div class="controls">\n                    <input type="text" class="js-rename-placeholder" maxlength="60" value="<%= url %>"/>\n                    <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n                    <button type="reset" class="btn js-btn-cancel">取消</button>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("components/pop/rename", ["backbone", "components/pop/base", "text!components/pop/templates/rename.html", "core/utils"], function(e, t, i, n) {
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keydown .js-rename-placeholder": function(e) {
                e.keyCode === n.keyCode.ENTER && this.triggerCallback()
            }},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            return this.$el.html(this.template({url: this.data})), this
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },setData: function(e) {
            this.data = e, this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className || "popover left";
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.clearInput(), this.show()
        },triggerCallback: function() {
            this.callback(this.$(".js-rename-placeholder").val()), this.hide()
        },clearInput: function() {
            this.$(".js-link-placeholder").val("")
        }})
}), define("text!components/pop/templates/change_category.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-category">\n    <div class="popover-content">\n        <div class="select-wrap loading" id="js-change-category">\n            <select data-placeholder=" " multiple="multiple" style="display:none;"></select>\n        </div>\n        <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定"> 确定</button>\n        <button type="reset" class="btn js-btn-cancel">取消</button>\n    </div>\n</div>\n'
}), define("components/pop/change_category", ["backbone", "components/pop/base", "text!components/pop/templates/change_category.html", "core/utils"], function(e, t, i, n) {
    return t.extend({template: _.template(i),className: "popover right popover-category-wrap",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keydown .js-text-placeholder": function(e) {
                e.keyCode === n.keyCode.ENTER && this.triggerCallback()
            }},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            return this.$el.html(this.template({url: this.data})), this
        },positioning: function() {
            var e = this.target.offset(), t = {height: this.target.outerHeight(),width: this.target.outerWidth()}, i = {height: this.$el.outerHeight(),width: this.$el.outerWidth()};
            this.$el.css({display: "block",left: e.left + t.width - 3,top: e.top - i.height / 2 + t.height / 2})
        },setData: function(e) {
            this.data = e, this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.clearInput(), this.show()
        },triggerCallback: function() {
            this.callback($.trim(this.$(".js-text-placeholder").val())), this.hide()
        },clearInput: function() {
            this.$(".js-link-placeholder").val("")
        }})
}), define("text!components/pop/templates/help_notes.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner">\n    <div class="popover-content">\n        <%= content %>\n    </div>\n</div>\n'
}), define("components/pop/help_notes", ["backbone", "components/pop/base", "text!components/pop/templates/help_notes.html", "core/utils", "clipboard"], function(e, t, i, n, s) {
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keydown .js-rename-placeholder": function(e) {
                e.keyCode === n.keyCode.ENTER && this.triggerCallback()
            }},initialize: function(e) {
            t.prototype.initialize.call(this, e), s.config({moviePath: window._global.url.static + "/vendor/plugin/ZeroClipboard.swf",trustedDomains: ["*"],allowScriptAccess: "always"}), this.data = e.data
        },render: function() {
            var e = this, t = this.target.parent().find(".js-notes-cont").html() || "暂时没有注释内容。";
            this.$el.html(this.template({content: t}));
            var i = new s(this.$(".js-help-notes-btn-copy"));
            return i.on("load", function(t) {
                t.on("complete", function() {
                    n.successNotify("复制成功"), e.hide(), i.off("load")
                })
            }), this
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : -1 !== t.indexOf("top") ? {of: e.target,my: "center bottom",at: "center top",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },setData: function(e) {
            this.data = e, this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className || "popover bottom";
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.clearInput(), this.show()
        },triggerCallback: function() {
            this.callback(), this.hide()
        },clearInput: function() {
            this.$(".js-link-placeholder").val("")
        }})
}), define("text!components/pop/templates/memo.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-memo">\n    <div class="popover-content">\n        <form class="form-inline">\n            <input type="text" class="text-placeholder js-text-placeholder" placeholder="最多 <%= maxLength || 15 %> 个字符" maxlength="<%= maxLength || 15 %>" value="<%=remark %>">\n            <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n            <button type="reset" class="btn js-btn-cancel">取消</button>\n        </form>\n    </div>\n</div>\n'
}), define("components/pop/memo", ["require", "backbone", "components/pop/base", "text!components/pop/templates/memo.html", "core/utils"], function(e) {
    var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/memo.html"), n = e("core/utils");
    return t.extend({template: _.template(i),className: "popover left popover-memo",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keydown .js-text-placeholder": function(e) {
                e.keyCode === n.keyCode.ENTER && (this.triggerCallback(), e.preventDefault())
            }},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this;
            return this.data.maxLength || (this.data.maxLength = 15), this.$el.html(this.template(this.data)), e.txt = e.$el.find(".js-text-placeholder"), e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover left", o = e.data;
            this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.clearInput(), this.setData(o), this.positioning(), this.show()
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className, e.txt.val(e.data.remark).focus()
        },clearInput: function() {
            var e = this;
            e.txt.val("")
        },setData: function(e) {
            this.data = e
        },triggerCallback: function() {
            var e = this, t = $.trim(e.txt.val()), i = {remark: t};
            e.callback(i), e.hide()
        }})
}), define("text!components/pop/templates/chosen.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-chosen">\n    <div class="popover-content">\n        <input type="hidden" class="js-select2 select2-offscreen" style="width: 242px;" tabindex="-1">\n        <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n        <button type="reset" class="btn js-btn-cancel">取消</button>\n    </div>\n</div>\n'
}), function(e) {
    "undefined" == typeof e.fn.each2 && e.extend(e.fn, {each2: function(t) {
            for (var i = e([0]), n = -1, s = this.length; ++n < s && (i.context = i[0] = this[n]) && t.call(i[0], n, i) !== !1; )
                ;
            return this
        }})
}(jQuery), function(e, t) {
    function i(e) {
        var t, i, n, s;
        if (!e || e.length < 1)
            return e;
        for (t = "", i = 0, n = e.length; n > i; i++)
            s = e.charAt(i), t += q[s] || s;
        return t
    }
    function n(e, t) {
        for (var i = 0, n = t.length; n > i; i += 1)
            if (o(e, t[i]))
                return i;
        return -1
    }
    function s() {
        var t = e(P);
        t.appendTo("body");
        var i = {width: t.width() - t[0].clientWidth,height: t.height() - t[0].clientHeight};
        return t.remove(), i
    }
    function o(e, i) {
        return e === i ? !0 : e === t || i === t ? !1 : null === e || null === i ? !1 : e.constructor === String ? e + "" == i + "" : i.constructor === String ? i + "" == e + "" : !1
    }
    function a(t, i) {
        var n, s, o;
        if (null === t || t.length < 1)
            return [];
        for (n = t.split(i), s = 0, o = n.length; o > s; s += 1)
            n[s] = e.trim(n[s]);
        return n
    }
    function r(e) {
        return e.outerWidth(!1) - e.width()
    }
    function l(i) {
        var n = "keyup-change-value";
        i.on("keydown", function() {
            e.data(i, n) === t && e.data(i, n, i.val())
        }), i.on("keyup", function() {
            var s = e.data(i, n);
            s !== t && i.val() !== s && (e.removeData(i, n), i.trigger("keyup-change"))
        })
    }
    function c(i) {
        i.on("mousemove", function(i) {
            var n = R;
            (n === t || n.x !== i.pageX || n.y !== i.pageY) && e(i.target).trigger("mousemove-filtered", i)
        })
    }
    function d(e, i, n) {
        n = n || t;
        var s;
        return function() {
            var t = arguments;
            window.clearTimeout(s), s = window.setTimeout(function() {
                i.apply(n, t)
            }, e)
        }
    }
    function u(e) {
        var t, i = !1;
        return function() {
            return i === !1 && (t = e(), i = !0), t
        }
    }
    function p(e, t) {
        var i = d(e, function(e) {
            t.trigger("scroll-debounced", e)
        });
        t.on("scroll", function(e) {
            n(e.target, t.get()) >= 0 && i(e)
        })
    }
    function h(e) {
        e[0] !== document.activeElement && window.setTimeout(function() {
            var t, i = e[0], n = e.val().length;
            e.focus(), e.is(":visible") && i === document.activeElement && (i.setSelectionRange ? i.setSelectionRange(n, n) : i.createTextRange && (t = i.createTextRange(), t.collapse(!1), t.select()))
        }, 0)
    }
    function m(t) {
        t = e(t)[0];
        var i = 0, n = 0;
        if ("selectionStart" in t)
            i = t.selectionStart, n = t.selectionEnd - i;
        else if ("selection" in document) {
            t.focus();
            var s = document.selection.createRange();
            n = document.selection.createRange().text.length, s.moveStart("character", -t.value.length), i = s.text.length - n
        }
        return {offset: i,length: n}
    }
    function f(e) {
        e.preventDefault(), e.stopPropagation()
    }
    function g(e) {
        e.preventDefault(), e.stopImmediatePropagation()
    }
    function v(t) {
        if (!z) {
            var i = t[0].currentStyle || window.getComputedStyle(t[0], null);
            z = e(document.createElement("div")).css({position: "absolute",left: "-10000px",top: "-10000px",display: "none",fontSize: i.fontSize,fontFamily: i.fontFamily,fontStyle: i.fontStyle,fontWeight: i.fontWeight,letterSpacing: i.letterSpacing,textTransform: i.textTransform,whiteSpace: "nowrap"}), z.attr("class", "select2-sizer"), e("body").append(z)
        }
        return z.text(t.val()), z.width()
    }
    function _(t, i, n) {
        var s, o, a = [];
        s = t.attr("class"), s && (s = "" + s, e(s.split(" ")).each2(function() {
            0 === this.indexOf("select2-") && a.push(this)
        })), s = i.attr("class"), s && (s = "" + s, e(s.split(" ")).each2(function() {
            0 !== this.indexOf("select2-") && (o = n(this), o && a.push(this))
        })), t.attr("class", a.join(" "))
    }
    function b(e, t, n, s) {
        var o = i(e.toUpperCase()).indexOf(i(t.toUpperCase())), a = t.length;
        return 0 > o ? void n.push(s(e)) : (n.push(s(e.substring(0, o))), n.push("<span class='select2-match'>"), n.push(s(e.substring(o, o + a))), n.push("</span>"), void n.push(s(e.substring(o + a, e.length))))
    }
    function y(e) {
        var t = {"\\": "&#92;","&": "&amp;","<": "&lt;",">": "&gt;",'"': "&quot;","'": "&#39;","/": "&#47;"};
        return String(e).replace(/[&<>"'\/\\]/g, function(e) {
            return t[e]
        })
    }
    function w(i) {
        var n, s = null, o = i.quietMillis || 100, a = i.url, r = this;
        return function(l) {
            window.clearTimeout(n), n = window.setTimeout(function() {
                var n = i.data, o = a, c = i.transport || e.fn.select2.ajaxDefaults.transport, d = {type: i.type || "GET",cache: i.cache || !1,jsonpCallback: i.jsonpCallback || t,dataType: i.dataType || "json"}, u = e.extend({}, e.fn.select2.ajaxDefaults.params, d);
                n = n ? n.call(r, l.term, l.page, l.context) : null, o = "function" == typeof o ? o.call(r, l.term, l.page, l.context) : o, s && s.abort(), i.params && (e.isFunction(i.params) ? e.extend(u, i.params.call(r)) : e.extend(u, i.params)), e.extend(u, {url: o,dataType: i.dataType,data: n,success: function(e) {
                        var t = i.results(e, l.page);
                        l.callback(t)
                    }}), s = c.call(r, u)
            }, o)
        }
    }
    function k(t) {
        var i, n, s = t, o = function(e) {
            return "" + e.text
        };
        e.isArray(s) && (n = s, s = {results: n}), e.isFunction(s) === !1 && (n = s, s = function() {
            return n
        });
        var a = s();
        return a.text && (o = a.text, e.isFunction(o) || (i = a.text, o = function(e) {
            return e[i]
        })), function(t) {
            var i, n = t.term, a = {results: []};
            return "" === n ? void t.callback(s()) : (i = function(s, a) {
                var r, l;
                if (s = s[0], s.children) {
                    r = {};
                    for (l in s)
                        s.hasOwnProperty(l) && (r[l] = s[l]);
                    r.children = [], e(s.children).each2(function(e, t) {
                        i(t, r.children)
                    }), (r.children.length || t.matcher(n, o(r), s)) && a.push(r)
                } else
                    t.matcher(n, o(s), s) && a.push(s)
            }, e(s().results).each2(function(e, t) {
                i(t, a.results)
            }), void t.callback(a))
        }
    }
    function x(i) {
        var n = e.isFunction(i);
        return function(s) {
            var o = s.term, a = {results: []};
            e(n ? i() : i).each(function() {
                var e = this.text !== t, i = e ? this.text : this;
                ("" === o || s.matcher(o, i)) && a.results.push(e ? this : {id: this,text: this})
            }), s.callback(a)
        }
    }
    function C(t, i) {
        if (e.isFunction(t))
            return !0;
        if (!t)
            return !1;
        throw new Error(i + " must be a function or a falsy value")
    }
    function j(t) {
        return e.isFunction(t) ? t() : t
    }
    function T(t) {
        var i = 0;
        return e.each(t, function(e, t) {
            t.children ? i += T(t.children) : i++
        }), i
    }
    function S(e, i, n, s) {
        var a, r, l, c, d, u = e, p = !1;
        if (!s.createSearchChoice || !s.tokenSeparators || s.tokenSeparators.length < 1)
            return t;
        for (; ; ) {
            for (r = -1, l = 0, c = s.tokenSeparators.length; c > l && (d = s.tokenSeparators[l], r = e.indexOf(d), !(r >= 0)); l++)
                ;
            if (0 > r)
                break;
            if (a = e.substring(0, r), e = e.substring(r + d.length), a.length > 0 && (a = s.createSearchChoice.call(this, a, i), a !== t && null !== a && s.id(a) !== t && null !== s.id(a))) {
                for (p = !1, l = 0, c = i.length; c > l; l++)
                    if (o(s.id(a), s.id(i[l]))) {
                        p = !0;
                        break
                    }
                p || n(a)
            }
        }
        return u !== e ? e : void 0
    }
    function $(t, i) {
        var n = function() {
        };
        return n.prototype = new t, n.prototype.constructor = n, n.prototype.parent = t.prototype, n.prototype = e.extend(n.prototype, i), n
    }
    if (window.Select2 === t) {
        var D, M, N, E, I, z, O, A, R = {x: 0,y: 0}, D = {TAB: 9,ENTER: 13,ESC: 27,SPACE: 32,LEFT: 37,UP: 38,RIGHT: 39,DOWN: 40,SHIFT: 16,CTRL: 17,ALT: 18,PAGE_UP: 33,PAGE_DOWN: 34,HOME: 36,END: 35,BACKSPACE: 8,DELETE: 46,isArrow: function(e) {
                switch (e = e.which ? e.which : e) {
                    case D.LEFT:
                    case D.RIGHT:
                    case D.UP:
                    case D.DOWN:
                        return !0
                }
                return !1
            },isControl: function(e) {
                var t = e.which;
                switch (t) {
                    case D.SHIFT:
                    case D.CTRL:
                    case D.ALT:
                        return !0
                }
                return e.metaKey ? !0 : !1
            },isFunctionKey: function(e) {
                return e = e.which ? e.which : e, e >= 112 && 123 >= e
            }}, P = "<div class='select2-measure-scrollbar'></div>", q = {"Ⓐ": "A","Ａ": "A","À": "A","Á": "A","Â": "A","Ầ": "A","Ấ": "A","Ẫ": "A","Ẩ": "A","Ã": "A","Ā": "A","Ă": "A","Ằ": "A","Ắ": "A","Ẵ": "A","Ẳ": "A","Ȧ": "A","Ǡ": "A","Ä": "A","Ǟ": "A","Ả": "A","Å": "A","Ǻ": "A","Ǎ": "A","Ȁ": "A","Ȃ": "A","Ạ": "A","Ậ": "A","Ặ": "A","Ḁ": "A","Ą": "A","Ⱥ": "A","Ɐ": "A","Ꜳ": "AA","Æ": "AE","Ǽ": "AE","Ǣ": "AE","Ꜵ": "AO","Ꜷ": "AU","Ꜹ": "AV","Ꜻ": "AV","Ꜽ": "AY","Ⓑ": "B","Ｂ": "B","Ḃ": "B","Ḅ": "B","Ḇ": "B","Ƀ": "B","Ƃ": "B","Ɓ": "B","Ⓒ": "C","Ｃ": "C","Ć": "C","Ĉ": "C","Ċ": "C","Č": "C","Ç": "C","Ḉ": "C","Ƈ": "C","Ȼ": "C","Ꜿ": "C","Ⓓ": "D","Ｄ": "D","Ḋ": "D","Ď": "D","Ḍ": "D","Ḑ": "D","Ḓ": "D","Ḏ": "D","Đ": "D","Ƌ": "D","Ɗ": "D","Ɖ": "D","Ꝺ": "D","Ǳ": "DZ","Ǆ": "DZ","ǲ": "Dz","ǅ": "Dz","Ⓔ": "E","Ｅ": "E","È": "E","É": "E","Ê": "E","Ề": "E","Ế": "E","Ễ": "E","Ể": "E","Ẽ": "E","Ē": "E","Ḕ": "E","Ḗ": "E","Ĕ": "E","Ė": "E","Ë": "E","Ẻ": "E","Ě": "E","Ȅ": "E","Ȇ": "E","Ẹ": "E","Ệ": "E","Ȩ": "E","Ḝ": "E","Ę": "E","Ḙ": "E","Ḛ": "E","Ɛ": "E","Ǝ": "E","Ⓕ": "F","Ｆ": "F","Ḟ": "F","Ƒ": "F","Ꝼ": "F","Ⓖ": "G","Ｇ": "G","Ǵ": "G","Ĝ": "G","Ḡ": "G","Ğ": "G","Ġ": "G","Ǧ": "G","Ģ": "G","Ǥ": "G","Ɠ": "G","Ꞡ": "G","Ᵹ": "G","Ꝿ": "G","Ⓗ": "H","Ｈ": "H","Ĥ": "H","Ḣ": "H","Ḧ": "H","Ȟ": "H","Ḥ": "H","Ḩ": "H","Ḫ": "H","Ħ": "H","Ⱨ": "H","Ⱶ": "H","Ɥ": "H","Ⓘ": "I","Ｉ": "I","Ì": "I","Í": "I","Î": "I","Ĩ": "I","Ī": "I","Ĭ": "I","İ": "I","Ï": "I","Ḯ": "I","Ỉ": "I","Ǐ": "I","Ȉ": "I","Ȋ": "I","Ị": "I","Į": "I","Ḭ": "I","Ɨ": "I","Ⓙ": "J","Ｊ": "J","Ĵ": "J","Ɉ": "J","Ⓚ": "K","Ｋ": "K","Ḱ": "K","Ǩ": "K","Ḳ": "K","Ķ": "K","Ḵ": "K","Ƙ": "K","Ⱪ": "K","Ꝁ": "K","Ꝃ": "K","Ꝅ": "K","Ꞣ": "K","Ⓛ": "L","Ｌ": "L","Ŀ": "L","Ĺ": "L","Ľ": "L","Ḷ": "L","Ḹ": "L","Ļ": "L","Ḽ": "L","Ḻ": "L","Ł": "L","Ƚ": "L","Ɫ": "L","Ⱡ": "L","Ꝉ": "L","Ꝇ": "L","Ꞁ": "L","Ǉ": "LJ","ǈ": "Lj","Ⓜ": "M","Ｍ": "M","Ḿ": "M","Ṁ": "M","Ṃ": "M","Ɱ": "M","Ɯ": "M","Ⓝ": "N","Ｎ": "N","Ǹ": "N","Ń": "N","Ñ": "N","Ṅ": "N","Ň": "N","Ṇ": "N","Ņ": "N","Ṋ": "N","Ṉ": "N","Ƞ": "N","Ɲ": "N","Ꞑ": "N","Ꞥ": "N","Ǌ": "NJ","ǋ": "Nj","Ⓞ": "O","Ｏ": "O","Ò": "O","Ó": "O","Ô": "O","Ồ": "O","Ố": "O","Ỗ": "O","Ổ": "O","Õ": "O","Ṍ": "O","Ȭ": "O","Ṏ": "O","Ō": "O","Ṑ": "O","Ṓ": "O","Ŏ": "O","Ȯ": "O","Ȱ": "O","Ö": "O","Ȫ": "O","Ỏ": "O","Ő": "O","Ǒ": "O","Ȍ": "O","Ȏ": "O","Ơ": "O","Ờ": "O","Ớ": "O","Ỡ": "O","Ở": "O","Ợ": "O","Ọ": "O","Ộ": "O","Ǫ": "O","Ǭ": "O","Ø": "O","Ǿ": "O","Ɔ": "O","Ɵ": "O","Ꝋ": "O","Ꝍ": "O","Ƣ": "OI","Ꝏ": "OO","Ȣ": "OU","Ⓟ": "P","Ｐ": "P","Ṕ": "P","Ṗ": "P","Ƥ": "P","Ᵽ": "P","Ꝑ": "P","Ꝓ": "P","Ꝕ": "P","Ⓠ": "Q","Ｑ": "Q","Ꝗ": "Q","Ꝙ": "Q","Ɋ": "Q","Ⓡ": "R","Ｒ": "R","Ŕ": "R","Ṙ": "R","Ř": "R","Ȑ": "R","Ȓ": "R","Ṛ": "R","Ṝ": "R","Ŗ": "R","Ṟ": "R","Ɍ": "R","Ɽ": "R","Ꝛ": "R","Ꞧ": "R","Ꞃ": "R","Ⓢ": "S","Ｓ": "S","ẞ": "S","Ś": "S","Ṥ": "S","Ŝ": "S","Ṡ": "S","Š": "S","Ṧ": "S","Ṣ": "S","Ṩ": "S","Ș": "S","Ş": "S","Ȿ": "S","Ꞩ": "S","Ꞅ": "S","Ⓣ": "T","Ｔ": "T","Ṫ": "T","Ť": "T","Ṭ": "T","Ț": "T","Ţ": "T","Ṱ": "T","Ṯ": "T","Ŧ": "T","Ƭ": "T","Ʈ": "T","Ⱦ": "T","Ꞇ": "T","Ꜩ": "TZ","Ⓤ": "U","Ｕ": "U","Ù": "U","Ú": "U","Û": "U","Ũ": "U","Ṹ": "U","Ū": "U","Ṻ": "U","Ŭ": "U","Ü": "U","Ǜ": "U","Ǘ": "U","Ǖ": "U","Ǚ": "U","Ủ": "U","Ů": "U","Ű": "U","Ǔ": "U","Ȕ": "U","Ȗ": "U","Ư": "U","Ừ": "U","Ứ": "U","Ữ": "U","Ử": "U","Ự": "U","Ụ": "U","Ṳ": "U","Ų": "U","Ṷ": "U","Ṵ": "U","Ʉ": "U","Ⓥ": "V","Ｖ": "V","Ṽ": "V","Ṿ": "V","Ʋ": "V","Ꝟ": "V","Ʌ": "V","Ꝡ": "VY","Ⓦ": "W","Ｗ": "W","Ẁ": "W","Ẃ": "W","Ŵ": "W","Ẇ": "W","Ẅ": "W","Ẉ": "W","Ⱳ": "W","Ⓧ": "X","Ｘ": "X","Ẋ": "X","Ẍ": "X","Ⓨ": "Y","Ｙ": "Y","Ỳ": "Y","Ý": "Y","Ŷ": "Y","Ỹ": "Y","Ȳ": "Y","Ẏ": "Y","Ÿ": "Y","Ỷ": "Y","Ỵ": "Y","Ƴ": "Y","Ɏ": "Y","Ỿ": "Y","Ⓩ": "Z","Ｚ": "Z","Ź": "Z","Ẑ": "Z","Ż": "Z","Ž": "Z","Ẓ": "Z","Ẕ": "Z","Ƶ": "Z","Ȥ": "Z","Ɀ": "Z","Ⱬ": "Z","Ꝣ": "Z","ⓐ": "a","ａ": "a","ẚ": "a","à": "a","á": "a","â": "a","ầ": "a","ấ": "a","ẫ": "a","ẩ": "a","ã": "a","ā": "a","ă": "a","ằ": "a","ắ": "a","ẵ": "a","ẳ": "a","ȧ": "a","ǡ": "a","ä": "a","ǟ": "a","ả": "a","å": "a","ǻ": "a","ǎ": "a","ȁ": "a","ȃ": "a","ạ": "a","ậ": "a","ặ": "a","ḁ": "a","ą": "a","ⱥ": "a","ɐ": "a","ꜳ": "aa","æ": "ae","ǽ": "ae","ǣ": "ae","ꜵ": "ao","ꜷ": "au","ꜹ": "av","ꜻ": "av","ꜽ": "ay","ⓑ": "b","ｂ": "b","ḃ": "b","ḅ": "b","ḇ": "b","ƀ": "b","ƃ": "b","ɓ": "b","ⓒ": "c","ｃ": "c","ć": "c","ĉ": "c","ċ": "c","č": "c","ç": "c","ḉ": "c","ƈ": "c","ȼ": "c","ꜿ": "c","ↄ": "c","ⓓ": "d","ｄ": "d","ḋ": "d","ď": "d","ḍ": "d","ḑ": "d","ḓ": "d","ḏ": "d","đ": "d","ƌ": "d","ɖ": "d","ɗ": "d","ꝺ": "d","ǳ": "dz","ǆ": "dz","ⓔ": "e","ｅ": "e","è": "e","é": "e","ê": "e","ề": "e","ế": "e","ễ": "e","ể": "e","ẽ": "e","ē": "e","ḕ": "e","ḗ": "e","ĕ": "e","ė": "e","ë": "e","ẻ": "e","ě": "e","ȅ": "e","ȇ": "e","ẹ": "e","ệ": "e","ȩ": "e","ḝ": "e","ę": "e","ḙ": "e","ḛ": "e","ɇ": "e","ɛ": "e","ǝ": "e","ⓕ": "f","ｆ": "f","ḟ": "f","ƒ": "f","ꝼ": "f","ⓖ": "g","ｇ": "g","ǵ": "g","ĝ": "g","ḡ": "g","ğ": "g","ġ": "g","ǧ": "g","ģ": "g","ǥ": "g","ɠ": "g","ꞡ": "g","ᵹ": "g","ꝿ": "g","ⓗ": "h","ｈ": "h","ĥ": "h","ḣ": "h","ḧ": "h","ȟ": "h","ḥ": "h","ḩ": "h","ḫ": "h","ẖ": "h","ħ": "h","ⱨ": "h","ⱶ": "h","ɥ": "h","ƕ": "hv","ⓘ": "i","ｉ": "i","ì": "i","í": "i","î": "i","ĩ": "i","ī": "i","ĭ": "i","ï": "i","ḯ": "i","ỉ": "i","ǐ": "i","ȉ": "i","ȋ": "i","ị": "i","į": "i","ḭ": "i","ɨ": "i","ı": "i","ⓙ": "j","ｊ": "j","ĵ": "j","ǰ": "j","ɉ": "j","ⓚ": "k","ｋ": "k","ḱ": "k","ǩ": "k","ḳ": "k","ķ": "k","ḵ": "k","ƙ": "k","ⱪ": "k","ꝁ": "k","ꝃ": "k","ꝅ": "k","ꞣ": "k","ⓛ": "l","ｌ": "l","ŀ": "l","ĺ": "l","ľ": "l","ḷ": "l","ḹ": "l","ļ": "l","ḽ": "l","ḻ": "l","ſ": "l","ł": "l","ƚ": "l","ɫ": "l","ⱡ": "l","ꝉ": "l","ꞁ": "l","ꝇ": "l","ǉ": "lj","ⓜ": "m","ｍ": "m","ḿ": "m","ṁ": "m","ṃ": "m","ɱ": "m","ɯ": "m","ⓝ": "n","ｎ": "n","ǹ": "n","ń": "n","ñ": "n","ṅ": "n","ň": "n","ṇ": "n","ņ": "n","ṋ": "n","ṉ": "n","ƞ": "n","ɲ": "n","ŉ": "n","ꞑ": "n","ꞥ": "n","ǌ": "nj","ⓞ": "o","ｏ": "o","ò": "o","ó": "o","ô": "o","ồ": "o","ố": "o","ỗ": "o","ổ": "o","õ": "o","ṍ": "o","ȭ": "o","ṏ": "o","ō": "o","ṑ": "o","ṓ": "o","ŏ": "o","ȯ": "o","ȱ": "o","ö": "o","ȫ": "o","ỏ": "o","ő": "o","ǒ": "o","ȍ": "o","ȏ": "o","ơ": "o","ờ": "o","ớ": "o","ỡ": "o","ở": "o","ợ": "o","ọ": "o","ộ": "o","ǫ": "o","ǭ": "o","ø": "o","ǿ": "o","ɔ": "o","ꝋ": "o","ꝍ": "o","ɵ": "o","ƣ": "oi","ȣ": "ou","ꝏ": "oo","ⓟ": "p","ｐ": "p","ṕ": "p","ṗ": "p","ƥ": "p","ᵽ": "p","ꝑ": "p","ꝓ": "p","ꝕ": "p","ⓠ": "q","ｑ": "q","ɋ": "q","ꝗ": "q","ꝙ": "q","ⓡ": "r","ｒ": "r","ŕ": "r","ṙ": "r","ř": "r","ȑ": "r","ȓ": "r","ṛ": "r","ṝ": "r","ŗ": "r","ṟ": "r","ɍ": "r","ɽ": "r","ꝛ": "r","ꞧ": "r","ꞃ": "r","ⓢ": "s","ｓ": "s","ß": "s","ś": "s","ṥ": "s","ŝ": "s","ṡ": "s","š": "s","ṧ": "s","ṣ": "s","ṩ": "s","ș": "s","ş": "s","ȿ": "s","ꞩ": "s","ꞅ": "s","ẛ": "s","ⓣ": "t","ｔ": "t","ṫ": "t","ẗ": "t","ť": "t","ṭ": "t","ț": "t","ţ": "t","ṱ": "t","ṯ": "t","ŧ": "t","ƭ": "t","ʈ": "t","ⱦ": "t","ꞇ": "t","ꜩ": "tz","ⓤ": "u","ｕ": "u","ù": "u","ú": "u","û": "u","ũ": "u","ṹ": "u","ū": "u","ṻ": "u","ŭ": "u","ü": "u","ǜ": "u","ǘ": "u","ǖ": "u","ǚ": "u","ủ": "u","ů": "u","ű": "u","ǔ": "u","ȕ": "u","ȗ": "u","ư": "u","ừ": "u","ứ": "u","ữ": "u","ử": "u","ự": "u","ụ": "u","ṳ": "u","ų": "u","ṷ": "u","ṵ": "u","ʉ": "u","ⓥ": "v","ｖ": "v","ṽ": "v","ṿ": "v","ʋ": "v","ꝟ": "v","ʌ": "v","ꝡ": "vy","ⓦ": "w","ｗ": "w","ẁ": "w","ẃ": "w","ŵ": "w","ẇ": "w","ẅ": "w","ẘ": "w","ẉ": "w","ⱳ": "w","ⓧ": "x","ｘ": "x","ẋ": "x","ẍ": "x","ⓨ": "y","ｙ": "y","ỳ": "y","ý": "y","ŷ": "y","ỹ": "y","ȳ": "y","ẏ": "y","ÿ": "y","ỷ": "y","ẙ": "y","ỵ": "y","ƴ": "y","ɏ": "y","ỿ": "y","ⓩ": "z","ｚ": "z","ź": "z","ẑ": "z","ż": "z","ž": "z","ẓ": "z","ẕ": "z","ƶ": "z","ȥ": "z","ɀ": "z","ⱬ": "z","ꝣ": "z"};
        O = e(document), I = function() {
            var e = 1;
            return function() {
                return e++
            }
        }(), O.on("mousemove", function(e) {
            R.x = e.pageX, R.y = e.pageY
        }), M = $(Object, {bind: function(e) {
                var t = this;
                return function() {
                    e.apply(t, arguments)
                }
            },init: function(i) {
                var n, o, a, r, d = ".select2-results";
                this.opts = i = this.prepareOpts(i), this.id = i.id, i.element.data("select2") !== t && null !== i.element.data("select2") && i.element.data("select2").destroy(), this.container = this.createContainer(), this.containerId = "s2id_" + (i.element.attr("id") || "autogen" + I()), this.containerSelector = "#" + this.containerId.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, "\\$1"), this.container.attr("id", this.containerId), this.body = u(function() {
                    return i.element.closest("body")
                }), _(this.container, this.opts.element, this.opts.adaptContainerCssClass), this.container.attr("style", i.element.attr("style")), this.container.css(j(i.containerCss)), this.container.addClass(j(i.containerCssClass)), this.elementTabIndex = this.opts.element.attr("tabindex"), this.opts.element.data("select2", this).attr("tabindex", "-1").before(this.container).on("click.select2", f), this.container.data("select2", this), this.dropdown = this.container.find(".select2-drop"), _(this.dropdown, this.opts.element, this.opts.adaptDropdownCssClass), this.dropdown.addClass(j(i.dropdownCssClass)), this.dropdown.data("select2", this), this.dropdown.on("click", f), this.results = n = this.container.find(d), this.search = o = this.container.find("input.select2-input"), this.queryCount = 0, this.resultsPage = 0, this.context = null, this.initContainer(), this.container.on("click", f), c(this.results), this.dropdown.on("mousemove-filtered touchstart touchmove touchend", d, this.bind(this.highlightUnderEvent)), p(80, this.results), this.dropdown.on("scroll-debounced", d, this.bind(this.loadMoreIfNeeded)), e(this.container).on("change", ".select2-input", function(e) {
                    e.stopPropagation()
                }), e(this.dropdown).on("change", ".select2-input", function(e) {
                    e.stopPropagation()
                }), e.fn.mousewheel && n.mousewheel(function(e, t, i, s) {
                    var o = n.scrollTop();
                    s > 0 && 0 >= o - s ? (n.scrollTop(0), f(e)) : 0 > s && n.get(0).scrollHeight - n.scrollTop() + s <= n.height() && (n.scrollTop(n.get(0).scrollHeight - n.height()), f(e))
                }), l(o), o.on("keyup-change input paste", this.bind(this.updateResults)), o.on("focus", function() {
                    o.addClass("select2-focused")
                }), o.on("blur", function() {
                    o.removeClass("select2-focused")
                }), this.dropdown.on("mouseup", d, this.bind(function(t) {
                    e(t.target).closest(".select2-result-selectable").length > 0 && (this.highlightUnderEvent(t), this.selectHighlighted(t))
                })), this.dropdown.on("click mouseup mousedown", function(e) {
                    e.stopPropagation()
                }), e.isFunction(this.opts.initSelection) && (this.initSelection(), this.monitorSource()), null !== i.maximumInputLength && this.search.attr("maxlength", i.maximumInputLength);
                var a = i.element.prop("disabled");
                a === t && (a = !1), this.enable(!a);
                var r = i.element.prop("readonly");
                r === t && (r = !1), this.readonly(r), A = A || s(), this.autofocus = i.element.prop("autofocus"), i.element.prop("autofocus", !1), this.autofocus && this.focus(), this.nextSearchTerm = t
            },destroy: function() {
                var e = this.opts.element, i = e.data("select2");
                this.close(), this.propertyObserver && (delete this.propertyObserver, this.propertyObserver = null), i !== t && (i.container.remove(), i.dropdown.remove(), e.removeClass("select2-offscreen").removeData("select2").off(".select2").prop("autofocus", this.autofocus || !1), this.elementTabIndex ? e.attr({tabindex: this.elementTabIndex}) : e.removeAttr("tabindex"), e.show())
            },optionToData: function(e) {
                return e.is("option") ? {id: e.prop("value"),text: e.text(),element: e.get(),css: e.attr("class"),disabled: e.prop("disabled"),locked: o(e.attr("locked"), "locked") || o(e.data("locked"), !0)} : e.is("optgroup") ? {text: e.attr("label"),children: [],element: e.get(),css: e.attr("class")} : void 0
            },prepareOpts: function(i) {
                var n, s, r, l, c = this;
                if (n = i.element, "select" === n.get(0).tagName.toLowerCase() && (this.select = s = i.element), s && e.each(["id", "multiple", "ajax", "query", "createSearchChoice", "initSelection", "data", "tags"], function() {
                    if (this in i)
                        throw new Error("Option '" + this + "' is not allowed for Select2 when attached to a <select> element.")
                }), i = e.extend({}, {populateResults: function(n, s, o) {
                        var a, r = this.opts.id;
                        (a = function(n, s, l) {
                            var d, u, p, h, m, f, g, v, _, b;
                            for (n = i.sortResults(n, s, o), d = 0, u = n.length; u > d; d += 1)
                                p = n[d], m = p.disabled === !0, h = !m && r(p) !== t, f = p.children && p.children.length > 0, g = e("<li></li>"), g.addClass("select2-results-dept-" + l), g.addClass("select2-result"), g.addClass(h ? "select2-result-selectable" : "select2-result-unselectable"), m && g.addClass("select2-disabled"), f && g.addClass("select2-result-with-children"), g.addClass(c.opts.formatResultCssClass(p)), v = e(document.createElement("div")), v.addClass("select2-result-label"), b = i.formatResult(p, v, o, c.opts.escapeMarkup), b !== t && v.html(b), g.append(v), f && (_ = e("<ul></ul>"), _.addClass("select2-result-sub"), a(p.children, _, l + 1), g.append(_)), g.data("select2-data", p), s.append(g)
                        })(s, n, 0)
                    }}, e.fn.select2.defaults, i), "function" != typeof i.id && (r = i.id, i.id = function(e) {
                    return e[r]
                }), e.isArray(i.element.data("select2Tags"))) {
                    if ("tags" in i)
                        throw "tags specified as both an attribute 'data-select2-tags' and in options of Select2 " + i.element.attr("id");
                    i.tags = i.element.data("select2Tags")
                }
                if (s ? (i.query = this.bind(function(e) {
                    var i, s, o, a = {results: [],more: !1}, r = e.term;
                    o = function(t, i) {
                        var n;
                        t.is("option") ? e.matcher(r, t.text(), t) && i.push(c.optionToData(t)) : t.is("optgroup") && (n = c.optionToData(t), t.children().each2(function(e, t) {
                            o(t, n.children)
                        }), n.children.length > 0 && i.push(n))
                    }, i = n.children(), this.getPlaceholder() !== t && i.length > 0 && (s = this.getPlaceholderOption(), s && (i = i.not(s))), i.each2(function(e, t) {
                        o(t, a.results)
                    }), e.callback(a)
                }), i.id = function(e) {
                    return e.id
                }, i.formatResultCssClass = function(e) {
                    return e.css
                }) : "query" in i || ("ajax" in i ? (l = i.element.data("ajax-url"), l && l.length > 0 && (i.ajax.url = l), i.query = w.call(i.element, i.ajax)) : "data" in i ? i.query = k(i.data) : "tags" in i && (i.query = x(i.tags), i.createSearchChoice === t && (i.createSearchChoice = function(t) {
                    return {id: e.trim(t),text: e.trim(t)}
                }), i.initSelection === t && (i.initSelection = function(t, n) {
                    var s = [];
                    e(a(t.val(), i.separator)).each(function() {
                        var t = {id: this,text: this}, n = i.tags;
                        e.isFunction(n) && (n = n()), e(n).each(function() {
                            return o(this.id, t.id) ? (t = this, !1) : void 0
                        }), s.push(t)
                    }), n(s)
                }))), "function" != typeof i.query)
                    throw "query function not defined for Select2 " + i.element.attr("id");
                return i
            },monitorSource: function() {
                var e, i = this.opts.element;
                i.on("change.select2", this.bind(function() {
                    this.opts.element.data("select2-change-triggered") !== !0 && this.initSelection()
                })), e = this.bind(function() {
                    var e, n = i.prop("disabled");
                    n === t && (n = !1), this.enable(!n);
                    var e = i.prop("readonly");
                    e === t && (e = !1), this.readonly(e), _(this.container, this.opts.element, this.opts.adaptContainerCssClass), this.container.addClass(j(this.opts.containerCssClass)), _(this.dropdown, this.opts.element, this.opts.adaptDropdownCssClass), this.dropdown.addClass(j(this.opts.dropdownCssClass))
                }), i.on("propertychange.select2 DOMAttrModified.select2", e), this.mutationCallback === t && (this.mutationCallback = function(t) {
                    t.forEach(e)
                }), "undefined" != typeof WebKitMutationObserver && (this.propertyObserver && (delete this.propertyObserver, this.propertyObserver = null), this.propertyObserver = new WebKitMutationObserver(this.mutationCallback), this.propertyObserver.observe(i.get(0), {attributes: !0,subtree: !1}))
            },triggerSelect: function(t) {
                var i = e.Event("select2-selecting", {val: this.id(t),object: t});
                return this.opts.element.trigger(i), !i.isDefaultPrevented()
            },triggerChange: function(t) {
                t = t || {}, t = e.extend({}, t, {type: "change",val: this.val()}), this.opts.element.data("select2-change-triggered", !0), this.opts.element.trigger(t), this.opts.element.data("select2-change-triggered", !1), this.opts.element.click(), this.opts.blurOnChange && this.opts.element.blur()
            },isInterfaceEnabled: function() {
                return this.enabledInterface === !0
            },enableInterface: function() {
                var e = this._enabled && !this._readonly, t = !e;
                return e === this.enabledInterface ? !1 : (this.container.toggleClass("select2-container-disabled", t), this.close(), this.enabledInterface = e, !0)
            },enable: function(e) {
                e === t && (e = !0), this._enabled !== e && (this._enabled = e, this.opts.element.prop("disabled", !e), this.enableInterface())
            },disable: function() {
                this.enable(!1)
            },readonly: function(e) {
                return e === t && (e = !1), this._readonly === e ? !1 : (this._readonly = e, this.opts.element.prop("readonly", e), this.enableInterface(), !0)
            },opened: function() {
                return this.container.hasClass("select2-dropdown-open")
            },positionDropdown: function() {
                var t, i, n, s, o = this.dropdown, a = this.container.offset(), r = this.container.outerHeight(!1), l = this.container.outerWidth(!1), c = o.outerHeight(!1), d = e(window).scrollLeft() + e(window).width(), u = e(window).scrollTop() + e(window).height(), p = a.top + r, h = a.left, m = u >= p + c, f = a.top - c >= this.body().scrollTop(), g = o.outerWidth(!1), v = d >= h + g, _ = o.hasClass("select2-drop-above");
                this.opts.dropdownAutoWidth ? (s = e(".select2-results", o)[0], o.addClass("select2-drop-auto-width"), o.css("width", ""), g = o.outerWidth(!1) + (s.scrollHeight === s.clientHeight ? 0 : A.width), g > l ? l = g : g = l, v = d >= h + g) : this.container.removeClass("select2-drop-auto-width"), "static" !== this.body().css("position") && (t = this.body().offset(), p -= t.top, h -= t.left), _ ? (i = !0, !f && m && (i = !1)) : (i = !1, !m && f && (i = !0)), v || (h = a.left + l - g), i ? (p = a.top - c, this.container.addClass("select2-drop-above"), o.addClass("select2-drop-above")) : (this.container.removeClass("select2-drop-above"), o.removeClass("select2-drop-above")), n = e.extend({top: p,left: h,width: l}, j(this.opts.dropdownCss)), o.css(n)
            },shouldOpen: function() {
                var t;
                return this.opened() ? !1 : this._enabled === !1 || this._readonly === !0 ? !1 : (t = e.Event("select2-opening"), this.opts.element.trigger(t), !t.isDefaultPrevented())
            },clearDropdownAlignmentPreference: function() {
                this.container.removeClass("select2-drop-above"), this.dropdown.removeClass("select2-drop-above")
            },open: function() {
                return this.shouldOpen() ? (this.opening(), !0) : !1
            },opening: function() {
                var t, i = this.containerId, n = "scroll." + i, s = "resize." + i, o = "orientationchange." + i;
                this.container.addClass("select2-dropdown-open").addClass("select2-container-active"), this.clearDropdownAlignmentPreference(), this.dropdown[0] !== this.body().children().last()[0] && this.dropdown.detach().appendTo(this.body()), t = e("#select2-drop-mask"), 0 == t.length && (t = e(document.createElement("div")), t.attr("id", "select2-drop-mask").attr("class", "select2-drop-mask"), t.hide(), t.appendTo(this.body()), t.on("mousedown touchstart click", function(t) {
                    var i, n = e("#select2-drop");
                    n.length > 0 && (i = n.data("select2"), i.opts.selectOnBlur && i.selectHighlighted({noFocus: !0}), i.close({focus: !1}), t.preventDefault(), t.stopPropagation())
                })), this.dropdown.prev()[0] !== t[0] && this.dropdown.before(t), e("#select2-drop").removeAttr("id"), this.dropdown.attr("id", "select2-drop"), t.show(), this.positionDropdown(), this.dropdown.show(), this.positionDropdown(), this.dropdown.addClass("select2-drop-active");
                var a = this;
                this.container.parents().add(window).each(function() {
                    e(this).on(s + " " + n + " " + o, function() {
                        a.positionDropdown()
                    })
                })
            },close: function() {
                if (this.opened()) {
                    var t = this.containerId, i = "scroll." + t, n = "resize." + t, s = "orientationchange." + t;
                    this.container.parents().add(window).each(function() {
                        e(this).off(i).off(n).off(s)
                    }), this.clearDropdownAlignmentPreference(), e("#select2-drop-mask").hide(), this.dropdown.removeAttr("id"), this.dropdown.hide(), this.container.removeClass("select2-dropdown-open").removeClass("select2-container-active"), this.results.empty(), this.clearSearch(), this.search.removeClass("select2-active"), this.opts.element.trigger(e.Event("select2-close"))
                }
            },externalSearch: function(e) {
                this.open(), this.search.val(e), this.updateResults(!1)
            },clearSearch: function() {
            },getMaximumSelectionSize: function() {
                return j(this.opts.maximumSelectionSize)
            },ensureHighlightVisible: function() {
                var t, i, n, s, o, a, r, l = this.results;
                if (i = this.highlight(), !(0 > i)) {
                    if (0 == i)
                        return void l.scrollTop(0);
                    t = this.findHighlightableChoices().find(".select2-result-label"), n = e(t[i]), s = n.offset().top + n.outerHeight(!0), i === t.length - 1 && (r = l.find("li.select2-more-results"), r.length > 0 && (s = r.offset().top + r.outerHeight(!0))), o = l.offset().top + l.outerHeight(!0), s > o && l.scrollTop(l.scrollTop() + (s - o)), a = n.offset().top - l.offset().top, 0 > a && "none" != n.css("display") && l.scrollTop(l.scrollTop() + a)
                }
            },findHighlightableChoices: function() {
                return this.results.find(".select2-result-selectable:not(.select2-disabled)")
            },moveHighlight: function(t) {
                for (var i = this.findHighlightableChoices(), n = this.highlight(); n > -1 && n < i.length; ) {
                    n += t;
                    var s = e(i[n]);
                    if (s.hasClass("select2-result-selectable") && !s.hasClass("select2-disabled") && !s.hasClass("select2-selected")) {
                        this.highlight(n);
                        break
                    }
                }
            },highlight: function(t) {
                var i, s, o = this.findHighlightableChoices();
                return 0 === arguments.length ? n(o.filter(".select2-highlighted")[0], o.get()) : (t >= o.length && (t = o.length - 1), 0 > t && (t = 0), this.removeHighlight(), i = e(o[t]), i.addClass("select2-highlighted"), this.ensureHighlightVisible(), s = i.data("select2-data"), void (s && this.opts.element.trigger({type: "select2-highlight",val: this.id(s),choice: s})))
            },removeHighlight: function() {
                this.results.find(".select2-highlighted").removeClass("select2-highlighted")
            },countSelectableResults: function() {
                return this.findHighlightableChoices().length
            },highlightUnderEvent: function(t) {
                var i = e(t.target).closest(".select2-result-selectable");
                if (i.length > 0 && !i.is(".select2-highlighted")) {
                    var n = this.findHighlightableChoices();
                    this.highlight(n.index(i))
                } else
                    0 == i.length && this.removeHighlight()
            },loadMoreIfNeeded: function() {
                var e, t = this.results, i = t.find("li.select2-more-results"), n = this.resultsPage + 1, s = this, o = this.search.val(), a = this.context;
                0 !== i.length && (e = i.offset().top - t.offset().top - t.height(), e <= this.opts.loadMorePadding && (i.addClass("select2-active"), this.opts.query({element: this.opts.element,term: o,page: n,context: a,matcher: this.opts.matcher,callback: this.bind(function(e) {
                        s.opened() && (s.opts.populateResults.call(this, t, e.results, {term: o,page: n,context: a}), s.postprocessResults(e, !1, !1), e.more === !0 ? (i.detach().appendTo(t).text(s.opts.formatLoadMore(n + 1)), window.setTimeout(function() {
                            s.loadMoreIfNeeded()
                        }, 10)) : i.remove(), s.positionDropdown(), s.resultsPage = n, s.context = e.context, this.opts.element.trigger({type: "select2-loaded",items: e}))
                    })})))
            },tokenize: function() {
            },updateResults: function(i) {
                function n() {
                    c.removeClass("select2-active"), p.positionDropdown()
                }
                function s(e) {
                    d.html(e), n()
                }
                var a, r, l, c = this.search, d = this.results, u = this.opts, p = this, h = c.val(), m = e.data(this.container, "select2-last-term");
                if ((i === !0 || !m || !o(h, m)) && (e.data(this.container, "select2-last-term", h), i === !0 || this.showSearchInput !== !1 && this.opened())) {
                    l = ++this.queryCount;
                    var f = this.getMaximumSelectionSize();
                    if (f >= 1 && (a = this.data(), e.isArray(a) && a.length >= f && C(u.formatSelectionTooBig, "formatSelectionTooBig")))
                        return void s("<li class='select2-selection-limit'>" + u.formatSelectionTooBig(f) + "</li>");
                    if (c.val().length < u.minimumInputLength)
                        return s(C(u.formatInputTooShort, "formatInputTooShort") ? "<li class='select2-no-results'>" + u.formatInputTooShort(c.val(), u.minimumInputLength) + "</li>" : ""), void (i && this.showSearch && this.showSearch(!0));
                    if (u.maximumInputLength && c.val().length > u.maximumInputLength)
                        return void s(C(u.formatInputTooLong, "formatInputTooLong") ? "<li class='select2-no-results'>" + u.formatInputTooLong(c.val(), u.maximumInputLength) + "</li>" : "");
                    u.formatSearching && 0 === this.findHighlightableChoices().length && s("<li class='select2-searching'>" + u.formatSearching() + "</li>"), c.addClass("select2-active"), this.removeHighlight(), r = this.tokenize(), r != t && null != r && c.val(r), this.resultsPage = 1, u.query({element: u.element,term: c.val(),page: this.resultsPage,context: null,matcher: u.matcher,callback: this.bind(function(a) {
                            var r;
                            if (l == this.queryCount) {
                                if (!this.opened())
                                    return void this.search.removeClass("select2-active");
                                if (this.context = a.context === t ? null : a.context, this.opts.createSearchChoice && "" !== c.val() && (r = this.opts.createSearchChoice.call(p, c.val(), a.results), r !== t && null !== r && p.id(r) !== t && null !== p.id(r) && 0 === e(a.results).filter(function() {
                                    return o(p.id(this), p.id(r))
                                }).length && a.results.unshift(r)), 0 === a.results.length && C(u.formatNoMatches, "formatNoMatches"))
                                    return void s("<li class='select2-no-results'>" + u.formatNoMatches(c.val()) + "</li>");
                                d.empty(), p.opts.populateResults.call(this, d, a.results, {term: c.val(),page: this.resultsPage,context: null}), a.more === !0 && C(u.formatLoadMore, "formatLoadMore") && (d.append("<li class='select2-more-results'>" + p.opts.escapeMarkup(u.formatLoadMore(this.resultsPage)) + "</li>"), window.setTimeout(function() {
                                    p.loadMoreIfNeeded()
                                }, 10)), this.postprocessResults(a, i), n(), this.opts.element.trigger({type: "select2-loaded",items: a})
                            }
                        })})
                }
            },cancel: function() {
                this.close()
            },blur: function() {
                this.opts.selectOnBlur && this.selectHighlighted({noFocus: !0}), this.close(), this.container.removeClass("select2-container-active"), this.search[0] === document.activeElement && this.search.blur(), this.clearSearch(), this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus")
            },focusSearch: function() {
                h(this.search)
            },selectHighlighted: function(e) {
                var t = this.highlight(), i = this.results.find(".select2-highlighted"), n = i.closest(".select2-result").data("select2-data");
                n ? (this.highlight(t), this.onSelect(n, e)) : e && e.noFocus && this.close()
            },getPlaceholder: function() {
                var e;
                return this.opts.element.attr("placeholder") || this.opts.element.attr("data-placeholder") || this.opts.element.data("placeholder") || this.opts.placeholder || ((e = this.getPlaceholderOption()) !== t ? e.text() : t)
            },getPlaceholderOption: function() {
                if (this.select) {
                    var e = this.select.children().first();
                    if (this.opts.placeholderOption !== t)
                        return "first" === this.opts.placeholderOption && e || "function" == typeof this.opts.placeholderOption && this.opts.placeholderOption(this.select);
                    if ("" === e.text() && "" === e.val())
                        return e
                }
            },initContainerWidth: function() {
                function i() {
                    var i, n, s, o, a;
                    if ("off" === this.opts.width)
                        return null;
                    if ("element" === this.opts.width)
                        return 0 === this.opts.element.outerWidth(!1) ? "auto" : this.opts.element.outerWidth(!1) + "px";
                    if ("copy" === this.opts.width || "resolve" === this.opts.width) {
                        if (i = this.opts.element.attr("style"), i !== t)
                            for (n = i.split(";"), o = 0, a = n.length; a > o; o += 1)
                                if (s = n[o].replace(/\s/g, "").match(/[^-]width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i), null !== s && s.length >= 1)
                                    return s[1];
                        return "resolve" === this.opts.width ? (i = this.opts.element.css("width"), i.indexOf("%") > 0 ? i : 0 === this.opts.element.outerWidth(!1) ? "auto" : this.opts.element.outerWidth(!1) + "px") : null
                    }
                    return e.isFunction(this.opts.width) ? this.opts.width() : this.opts.width
                }
                var n = i.call(this);
                null !== n && this.container.css("width", n)
            }}), N = $(M, {createContainer: function() {
                var t = e(document.createElement("div")).attr({"class": "select2-container"}).html(["<a href='javascript:void(0)' onclick='return false;' class='select2-choice' tabindex='-1'>", "   <span class='select2-chosen'>&nbsp;</span><abbr class='select2-search-choice-close'></abbr>", "   <span class='select2-arrow'><b></b></span>", "</a>", "<input class='select2-focusser select2-offscreen' type='text'/>", "<div class='select2-drop select2-display-none'>", "   <div class='select2-search'>", "       <input type='text' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false' class='select2-input'/>", "   </div>", "   <ul class='select2-results'>", "   </ul>", "</div>"].join(""));
                return t
            },enableInterface: function() {
                this.parent.enableInterface.apply(this, arguments) && this.focusser.prop("disabled", !this.isInterfaceEnabled())
            },opening: function() {
                var i, n, s;
                this.opts.minimumResultsForSearch >= 0 && this.showSearch(!0), this.parent.opening.apply(this, arguments), this.showSearchInput !== !1 && this.search.val(this.focusser.val()), this.search.focus(), i = this.search.get(0), i.createTextRange ? (n = i.createTextRange(), n.collapse(!1), n.select()) : i.setSelectionRange && (s = this.search.val().length, i.setSelectionRange(s, s)), "" === this.search.val() && this.nextSearchTerm != t && (this.search.val(this.nextSearchTerm), this.search.select()), this.focusser.prop("disabled", !0).val(""), this.updateResults(!0), this.opts.element.trigger(e.Event("select2-open"))
            },close: function(e) {
                this.opened() && (this.parent.close.apply(this, arguments), e = e || {focus: !0}, this.focusser.removeAttr("disabled"), e.focus && this.focusser.focus())
            },focus: function() {
                this.opened() ? this.close() : (this.focusser.removeAttr("disabled"), this.focusser.focus())
            },isFocused: function() {
                return this.container.hasClass("select2-container-active")
            },cancel: function() {
                this.parent.cancel.apply(this, arguments), this.focusser.removeAttr("disabled"), this.focusser.focus()
            },destroy: function() {
                e("label[for='" + this.focusser.attr("id") + "']").attr("for", this.opts.element.attr("id")), this.parent.destroy.apply(this, arguments)
            },initContainer: function() {
                var t, i = this.container, n = this.dropdown;
                this.showSearch(this.opts.minimumResultsForSearch < 0 ? !1 : !0), this.selection = t = i.find(".select2-choice"), this.focusser = i.find(".select2-focusser"), this.focusser.attr("id", "s2id_autogen" + I()), e("label[for='" + this.opts.element.attr("id") + "']").attr("for", this.focusser.attr("id")), this.focusser.attr("tabindex", this.elementTabIndex), this.search.on("keydown", this.bind(function(e) {
                    if (this.isInterfaceEnabled()) {
                        if (e.which === D.PAGE_UP || e.which === D.PAGE_DOWN)
                            return void f(e);
                        switch (e.which) {
                            case D.UP:
                            case D.DOWN:
                                return this.moveHighlight(e.which === D.UP ? -1 : 1), void f(e);
                            case D.ENTER:
                                return this.selectHighlighted(), void f(e);
                            case D.TAB:
                                return void this.selectHighlighted({noFocus: !0});
                            case D.ESC:
                                return this.cancel(e), void f(e)
                        }
                    }
                })), this.search.on("blur", this.bind(function() {
                    document.activeElement === this.body().get(0) && window.setTimeout(this.bind(function() {
                        this.search.focus()
                    }), 0)
                })), this.focusser.on("keydown", this.bind(function(e) {
                    if (this.isInterfaceEnabled() && e.which !== D.TAB && !D.isControl(e) && !D.isFunctionKey(e) && e.which !== D.ESC) {
                        if (this.opts.openOnEnter === !1 && e.which === D.ENTER)
                            return void f(e);
                        if (e.which == D.DOWN || e.which == D.UP || e.which == D.ENTER && this.opts.openOnEnter) {
                            if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey)
                                return;
                            return this.open(), void f(e)
                        }
                        return e.which == D.DELETE || e.which == D.BACKSPACE ? (this.opts.allowClear && this.clear(), void f(e)) : void 0
                    }
                })), l(this.focusser), this.focusser.on("keyup-change input", this.bind(function(e) {
                    if (this.opts.minimumResultsForSearch >= 0) {
                        if (e.stopPropagation(), this.opened())
                            return;
                        this.open()
                    }
                })), t.on("mousedown", "abbr", this.bind(function(e) {
                    this.isInterfaceEnabled() && (this.clear(), g(e), this.close(), this.selection.focus())
                })), t.on("mousedown", this.bind(function(t) {
                    this.container.hasClass("select2-container-active") || this.opts.element.trigger(e.Event("select2-focus")), this.opened() ? this.close() : this.isInterfaceEnabled() && this.open(), f(t)
                })), n.on("mousedown", this.bind(function() {
                    this.search.focus()
                })), t.on("focus", this.bind(function(e) {
                    f(e)
                })), this.focusser.on("focus", this.bind(function() {
                    this.container.hasClass("select2-container-active") || this.opts.element.trigger(e.Event("select2-focus")), this.container.addClass("select2-container-active")
                })).on("blur", this.bind(function() {
                    this.opened() || (this.container.removeClass("select2-container-active"), this.opts.element.trigger(e.Event("select2-blur")))
                })), this.search.on("focus", this.bind(function() {
                    this.container.hasClass("select2-container-active") || this.opts.element.trigger(e.Event("select2-focus")), this.container.addClass("select2-container-active")
                })), this.initContainerWidth(), this.opts.element.addClass("select2-offscreen"), this.setPlaceholder()
            },clear: function(t) {
                var i = this.selection.data("select2-data");
                if (i) {
                    var n = e.Event("select2-clearing");
                    if (this.opts.element.trigger(n), n.isDefaultPrevented())
                        return;
                    var s = this.getPlaceholderOption();
                    this.opts.element.val(s ? s.val() : ""), this.selection.find(".select2-chosen").empty(), this.selection.removeData("select2-data"), this.setPlaceholder(), t !== !1 && (this.opts.element.trigger({type: "select2-removed",val: this.id(i),choice: i}), this.triggerChange({removed: i}))
                }
            },initSelection: function() {
                if (this.isPlaceholderOptionSelected())
                    this.updateSelection(null), this.close(), this.setPlaceholder();
                else {
                    var e = this;
                    this.opts.initSelection.call(null, this.opts.element, function(i) {
                        i !== t && null !== i && (e.updateSelection(i), e.close(), e.setPlaceholder())
                    })
                }
            },isPlaceholderOptionSelected: function() {
                var e;
                return this.getPlaceholder() ? (e = this.getPlaceholderOption()) !== t && e.is(":selected") || "" === this.opts.element.val() || this.opts.element.val() === t || null === this.opts.element.val() : !1
            },prepareOpts: function() {
                var t = this.parent.prepareOpts.apply(this, arguments), i = this;
                return "select" === t.element.get(0).tagName.toLowerCase() ? t.initSelection = function(e, t) {
                    var n = e.find(":selected");
                    t(i.optionToData(n))
                } : "data" in t && (t.initSelection = t.initSelection || function(i, n) {
                    var s = i.val(), a = null;
                    t.query({matcher: function(e, i, n) {
                            var r = o(s, t.id(n));
                            return r && (a = n), r
                        },callback: e.isFunction(n) ? function() {
                            n(a)
                        } : e.noop})
                }), t
            },getPlaceholder: function() {
                return this.select && this.getPlaceholderOption() === t ? t : this.parent.getPlaceholder.apply(this, arguments)
            },setPlaceholder: function() {
                var e = this.getPlaceholder();
                if (this.isPlaceholderOptionSelected() && e !== t) {
                    if (this.select && this.getPlaceholderOption() === t)
                        return;
                    this.selection.find(".select2-chosen").html(this.opts.escapeMarkup(e)), this.selection.addClass("select2-default"), this.container.removeClass("select2-allowclear")
                }
            },postprocessResults: function(e, t, i) {
                var n = 0, s = this;
                if (this.findHighlightableChoices().each2(function(e, t) {
                    return o(s.id(t.data("select2-data")), s.opts.element.val()) ? (n = e, !1) : void 0
                }), i !== !1 && this.highlight(t === !0 && n >= 0 ? n : 0), t === !0) {
                    var a = this.opts.minimumResultsForSearch;
                    a >= 0 && this.showSearch(T(e.results) >= a)
                }
            },showSearch: function(t) {
                this.showSearchInput !== t && (this.showSearchInput = t, this.dropdown.find(".select2-search").toggleClass("select2-search-hidden", !t), this.dropdown.find(".select2-search").toggleClass("select2-offscreen", !t), e(this.dropdown, this.container).toggleClass("select2-with-searchbox", t))
            },onSelect: function(e, t) {
                if (this.triggerSelect(e)) {
                    var i = this.opts.element.val(), n = this.data();
                    this.opts.element.val(this.id(e)), this.updateSelection(e), this.opts.element.trigger({type: "select2-selected",val: this.id(e),choice: e}), this.nextSearchTerm = this.opts.nextSearchTerm(e, this.search.val()), this.close(), t && t.noFocus || this.focusser.focus(), o(i, this.id(e)) || this.triggerChange({added: e,removed: n})
                }
            },updateSelection: function(e) {
                var i, n, s = this.selection.find(".select2-chosen");
                this.selection.data("select2-data", e), s.empty(), null !== e && (i = this.opts.formatSelection(e, s, this.opts.escapeMarkup)), i !== t && s.append(i), n = this.opts.formatSelectionCssClass(e, s), n !== t && s.addClass(n), this.selection.removeClass("select2-default"), this.opts.allowClear && this.getPlaceholder() !== t && this.container.addClass("select2-allowclear")
            },val: function() {
                var e, i = !1, n = null, s = this, o = this.data();
                if (0 === arguments.length)
                    return this.opts.element.val();
                if (e = arguments[0], arguments.length > 1 && (i = arguments[1]), this.select)
                    this.select.val(e).find(":selected").each2(function(e, t) {
                        return n = s.optionToData(t), !1
                    }), this.updateSelection(n), this.setPlaceholder(), i && this.triggerChange({added: n,removed: o});
                else {
                    if (!e && 0 !== e)
                        return void this.clear(i);
                    if (this.opts.initSelection === t)
                        throw new Error("cannot call val() if initSelection() is not defined");
                    this.opts.element.val(e), this.opts.initSelection(this.opts.element, function(e) {
                        s.opts.element.val(e ? s.id(e) : ""), s.updateSelection(e), s.setPlaceholder(), i && s.triggerChange({added: e,removed: o})
                    })
                }
            },clearSearch: function() {
                this.search.val(""), this.focusser.val("")
            },data: function(e) {
                var i, n = !1;
                return 0 === arguments.length ? (i = this.selection.data("select2-data"), i == t && (i = null), i) : (arguments.length > 1 && (n = arguments[1]), void (e ? (i = this.data(), this.opts.element.val(e ? this.id(e) : ""), this.updateSelection(e), n && this.triggerChange({added: e,removed: i})) : this.clear(n)))
            }}), E = $(M, {createContainer: function() {
                var t = e(document.createElement("div")).attr({"class": "select2-container select2-container-multi"}).html(["<ul class='select2-choices'>", "  <li class='select2-search-field'>", "    <input type='text' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false' class='select2-input'>", "  </li>", "</ul>", "<div class='select2-drop select2-drop-multi select2-display-none'>", "   <ul class='select2-results'>", "   </ul>", "</div>"].join(""));
                return t
            },prepareOpts: function() {
                var t = this.parent.prepareOpts.apply(this, arguments), i = this;
                return "select" === t.element.get(0).tagName.toLowerCase() ? t.initSelection = function(e, t) {
                    var n = [];
                    e.find(":selected").each2(function(e, t) {
                        n.push(i.optionToData(t))
                    }), t(n)
                } : "data" in t && (t.initSelection = t.initSelection || function(i, n) {
                    var s = a(i.val(), t.separator), r = [];
                    t.query({matcher: function(i, n, a) {
                            var l = e.grep(s, function(e) {
                                return o(e, t.id(a))
                            }).length;
                            return l && r.push(a), l
                        },callback: e.isFunction(n) ? function() {
                            for (var e = [], i = 0; i < s.length; i++)
                                for (var a = s[i], l = 0; l < r.length; l++) {
                                    var c = r[l];
                                    if (o(a, t.id(c))) {
                                        e.push(c), r.splice(l, 1);
                                        break
                                    }
                                }
                            n(e)
                        } : e.noop})
                }), t
            },selectChoice: function(e) {
                var t = this.container.find(".select2-search-choice-focus");
                t.length && e && e[0] == t[0] || (t.length && this.opts.element.trigger("choice-deselected", t), t.removeClass("select2-search-choice-focus"), e && e.length && (this.close(), e.addClass("select2-search-choice-focus"), this.opts.element.trigger("choice-selected", e)))
            },destroy: function() {
                e("label[for='" + this.search.attr("id") + "']").attr("for", this.opts.element.attr("id")), this.parent.destroy.apply(this, arguments)
            },initContainer: function() {
                var t, i = ".select2-choices";
                this.searchContainer = this.container.find(".select2-search-field"), this.selection = t = this.container.find(i);
                var n = this;
                this.selection.on("click", ".select2-search-choice:not(.select2-locked)", function() {
                    n.search[0].focus(), n.selectChoice(e(this))
                }), this.search.attr("id", "s2id_autogen" + I()), e("label[for='" + this.opts.element.attr("id") + "']").attr("for", this.search.attr("id")), this.search.on("input paste", this.bind(function() {
                    this.isInterfaceEnabled() && (this.opened() || this.open())
                })), this.search.attr("tabindex", this.elementTabIndex), this.keydowns = 0, this.search.on("keydown", this.bind(function(e) {
                    if (this.isInterfaceEnabled()) {
                        ++this.keydowns;
                        var i = t.find(".select2-search-choice-focus"), n = i.prev(".select2-search-choice:not(.select2-locked)"), s = i.next(".select2-search-choice:not(.select2-locked)"), o = m(this.search);
                        if (i.length && (e.which == D.LEFT || e.which == D.RIGHT || e.which == D.BACKSPACE || e.which == D.DELETE || e.which == D.ENTER)) {
                            var a = i;
                            return e.which == D.LEFT && n.length ? a = n : e.which == D.RIGHT ? a = s.length ? s : null : e.which === D.BACKSPACE ? (this.unselect(i.first()), this.search.width(10), a = n.length ? n : s) : e.which == D.DELETE ? (this.unselect(i.first()), this.search.width(10), a = s.length ? s : null) : e.which == D.ENTER && (a = null), this.selectChoice(a), f(e), void (a && a.length || this.open())
                        }
                        if ((e.which === D.BACKSPACE && 1 == this.keydowns || e.which == D.LEFT) && 0 == o.offset && !o.length)
                            return this.selectChoice(t.find(".select2-search-choice:not(.select2-locked)").last()), void f(e);
                        if (this.selectChoice(null), this.opened())
                            switch (e.which) {
                                case D.UP:
                                case D.DOWN:
                                    return this.moveHighlight(e.which === D.UP ? -1 : 1), void f(e);
                                case D.ENTER:
                                    return this.selectHighlighted(), void f(e);
                                case D.TAB:
                                    return this.selectHighlighted({noFocus: !0}), void this.close();
                                case D.ESC:
                                    return this.cancel(e), void f(e)
                            }
                        if (e.which !== D.TAB && !D.isControl(e) && !D.isFunctionKey(e) && e.which !== D.BACKSPACE && e.which !== D.ESC) {
                            if (e.which === D.ENTER) {
                                if (this.opts.openOnEnter === !1)
                                    return;
                                if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey)
                                    return
                            }
                            this.open(), (e.which === D.PAGE_UP || e.which === D.PAGE_DOWN) && f(e), e.which === D.ENTER && f(e)
                        }
                    }
                })), this.search.on("keyup", this.bind(function() {
                    this.keydowns = 0, this.resizeSearch()
                })), this.search.on("blur", this.bind(function(t) {
                    this.container.removeClass("select2-container-active"), this.search.removeClass("select2-focused"), this.selectChoice(null), this.opened() || this.clearSearch(), t.stopImmediatePropagation(), this.opts.element.trigger(e.Event("select2-blur"))
                })), this.container.on("click", i, this.bind(function(t) {
                    this.isInterfaceEnabled() && (e(t.target).closest(".select2-search-choice").length > 0 || (this.selectChoice(null), this.clearPlaceholder(), this.container.hasClass("select2-container-active") || this.opts.element.trigger(e.Event("select2-focus")), this.open(), this.focusSearch(), t.preventDefault()))
                })), this.container.on("focus", i, this.bind(function() {
                    this.isInterfaceEnabled() && (this.container.hasClass("select2-container-active") || this.opts.element.trigger(e.Event("select2-focus")), this.container.addClass("select2-container-active"), this.dropdown.addClass("select2-drop-active"), this.clearPlaceholder())
                })), this.initContainerWidth(), this.opts.element.addClass("select2-offscreen"), this.clearSearch()
            },enableInterface: function() {
                this.parent.enableInterface.apply(this, arguments) && this.search.prop("disabled", !this.isInterfaceEnabled())
            },initSelection: function() {
                if ("" === this.opts.element.val() && "" === this.opts.element.text() && (this.updateSelection([]), this.close(), this.clearSearch()), this.select || "" !== this.opts.element.val()) {
                    var e = this;
                    this.opts.initSelection.call(null, this.opts.element, function(i) {
                        i !== t && null !== i && (e.updateSelection(i), e.close(), e.clearSearch())
                    })
                }
            },clearSearch: function() {
                var e = this.getPlaceholder(), i = this.getMaxSearchWidth();
                e !== t && 0 === this.getVal().length && this.search.hasClass("select2-focused") === !1 ? (this.search.val(e).addClass("select2-default"), this.search.width(i > 0 ? i : this.container.css("width"))) : this.search.val("").width(10)
            },clearPlaceholder: function() {
                this.search.hasClass("select2-default") && this.search.val("").removeClass("select2-default")
            },opening: function() {
                this.clearPlaceholder(), this.resizeSearch(), this.parent.opening.apply(this, arguments), this.focusSearch(), this.updateResults(!0), this.search.focus(), this.opts.element.trigger(e.Event("select2-open"))
            },close: function() {
                this.opened() && this.parent.close.apply(this, arguments)
            },focus: function() {
                this.close(), this.search.focus()
            },isFocused: function() {
                return this.search.hasClass("select2-focused")
            },updateSelection: function(t) {
                var i = [], s = [], o = this;
                e(t).each(function() {
                    n(o.id(this), i) < 0 && (i.push(o.id(this)), s.push(this))
                }), t = s, this.selection.find(".select2-search-choice").remove(), e(t).each(function() {
                    o.addSelectedChoice(this)
                }), o.postprocessResults()
            },tokenize: function() {
                var e = this.search.val();
                e = this.opts.tokenizer.call(this, e, this.data(), this.bind(this.onSelect), this.opts), null != e && e != t && (this.search.val(e), e.length > 0 && this.open())
            },onSelect: function(e, t) {
                this.triggerSelect(e) && (this.addSelectedChoice(e), this.opts.element.trigger({type: "selected",val: this.id(e),choice: e}), (this.select || !this.opts.closeOnSelect) && this.postprocessResults(e, !1, this.opts.closeOnSelect === !0), this.opts.closeOnSelect ? (this.close(), this.search.width(10)) : this.countSelectableResults() > 0 ? (this.search.width(10), this.resizeSearch(), this.getMaximumSelectionSize() > 0 && this.val().length >= this.getMaximumSelectionSize() && this.updateResults(!0), this.positionDropdown()) : (this.close(), this.search.width(10)), this.triggerChange({added: e}), t && t.noFocus || this.focusSearch())
            },cancel: function() {
                this.close(), this.focusSearch()
            },addSelectedChoice: function(i) {
                var n, s, o = !i.locked, a = e("<li class='select2-search-choice'>    <div></div>    <a href='#' onclick='return false;' class='select2-search-choice-close' tabindex='-1'></a></li>"), r = e("<li class='select2-search-choice select2-locked'><div></div></li>"), l = o ? a : r, c = this.id(i), d = this.getVal();
                n = this.opts.formatSelection(i, l.find("div"), this.opts.escapeMarkup), n != t && l.find("div").replaceWith("<div>" + n + "</div>"), s = this.opts.formatSelectionCssClass(i, l.find("div")), s != t && l.addClass(s), o && l.find(".select2-search-choice-close").on("mousedown", f).on("click dblclick", this.bind(function(t) {
                    this.isInterfaceEnabled() && (e(t.target).closest(".select2-search-choice").fadeOut("fast", this.bind(function() {
                        this.unselect(e(t.target)), this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus"), this.close(), this.focusSearch()
                    })).dequeue(), f(t))
                })).on("focus", this.bind(function() {
                    this.isInterfaceEnabled() && (this.container.addClass("select2-container-active"), this.dropdown.addClass("select2-drop-active"))
                })), l.data("select2-data", i), l.insertBefore(this.searchContainer), d.push(c), this.setVal(d)
            },unselect: function(e) {
                var t, i, s = this.getVal();
                if (e = e.closest(".select2-search-choice"), 0 === e.length)
                    throw "Invalid argument: " + e + ". Must be .select2-search-choice";
                if (t = e.data("select2-data")) {
                    for (; (i = n(this.id(t), s)) >= 0; )
                        s.splice(i, 1), this.setVal(s), this.select && this.postprocessResults();
                    e.remove(), this.opts.element.trigger({type: "removed",val: this.id(t),choice: t}), this.triggerChange({removed: t})
                }
            },postprocessResults: function(e, t, i) {
                var s = this.getVal(), o = this.results.find(".select2-result"), a = this.results.find(".select2-result-with-children"), r = this;
                o.each2(function(e, t) {
                    var i = r.id(t.data("select2-data"));
                    n(i, s) >= 0 && (t.addClass("select2-selected"), t.find(".select2-result-selectable").addClass("select2-selected"))
                }), a.each2(function(e, t) {
                    t.is(".select2-result-selectable") || 0 !== t.find(".select2-result-selectable:not(.select2-selected)").length || t.addClass("select2-selected")
                }), -1 == this.highlight() && i !== !1 && r.highlight(0), !this.opts.createSearchChoice && !o.filter(".select2-result:not(.select2-selected)").length > 0 && (!e || e && !e.more && 0 === this.results.find(".select2-no-results").length) && C(r.opts.formatNoMatches, "formatNoMatches") && this.results.append("<li class='select2-no-results'>" + r.opts.formatNoMatches(r.search.val()) + "</li>")
            },getMaxSearchWidth: function() {
                return this.selection.width() - r(this.search)
            },resizeSearch: function() {
                var e, t, i, n, s, o = r(this.search);
                e = v(this.search) + 10, t = this.search.offset().left, i = this.selection.width(), n = this.selection.offset().left, s = i - (t - n) - o, e > s && (s = i - o), 40 > s && (s = i - o), 0 >= s && (s = e), this.search.width(Math.floor(s))
            },getVal: function() {
                var e;
                return this.select ? (e = this.select.val(), null === e ? [] : e) : (e = this.opts.element.val(), a(e, this.opts.separator))
            },setVal: function(t) {
                var i;
                this.select ? this.select.val(t) : (i = [], e(t).each(function() {
                    n(this, i) < 0 && i.push(this)
                }), this.opts.element.val(0 === i.length ? "" : i.join(this.opts.separator)))
            },buildChangeDetails: function(e, t) {
                for (var t = t.slice(0), e = e.slice(0), i = 0; i < t.length; i++)
                    for (var n = 0; n < e.length; n++)
                        o(this.opts.id(t[i]), this.opts.id(e[n])) && (t.splice(i, 1), i--, e.splice(n, 1), n--);
                return {added: t,removed: e}
            },val: function(i, n) {
                var s, o = this;
                if (0 === arguments.length)
                    return this.getVal();
                if (s = this.data(), s.length || (s = []), !i && 0 !== i)
                    return this.opts.element.val(""), this.updateSelection([]), this.clearSearch(), void (n && this.triggerChange({added: this.data(),removed: s}));
                if (this.setVal(i), this.select)
                    this.opts.initSelection(this.select, this.bind(this.updateSelection)), n && this.triggerChange(this.buildChangeDetails(s, this.data()));
                else {
                    if (this.opts.initSelection === t)
                        throw new Error("val() cannot be called if initSelection() is not defined");
                    this.opts.initSelection(this.opts.element, function(t) {
                        var i = e.map(t, o.id);
                        o.setVal(i), o.updateSelection(t), o.clearSearch(), n && o.triggerChange(o.buildChangeDetails(s, this.data()))
                    })
                }
                this.clearSearch()
            },onSortStart: function() {
                if (this.select)
                    throw new Error("Sorting of elements is not supported when attached to <select>. Attach to <input type='hidden'/> instead.");
                this.search.width(0), this.searchContainer.hide()
            },onSortEnd: function() {
                var t = [], i = this;
                this.searchContainer.show(), this.searchContainer.appendTo(this.searchContainer.parent()), this.resizeSearch(), this.selection.find(".select2-search-choice").each(function() {
                    t.push(i.opts.id(e(this).data("select2-data")))
                }), this.setVal(t), this.triggerChange()
            },data: function(t, i) {
                var n, s, o = this;
                return 0 === arguments.length ? this.selection.find(".select2-search-choice").map(function() {
                    return e(this).data("select2-data")
                }).get() : (s = this.data(), t || (t = []), n = e.map(t, function(e) {
                    return o.opts.id(e)
                }), this.setVal(n), this.updateSelection(t), this.clearSearch(), i && this.triggerChange(this.buildChangeDetails(s, this.data())), void 0)
            }}), e.fn.select2 = function() {
            var i, s, o, a, r, l = Array.prototype.slice.call(arguments, 0), c = ["val", "destroy", "opened", "open", "close", "focus", "isFocused", "container", "dropdown", "onSortStart", "onSortEnd", "enable", "disable", "readonly", "positionDropdown", "data", "search"], d = ["opened", "isFocused", "container", "dropdown"], u = ["val", "data"], p = {search: "externalSearch"};
            return this.each(function() {
                if (0 === l.length || "object" == typeof l[0])
                    i = 0 === l.length ? {} : e.extend({}, l[0]), i.element = e(this), "select" === i.element.get(0).tagName.toLowerCase() ? r = i.element.prop("multiple") : (r = i.multiple || !1, "tags" in i && (i.multiple = r = !0)), s = r ? new E : new N, s.init(i);
                else {
                    if ("string" != typeof l[0])
                        throw "Invalid arguments to select2 plugin: " + l;
                    if (n(l[0], c) < 0)
                        throw "Unknown method: " + l[0];
                    if (a = t, s = e(this).data("select2"), s === t)
                        return;
                    if (o = l[0], "container" === o ? a = s.container : "dropdown" === o ? a = s.dropdown : (p[o] && (o = p[o]), a = s[o].apply(s, l.slice(1))), n(l[0], d) >= 0 || n(l[0], u) && 1 == l.length)
                        return !1
                }
            }), a === t ? this : a
        }, e.fn.select2.defaults = {width: "copy",loadMorePadding: 0,closeOnSelect: !0,openOnEnter: !0,containerCss: {},dropdownCss: {},containerCssClass: "",dropdownCssClass: "",formatResult: function(e, t, i, n) {
                var s = [];
                return b(e.text, i.term, s, n), s.join("")
            },formatSelection: function(e, i, n) {
                return e ? n(e.text) : t
            },sortResults: function(e) {
                return e
            },formatResultCssClass: function() {
                return t
            },formatSelectionCssClass: function() {
                return t
            },formatNoMatches: function() {
                return "No matches found"
            },formatInputTooShort: function(e, t) {
                var i = t - e.length;
                return "Please enter " + i + " more character" + (1 == i ? "" : "s")
            },formatInputTooLong: function(e, t) {
                var i = e.length - t;
                return "Please delete " + i + " character" + (1 == i ? "" : "s")
            },formatSelectionTooBig: function(e) {
                return "You can only select " + e + " item" + (1 == e ? "" : "s")
            },formatLoadMore: function() {
                return "Loading more results..."
            },formatSearching: function() {
                return "Searching..."
            },minimumResultsForSearch: 0,minimumInputLength: 0,maximumInputLength: null,maximumSelectionSize: 0,id: function(e) {
                return e.id
            },matcher: function(e, t) {
                return i("" + t).toUpperCase().indexOf(i("" + e).toUpperCase()) >= 0
            },separator: ",",tokenSeparators: [],tokenizer: S,escapeMarkup: y,blurOnChange: !1,selectOnBlur: !1,adaptContainerCssClass: function(e) {
                return e
            },adaptDropdownCssClass: function() {
                return null
            },nextSearchTerm: function() {
                return t
            }}, e.fn.select2.ajaxDefaults = {transport: e.ajax,params: {type: "GET",cache: !1,dataType: "json"}}, window.Select2 = {query: {ajax: w,local: k,tags: x},util: {debounce: d,markMatch: b,escapeMarkup: y,stripDiacritics: i},"class": {"abstract": M,single: N,multi: E}}, e.extend(e.fn.select2.defaults, {formatNoMatches: function() {
                return "没有找到匹配项"
            },formatInputTooShort: function(e, t) {
                var i = t - e.length;
                return "请再输入" + i + "个字符"
            },formatInputTooLong: function(e, t) {
                var i = e.length - t;
                return "请删掉" + i + "个字符"
            },formatSelectionTooBig: function(e) {
                return "你只能选择最多" + e + "项"
            },formatLoadMore: function() {
                return "加载结果中..."
            },formatSearching: function() {
                return "搜索中..."
            }})
    }
}(jQuery), define("select2", ["jquery"], function() {
}), define("components/pop/chosen", ["require", "underscore", "backbone", "components/pop/base", "text!components/pop/templates/chosen.html", "core/utils", "select2"], function(e) {
    {
        var t = e("underscore"), i = (e("backbone"), e("components/pop/base")), n = e("text!components/pop/templates/chosen.html"), s = e("core/utils");
        e("select2")
    }
    return i.extend({template: t.template(n),className: "popover bottom popover-chosen-wrap",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            var t = this;
            i.prototype.initialize.call(t, e), window.NC.on("chosen:hide", function() {
                t.hide()
            }), t.data = e.data
        },render: function() {
            var e = this;
            return e.$el.html(e.template({})), e
        },reset: function(e) {
            var t = this, i = e.callback, n = e.target, s = e.trigger, o = e.data;
            t.setCallback(i), t.setTarget(n), t.setTrigger(s), t.setData(o), t.positioning(), t.show()
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: e.target,my: "center top+5",at: "center bottom",collision: "none"}), e.initSkuList();
            var t = e.getSelect2Tags();
            e.openSelect2(t)
        },getSelect2Tags: function() {
            var e, i = this, n = i._skuList[i.data.id];
            return e = !t.isUndefined(n) && n.length > 0 ? n : [], i.mergeBaseData(e), e
        },mergeBaseData: function(e) {
            var i = this, n = i.data.atomData;
            if (!n || t.isEmpty(n))
                return !1;
            var s = [];
            return t(n).each(function(i) {
                var n = t(e).findWhere({text: i.text});
                n || s.push(i)
            }), e = e.concat(s)
        },initSkuList: function() {
            var e = this;
            e._skuList || (e._skuList = {}, t.each(window._global.skuTree, function(i) {
                var n = [];
                t.each(i.list, function(e) {
                    n.push({id: e._id,text: e.text})
                }), e._skuList[i._id] = n
            }))
        },openSelect2: function(e) {
            var t = this;
            t._selectedData = [], t.$(".js-select2").off("select2-selecting change").select2({allowClear: !0,multiple: !0,placeholder: "添加规格值",tags: e}).on("select2-selecting", function(e) {
                t.onSelect2Selecting(e)
            }).on("change", function(e) {
                t.onSelect2Change(e)
            }).select2("open")
        },onSelect2Selecting: function(e) {
            var i = this, n = e.object, o = $(e.target);
            t.isString(n.id) && n.id === n.text ? ($.post(window._global.url.www + "/showcase/WCGoodsSkuTree/skuLeaf.json", {text: n.text,_id: i.data.id}, function(e) {
                if (0 === e.code) {
                    var t = {id: Number(e.data),text: n.text};
                    i._selectedData.push(t), o.select2("data", i._selectedData), o.select2("close")
                } else
                    s.errorNotify(e.msg || "出错啦。")
            }, "json"), e.preventDefault()) : i._selectedData.push(n)
        },onSelect2Change: function(e) {
            var i = this;
            if (e.removed) {
                var n = t.find(i._selectedData, function(t) {
                    return t.id === e.removed.id
                });
                i._selectedData.splice(i._selectedData.indexOf(n), 1)
            }
        },clearInput: function() {
            var e = this;
            e.txt.val("")
        },setData: function(e) {
            var t = this;
            t.data = e
        },hide: function() {
            var e = this;
            return e.$(".js-select2").select2("data", "").select2("destroy"), e._selectedData = [], e.$el.hide(), e.$el
        },triggerCallback: function() {
            var e = this;
            e.callback(e._selectedData), e.hide()
        }})
}), define("text!components/pop/templates/template.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-template">\n    <div class="popover-content">\n        <h5 class="popover-template-title">请选择一个页面展示模板：</h5>\n        <ul class="popover-template-list js-popover-template-list clearfix">\n            <li class="js-popover-template-item pull-left">\n                <a href="<%= window._global.url.www %>/showcase/feature#create/1">\n                    <img src="http://imgqn.koudaitong.com/upload_files/2013/10/09/138130037668867047.jpg" alt="新鲜资讯" width="170" height="245">\n                </a>\n                <p class="text-center">新鲜资讯</p>\n            </li>\n            <li class="js-popover-template-item pull-left">\n                <a href="<%= window._global.url.www %>/showcase/feature#create/2">\n                    <img src="http://imgqn.koudaitong.com/upload_files/2013/10/09/138130036538547689.jpg" alt="新品推荐" width="170" height="245">\n                </a>\n                <p class="text-center">新品推荐</p>\n            </li>\n            <li class="js-popover-template-item pull-left">\n                <a href="<%= window._global.url.www %>/showcase/feature#create/4">\n                    <img src="<%= window._global.url.cdn_static %>/image/scroll/template2.jpg" alt="全画幅场景" width="170" height="245">\n                </a>\n                <p class="text-center">全画幅场景 <em>NEW</em></p>\n            </li>\n            <li class="js-popover-template-item pull-left">\n                <a href="<%= window._global.url.www %>/showcase/feature#create/3">\n                    <img src="http://imgqn.koudaitong.com/upload_files/2013/09/27/13802929829327758.jpg" alt="空白模板" width="170" height="245">\n                </a>\n                <p class="text-center">空白模板</p>\n            </li>\n        </ul>\n    </div>\n</div>\n'
}), define("components/pop/template", ["require", "backbone", "components/pop/base", "text!components/pop/templates/template.html", "core/utils"], function(e) {
    var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/template.html"), n = e("core/utils");
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },initialize: function(e) {
            t.prototype.initialize.call(this, e)
        },events: {"click .js-popover-template-item > a": "triggerCallback"},isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left-bottom") ? {of: e.target,my: "left top",at: "left bottom",collision: "none"} : -1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover left";
            this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        },triggerCallback: function(e) {
            var t = $(e.target), i = t.parent();
            i.attr("href") != window.location.href && n.successNotify("正在获取数据，请稍后。", void 0, {fade: !1}), this.hide()
        }})
}), define("text!components/pop/templates/categorys.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-memo">\n    <div class="popover-content">\n        <div class="category-cks">\n            <!-- <label><input class="category" name="category" type="checkbox" value="2" />女装</label>\n            <label><input class="category" name="category" type="checkbox" value="3" />男装</label>\n            <label><input class="category" name="category" type="checkbox" value="4" />美妆护肤</label>\n            <label><input class="category" name="category" type="checkbox" value="5" />男鞋</label>\n            <label><input class="category" name="category" type="checkbox" value="6" />箱包</label>\n            <label><input class="category" name="category" type="checkbox" value="7" />母婴玩具</label>\n            <label><input class="category" name="category" type="checkbox" value="8" />食品特产</label>\n            <label><input class="category" name="category" type="checkbox" value="9" />家居家纺</label>\n            <label><input class="category" name="category" type="checkbox" value="10" />创意礼品</label>\n            <label><input class="category" name="category" type="checkbox" value="11" />3C 数码</label>\n            <label><input class="category" name="category" type="checkbox" value="12" />女鞋</label>\n            <label><input class="category" name="category" type="checkbox" value="14" />男女内衣</label>\n            <label><input class="category" name="category" type="checkbox" value="15" />运动户外</label>\n            <label><input class="category" name="category" type="checkbox" value="16" />童装童鞋</label>\n            <label><input class="category" name="category" type="checkbox" value="17" />珠宝首饰</label>\n            <label><input class="category" name="category" type="checkbox" value="18" />日用百货</label>\n            <label><input class="category" name="category" type="checkbox" value="19" />汽车配件</label>\n            <label><input class="category" name="category" type="checkbox" value="20" />医药保健</label>\n            <label><input class="category" name="category" type="checkbox" value="21" />生活服务</label>\n            <label><input class="category" name="category" type="checkbox" value="22" />媒体服务</label>\n            <label><input class="category" name="category" type="checkbox" value="23" />装修建材</label>\n            <label><input class="category" name="category" type="checkbox" value="24" />服装配饰</label>\n            <label><input class="category" name="category" type="checkbox" value="25" />家用电器</label>\n            <label><input class="category" name="category" type="checkbox" value="1" />综合其它</label> -->\n        </div>\n        <% if(number != 1) { %>\n            <div class="category-actions">\n                <button type="button" class="btn js-btn-confirm" data-loading-text="确定">确定</button>\n                <span class="category-tip" style="color: #ff0000; font-size: 12px; margin-left: 20px">最多只能同时选择 <%= number %> 个分类。</span>\n            </div>\n        <% } %>\n    </div>\n</div>'
}), define("text!components/pop/templates/category.html", [], function() {
    return '<label><input class="category" name="category" type="checkbox" value="<%= id %>" /><%= name %></label>'
}), define("text!components/pop/templates/category_single.html", [], function() {
    return '<label><input class="category" name="category" type="radio" value="<%= id %>" /><%= name %></label>'
}), define("components/pop/category", ["require", "backbone", "jquery", "components/pop/base", "text!components/pop/templates/categorys.html", "text!components/pop/templates/category.html", "text!components/pop/templates/category_single.html", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("jquery")), i = e("components/pop/base"), n = e("text!components/pop/templates/categorys.html"), s = e("text!components/pop/templates/category.html"), o = e("text!components/pop/templates/category_single.html");
        e("core/utils")
    }
    return i.extend({template: _.template(n),className: "popover right",events: {"click .category": "clickCategory","click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            i.prototype.initialize.call(this, e), this.data = e.data || {}, this.data.number = this.data.number || 5, this.data.business = this.data.business || [], this.initTpls(), this.loadingCategory()
        },initTpls: function() {
            this.catTpl = _.template(s), this.catSingleTpl = _.template(o)
        },loadingCategory: function() {
            var e = this, i = window._global.url.www + "/account/team/category.json";
            t.ajax({url: i,type: "GET",dataType: "json"}).done(function(i) {
                if (0 == i.code) {
                    var n = i.data;
                    n.forEach(function(i) {
                        var n;
                        n = 1 == e.data.number ? e.catSingleTpl(i) : e.catTpl(i), t(".category-cks").append(n)
                    }), e.$el.find(".category-cks").removeClass("loading"), e.setInitialValue(e.data)
                }
            })
        },setInitialValue: function(e) {
            var i = e.business;
            t('input[name="category"]').filter(function() {
                var e = this;
                return i.some(function(t) {
                    return t.value == e.value
                })
            }).prop("checked", !0)
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e.$el.find(".category-cks").addClass("loading"), e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setInitialValue(s), this.positioning(), this.show()
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: this.target,my: "left center",at: "right center",collision: "none"})
        },clickCategory: function() {
            var e = this.data.number;
            return 1 == e ? void this.triggerCallback() : void (t('input[name="category"]:checked').length > e - 1 ? t('input[name="category').not(":checked").prop("disabled", !0) : t('input[name="category').prop("disabled", !1))
        },triggerCallback: function() {
            var e = this, i = [];
            t('input[name="category"]:checked').each(function() {
                var e = {text: t(this).parent().text(),value: t(this).val()};
                i.push(e)
            }), e.callback(i), e.hide()
        }})
}), define("text!components/pop/templates/msgcat.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-text">\n    <div class="popover-content">\n        <form class="form-horizontal">\n            <div class="control-group">\n                <label class="control-label">分类名称：</label>\n                <div class="controls">\n                    <input type="text" class="text-placeholder js-text-placeholder cat-text" vlaue="<%- text %>">\n                    <p class="help-block">应用于：对微信、微博消息添加分类</p>\n                </div>\n            </div>\n            <div class="form-actions">\n                <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定"> 确定</button>\n                <button type="reset" class="btn js-btn-cancel">取消</button>\n            </div>\n        </form>\n    </div>\n</div>\n'
}), define("components/pop/msgcat", ["require", "backbone", "jquery", "components/pop/base", "text!components/pop/templates/msgcat.html", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("jquery")), i = e("components/pop/base"), n = e("text!components/pop/templates/msgcat.html");
        e("core/utils")
    }
    return i.extend({template: _.template(n),className: "popover bottom",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            i.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: this.target,my: "center top",at: "center bottom",collision: "none"})
        },triggerCallback: function() {
            var e = this;
            e.callback({text: t.trim(t(".cat-text").val())}), e.hide()
        }})
}), define("text!components/pop/templates/tag.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-tag">\n    <div class="popover-content">\n        <form class="form-inline">\n            <input type="text" class="text-placeholder js-text-placeholder" placeholder="" maxlength="" value="<%- text %>">\n            <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n            <button type="reset" class="btn js-btn-cancel">取消</button>\n        </form>\n    </div>\n</div>'
}), define("components/pop/tag", ["require", "backbone", "jquery", "components/pop/base", "text!components/pop/templates/tag.html", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("jquery")), i = e("components/pop/base"), n = e("text!components/pop/templates/tag.html");
        e("core/utils")
    }
    return i.extend({template: _.template(n),className: "popover bottom",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            i.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: this.target,my: "center top",at: "center bottom",collision: "none"})
        },triggerCallback: function() {
            var e = this, i = t.trim(t(".popover-tag input").val());
            e.callback(i), e.hide()
        }})
}), define("text!components/pop/templates/form_actions_confirm.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-confirm">\n    <div class="popover-content text-center">\n        <div class="form-inline">\n            <div><%= options.data || \'\' %></div>\n            <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n            <button type="reset" class="btn js-btn-cancel">取消</button>\n        </div>\n    </div>\n</div>\n'
}), define("components/pop/form_actions_confirm", ["require", "backbone", "components/pop/base", "text!components/pop/templates/form_actions_confirm.html", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/form_actions_confirm.html");
        e("core/utils")
    }
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },initialize: function(e) {
            t.prototype.initialize.call(this, e)
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},render: function() {
            return this.$el.html(this.template({options: this.options})), this
        },isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), -1 !== t.indexOf("left") ? e.$el.position({of: e.target,my: "right center",at: "left center",collision: "none"}) : -1 !== t.indexOf("bottom") ? e.$el.position({of: e.target,my: "center top",at: "center bottom",collision: "none"}) : -1 !== t.indexOf("top") ? ($(".arrow", e.$el).css("margin-bottom", 4), $(".popover-inner", e.$el).css("margin-bottom", 4), e.$el.position({of: e.target,my: "center bottom",at: "center top",collision: "none"})) : e.$el.position({of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover left";
            this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        },triggerCallback: function() {
            this.callback.call(this), this.hide()
        }})
}), define("text!components/pop/templates/send_goods.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-send-goods">\n    <div class="popover-content">\n        <form class="form-horizontal">\n            <div class="control-group">\n                <label class="control-label">发货方式：</label>\n                <div class="controls">\n                    <label class="radio inline">\n                        <input type="radio" name="no_express" class="js-express" value="0"<% if (!_global.self_fetch) { %> checked<% } %>>需要物流\n                    </label>\n                    <label class="radio inline">\n                        <input type="radio" name="no_express" class="js-express" value="1"<% if (_global.self_fetch) { %> checked<% } %>>无需物流\n                    </label>\n                </div>\n            </div>\n            <div class="js-express-section">\n                <div class="control-group">\n                    <label class="control-label">物流公司：</label>\n                    <div class="controls">\n                        <select class="js-company">\n                            <option value="0">请选择一个物流公司</option>\n                            <% _.each(window._global.express, function(item, index) { %>\n                            <option value="<%= index %>"><%= item.name %></option>\n                            <% }) %>\n                        </select>\n                        <div class="help-block">\n                            标记发货后，10分钟内仅可修改一次物流信息\n                        </div>\n                    </div>\n                </div>\n                <div class="control-group">\n                    <label class="control-label">快递单号：</label>\n                    <div class="controls">\n                        <input type="text" class="input js-number">\n                    </div>\n                </div>\n            </div>\n            <div class="form-actions">\n                <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定"> 确定</button>\n                <button type="reset" class="btn js-btn-cancel">取消</button>\n            </div>\n        </form>\n    </div>\n</div>\n'
}), define("components/pop/send_goods", ["backbone", "components/pop/base", "text!components/pop/templates/send_goods.html", "core/utils", "jqueryui", "select2"], function(e, t, i, n) {
    return t.extend({template: _.template(i),events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","change .js-express": "toggleForm","keydown .js-text-placeholder": function(e) {
                e.keyCode === n.keyCode.ENTER && this.triggerCallback()
            }},onRender: function() {
            console.log(this.$(".js-company")), $.extend($.fn.select2.defaults, {formatNoMatches: function() {
                    return "找不到相关的物流公司~喵。"
                }}), this.$(".js-company").select2({placeholder: "请选择一个物流公司",width: 220})
        },toggleForm: function(e) {
            var t = $(e.target);
            1 === +t.val() ? this.$(".js-express-section").hide() : this.$(".js-express-section").show()
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : -1 !== t.indexOf("top") ? {of: e.target,my: "center bottom",at: "center top",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className + " popover-goods"
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.clearInput(), this.show()
        },triggerCallback: function() {
            var e = $.trim(this.$(".js-number").val()), t = Number($.trim(this.$(".js-company").select2("val"))), i = this.$(".js-express:checked").val();
            if (1 !== +i) {
                if (!e)
                    return void n.errorNotify("快递单号不能为空");
                if (!t)
                    return void n.errorNotify("请选择一个物流公司")
            }
            this.callback({no: e,id: t,no_express: i}), this.hide()
        },clearInput: function() {
            this.$(".js-number").val("")
        }})
}), define("text!components/pop/templates/note.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-delete">\n    <div class="popover-content text-center">\n        <div class="js-content">\n            <%= data %>\n        </div>\n    </div>\n</div>\n'
}), define("components/pop/note", ["require", "backbone", "components/pop/base", "text!components/pop/templates/note.html", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/note.html");
        e("core/utils")
    }
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },initialize: function(e) {
            t.prototype.initialize.call(this, e)
        },render: function() {
            return this.$el.html(this.template({data: this.options.data})), this
        },isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : -1 !== t.indexOf("top") ? {of: e.target,my: "center bottom",at: "center top",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover right";
            this.$(".js-content").html(e.data), this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        },triggerCallback: function() {
            this.callback.call(this), this.hide()
        }})
}), define("text!components/pop/templates/cube.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-memo">\n    <div class="popover-content">\n        <form class="form-inline">\n            <select class="js-cube-selection">\n                <% if (cols < 1) { %>\n                <option value="">最多支持4列，不能添加了。</option>\n                <% } %>\n                <% if (cols >= 1) { %>\n                <option value="1">图片占 1列</option>\n                <% } %>\n                <% if (cols >= 2) { %>\n                <option value="2">图片占 2列</option>\n                <% } %>\n                <% if (cols >= 3) { %>\n                <option value="3">图片占 3列</option>\n                <% } %>\n                <% if (cols >= 4) { %>\n                <option value="4">图片占 4列</option>\n                <% } %>\n            </select>\n            <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n            <button type="reset" class="btn js-btn-cancel">取消</button>\n        </form>\n    </div>\n</div>\n'
}), define("components/pop/cube", ["require", "backbone", "components/pop/base", "text!components/pop/templates/cube.html", "core/utils"], function(e) {
    {
        var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/cube.html");
        e("core/utils")
    }
    return t.extend({template: _.template(i),className: "popover bottom popover-cube",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this, t = this.data || {};
            return console.log(t), this.$el.html(this.template(t)), e.selection = e.$el.find(".js-cube-selection"), e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover bottom", o = e.data;
            this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(o), this.render(), this.positioning(), this.show()
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },setData: function(e) {
            this.data = e
        },triggerCallback: function() {
            var e = this, t = {show_method: e.selection.val()};
            _.isEmpty(t.show_method) || e.callback(t), e.hide()
        }})
}), define("text!components/pop/templates/timer.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-timer">\n    <div class="popover-content">\n        <div class="form-inline">\n            上架时间：\n            <div class="input-append">\n                <input id="time-input-popover" name="publish_time" readonly class="for-post input-medium js-time-input" size="16" type="text">\n                <label for="time-input-popover" class="add-on"><i class="icon-calendar"></i></label>\n            </div>\n        </div>\n        <div class="text-center">\n            <a href="javascript:void(0);" class="btn btn-primary js-btn-confirm" data-loading-text="正在提交..."> 确 定 </a>\n            <a href="javascript:void(0);" class="btn js-btn-cancel"> 取 消 </a>\n        </div>\n    </div>\n</div>\n'
}), function($) {
    if ($.ui.timepicker = $.ui.timepicker || {}, !$.ui.timepicker.version) {
        $.extend($.ui, {timepicker: {version: "1.2.2"}});
        var Timepicker = function() {
            this.regional = [], this.regional[""] = {currentText: "Now",closeText: "Done",amNames: ["AM", "A"],pmNames: ["PM", "P"],timeFormat: "HH:mm",timeSuffix: "",timeOnlyTitle: "Choose Time",timeText: "Time",hourText: "Hour",minuteText: "Minute",secondText: "Second",millisecText: "Millisecond",timezoneText: "Time Zone",isRTL: !1}, this._defaults = {showButtonPanel: !0,timeOnly: !1,showHour: !0,showMinute: !0,showSecond: !1,showMillisec: !1,showTimezone: !1,showTime: !0,stepHour: 1,stepMinute: 1,stepSecond: 1,stepMillisec: 1,hour: 0,minute: 0,second: 0,millisec: 0,timezone: null,useLocalTimezone: !1,defaultTimezone: "+0000",hourMin: 0,minuteMin: 0,secondMin: 0,millisecMin: 0,hourMax: 23,minuteMax: 59,secondMax: 59,millisecMax: 999,minDateTime: null,maxDateTime: null,onSelect: null,hourGrid: 0,minuteGrid: 0,secondGrid: 0,millisecGrid: 0,alwaysSetTime: !0,separator: " ",altFieldTimeOnly: !0,altTimeFormat: null,altSeparator: null,altTimeSuffix: null,pickerTimeFormat: null,pickerTimeSuffix: null,showTimepicker: !0,timezoneIso8601: !1,timezoneList: null,addSliderAccess: !1,sliderAccessArgs: null,controlType: "slider",defaultValue: null,parse: "strict"}, $.extend(this._defaults, this.regional[""])
        };
        $.extend(Timepicker.prototype, {$input: null,$altInput: null,$timeObj: null,inst: null,hour_slider: null,minute_slider: null,second_slider: null,millisec_slider: null,timezone_select: null,hour: 0,minute: 0,second: 0,millisec: 0,timezone: null,defaultTimezone: "+0000",hourMinOriginal: null,minuteMinOriginal: null,secondMinOriginal: null,millisecMinOriginal: null,hourMaxOriginal: null,minuteMaxOriginal: null,secondMaxOriginal: null,millisecMaxOriginal: null,ampm: "",formattedDate: "",formattedTime: "",formattedDateTime: "",timezoneList: null,units: ["hour", "minute", "second", "millisec"],control: null,setDefaults: function(e) {
                return extendRemove(this._defaults, e || {}), this
            },_newInst: function($input, o) {
                var tp_inst = new Timepicker, inlineSettings = {}, fns = {}, overrides, i;
                for (var attrName in this._defaults)
                    if (this._defaults.hasOwnProperty(attrName)) {
                        var attrValue = $input.attr("time:" + attrName);
                        if (attrValue)
                            try {
                                inlineSettings[attrName] = eval(attrValue)
                            } catch (err) {
                                inlineSettings[attrName] = attrValue
                            }
                    }
                overrides = {beforeShow: function(e, t) {
                        return $.isFunction(tp_inst._defaults.evnts.beforeShow) ? tp_inst._defaults.evnts.beforeShow.call($input[0], e, t, tp_inst) : void 0
                    },onChangeMonthYear: function(e, t, i) {
                        tp_inst._updateDateTime(i), $.isFunction(tp_inst._defaults.evnts.onChangeMonthYear) && tp_inst._defaults.evnts.onChangeMonthYear.call($input[0], e, t, i, tp_inst)
                    },onClose: function(e, t) {
                        tp_inst.timeDefined === !0 && "" !== $input.val() && tp_inst._updateDateTime(t), $.isFunction(tp_inst._defaults.evnts.onClose) && tp_inst._defaults.evnts.onClose.call($input[0], e, t, tp_inst)
                    }};
                for (i in overrides)
                    overrides.hasOwnProperty(i) && (fns[i] = o[i] || null);
                if (tp_inst._defaults = $.extend({}, this._defaults, inlineSettings, o, overrides, {evnts: fns,timepicker: tp_inst}), tp_inst.amNames = $.map(tp_inst._defaults.amNames, function(e) {
                    return e.toUpperCase()
                }), tp_inst.pmNames = $.map(tp_inst._defaults.pmNames, function(e) {
                    return e.toUpperCase()
                }), "string" == typeof tp_inst._defaults.controlType ? (void 0 === $.fn[tp_inst._defaults.controlType] && (tp_inst._defaults.controlType = "select"), tp_inst.control = tp_inst._controls[tp_inst._defaults.controlType]) : tp_inst.control = tp_inst._defaults.controlType, null === tp_inst._defaults.timezoneList) {
                    var timezoneList = ["-1200", "-1100", "-1000", "-0930", "-0900", "-0800", "-0700", "-0600", "-0500", "-0430", "-0400", "-0330", "-0300", "-0200", "-0100", "+0000", "+0100", "+0200", "+0300", "+0330", "+0400", "+0430", "+0500", "+0530", "+0545", "+0600", "+0630", "+0700", "+0800", "+0845", "+0900", "+0930", "+1000", "+1030", "+1100", "+1130", "+1200", "+1245", "+1300", "+1400"];
                    tp_inst._defaults.timezoneIso8601 && (timezoneList = $.map(timezoneList, function(e) {
                        return "+0000" == e ? "Z" : e.substring(0, 3) + ":" + e.substring(3)
                    })), tp_inst._defaults.timezoneList = timezoneList
                }
                return tp_inst.timezone = tp_inst._defaults.timezone, tp_inst.hour = tp_inst._defaults.hour < tp_inst._defaults.hourMin ? tp_inst._defaults.hourMin : tp_inst._defaults.hour > tp_inst._defaults.hourMax ? tp_inst._defaults.hourMax : tp_inst._defaults.hour, tp_inst.minute = tp_inst._defaults.minute < tp_inst._defaults.minuteMin ? tp_inst._defaults.minuteMin : tp_inst._defaults.minute > tp_inst._defaults.minuteMax ? tp_inst._defaults.minuteMax : tp_inst._defaults.minute, tp_inst.second = tp_inst._defaults.second < tp_inst._defaults.secondMin ? tp_inst._defaults.secondMin : tp_inst._defaults.second > tp_inst._defaults.secondMax ? tp_inst._defaults.secondMax : tp_inst._defaults.second, tp_inst.millisec = tp_inst._defaults.millisec < tp_inst._defaults.millisecMin ? tp_inst._defaults.millisecMin : tp_inst._defaults.millisec > tp_inst._defaults.millisecMax ? tp_inst._defaults.millisecMax : tp_inst._defaults.millisec, tp_inst.ampm = "", tp_inst.$input = $input, o.altField && (tp_inst.$altInput = $(o.altField).css({cursor: "pointer"}).focus(function() {
                    $input.trigger("focus")
                })), (0 === tp_inst._defaults.minDate || 0 === tp_inst._defaults.minDateTime) && (tp_inst._defaults.minDate = new Date), (0 === tp_inst._defaults.maxDate || 0 === tp_inst._defaults.maxDateTime) && (tp_inst._defaults.maxDate = new Date), void 0 !== tp_inst._defaults.minDate && tp_inst._defaults.minDate instanceof Date && (tp_inst._defaults.minDateTime = new Date(tp_inst._defaults.minDate.getTime())), void 0 !== tp_inst._defaults.minDateTime && tp_inst._defaults.minDateTime instanceof Date && (tp_inst._defaults.minDate = new Date(tp_inst._defaults.minDateTime.getTime())), void 0 !== tp_inst._defaults.maxDate && tp_inst._defaults.maxDate instanceof Date && (tp_inst._defaults.maxDateTime = new Date(tp_inst._defaults.maxDate.getTime())), void 0 !== tp_inst._defaults.maxDateTime && tp_inst._defaults.maxDateTime instanceof Date && (tp_inst._defaults.maxDate = new Date(tp_inst._defaults.maxDateTime.getTime())), tp_inst.$input.bind("focus", function() {
                    tp_inst._onFocus()
                }), tp_inst
            },_addTimePicker: function(e) {
                var t = this.$altInput && this._defaults.altFieldTimeOnly ? this.$input.val() + " " + this.$altInput.val() : this.$input.val();
                this.timeDefined = this._parseTime(t), this._limitMinMaxDateTime(e, !1), this._injectTimePicker()
            },_parseTime: function(e, t) {
                if (this.inst || (this.inst = $.datepicker._getInst(this.$input[0])), t || !this._defaults.timeOnly) {
                    var i = $.datepicker._get(this.inst, "dateFormat");
                    try {
                        var n = parseDateTimeInternal(i, this._defaults.timeFormat, e, $.datepicker._getFormatConfig(this.inst), this._defaults);
                        if (!n.timeObj)
                            return !1;
                        $.extend(this, n.timeObj)
                    } catch (s) {
                        return $.timepicker.log("Error parsing the date/time string: " + s + "\ndate/time string = " + e + "\ntimeFormat = " + this._defaults.timeFormat + "\ndateFormat = " + i), !1
                    }
                    return !0
                }
                var o = $.datepicker.parseTime(this._defaults.timeFormat, e, this._defaults);
                return o ? ($.extend(this, o), !0) : !1
            },_injectTimePicker: function() {
                var e = this.inst.dpDiv, t = this.inst.settings, i = this, n = "", s = "", o = {}, a = {}, r = null, l = 0, c = 0;
                if (0 === e.find("div.ui-timepicker-div").length && t.showTimepicker) {
                    var d = ' style="display:none;"', u = '<div class="ui-timepicker-div' + (t.isRTL ? " ui-timepicker-rtl" : "") + '"><dl><dt class="ui_tpicker_time_label"' + (t.showTime ? "" : d) + ">" + t.timeText + '</dt><dd class="ui_tpicker_time"' + (t.showTime ? "" : d) + "></dd>";
                    for (l = 0, c = this.units.length; c > l; l++) {
                        if (n = this.units[l], s = n.substr(0, 1).toUpperCase() + n.substr(1), o[n] = parseInt(t[n + "Max"] - (t[n + "Max"] - t[n + "Min"]) % t["step" + s], 10), a[n] = 0, u += '<dt class="ui_tpicker_' + n + '_label"' + (t["show" + s] ? "" : d) + ">" + t[n + "Text"] + '</dt><dd class="ui_tpicker_' + n + '"><div class="ui_tpicker_' + n + '_slider"' + (t["show" + s] ? "" : d) + "></div>", t["show" + s] && t[n + "Grid"] > 0) {
                            if (u += '<div style="padding-left: 1px"><table class="ui-tpicker-grid-label"><tr>', "hour" == n)
                                for (var p = t[n + "Min"]; p <= o[n]; p += parseInt(t[n + "Grid"], 10)) {
                                    a[n]++;
                                    var h = $.datepicker.formatTime(useAmpm(t.pickerTimeFormat || t.timeFormat) ? "hht" : "HH", {hour: p}, t);
                                    u += '<td data-for="' + n + '">' + h + "</td>"
                                }
                            else
                                for (var m = t[n + "Min"]; m <= o[n]; m += parseInt(t[n + "Grid"], 10))
                                    a[n]++, u += '<td data-for="' + n + '">' + (10 > m ? "0" : "") + m + "</td>";
                            u += "</tr></table></div>"
                        }
                        u += "</dd>"
                    }
                    u += '<dt class="ui_tpicker_timezone_label"' + (t.showTimezone ? "" : d) + ">" + t.timezoneText + "</dt>", u += '<dd class="ui_tpicker_timezone" ' + (t.showTimezone ? "" : d) + "></dd>", u += "</dl></div>";
                    var f = $(u);
                    for (t.timeOnly === !0 && (f.prepend('<div class="ui-widget-header ui-helper-clearfix ui-corner-all"><div class="ui-datepicker-title">' + t.timeOnlyTitle + "</div></div>"), e.find(".ui-datepicker-header, .ui-datepicker-calendar").hide()), l = 0, c = i.units.length; c > l; l++)
                        n = i.units[l], s = n.substr(0, 1).toUpperCase() + n.substr(1), i[n + "_slider"] = i.control.create(i, f.find(".ui_tpicker_" + n + "_slider"), n, i[n], t[n + "Min"], o[n], t["step" + s]), t["show" + s] && t[n + "Grid"] > 0 && (r = 100 * a[n] * t[n + "Grid"] / (o[n] - t[n + "Min"]), f.find(".ui_tpicker_" + n + " table").css({width: r + "%",marginLeft: t.isRTL ? "0" : r / (-2 * a[n]) + "%",marginRight: t.isRTL ? r / (-2 * a[n]) + "%" : "0",borderCollapse: "collapse"}).find("td").click(function() {
                            var e = $(this), t = e.html(), s = parseInt(t.replace(/[^0-9]/g), 10), o = t.replace(/[^apm]/gi), a = e.data("for");
                            "hour" == a && (-1 !== o.indexOf("p") && 12 > s ? s += 12 : -1 !== o.indexOf("a") && 12 === s && (s = 0)), i.control.value(i, i[a + "_slider"], n, s), i._onTimeChange(), i._onSelectHandler()
                        }).css({cursor: "pointer",width: 100 / a[n] + "%",textAlign: "center",overflow: "hidden"}));
                    if (this.timezone_select = f.find(".ui_tpicker_timezone").append("<select></select>").find("select"), $.fn.append.apply(this.timezone_select, $.map(t.timezoneList, function(e) {
                        return $("<option />").val("object" == typeof e ? e.value : e).text("object" == typeof e ? e.label : e)
                    })), "undefined" != typeof this.timezone && null !== this.timezone && "" !== this.timezone) {
                        var g = new Date(this.inst.selectedYear, this.inst.selectedMonth, this.inst.selectedDay, 12), v = $.timepicker.timeZoneOffsetString(g);
                        v == this.timezone ? selectLocalTimeZone(i) : this.timezone_select.val(this.timezone)
                    } else
                        "undefined" != typeof this.hour && null !== this.hour && "" !== this.hour ? this.timezone_select.val(t.defaultTimezone) : selectLocalTimeZone(i);
                    this.timezone_select.change(function() {
                        i._defaults.useLocalTimezone = !1, i._onTimeChange(), i._onSelectHandler()
                    });
                    var _ = e.find(".ui-datepicker-buttonpane");
                    if (_.length ? _.before(f) : e.append(f), this.$timeObj = f.find(".ui_tpicker_time"), null !== this.inst) {
                        var b = this.timeDefined;
                        this._onTimeChange(), this.timeDefined = b
                    }
                    if (this._defaults.addSliderAccess) {
                        var y = this._defaults.sliderAccessArgs, w = this._defaults.isRTL;
                        y.isRTL = w, setTimeout(function() {
                            if (0 === f.find(".ui-slider-access").length) {
                                f.find(".ui-slider:visible").sliderAccess(y);
                                var e = f.find(".ui-slider-access:eq(0)").outerWidth(!0);
                                e && f.find("table:visible").each(function() {
                                    var t = $(this), i = t.outerWidth(), n = t.css(w ? "marginRight" : "marginLeft").toString().replace("%", ""), s = i - e, o = n * s / i + "%", a = {width: s,marginRight: 0,marginLeft: 0};
                                    a[w ? "marginRight" : "marginLeft"] = o, t.css(a)
                                })
                            }
                        }, 10)
                    }
                }
            },_limitMinMaxDateTime: function(e, t) {
                var i = this._defaults, n = new Date(e.selectedYear, e.selectedMonth, e.selectedDay);
                if (this._defaults.showTimepicker) {
                    if (null !== $.datepicker._get(e, "minDateTime") && void 0 !== $.datepicker._get(e, "minDateTime") && n) {
                        var s = $.datepicker._get(e, "minDateTime"), o = new Date(s.getFullYear(), s.getMonth(), s.getDate(), 0, 0, 0, 0);
                        (null === this.hourMinOriginal || null === this.minuteMinOriginal || null === this.secondMinOriginal || null === this.millisecMinOriginal) && (this.hourMinOriginal = i.hourMin, this.minuteMinOriginal = i.minuteMin, this.secondMinOriginal = i.secondMin, this.millisecMinOriginal = i.millisecMin), e.settings.timeOnly || o.getTime() == n.getTime() ? (this._defaults.hourMin = s.getHours(), this.hour <= this._defaults.hourMin ? (this.hour = this._defaults.hourMin, this._defaults.minuteMin = s.getMinutes(), this.minute <= this._defaults.minuteMin ? (this.minute = this._defaults.minuteMin, this._defaults.secondMin = s.getSeconds(), this.second <= this._defaults.secondMin ? (this.second = this._defaults.secondMin, this._defaults.millisecMin = s.getMilliseconds()) : (this.millisec < this._defaults.millisecMin && (this.millisec = this._defaults.millisecMin), this._defaults.millisecMin = this.millisecMinOriginal)) : (this._defaults.secondMin = this.secondMinOriginal, this._defaults.millisecMin = this.millisecMinOriginal)) : (this._defaults.minuteMin = this.minuteMinOriginal, this._defaults.secondMin = this.secondMinOriginal, this._defaults.millisecMin = this.millisecMinOriginal)) : (this._defaults.hourMin = this.hourMinOriginal, this._defaults.minuteMin = this.minuteMinOriginal, this._defaults.secondMin = this.secondMinOriginal, this._defaults.millisecMin = this.millisecMinOriginal)
                    }
                    if (null !== $.datepicker._get(e, "maxDateTime") && void 0 !== $.datepicker._get(e, "maxDateTime") && n) {
                        var a = $.datepicker._get(e, "maxDateTime"), r = new Date(a.getFullYear(), a.getMonth(), a.getDate(), 0, 0, 0, 0);
                        (null === this.hourMaxOriginal || null === this.minuteMaxOriginal || null === this.secondMaxOriginal) && (this.hourMaxOriginal = i.hourMax, this.minuteMaxOriginal = i.minuteMax, this.secondMaxOriginal = i.secondMax, this.millisecMaxOriginal = i.millisecMax), e.settings.timeOnly || r.getTime() == n.getTime() ? (this._defaults.hourMax = a.getHours(), this.hour >= this._defaults.hourMax ? (this.hour = this._defaults.hourMax, this._defaults.minuteMax = a.getMinutes(), this.minute >= this._defaults.minuteMax ? (this.minute = this._defaults.minuteMax, this._defaults.secondMax = a.getSeconds(), this.second >= this._defaults.secondMax ? (this.second = this._defaults.secondMax, this._defaults.millisecMax = a.getMilliseconds()) : (this.millisec > this._defaults.millisecMax && (this.millisec = this._defaults.millisecMax), this._defaults.millisecMax = this.millisecMaxOriginal)) : (this._defaults.secondMax = this.secondMaxOriginal, this._defaults.millisecMax = this.millisecMaxOriginal)) : (this._defaults.minuteMax = this.minuteMaxOriginal, this._defaults.secondMax = this.secondMaxOriginal, this._defaults.millisecMax = this.millisecMaxOriginal)) : (this._defaults.hourMax = this.hourMaxOriginal, this._defaults.minuteMax = this.minuteMaxOriginal, this._defaults.secondMax = this.secondMaxOriginal, this._defaults.millisecMax = this.millisecMaxOriginal)
                    }
                    if (void 0 !== t && t === !0) {
                        var l = parseInt(this._defaults.hourMax - (this._defaults.hourMax - this._defaults.hourMin) % this._defaults.stepHour, 10), c = parseInt(this._defaults.minuteMax - (this._defaults.minuteMax - this._defaults.minuteMin) % this._defaults.stepMinute, 10), d = parseInt(this._defaults.secondMax - (this._defaults.secondMax - this._defaults.secondMin) % this._defaults.stepSecond, 10), u = parseInt(this._defaults.millisecMax - (this._defaults.millisecMax - this._defaults.millisecMin) % this._defaults.stepMillisec, 10);
                        this.hour_slider && (this.control.options(this, this.hour_slider, "hour", {min: this._defaults.hourMin,max: l}), this.control.value(this, this.hour_slider, "hour", this.hour - this.hour % this._defaults.stepHour)), this.minute_slider && (this.control.options(this, this.minute_slider, "minute", {min: this._defaults.minuteMin,max: c}), this.control.value(this, this.minute_slider, "minute", this.minute - this.minute % this._defaults.stepMinute)), this.second_slider && (this.control.options(this, this.second_slider, "second", {min: this._defaults.secondMin,max: d}), this.control.value(this, this.second_slider, "second", this.second - this.second % this._defaults.stepSecond)), this.millisec_slider && (this.control.options(this, this.millisec_slider, "millisec", {min: this._defaults.millisecMin,max: u}), this.control.value(this, this.millisec_slider, "millisec", this.millisec - this.millisec % this._defaults.stepMillisec))
                    }
                }
            },_onTimeChange: function() {
                var e = this.hour_slider ? this.control.value(this, this.hour_slider, "hour") : !1, t = this.minute_slider ? this.control.value(this, this.minute_slider, "minute") : !1, i = this.second_slider ? this.control.value(this, this.second_slider, "second") : !1, n = this.millisec_slider ? this.control.value(this, this.millisec_slider, "millisec") : !1, s = this.timezone_select ? this.timezone_select.val() : !1, o = this._defaults, a = o.pickerTimeFormat || o.timeFormat, r = o.pickerTimeSuffix || o.timeSuffix;
                "object" == typeof e && (e = !1), "object" == typeof t && (t = !1), "object" == typeof i && (i = !1), "object" == typeof n && (n = !1), "object" == typeof s && (s = !1), e !== !1 && (e = parseInt(e, 10)), t !== !1 && (t = parseInt(t, 10)), i !== !1 && (i = parseInt(i, 10)), n !== !1 && (n = parseInt(n, 10));
                var l = o[12 > e ? "amNames" : "pmNames"][0], c = e != this.hour || t != this.minute || i != this.second || n != this.millisec || this.ampm.length > 0 && 12 > e != (-1 !== $.inArray(this.ampm.toUpperCase(), this.amNames)) || null === this.timezone && s != this.defaultTimezone || null !== this.timezone && s != this.timezone;
                c && (e !== !1 && (this.hour = e), t !== !1 && (this.minute = t), i !== !1 && (this.second = i), n !== !1 && (this.millisec = n), s !== !1 && (this.timezone = s), this.inst || (this.inst = $.datepicker._getInst(this.$input[0])), this._limitMinMaxDateTime(this.inst, !0)), useAmpm(o.timeFormat) && (this.ampm = l), this.formattedTime = $.datepicker.formatTime(o.timeFormat, this, o), this.$timeObj && this.$timeObj.text(a === o.timeFormat ? this.formattedTime + r : $.datepicker.formatTime(a, this, o) + r), this.timeDefined = !0, c && this._updateDateTime()
            },_onSelectHandler: function() {
                var e = this._defaults.onSelect || this.inst.settings.onSelect, t = this.$input ? this.$input[0] : null;
                e && t && e.apply(t, [this.formattedDateTime, this])
            },_updateDateTime: function(e) {
                e = this.inst || e;
                var t = $.datepicker._daylightSavingAdjust(new Date(e.selectedYear, e.selectedMonth, e.selectedDay)), i = $.datepicker._get(e, "dateFormat"), n = $.datepicker._getFormatConfig(e), s = null !== t && this.timeDefined;
                this.formattedDate = $.datepicker.formatDate(i, null === t ? new Date : t, n);
                var o = this.formattedDate;
                if ("" === e.lastVal && (e.currentYear = e.selectedYear, e.currentMonth = e.selectedMonth, e.currentDay = e.selectedDay), this._defaults.timeOnly === !0 ? o = this.formattedTime : this._defaults.timeOnly !== !0 && (this._defaults.alwaysSetTime || s) && (o += this._defaults.separator + this.formattedTime + this._defaults.timeSuffix), this.formattedDateTime = o, this._defaults.showTimepicker)
                    if (this.$altInput && this._defaults.altFieldTimeOnly === !0)
                        this.$altInput.val(this.formattedTime), this.$input.val(this.formattedDate);
                    else if (this.$altInput) {
                        this.$input.val(o);
                        var a = "", r = this._defaults.altSeparator ? this._defaults.altSeparator : this._defaults.separator, l = this._defaults.altTimeSuffix ? this._defaults.altTimeSuffix : this._defaults.timeSuffix;
                        a = this._defaults.altFormat ? $.datepicker.formatDate(this._defaults.altFormat, null === t ? new Date : t, n) : this.formattedDate, a && (a += r), a += this._defaults.altTimeFormat ? $.datepicker.formatTime(this._defaults.altTimeFormat, this, this._defaults) + l : this.formattedTime + l, this.$altInput.val(a)
                    } else
                        this.$input.val(o);
                else
                    this.$input.val(this.formattedDate);
                this.$input.trigger("change")
            },_onFocus: function() {
                if (!this.$input.val() && this._defaults.defaultValue) {
                    this.$input.val(this._defaults.defaultValue);
                    var e = $.datepicker._getInst(this.$input.get(0)), t = $.datepicker._get(e, "timepicker");
                    if (t && t._defaults.timeOnly && e.input.val() != e.lastVal)
                        try {
                            $.datepicker._updateDatepicker(e)
                        } catch (i) {
                            $.timepicker.log(i)
                        }
                }
            },_controls: {slider: {create: function(e, t, i, n, s, o, a) {
                        var r = e._defaults.isRTL;
                        return t.prop("slide", null).slider({orientation: "horizontal",value: r ? -1 * n : n,min: r ? -1 * o : s,max: r ? -1 * s : o,step: a,slide: function(t, n) {
                                e.control.value(e, $(this), i, r ? -1 * n.value : n.value), e._onTimeChange()
                            },stop: function() {
                                e._onSelectHandler()
                            }})
                    },options: function(e, t, i, n, s) {
                        if (e._defaults.isRTL) {
                            if ("string" == typeof n)
                                return "min" == n || "max" == n ? void 0 !== s ? t.slider(n, -1 * s) : Math.abs(t.slider(n)) : t.slider(n);
                            var o = n.min, a = n.max;
                            return n.min = n.max = null, void 0 !== o && (n.max = -1 * o), void 0 !== a && (n.min = -1 * a), t.slider(n)
                        }
                        return "string" == typeof n && void 0 !== s ? t.slider(n, s) : t.slider(n)
                    },value: function(e, t, i, n) {
                        return e._defaults.isRTL ? void 0 !== n ? t.slider("value", -1 * n) : Math.abs(t.slider("value")) : void 0 !== n ? t.slider("value", n) : t.slider("value")
                    }},select: {create: function(e, t, i, n, s, o, a) {
                        for (var r = '<select class="ui-timepicker-select" data-unit="' + i + '" data-min="' + s + '" data-max="' + o + '" data-step="' + a + '">', l = e._defaults.pickerTimeFormat || e._defaults.timeFormat, c = s; o >= c; c += a)
                            r += '<option value="' + c + '"' + (c == n ? " selected" : "") + ">", r += "hour" == i ? $.datepicker.formatTime($.trim(l.replace(/[^ht ]/gi, "")), {hour: c}, e._defaults) : "millisec" == i || c >= 10 ? c : "0" + c.toString(), r += "</option>";
                        return r += "</select>", t.children("select").remove(), $(r).appendTo(t).change(function() {
                            e._onTimeChange(), e._onSelectHandler()
                        }), t
                    },options: function(e, t, i, n, s) {
                        var o = {}, a = t.children("select");
                        if ("string" == typeof n) {
                            if (void 0 === s)
                                return a.data(n);
                            o[n] = s
                        } else
                            o = n;
                        return e.control.create(e, t, a.data("unit"), a.val(), o.min || a.data("min"), o.max || a.data("max"), o.step || a.data("step"))
                    },value: function(e, t, i, n) {
                        var s = t.children("select");
                        return void 0 !== n ? s.val(n) : s.val()
                    }}}}), $.fn.extend({timepicker: function(e) {
                e = e || {};
                var t = Array.prototype.slice.call(arguments);
                return "object" == typeof e && (t[0] = $.extend(e, {timeOnly: !0})), $(this).each(function() {
                    $.fn.datetimepicker.apply($(this), t)
                })
            },datetimepicker: function(e) {
                e = e || {};
                var t = arguments;
                return "string" == typeof e ? "getDate" == e ? $.fn.datepicker.apply($(this[0]), t) : this.each(function() {
                    var e = $(this);
                    e.datepicker.apply(e, t)
                }) : this.each(function() {
                    var t = $(this);
                    t.datepicker($.timepicker._newInst(t, e)._defaults)
                })
            }}), $.datepicker.parseDateTime = function(e, t, i, n, s) {
            var o = parseDateTimeInternal(e, t, i, n, s);
            if (o.timeObj) {
                var a = o.timeObj;
                o.date.setHours(a.hour, a.minute, a.second, a.millisec)
            }
            return o.date
        }, $.datepicker.parseTime = function(e, t, i) {
            var n = extendRemove(extendRemove({}, $.timepicker._defaults), i || {}), s = function(e, t, i) {
                var n, s = function(e, t) {
                    var i = [];
                    return e && $.merge(i, e), t && $.merge(i, t), i = $.map(i, function(e) {
                        return e.replace(/[.*+?|()\[\]{}\\]/g, "\\$&")
                    }), "(" + i.join("|") + ")?"
                }, o = function(e) {
                    var t = e.toLowerCase().match(/(h{1,2}|m{1,2}|s{1,2}|l{1}|t{1,2}|z|'.*?')/g), i = {h: -1,m: -1,s: -1,l: -1,t: -1,z: -1};
                    if (t)
                        for (var n = 0; n < t.length; n++)
                            -1 == i[t[n].toString().charAt(0)] && (i[t[n].toString().charAt(0)] = n + 1);
                    return i
                }, a = "^" + e.toString().replace(/([hH]{1,2}|mm?|ss?|[tT]{1,2}|[lz]|'.*?')/g, function(e) {
                    var t = e.length;
                    switch (e.charAt(0).toLowerCase()) {
                        case "h":
                            return 1 === t ? "(\\d?\\d)" : "(\\d{" + t + "})";
                        case "m":
                            return 1 === t ? "(\\d?\\d)" : "(\\d{" + t + "})";
                        case "s":
                            return 1 === t ? "(\\d?\\d)" : "(\\d{" + t + "})";
                        case "l":
                            return "(\\d?\\d?\\d)";
                        case "z":
                            return "(z|[-+]\\d\\d:?\\d\\d|\\S+)?";
                        case "t":
                            return s(i.amNames, i.pmNames);
                        default:
                            return "(" + e.replace(/\'/g, "").replace(/(\.|\$|\^|\\|\/|\(|\)|\[|\]|\?|\+|\*)/g, function(e) {
                                return "\\" + e
                            }) + ")?"
                    }
                }).replace(/\s/g, "\\s?") + i.timeSuffix + "$", r = o(e), l = "";
                n = t.match(new RegExp(a, "i"));
                var c = {hour: 0,minute: 0,second: 0,millisec: 0};
                if (n) {
                    if (-1 !== r.t && (void 0 === n[r.t] || 0 === n[r.t].length ? (l = "", c.ampm = "") : (l = -1 !== $.inArray(n[r.t].toUpperCase(), i.amNames) ? "AM" : "PM", c.ampm = i["AM" == l ? "amNames" : "pmNames"][0])), -1 !== r.h && (c.hour = "AM" == l && "12" == n[r.h] ? 0 : "PM" == l && "12" != n[r.h] ? parseInt(n[r.h], 10) + 12 : Number(n[r.h])), -1 !== r.m && (c.minute = Number(n[r.m])), -1 !== r.s && (c.second = Number(n[r.s])), -1 !== r.l && (c.millisec = Number(n[r.l])), -1 !== r.z && void 0 !== n[r.z]) {
                        var d = n[r.z].toUpperCase();
                        switch (d.length) {
                            case 1:
                                d = i.timezoneIso8601 ? "Z" : "+0000";
                                break;
                            case 5:
                                i.timezoneIso8601 && (d = "0000" == d.substring(1) ? "Z" : d.substring(0, 3) + ":" + d.substring(3));
                                break;
                            case 6:
                                i.timezoneIso8601 ? "00:00" == d.substring(1) && (d = "Z") : d = "Z" == d || "00:00" == d.substring(1) ? "+0000" : d.replace(/:/, "")
                        }
                        c.timezone = d
                    }
                    return c
                }
                return !1
            }, o = function(e, t, i) {
                try {
                    var n = new Date("2012-01-01 " + t);
                    if (isNaN(n.getTime()) && (n = new Date("2012-01-01T" + t), isNaN(n.getTime()) && (n = new Date("01/01/2012 " + t), isNaN(n.getTime()))))
                        throw "Unable to parse time with native Date: " + t;
                    return {hour: n.getHours(),minute: n.getMinutes(),second: n.getSeconds(),millisec: n.getMilliseconds(),timezone: $.timepicker.timeZoneOffsetString(n)}
                } catch (o) {
                    try {
                        return s(e, t, i)
                    } catch (a) {
                        $.timepicker.log("Unable to parse \ntimeString: " + t + "\ntimeFormat: " + e)
                    }
                }
                return !1
            };
            return "function" == typeof n.parse ? n.parse(e, t, n) : "loose" === n.parse ? o(e, t, n) : s(e, t, n)
        }, $.datepicker.formatTime = function(e, t, i) {
            i = i || {}, i = $.extend({}, $.timepicker._defaults, i), t = $.extend({hour: 0,minute: 0,second: 0,millisec: 0,timezone: "+0000"}, t);
            var n = e, s = i.amNames[0], o = parseInt(t.hour, 10);
            return o > 11 && (s = i.pmNames[0]), n = n.replace(/(?:HH?|hh?|mm?|ss?|[tT]{1,2}|[lz]|('.*?'|".*?"))/g, function(e) {
                switch (e) {
                    case "HH":
                        return ("0" + o).slice(-2);
                    case "H":
                        return o;
                    case "hh":
                        return ("0" + convert24to12(o)).slice(-2);
                    case "h":
                        return convert24to12(o);
                    case "mm":
                        return ("0" + t.minute).slice(-2);
                    case "m":
                        return t.minute;
                    case "ss":
                        return ("0" + t.second).slice(-2);
                    case "s":
                        return t.second;
                    case "l":
                        return ("00" + t.millisec).slice(-3);
                    case "z":
                        return null === t.timezone ? i.defaultTimezone : t.timezone;
                    case "T":
                        return s.charAt(0).toUpperCase();
                    case "TT":
                        return s.toUpperCase();
                    case "t":
                        return s.charAt(0).toLowerCase();
                    case "tt":
                        return s.toLowerCase();
                    default:
                        return e.replace(/\'/g, "") || "'"
                }
            }), n = $.trim(n)
        }, $.datepicker._base_selectDate = $.datepicker._selectDate, $.datepicker._selectDate = function(e, t) {
            var i = this._getInst($(e)[0]), n = this._get(i, "timepicker");
            n ? (n._limitMinMaxDateTime(i, !0), i.inline = i.stay_open = !0, this._base_selectDate(e, t), i.inline = i.stay_open = !1, this._notifyChange(i), this._updateDatepicker(i)) : this._base_selectDate(e, t)
        }, $.datepicker._base_updateDatepicker = $.datepicker._updateDatepicker, $.datepicker._updateDatepicker = function(e) {
            var t = e.input[0];
            if (!($.datepicker._curInst && $.datepicker._curInst != e && $.datepicker._datepickerShowing && $.datepicker._lastInput != t || "boolean" == typeof e.stay_open && e.stay_open !== !1)) {
                this._base_updateDatepicker(e);
                var i = this._get(e, "timepicker");
                i && i._addTimePicker(e)
            }
        }, $.datepicker._base_doKeyPress = $.datepicker._doKeyPress, $.datepicker._doKeyPress = function(e) {
            var t = $.datepicker._getInst(e.target), i = $.datepicker._get(t, "timepicker");
            if (i && $.datepicker._get(t, "constrainInput")) {
                var n = useAmpm(i._defaults.timeFormat), s = $.datepicker._possibleChars($.datepicker._get(t, "dateFormat")), o = i._defaults.timeFormat.toString().replace(/[hms]/g, "").replace(/TT/g, n ? "APM" : "").replace(/Tt/g, n ? "AaPpMm" : "").replace(/tT/g, n ? "AaPpMm" : "").replace(/T/g, n ? "AP" : "").replace(/tt/g, n ? "apm" : "").replace(/t/g, n ? "ap" : "") + " " + i._defaults.separator + i._defaults.timeSuffix + (i._defaults.showTimezone ? i._defaults.timezoneList.join("") : "") + i._defaults.amNames.join("") + i._defaults.pmNames.join("") + s, a = String.fromCharCode(void 0 === e.charCode ? e.keyCode : e.charCode);
                return e.ctrlKey || " " > a || !s || o.indexOf(a) > -1
            }
            return $.datepicker._base_doKeyPress(e)
        }, $.datepicker._base_updateAlternate = $.datepicker._updateAlternate, $.datepicker._updateAlternate = function(e) {
            var t = this._get(e, "timepicker");
            if (t) {
                var i = t._defaults.altField;
                if (i) {
                    var n = (t._defaults.altFormat || t._defaults.dateFormat, this._getDate(e)), s = $.datepicker._getFormatConfig(e), o = "", a = t._defaults.altSeparator ? t._defaults.altSeparator : t._defaults.separator, r = t._defaults.altTimeSuffix ? t._defaults.altTimeSuffix : t._defaults.timeSuffix, l = null !== t._defaults.altTimeFormat ? t._defaults.altTimeFormat : t._defaults.timeFormat;
                    o += $.datepicker.formatTime(l, t, t._defaults) + r, t._defaults.timeOnly || t._defaults.altFieldTimeOnly || null === n || (o = t._defaults.altFormat ? $.datepicker.formatDate(t._defaults.altFormat, n, s) + a + o : t.formattedDate + a + o), $(i).val(o)
                }
            } else
                $.datepicker._base_updateAlternate(e)
        }, $.datepicker._base_doKeyUp = $.datepicker._doKeyUp, $.datepicker._doKeyUp = function(e) {
            var t = $.datepicker._getInst(e.target), i = $.datepicker._get(t, "timepicker");
            if (i && i._defaults.timeOnly && t.input.val() != t.lastVal)
                try {
                    $.datepicker._updateDatepicker(t)
                } catch (n) {
                    $.timepicker.log(n)
                }
            return $.datepicker._base_doKeyUp(e)
        }, $.datepicker._base_gotoToday = $.datepicker._gotoToday, $.datepicker._gotoToday = function(e) {
            var t = this._getInst($(e)[0]), i = t.dpDiv;
            this._base_gotoToday(e);
            var n = this._get(t, "timepicker");
            selectLocalTimeZone(n);
            var s = new Date;
            this._setTime(t, s), $(".ui-datepicker-today", i).click()
        }, $.datepicker._disableTimepickerDatepicker = function(e) {
            var t = this._getInst(e);
            if (t) {
                var i = this._get(t, "timepicker");
                $(e).datepicker("getDate"), i && (i._defaults.showTimepicker = !1, i._updateDateTime(t))
            }
        }, $.datepicker._enableTimepickerDatepicker = function(e) {
            var t = this._getInst(e);
            if (t) {
                var i = this._get(t, "timepicker");
                $(e).datepicker("getDate"), i && (i._defaults.showTimepicker = !0, i._addTimePicker(t), i._updateDateTime(t))
            }
        }, $.datepicker._setTime = function(e, t) {
            var i = this._get(e, "timepicker");
            if (i) {
                var n = i._defaults;
                i.hour = t ? t.getHours() : n.hour, i.minute = t ? t.getMinutes() : n.minute, i.second = t ? t.getSeconds() : n.second, i.millisec = t ? t.getMilliseconds() : n.millisec, i._limitMinMaxDateTime(e, !0), i._onTimeChange(), i._updateDateTime(e)
            }
        }, $.datepicker._setTimeDatepicker = function(e, t, i) {
            var n = this._getInst(e);
            if (n) {
                var s = this._get(n, "timepicker");
                if (s) {
                    this._setDateFromField(n);
                    var o;
                    t && ("string" == typeof t ? (s._parseTime(t, i), o = new Date, o.setHours(s.hour, s.minute, s.second, s.millisec)) : o = new Date(t.getTime()), "Invalid Date" == o.toString() && (o = void 0), this._setTime(n, o))
                }
            }
        }, $.datepicker._base_setDateDatepicker = $.datepicker._setDateDatepicker, $.datepicker._setDateDatepicker = function(e, t) {
            var i = this._getInst(e);
            if (i) {
                var n = t instanceof Date ? new Date(t.getTime()) : t;
                this._updateDatepicker(i), this._base_setDateDatepicker.apply(this, arguments), this._setTimeDatepicker(e, n, !0)
            }
        }, $.datepicker._base_getDateDatepicker = $.datepicker._getDateDatepicker, $.datepicker._getDateDatepicker = function(e, t) {
            var i = this._getInst(e);
            if (i) {
                var n = this._get(i, "timepicker");
                if (n) {
                    void 0 === i.lastVal && this._setDateFromField(i, t);
                    var s = this._getDate(i);
                    return s && n._parseTime($(e).val(), n.timeOnly) && s.setHours(n.hour, n.minute, n.second, n.millisec), s
                }
                return this._base_getDateDatepicker(e, t)
            }
        }, $.datepicker._base_parseDate = $.datepicker.parseDate, $.datepicker.parseDate = function(e, t, i) {
            var n;
            try {
                n = this._base_parseDate(e, t, i)
            } catch (s) {
                if (!(s.indexOf(":") >= 0))
                    throw s;
                n = this._base_parseDate(e, t.substring(0, t.length - (s.length - s.indexOf(":") - 2)), i), $.timepicker.log("Error parsing the date string: " + s + "\ndate string = " + t + "\ndate format = " + e)
            }
            return n
        }, $.datepicker._base_formatDate = $.datepicker._formatDate, $.datepicker._formatDate = function(e) {
            var t = this._get(e, "timepicker");
            return t ? (t._updateDateTime(e), t.$input.val()) : this._base_formatDate(e)
        }, $.datepicker._base_optionDatepicker = $.datepicker._optionDatepicker, $.datepicker._optionDatepicker = function(e, t, i) {
            var n, s = this._getInst(e);
            if (!s)
                return null;
            var o = this._get(s, "timepicker");
            if (o) {
                var a, r = null, l = null, c = null, d = o._defaults.evnts, u = {};
                if ("string" == typeof t) {
                    if ("minDate" === t || "minDateTime" === t)
                        r = i;
                    else if ("maxDate" === t || "maxDateTime" === t)
                        l = i;
                    else if ("onSelect" === t)
                        c = i;
                    else if (d.hasOwnProperty(t)) {
                        if ("undefined" == typeof i)
                            return d[t];
                        u[t] = i, n = {}
                    }
                } else if ("object" == typeof t) {
                    t.minDate ? r = t.minDate : t.minDateTime ? r = t.minDateTime : t.maxDate ? l = t.maxDate : t.maxDateTime && (l = t.maxDateTime);
                    for (a in d)
                        d.hasOwnProperty(a) && t[a] && (u[a] = t[a])
                }
                for (a in u)
                    u.hasOwnProperty(a) && (d[a] = u[a], n || (n = $.extend({}, t)), delete n[a]);
                if (n && isEmptyObject(n))
                    return;
                r ? (r = 0 === r ? new Date : new Date(r), o._defaults.minDate = r, o._defaults.minDateTime = r) : l ? (l = 0 === l ? new Date : new Date(l), o._defaults.maxDate = l, o._defaults.maxDateTime = l) : c && (o._defaults.onSelect = c)
            }
            return void 0 === i ? this._base_optionDatepicker.call($.datepicker, e, t) : this._base_optionDatepicker.call($.datepicker, e, n || t, i)
        };
        var isEmptyObject = function(e) {
            var t;
            for (t in e)
                if (e.hasOwnProperty(e))
                    return !1;
            return !0
        }, extendRemove = function(e, t) {
            $.extend(e, t);
            for (var i in t)
                (null === t[i] || void 0 === t[i]) && (e[i] = t[i]);
            return e
        }, useAmpm = function(e) {
            return (-1 !== e.indexOf("t") || -1 !== e.indexOf("T")) && -1 !== e.indexOf("h")
        }, convert24to12 = function(e) {
            return e > 12 && (e -= 12), 0 === e && (e = 12), String(e)
        }, splitDateTime = function(e, t, i, n) {
            try {
                var s = n && n.separator ? n.separator : $.timepicker._defaults.separator, o = n && n.timeFormat ? n.timeFormat : $.timepicker._defaults.timeFormat, a = o.split(s), r = a.length, l = t.split(s), c = l.length;
                if (c > 1)
                    return [l.splice(0, c - r).join(s), l.splice(0, r).join(s)]
            } catch (d) {
                if ($.timepicker.log("Could not split the date from the time. Please check the following datetimepicker options\nthrown error: " + d + "\ndateTimeString" + t + "\ndateFormat = " + e + "\nseparator = " + n.separator + "\ntimeFormat = " + n.timeFormat), d.indexOf(":") >= 0) {
                    {
                        var u = t.length - (d.length - d.indexOf(":") - 2);
                        t.substring(u)
                    }
                    return [$.trim(t.substring(0, u)), $.trim(t.substring(u))]
                }
                throw d
            }
            return [t, ""]
        }, parseDateTimeInternal = function(e, t, i, n, s) {
            var o, a = splitDateTime(e, i, n, s);
            if (o = $.datepicker._base_parseDate(e, a[0], n), "" !== a[1]) {
                var r = a[1], l = $.datepicker.parseTime(t, r, s);
                if (null === l)
                    throw "Wrong time format";
                return {date: o,timeObj: l}
            }
            return {date: o}
        }, selectLocalTimeZone = function(e, t) {
            if (e && e.timezone_select) {
                e._defaults.useLocalTimezone = !0;
                var i = "undefined" != typeof t ? t : new Date, n = $.timepicker.timeZoneOffsetString(i);
                e._defaults.timezoneIso8601 && (n = n.substring(0, 3) + ":" + n.substring(3)), e.timezone_select.val(n)
            }
        };
        $.timepicker = new Timepicker, $.timepicker.timeZoneOffsetString = function(e) {
            var t = -1 * e.getTimezoneOffset(), i = t % 60, n = (t - i) / 60;
            return (t >= 0 ? "+" : "-") + ("0" + (101 * n).toString()).slice(-2) + ("0" + (101 * i).toString()).slice(-2)
        }, $.timepicker.timeRange = function(e, t, i) {
            return $.timepicker.handleRange("timepicker", e, t, i)
        }, $.timepicker.dateTimeRange = function(e, t, i) {
            $.timepicker.dateRange(e, t, i, "datetimepicker")
        }, $.timepicker.dateRange = function(e, t, i, n) {
            n = n || "datepicker", $.timepicker.handleRange(n, e, t, i)
        }, $.timepicker.handleRange = function(e, t, i, n) {
            function s(e, n, s) {
                n.val() && new Date(t.val()) > new Date(i.val()) && n.val(s)
            }
            function o(t, i, n) {
                if ($(t).val()) {
                    var s = $(t)[e].call($(t), "getDate");
                    s.getTime && $(i)[e].call($(i), "option", n, s)
                }
            }
            return $.fn[e].call(t, $.extend({onClose: function(e) {
                    s(this, i, e)
                },onSelect: function() {
                    o(this, i, "minDate")
                }}, n, n.start)), $.fn[e].call(i, $.extend({onClose: function(e) {
                    s(this, t, e)
                },onSelect: function() {
                    o(this, t, "maxDate")
                }}, n, n.end)), "timepicker" != e && n.reformat && $([t, i]).each(function() {
                var t = $(this)[e].call($(this), "option", "dateFormat"), i = new Date($(this).val());
                $(this).val() && i && $(this).val($.datepicker.formatDate(t, i))
            }), s(t, i, t.val()), o(t, i, "minDate"), o(i, t, "maxDate"), $([t.get(0), i.get(0)])
        }, $.timepicker.log = function(e) {
            window.console && console.log(e)
        }, $.timepicker.version = "1.2.2"
    }
}(jQuery), function(e) {
    e.timepicker.regional["zh-CN"] = {timeOnlyTitle: "选择时间",timeText: "时间",hourText: "小时",minuteText: "分钟",secondText: "秒钟",millisecText: "微秒",timezoneText: "时区",currentText: "当前时间",closeText: "确认",timeFormat: "HH:mm",amNames: ["AM", "A"],pmNames: ["PM", "P"],isRTL: !1}, e.timepicker.setDefaults(e.timepicker.regional["zh-CN"])
}(jQuery), define("datetimepicker", ["jqueryui", "jquery"], function() {
}), define("components/pop/timer", ["require", "backbone", "components/pop/base", "text!components/pop/templates/timer.html", "core/utils", "datetimepicker"], function(e) {
    var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/timer.html"), n = e("core/utils");
    return e("datetimepicker"), t.extend({template: _.template(i),events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},className: function() {
            return this.options.className
        },initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            this.$el.html(this.template({}));
            var e = this.$(".js-time-input"), t = this.data.time;
            if (t > 0) {
                var i = n.getFullTime(new Date(t));
                e.val(i)
            }
            return e.datetimepicker({minDate: new Date}), $("#ui-datepicker-div").on("click", function(e) {
                e.stopPropagation()
            }), this
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : -1 !== t.indexOf("top") ? {of: e.target,my: "center bottom",at: "center top",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover top", o = e.data;
            this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(o), this.positioning(), this.show()
        },triggerCallback: function() {
            this.callback.call(this, this.$(".js-time-input").datetimepicker("getDate")), this.hide()
        }})
}), define("text!components/pop/templates/fans_tag.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-fans-tag">\n    <div class="popover-content">\n        <form class="form-inline loading">\n            <select data-placeholder="选择标签" multiple class="js-chosen-select">\n                <% for(var i = 0; i < tags.length; i ++){ %>\n                    <option value="<%= tags[i].id %>"\n                        <% if(tags[i].isSelected) {%>\n                            selected\n                        <% } %>\n                    ><%= tags[i].name %></option>\n                <% } %>\n            </select>\n            <div class="pull-right">\n                <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n                <button type="reset" class="btn js-btn-cancel">取消</button>\n            </div>\n            \n        </form>\n    </div>\n</div>'
}), function() {
    var e, t, i, n, s, o = {}.hasOwnProperty, a = function(e, t) {
        function i() {
            this.constructor = e
        }
        for (var n in t)
            o.call(t, n) && (e[n] = t[n]);
        return i.prototype = t.prototype, e.prototype = new i, e.__super__ = t.prototype, e
    };
    n = function() {
        function e() {
            this.options_index = 0, this.parsed = []
        }
        return e.prototype.add_node = function(e) {
            return "OPTGROUP" === e.nodeName.toUpperCase() ? this.add_group(e) : this.add_option(e)
        }, e.prototype.add_group = function(e) {
            var t, i, n, s, o, a;
            for (t = this.parsed.length, this.parsed.push({array_index: t,group: !0,label: this.escapeExpression(e.label),children: 0,disabled: e.disabled}), o = e.childNodes, a = [], n = 0, s = o.length; s > n; n++)
                i = o[n], a.push(this.add_option(i, t, e.disabled));
            return a
        }, e.prototype.add_option = function(e, t, i) {
            return "OPTION" === e.nodeName.toUpperCase() ? ("" !== e.text ? (null != t && (this.parsed[t].children += 1), this.parsed.push({array_index: this.parsed.length,options_index: this.options_index,value: e.value,text: e.text,html: e.innerHTML,selected: e.selected,disabled: i === !0 ? i : e.disabled,group_array_index: t,classes: e.className,style: e.style.cssText})) : this.parsed.push({array_index: this.parsed.length,options_index: this.options_index,empty: !0}), this.options_index += 1) : void 0
        }, e.prototype.escapeExpression = function(e) {
            var t, i;
            return null == e || e === !1 ? "" : /[\&\<\>\"\'\`]/.test(e) ? (t = {"<": "&lt;",">": "&gt;",'"': "&quot;","'": "&#x27;","`": "&#x60;"}, i = /&(?!\w+;)|[\<\>\"\'\`]/g, e.replace(i, function(e) {
                return t[e] || "&amp;"
            })) : e
        }, e
    }(), n.select_to_array = function(e) {
        var t, i, s, o, a;
        for (i = new n, a = e.childNodes, s = 0, o = a.length; o > s; s++)
            t = a[s], i.add_node(t);
        return i.parsed
    }, t = function() {
        function e(t, i) {
            this.form_field = t, this.options = null != i ? i : {}, e.browser_is_supported() && (this.is_multiple = this.form_field.multiple, this.set_default_text(), this.set_default_values(), this.setup(), this.set_up_html(), this.register_observers())
        }
        return e.prototype.set_default_values = function() {
            var e = this;
            return this.click_test_action = function(t) {
                return e.test_active_click(t)
            }, this.activate_action = function(t) {
                return e.activate_field(t)
            }, this.active_field = !1, this.mouse_on_container = !1, this.results_showing = !1, this.result_highlighted = null, this.result_single_selected = null, this.allow_single_deselect = null != this.options.allow_single_deselect && null != this.form_field.options[0] && "" === this.form_field.options[0].text ? this.options.allow_single_deselect : !1, this.disable_search_threshold = this.options.disable_search_threshold || 0, this.disable_search = this.options.disable_search || !1, this.enable_split_word_search = null != this.options.enable_split_word_search ? this.options.enable_split_word_search : !0, this.group_search = null != this.options.group_search ? this.options.group_search : !0, this.search_contains = this.options.search_contains || !1, this.single_backstroke_delete = null != this.options.single_backstroke_delete ? this.options.single_backstroke_delete : !0, this.max_selected_options = this.options.max_selected_options || 1 / 0, this.inherit_select_classes = this.options.inherit_select_classes || !1, this.display_selected_options = null != this.options.display_selected_options ? this.options.display_selected_options : !0, this.display_disabled_options = null != this.options.display_disabled_options ? this.options.display_disabled_options : !0
        }, e.prototype.set_default_text = function() {
            return this.default_text = this.form_field.getAttribute("data-placeholder") ? this.form_field.getAttribute("data-placeholder") : this.is_multiple ? this.options.placeholder_text_multiple || this.options.placeholder_text || e.default_multiple_text : this.options.placeholder_text_single || this.options.placeholder_text || e.default_single_text, this.results_none_found = this.form_field.getAttribute("data-no_results_text") || this.options.no_results_text || e.default_no_result_text
        }, e.prototype.mouse_enter = function() {
            return this.mouse_on_container = !0
        }, e.prototype.mouse_leave = function() {
            return this.mouse_on_container = !1
        }, e.prototype.input_focus = function() {
            var e = this;
            if (this.is_multiple) {
                if (!this.active_field)
                    return setTimeout(function() {
                        return e.container_mousedown()
                    }, 50)
            } else if (!this.active_field)
                return this.activate_field()
        }, e.prototype.input_blur = function() {
            var e = this;
            return this.mouse_on_container ? void 0 : (this.active_field = !1, setTimeout(function() {
                return e.blur_test()
            }, 100))
        }, e.prototype.results_option_build = function(e) {
            var t, i, n, s, o;
            for (t = "", o = this.results_data, n = 0, s = o.length; s > n; n++)
                i = o[n], t += i.group ? this.result_add_group(i) : this.result_add_option(i), (null != e ? e.first : void 0) && (i.selected && this.is_multiple ? this.choice_build(i) : i.selected && !this.is_multiple && this.single_set_selected_text(i.text));
            return t
        }, e.prototype.result_add_option = function(e) {
            var t, i;
            return e.search_match && this.include_option_in_results(e) ? (t = [], e.disabled || e.selected && this.is_multiple || t.push("active-result"), !e.disabled || e.selected && this.is_multiple || t.push("disabled-result"), e.selected && t.push("result-selected"), null != e.group_array_index && t.push("group-option"), "" !== e.classes && t.push(e.classes), i = "" !== e.style.cssText ? ' style="' + e.style + '"' : "", '<li class="' + t.join(" ") + '"' + i + ' data-option-array-index="' + e.array_index + '">' + e.search_text + "</li>") : ""
        }, e.prototype.result_add_group = function(e) {
            return (e.search_match || e.group_match) && e.active_options > 0 ? '<li class="group-result">' + e.search_text + "</li>" : ""
        }, e.prototype.results_update_field = function() {
            return this.set_default_text(), this.is_multiple || this.results_reset_cleanup(), this.result_clear_highlight(), this.result_single_selected = null, this.results_build(), this.results_showing ? this.winnow_results() : void 0
        }, e.prototype.results_toggle = function() {
            return this.results_showing ? this.results_hide() : this.results_show()
        }, e.prototype.results_search = function() {
            return this.results_showing ? this.winnow_results() : this.results_show()
        }, e.prototype.winnow_results = function() {
            var e, t, i, n, s, o, a, r, l, c, d, u, p;
            for (this.no_results_clear(), s = 0, a = this.get_search_text(), e = a.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), n = this.search_contains ? "" : "^", i = new RegExp(n + e, "i"), c = new RegExp(e, "i"), p = this.results_data, d = 0, u = p.length; u > d; d++)
                t = p[d], t.search_match = !1, o = null, this.include_option_in_results(t) && (t.group && (t.group_match = !1, t.active_options = 0), null != t.group_array_index && this.results_data[t.group_array_index] && (o = this.results_data[t.group_array_index], 0 === o.active_options && o.search_match && (s += 1), o.active_options += 1), (!t.group || this.group_search) && (t.search_text = t.group ? t.label : t.html, t.search_match = this.search_string_match(t.search_text, i), t.search_match && !t.group && (s += 1), t.search_match ? (a.length && (r = t.search_text.search(c), l = t.search_text.substr(0, r + a.length) + "</em>" + t.search_text.substr(r + a.length), t.search_text = l.substr(0, r) + "<em>" + l.substr(r)), null != o && (o.group_match = !0)) : null != t.group_array_index && this.results_data[t.group_array_index].search_match && (t.search_match = !0)));
            return this.result_clear_highlight(), 1 > s && a.length ? (this.update_results_content(""), this.no_results(a)) : (this.update_results_content(this.results_option_build()), this.winnow_results_set_highlight())
        }, e.prototype.search_string_match = function(e, t) {
            var i, n, s, o;
            if (t.test(e))
                return !0;
            if (this.enable_split_word_search && (e.indexOf(" ") >= 0 || 0 === e.indexOf("[")) && (n = e.replace(/\[|\]/g, "").split(" "), n.length))
                for (s = 0, o = n.length; o > s; s++)
                    if (i = n[s], t.test(i))
                        return !0
        }, e.prototype.choices_count = function() {
            var e, t, i, n;
            if (null != this.selected_option_count)
                return this.selected_option_count;
            for (this.selected_option_count = 0, n = this.form_field.options, t = 0, i = n.length; i > t; t++)
                e = n[t], e.selected && (this.selected_option_count += 1);
            return this.selected_option_count
        }, e.prototype.choices_click = function(e) {
            return e.preventDefault(), this.results_showing || this.is_disabled ? void 0 : this.results_show()
        }, e.prototype.keyup_checker = function(e) {
            var t, i;
            switch (t = null != (i = e.which) ? i : e.keyCode, this.search_field_scale(), t) {
                case 8:
                    if (this.is_multiple && this.backstroke_length < 1 && this.choices_count() > 0)
                        return this.keydown_backstroke();
                    if (!this.pending_backstroke)
                        return this.result_clear_highlight(), this.results_search();
                    break;
                case 13:
                    if (e.preventDefault(), this.results_showing)
                        return this.result_select(e);
                    break;
                case 27:
                    return this.results_showing && this.results_hide(), !0;
                case 9:
                case 38:
                case 40:
                case 16:
                case 91:
                case 17:
                    break;
                default:
                    return this.results_search()
            }
        }, e.prototype.container_width = function() {
            return null != this.options.width ? this.options.width : "" + this.form_field.offsetWidth + "px"
        }, e.prototype.include_option_in_results = function(e) {
            return this.is_multiple && !this.display_selected_options && e.selected ? !1 : !this.display_disabled_options && e.disabled ? !1 : e.empty ? !1 : !0
        }, e.browser_is_supported = function() {
            return "Microsoft Internet Explorer" === window.navigator.appName ? document.documentMode >= 8 : /iP(od|hone)/i.test(window.navigator.userAgent) ? !1 : /Android/i.test(window.navigator.userAgent) && /Mobile/i.test(window.navigator.userAgent) ? !1 : !0
        }, e.default_multiple_text = "Select Some Options", e.default_single_text = "Select an Option", e.default_no_result_text = "No results match", e
    }(), e = jQuery, e.fn.extend({chosen: function(n) {
            return t.browser_is_supported() ? this.each(function() {
                var t, s;
                t = e(this), s = t.data("chosen"), "destroy" === n && s ? s.destroy() : s || t.data("chosen", new i(this, n))
            }) : this
        }}), i = function(t) {
        function i() {
            return s = i.__super__.constructor.apply(this, arguments)
        }
        return a(i, t), i.prototype.setup = function() {
            return this.form_field_jq = e(this.form_field), this.current_selectedIndex = this.form_field.selectedIndex, this.is_rtl = this.form_field_jq.hasClass("chosen-rtl")
        }, i.prototype.set_up_html = function() {
            var t, i;
            return t = ["chosen-container"], t.push("chosen-container-" + (this.is_multiple ? "multi" : "single")), this.inherit_select_classes && this.form_field.className && t.push(this.form_field.className), this.is_rtl && t.push("chosen-rtl"), i = {"class": t.join(" "),style: "width: " + this.container_width() + ";",title: this.form_field.title}, this.form_field.id.length && (i.id = this.form_field.id.replace(/[^\w]/g, "_") + "_chosen"), this.container = e("<div />", i), this.container.html(this.is_multiple ? '<ul class="chosen-choices"><li class="search-field"><input type="text" value="' + this.default_text + '" class="default" autocomplete="off" style="width:25px;" /></li></ul><div class="chosen-drop"><ul class="chosen-results"></ul></div>' : '<a class="chosen-single chosen-default" tabindex="-1"><span>' + this.default_text + '</span><div><b></b></div></a><div class="chosen-drop"><div class="chosen-search"><input type="text" autocomplete="off" /></div><ul class="chosen-results"></ul></div>'), this.form_field_jq.hide().after(this.container), this.dropdown = this.container.find("div.chosen-drop").first(), this.search_field = this.container.find("input").first(), this.search_results = this.container.find("ul.chosen-results").first(), this.search_field_scale(), this.search_no_results = this.container.find("li.no-results").first(), this.is_multiple ? (this.search_choices = this.container.find("ul.chosen-choices").first(), this.search_container = this.container.find("li.search-field").first()) : (this.search_container = this.container.find("div.chosen-search").first(), this.selected_item = this.container.find(".chosen-single").first()), this.results_build(), this.set_tab_index(), this.set_label_behavior(), this.form_field_jq.trigger("chosen:ready", {chosen: this})
        }, i.prototype.register_observers = function() {
            var e = this;
            return this.container.bind("mousedown.chosen", function(t) {
                e.container_mousedown(t)
            }), this.container.bind("mouseup.chosen", function(t) {
                e.container_mouseup(t)
            }), this.container.bind("mouseenter.chosen", function(t) {
                e.mouse_enter(t)
            }), this.container.bind("mouseleave.chosen", function(t) {
                e.mouse_leave(t)
            }), this.search_results.bind("mouseup.chosen", function(t) {
                e.search_results_mouseup(t)
            }), this.search_results.bind("mouseover.chosen", function(t) {
                e.search_results_mouseover(t)
            }), this.search_results.bind("mouseout.chosen", function(t) {
                e.search_results_mouseout(t)
            }), this.search_results.bind("mousewheel.chosen DOMMouseScroll.chosen", function(t) {
                e.search_results_mousewheel(t)
            }), this.form_field_jq.bind("chosen:updated.chosen", function(t) {
                e.results_update_field(t)
            }), this.form_field_jq.bind("chosen:activate.chosen", function(t) {
                e.activate_field(t)
            }), this.form_field_jq.bind("chosen:open.chosen", function(t) {
                e.container_mousedown(t)
            }), this.search_field.bind("blur.chosen", function(t) {
                e.input_blur(t)
            }), this.search_field.bind("keyup.chosen", function(t) {
                e.keyup_checker(t)
            }), this.search_field.bind("keydown.chosen", function(t) {
                e.keydown_checker(t)
            }), this.search_field.bind("focus.chosen", function(t) {
                e.input_focus(t)
            }), this.is_multiple ? this.search_choices.bind("click.chosen", function(t) {
                e.choices_click(t)
            }) : this.container.bind("click.chosen", function(e) {
                e.preventDefault()
            })
        }, i.prototype.destroy = function() {
            return e(document).unbind("click.chosen", this.click_test_action), this.search_field[0].tabIndex && (this.form_field_jq[0].tabIndex = this.search_field[0].tabIndex), this.container.remove(), this.form_field_jq.removeData("chosen"), this.form_field_jq.show()
        }, i.prototype.search_field_disabled = function() {
            return this.is_disabled = this.form_field_jq[0].disabled, this.is_disabled ? (this.container.addClass("chosen-disabled"), this.search_field[0].disabled = !0, this.is_multiple || this.selected_item.unbind("focus.chosen", this.activate_action), this.close_field()) : (this.container.removeClass("chosen-disabled"), this.search_field[0].disabled = !1, this.is_multiple ? void 0 : this.selected_item.bind("focus.chosen", this.activate_action))
        }, i.prototype.container_mousedown = function(t) {
            return this.is_disabled || (t && "mousedown" === t.type && !this.results_showing && t.preventDefault(), null != t && e(t.target).hasClass("search-choice-close")) ? void 0 : (this.active_field ? this.is_multiple || !t || e(t.target)[0] !== this.selected_item[0] && !e(t.target).parents("a.chosen-single").length || (t.preventDefault(), this.results_toggle()) : (this.is_multiple && this.search_field.val(""), e(document).bind("click.chosen", this.click_test_action), this.results_show()), this.activate_field())
        }, i.prototype.container_mouseup = function(e) {
            return "ABBR" !== e.target.nodeName || this.is_disabled ? void 0 : this.results_reset(e)
        }, i.prototype.search_results_mousewheel = function(e) {
            var t, i, n;
            return t = -(null != (i = e.originalEvent) ? i.wheelDelta : void 0) || (null != (n = e.originialEvent) ? n.detail : void 0), null != t ? (e.preventDefault(), "DOMMouseScroll" === e.type && (t = 40 * t), this.search_results.scrollTop(t + this.search_results.scrollTop())) : void 0
        }, i.prototype.blur_test = function() {
            return !this.active_field && this.container.hasClass("chosen-container-active") ? this.close_field() : void 0
        }, i.prototype.close_field = function() {
            return e(document).unbind("click.chosen", this.click_test_action), this.active_field = !1, this.results_hide(), this.container.removeClass("chosen-container-active"), this.clear_backstroke(), this.show_search_field_default(), this.search_field_scale()
        }, i.prototype.activate_field = function() {
            return this.container.addClass("chosen-container-active"), this.active_field = !0, this.search_field.val(this.search_field.val()), this.search_field.focus()
        }, i.prototype.test_active_click = function(t) {
            return this.container.is(e(t.target).closest(".chosen-container")) ? this.active_field = !0 : this.close_field()
        }, i.prototype.results_build = function() {
            return this.parsing = !0, this.selected_option_count = null, this.results_data = n.select_to_array(this.form_field), this.is_multiple ? this.search_choices.find("li.search-choice").remove() : this.is_multiple || (this.single_set_selected_text(), this.disable_search || this.form_field.options.length <= this.disable_search_threshold ? (this.search_field[0].readOnly = !0, this.container.addClass("chosen-container-single-nosearch")) : (this.search_field[0].readOnly = !1, this.container.removeClass("chosen-container-single-nosearch"))), this.update_results_content(this.results_option_build({first: !0})), this.search_field_disabled(), this.show_search_field_default(), this.search_field_scale(), this.parsing = !1
        }, i.prototype.result_do_highlight = function(e) {
            var t, i, n, s, o;
            if (e.length) {
                if (this.result_clear_highlight(), this.result_highlight = e, this.result_highlight.addClass("highlighted"), n = parseInt(this.search_results.css("maxHeight"), 10), o = this.search_results.scrollTop(), s = n + o, i = this.result_highlight.position().top + this.search_results.scrollTop(), t = i + this.result_highlight.outerHeight(), t >= s)
                    return this.search_results.scrollTop(t - n > 0 ? t - n : 0);
                if (o > i)
                    return this.search_results.scrollTop(i)
            }
        }, i.prototype.result_clear_highlight = function() {
            return this.result_highlight && this.result_highlight.removeClass("highlighted"), this.result_highlight = null
        }, i.prototype.results_show = function() {
            return this.is_multiple && this.max_selected_options <= this.choices_count() ? (this.form_field_jq.trigger("chosen:maxselected", {chosen: this}), !1) : (this.container.addClass("chosen-with-drop"), this.form_field_jq.trigger("chosen:showing_dropdown", {chosen: this}), this.results_showing = !0, this.search_field.focus(), this.search_field.val(this.search_field.val()), this.winnow_results())
        }, i.prototype.update_results_content = function(e) {
            return this.search_results.html(e)
        }, i.prototype.results_hide = function() {
            return this.results_showing && (this.result_clear_highlight(), this.container.removeClass("chosen-with-drop"), this.form_field_jq.trigger("chosen:hiding_dropdown", {chosen: this})), this.results_showing = !1
        }, i.prototype.set_tab_index = function() {
            var e;
            return this.form_field.tabIndex ? (e = this.form_field.tabIndex, this.form_field.tabIndex = -1, this.search_field[0].tabIndex = e) : void 0
        }, i.prototype.set_label_behavior = function() {
            var t = this;
            return this.form_field_label = this.form_field_jq.parents("label"), !this.form_field_label.length && this.form_field.id.length && (this.form_field_label = e("label[for='" + this.form_field.id + "']")), this.form_field_label.length > 0 ? this.form_field_label.bind("click.chosen", function(e) {
                return t.is_multiple ? t.container_mousedown(e) : t.activate_field()
            }) : void 0
        }, i.prototype.show_search_field_default = function() {
            return this.is_multiple && this.choices_count() < 1 && !this.active_field ? (this.search_field.val(this.default_text), this.search_field.addClass("default")) : (this.search_field.val(""), this.search_field.removeClass("default"))
        }, i.prototype.search_results_mouseup = function(t) {
            var i;
            return i = e(t.target).hasClass("active-result") ? e(t.target) : e(t.target).parents(".active-result").first(), i.length ? (this.result_highlight = i, this.result_select(t), this.search_field.focus()) : void 0
        }, i.prototype.search_results_mouseover = function(t) {
            var i;
            return i = e(t.target).hasClass("active-result") ? e(t.target) : e(t.target).parents(".active-result").first(), i ? this.result_do_highlight(i) : void 0
        }, i.prototype.search_results_mouseout = function(t) {
            return e(t.target).hasClass("active-result") ? this.result_clear_highlight() : void 0
        }, i.prototype.choice_build = function(t) {
            var i, n, s = this;
            return i = e("<li />", {"class": "search-choice"}).html("<span>" + t.html + "</span>"), t.disabled ? i.addClass("search-choice-disabled") : (n = e("<a />", {"class": "search-choice-close","data-option-array-index": t.array_index}), n.bind("click.chosen", function(e) {
                return s.choice_destroy_link_click(e)
            }), i.append(n)), this.search_container.before(i)
        }, i.prototype.choice_destroy_link_click = function(t) {
            return t.preventDefault(), t.stopPropagation(), this.is_disabled ? void 0 : this.choice_destroy(e(t.target))
        }, i.prototype.choice_destroy = function(e) {
            return this.result_deselect(e[0].getAttribute("data-option-array-index")) ? (this.show_search_field_default(), this.is_multiple && this.choices_count() > 0 && this.search_field.val().length < 1 && this.results_hide(), e.parents("li").first().remove(), this.search_field_scale()) : void 0
        }, i.prototype.results_reset = function() {
            return this.form_field.options[0].selected = !0, this.selected_option_count = null, this.single_set_selected_text(), this.show_search_field_default(), this.results_reset_cleanup(), this.form_field_jq.trigger("change"), this.active_field ? this.results_hide() : void 0
        }, i.prototype.results_reset_cleanup = function() {
            return this.current_selectedIndex = this.form_field.selectedIndex, this.selected_item.find("abbr").remove()
        }, i.prototype.result_select = function(e) {
            var t, i, n;
            return this.result_highlight ? (t = this.result_highlight, this.result_clear_highlight(), this.is_multiple && this.max_selected_options <= this.choices_count() ? (this.form_field_jq.trigger("chosen:maxselected", {chosen: this}), !1) : (this.is_multiple ? t.removeClass("active-result") : (this.result_single_selected && (this.result_single_selected.removeClass("result-selected"), n = this.result_single_selected[0].getAttribute("data-option-array-index"), this.results_data[n].selected = !1), this.result_single_selected = t), t.addClass("result-selected"), i = this.results_data[t[0].getAttribute("data-option-array-index")], i.selected = !0, this.form_field.options[i.options_index].selected = !0, this.selected_option_count = null, this.is_multiple ? this.choice_build(i) : this.single_set_selected_text(i.text), (e.metaKey || e.ctrlKey) && this.is_multiple || this.results_hide(), this.search_field.val(""), (this.is_multiple || this.form_field.selectedIndex !== this.current_selectedIndex) && this.form_field_jq.trigger("change", {selected: this.form_field.options[i.options_index].value}), this.current_selectedIndex = this.form_field.selectedIndex, this.search_field_scale())) : void 0
        }, i.prototype.single_set_selected_text = function(e) {
            return null == e && (e = this.default_text), e === this.default_text ? this.selected_item.addClass("chosen-default") : (this.single_deselect_control_build(), this.selected_item.removeClass("chosen-default")), this.selected_item.find("span").text(e)
        }, i.prototype.result_deselect = function(e) {
            var t;
            return t = this.results_data[e], this.form_field.options[t.options_index].disabled ? !1 : (t.selected = !1, this.form_field.options[t.options_index].selected = !1, this.selected_option_count = null, this.result_clear_highlight(), this.results_showing && this.winnow_results(), this.form_field_jq.trigger("change", {deselected: this.form_field.options[t.options_index].value}), this.search_field_scale(), !0)
        }, i.prototype.single_deselect_control_build = function() {
            return this.allow_single_deselect ? (this.selected_item.find("abbr").length || this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>'), this.selected_item.addClass("chosen-single-with-deselect")) : void 0
        }, i.prototype.get_search_text = function() {
            return this.search_field.val() === this.default_text ? "" : e("<div/>").text(e.trim(this.search_field.val())).html()
        }, i.prototype.winnow_results_set_highlight = function() {
            var e, t;
            return t = this.is_multiple ? [] : this.search_results.find(".result-selected.active-result"), e = t.length ? t.first() : this.search_results.find(".active-result").first(), null != e ? this.result_do_highlight(e) : void 0
        }, i.prototype.no_results = function(t) {
            var i;
            return i = e('<li class="no-results">' + this.results_none_found + ' "<span></span>"</li>'), i.find("span").first().html(t), this.search_results.append(i)
        }, i.prototype.no_results_clear = function() {
            return this.search_results.find(".no-results").remove()
        }, i.prototype.keydown_arrow = function() {
            var e;
            return this.results_showing && this.result_highlight ? (e = this.result_highlight.nextAll("li.active-result").first()) ? this.result_do_highlight(e) : void 0 : this.results_show()
        }, i.prototype.keyup_arrow = function() {
            var e;
            return this.results_showing || this.is_multiple ? this.result_highlight ? (e = this.result_highlight.prevAll("li.active-result"), e.length ? this.result_do_highlight(e.first()) : (this.choices_count() > 0 && this.results_hide(), this.result_clear_highlight())) : void 0 : this.results_show()
        }, i.prototype.keydown_backstroke = function() {
            var e;
            return this.pending_backstroke ? (this.choice_destroy(this.pending_backstroke.find("a").first()), this.clear_backstroke()) : (e = this.search_container.siblings("li.search-choice").last(), e.length && !e.hasClass("search-choice-disabled") ? (this.pending_backstroke = e, this.single_backstroke_delete ? this.keydown_backstroke() : this.pending_backstroke.addClass("search-choice-focus")) : void 0)
        }, i.prototype.clear_backstroke = function() {
            return this.pending_backstroke && this.pending_backstroke.removeClass("search-choice-focus"), this.pending_backstroke = null
        }, i.prototype.keydown_checker = function(e) {
            var t, i;
            switch (t = null != (i = e.which) ? i : e.keyCode, this.search_field_scale(), 8 !== t && this.pending_backstroke && this.clear_backstroke(), t) {
                case 8:
                    this.backstroke_length = this.search_field.val().length;
                    break;
                case 9:
                    this.results_showing && !this.is_multiple && this.result_select(e), this.mouse_on_container = !1;
                    break;
                case 13:
                    e.preventDefault();
                    break;
                case 38:
                    e.preventDefault(), this.keyup_arrow();
                    break;
                case 40:
                    e.preventDefault(), this.keydown_arrow()
            }
        }, i.prototype.search_field_scale = function() {
            var t, i, n, s, o, a, r, l, c;
            if (this.is_multiple) {
                for (n = 0, r = 0, o = "position:absolute; left: -1000px; top: -1000px; display:none;", a = ["font-size", "font-style", "font-weight", "font-family", "line-height", "text-transform", "letter-spacing"], l = 0, c = a.length; c > l; l++)
                    s = a[l], o += s + ":" + this.search_field.css(s) + ";";
                return t = e("<div />", {style: o}), t.text(this.search_field.val()), e("body").append(t), r = t.width() + 25, t.remove(), i = this.container.outerWidth(), r > i - 10 && (r = i - 10), this.search_field.css({width: r + "px"})
            }
        }, i
    }(t)
}.call(this), define("chosen", ["jquery"], function() {
}), define("components/pop/fans_tag", ["require", "backbone", "jquery", "components/pop/base", "text!components/pop/templates/fans_tag.html", "core/utils", "chosen"], function(e) {
    var t = (e("backbone"), e("jquery")), i = e("components/pop/base"), n = e("text!components/pop/templates/fans_tag.html"), s = e("core/utils");
    return e("chosen"), i.extend({template: _.template(n),className: "popover bottom",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            i.prototype.initialize.call(this, e), this.data = e.data;
            var n = _global.url.www;
            this.loadTagsUrl = n + "/fans/tag/list.json", this.addTagUrl = n + "/fans/tag/item.json", this.tagsData = {tags: []}, this.loadingTags();
            var s = this;
            this.$el.on("keyup", ".chosen-choices input", function(e) {
                var i = t(this).parents(".chosen-container").find(".no-results");
                return i.size() > 0 && 0 === t(".add-new-tag", this.$el).size() && i.append(' <div><a class="add-new-tag" href="javascript:void(0);">添加该标签</a></div>'), 13 === e.keyCode ? (t(".add-new-tag", this.$el).click(), !1) : void 0
            }), this.$el.on("click", ".add-new-tag", function() {
                var e = t(this).parents(".chosen-container").find(".chosen-choices").find("input").val();
                s.createTag(e)
            })
        },createTag: function(e) {
            var i = this;
            t.post(this.addTagUrl, {name: e}).success(function(t) {
                if (0 == t.code) {
                    var n = t.data;
                    i.chosen.append("<option value=" + n.tag_id + " selected>" + e + "</option>").trigger("chosen:updated"), s.successNotify("创建新标签成功")
                } else
                    s.errorNotify(t.msg)
            }).fail(function() {
                s.errorNotify("操作失败")
            })
        },loadingTags: function() {
            var e = this;
            t.get(this.loadTagsUrl, {scope: "all"}).success(function(t) {
                0 == t.code ? (e.tagsData.tags = t.data, e.render()) : s.errorNotify(t.msg + "")
            }).fail(function() {
                s.errorNotify("数据获取失败")
            })
        },render: function() {
            var e = this, t = this.tagsData, i = this.data.get();
            return t.tags.forEach(function(e) {
                e.isSelected = i.some(function(t) {
                    return e.id == t.id
                })
            }), e.$el.html(e.template(t)), this.chosen = this.$(".js-chosen-select").chosen({no_results_text: "木有这个标签哦"}), e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show(), this.$(".js-chosen-select").empty().trigger("chosen:updated"), this.loadingTags()
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: this.target,my: "center top",at: "center bottom",collision: "none"})
        },triggerCallback: function() {
            var e = this, i = [];
            this.chosen.find(":selected").each(function() {
                var e = t(this);
                i.push({text: e.text(),value: e.val()})
            }), e.callback(i), e.hide()
        }})
}), define("text!components/pop/templates/fans_level.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-fans-tag">\n    <div class="popover-content">\n        <form class="form-inline loading">\n            <select data-placeholder="选择会员等级" class="js-chosen-select">\n                <% for(var i = 0; i < levels.length; i ++){ %>\n                    <option value="<%= levels[i].id %>"\n                        <% if(levels[i].isSelected) {%>\n                            selected\n                        <% } %>\n                    ><%= levels[i].name %></option>\n                <% } %>\n            </select>\n            <div class="pull-right">\n                <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n                <button type="reset" class="btn js-btn-cancel">取消</button>\n            </div>\n\n        </form>\n    </div>\n</div>\n'
}), define("components/pop/fans_level", ["require", "underscore", "jquery", "components/pop/base", "text!components/pop/templates/fans_level.html", "core/utils", "chosen"], function(e) {
    var t = e("underscore"), i = e("jquery"), n = e("components/pop/base"), s = e("text!components/pop/templates/fans_level.html"), o = e("core/utils");
    return e("chosen"), n.extend({template: t.template(s),className: "popover bottom",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            n.prototype.initialize.call(this, e), this.data = e.data;
            var t = _global.url.www;
            this.loadLevelsUrl = t + "/weixin/autoreply/fansLevels.json", this.levelsData = {levels: []}, this.loadingLevels()
        },loadingLevels: function() {
            var e = this;
            i.get(this.loadLevelsUrl).success(function(t) {
                0 === +t.code ? (e.levelsData.levels = t.data, e.render()) : o.errorNotify(t.msg + "")
            }).fail(function() {
                o.errorNotify("数据获取失败")
            })
        },render: function() {
            var e = this, t = this.levelsData, i = this.data.get();
            return t.levels.forEach(function(e) {
                e.isSelected = e.id === i.id
            }), e.$el.html(e.template(t)), this.chosen = this.$(".js-chosen-select").chosen({no_results_text: "木有这个会员等级哦"}), e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show(), this.$(".js-chosen-select").empty().trigger("chosen:updated"), this.loadingLevels()
        },setData: function(e) {
            this.data = t.extend(this.data, e), this.render()
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: this.target,my: "center top",at: "center bottom",collision: "none"})
        },triggerCallback: function() {
            var e = this, t = [];
            this.chosen.find(":selected").each(function() {
                var e = i(this);
                t.push({text: e.text(),value: e.val()})
            }), e.callback(t), e.hide()
        }})
}), define("text!components/pop/templates/qrcode.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-thin">\n    <div class="popover-content">             \n        <div class="controls">\n            <input class="js-txt" type="text" maxlength="15" class="span3 txt" placeholder="二维码名称" value="<%= name %>">\n        </div>\n           \n        <button class="js-btn-confirm btn btn-primary">确定</button>\n        <button class="js-btn-cancel btn">取消</button>        \n    </div>\n</div>'
}), define("components/pop/qrcode", ["backbone", "components/pop/base", "text!components/pop/templates/qrcode.html", "core/utils"], function(e, t, i, n) {
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback","keypress .js-txt": "enterSave"},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e.el.className = e.options.className, e.txt = e.$el.find(".js-txt"), e
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: e.target,my: "center top",at: "center bottom",collision: "none"}), e.txt.focus()
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className;
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },enterSave: function(e) {
            return e.which === n.keyCode.ENTER ? (this.triggerCallback(), !1) : void 0
        },triggerCallback: function() {
            var e = this, t = e.txt.val();
            return n.isEmpty(t) ? (e.txt.focus(), !1) : (e.setData({name: t}), e.callback.call(e, e.data), void e.hide())
        }})
}), define("components/editor_v2/models/editor", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = (e("core/utils"), t.Model.extend({defaults: function() {
            return {charLimit: 300,picLimit: 9,topic: "在这里输入你想要说的话题",hasSign: !1,supportModules: {emotion: "Emotion",hyperlink: "Hyperlink",picture: "Picture",articles: "Articles",feature: "Feature",goods: "Goods",homepage: "Homepage",shortlink: "Shortlink",image: "Image",usercenter: "Usercenter",voice: "Voice",wx_emotion: "WxEmotion",music: "Music"},i18n: {text: "文本",hyperlink: "插入链接",picture: "图片",image: "图片",articles: "选择图文",feature: "微杂志及分类",goods: "商品及分类",homepage: "店铺主页",usercenter: "会员主页",voice: "语音",music: "音乐"},mode: "top-action",modules: ["emotion", "hyperlink"],flat: [],thumb: [],data: {type: "text",pics: [],content: ""}}
        },initialize: function() {
            var e = this.toJSON();
            e.flat = e.modules.filter(function(t) {
                return e.thumb.every(function(e) {
                    return e != t
                })
            }), this.set(e, {silent: !0})
        }}));
    return i
}), define("text!components/audio/templates/audio_list.html", [], function() {
    return '<div class="modal-header">\n    <a class="close" data-dismiss="modal">×</a>\n    <!-- 顶部tab -->\n    <ul class="module-nav modal-tab">\n        <li class="active"><a href="javascript:;" data-pane="audio" class="js-modal-tab">用过的语音</a> | </li>\n        <li><a href="javascript:;" data-pane="upload" class="js-modal-tab">新语音</a></li>\n    </ul>\n</div>\n<div class="tab-pane js-tab-pane-audio">\n    <div class="modal-body module-audio">\n        <table class="table">\n            <thead>\n                <tr>\n                    <th class="time"><span>语音</span></th>\n                    <th class="title"><span>文件名</span></th>\n                    <th class="time"><span>创建时间</span></th>\n                    <th class="opts">\n                        <form class="form-search search-box">\n                            <div class="input-append">\n                                <input class="input-small js-modal-search-input" type="text"/>\n                                <a href="javascript:void(0);" class="btn js-fetch-page js-modal-search" data-action-type="search">搜</a>\n                            </div>\n                        </form>\n                    </th>\n                </tr>\n            </thead>\n            <tbody class="module-body clearfix">\n\n            </tbody>\n        </table>\n    </div>\n    <div class="modal-footer">\n        <div class="modal-action pull-left">\n            <input type="button" class="btn btn-primary js-choose-audio hide" value="确定使用">\n        </div>\n        <div class="pagenavi js-pagenavi"></div>\n    </div>\n</div>\n<div class="tab-pane js-tab-pane-upload hide">\n    <div class="modal-body">\n        <div class="upload-local-img js-upload-local-img"></div>\n    </div>\n    <div class="modal-footer">\n        <div class="modal-action pull-right">\n            <input type="button" class="btn btn-primary js-upload-audio" data-loading-text="上传中..." value="确定上传">\n        </div>\n    </div>\n</div>\n\n'
}), define("text!components/audio/templates/audio.html", [], function() {
    return '<td>\n    <div class="td_cont">\n        <div class="voice-wrapper" data-voice-src="<%= window._global.url.imgqn %>/<%= attachment_url %>!8k.mp3">\n            <span class="voice-player">\n                <span class="arrow"></span>\n                <span class="stop">点击播放</span>\n                <span class="second"></span>\n                <i class="play" style="display:none;"></i>\n            </span>\n        </div>\n    </div>\n</td>\n<td class="title"><%= attachment_title %></td>\n<td class="time">\n    <span class="td_cont"><%= created_time %></span>\n</td>\n<td class="opts">\n    <div class="td_cont"><button class="btn btn-choose">选取</button></div>\n</td>'
}), define("components/audio/views/audio_view", ["require", "marionette", "text!components/audio/templates/audio.html"], function(e) {
    var t = e("marionette"), i = e("text!components/audio/templates/audio.html");
    return t.ItemView.extend({template: _.template(i),tagName: "tr",events: {"click .btn-choose": "select"},collectionEvents: {clear: "clear"},className: function() {
            return this.model.collection.isSelected(this.model) ? "selected" : ""
        },select: function() {
            this.model.collection.select(this.model), this.$el.toggleClass("selected"), 0 == this.model.collection.multiChoose && this.model.collection.trigger("audio:choose:success")
        },clear: function() {
            this.$el.removeClass("selected")
        }})
}), define("components/audio/views/audio_list_view", ["require", "marionette", "text!components/audio/templates/audio_list.html", "components/audio/views/audio_view", "core/utils"], function(e) {
    var t = e("marionette"), i = e("text!components/audio/templates/audio_list.html"), n = e("components/audio/views/audio_view"), s = e("core/utils");
    return t.CompositeView.extend({className: "modal fade hide",ui: {pagenavi: ".js-pagenavi",chooseButton: ".js-choose-audio",audio: ".js-tab-pane-audio",upload: ".js-tab-pane-upload",pane: ".tab-pane",uploadButton: ".js-upload-audio",searchInput: ".js-modal-search-input"},events: {"click .fetch_page": "fetchPage","click .js-choose-audio": "chooseAudio","click .js-update-audio-list": "update","click .js-modal-tab": "tab","click .js-upload-audio": "uploadAudio","click .js-modal-search": "update","keydown .js-modal-search-input": "search"},template: _.template(i),itemView: n,itemViewContainer: ".module-body",collectionEvents: {sync: "renderPagenavi",select: "toggleChooseButton"},initialize: function() {
            this.update();
            var e = this;
            this.listenTo(window.NC, "audio:upload:always", function() {
                e.ui.uploadButton.button("reset")
            }), this.listenTo(this.collection, "audio:choose:success", this.chooseAudio)
        },tab: function(e) {
            var t = $(e.target), i = t.parent();
            i.addClass("active"), i.siblings("li").removeClass("active");
            var n = t.data("pane");
            this.ui.pane.addClass("hide"), this.ui[n].removeClass("hide")
        },fetchPage: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = $(e.target);
            this.collection._pageNumber = t.data("page-num"), this._fetch()
        },update: function() {
            this._fetch()
        },search: function(e) {
            e.stopPropagation(), e.keyCode == s.keyCode.ENTER && (this._fetch(), e.preventDefault())
        },_fetch: function() {
            this.$el.addClass("audio-loading");
            var e = "";
            _.isFunction(this.ui.searchInput.val) && (e = this.ui.searchInput.val());
            var t = s.addParameter(this.collection.url, {p: this.collection._pageNumber,keyword: e}), i = this;
            this.collection.fetch({url: t,success: function() {
                    i.$el.removeClass("audio-loading")
                }})
        },chooseAudio: function() {
            var e = this.collection.getSelectedAudios();
            window.NC.trigger("audio:choose:success", e), this.clearAllSelected()
        },clearAllSelected: function() {
            this.collection.clearAllSelected(), this.$("li.selected").removeClass("selected")
        },renderPagenavi: function() {
            this.ui.pagenavi.html(this.collection.pagenavi)
        },toggleChooseButton: function(e) {
            e > 0 ? this.ui.chooseButton.removeClass("hide") : this.ui.chooseButton.addClass("hide")
        },uploadAudio: function() {
            this.ui.uploadButton.button("loading"), window.NC.trigger("audio:upload")
        }})
}), define("components/audio/models/audio", ["require", "backbone"], function(e) {
    var t = e("backbone");
    return t.Model.extend({})
}), define("components/audio/collections/audios", ["require", "backbone", "components/audio/models/audio", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/audio/models/audio"), n = e("core/utils");
    return t.Collection.extend({url: _global.url.www + "/showcase/attachment/alert.json?media_type=2&v=2",model: i,_pageNumber: 1,parse: function(e) {
            return 0 == e.errcode ? (this.pagenavi = e.data.page_view, e.data.data_list) : void n.errorNotify(e.errmsg || "出错啦。")
        },_selectedModel: [],_idAttribute: "attachment_id",getSelectedAudios: function() {
            return this._selectedModel
        },select: function(e) {
            var t = this, i = {};
            i[this._idAttribute] = e.get(this._idAttribute);
            var n = _.where(this._selectedModel, i);
            n.length > 0 ? _.each(n, function(e) {
                t._selectedModel.splice(t._selectedModel.indexOf(e), 1)
            }) : this._selectedModel.push(e.attributes), this.trigger("select", this._selectedModel.length, e)
        },isSelected: function(e) {
            var t = {};
            t[this._idAttribute] = e.get(this._idAttribute);
            var i = _.where(this._selectedModel, t);
            return i.length > 0 ? !0 : !1
        },clearAllSelected: function() {
            this._selectedModel.length = 0, this.trigger("clear"), this.trigger("select", 0)
        }})
}), define("text!components/audio/templates/audio_upload.html", [], function() {
    return '<div class="control-group">\n    <label class="control-label">本地语音：</label>\n    <div class="controls preview-container js-preview-upload-img">\n    </div>\n    <div class="controls">\n        <div class="control-action">\n            <div class="voice-preview hide">\n                <p class="name"></p>\n                <p class="size"></p>\n            </div>\n            <div class="voice-file-input">\n                <a href="javascript:;" class="js-fileupload-btn">添加语音...</a>\n                <input class="js-fileupload-input" type="file" name="upload_file" data-url="http://img.koudaitong.com/uploadmultiple?format=json">\n            </div>\n\n            <p class="help-desc">微信支持 2 MB 以内的语音 (amr, mp3 格式)</p>\n        </div>\n    </div>\n</div>'
}), define("components/audio/views/audio_upload", ["require", "marionette", "text!components/audio/templates/audio_upload.html"], function(e) {
    var t = e("marionette"), i = e("text!components/audio/templates/audio_upload.html");
    return t.CompositeView.extend({tagName: "form",className: "form-horizontal",template: _.template(i),events: {"change .js-fileupload-input": "selectFile"},initialize: function() {
            this.listenTo(window.NC, "audio:upload", this.upload)
        },appendBuffer: function(e, t) {
            e.$(".fileinput-button").before(t)
        },appendHtml: function(e, t) {
            e.isBuffering ? (e.elBuffer.appendChild(t.el), e._bufferedChildren.push(t)) : e.$(".fileinput-button").before(t.el)
        },selectFile: function(e) {
            var t = e.target.files, i = this;
            _.each(t, function(e) {
                var t = e.size / 1024 / 1024;
                t > 2 || e.name.match(/(\.|\/)(mp3|amr)$/i) && i.previewAndAdd(e)
            })
        },previewAndAdd: function(e) {
            var t = new FileReader, i = this;
            t.onload = function(t) {
                i.collection.reset({src: t.target.result,file: e}, {silent: !0}), i.$(".voice-preview").show(), i.$(".voice-preview .name").html(e.name), i.$(".voice-preview .size").html((e.size / 1024).toFixed(1) + "kb")
            }, this.$(".js-fileupload-btn").html("重新选择.."), t.readAsDataURL(e)
        },upload: function() {
            this.collection.sync({success: function() {
                    this.render()
                }.bind(this)})
        }})
}), define("components/audio/models/audio_upload", ["require", "backbone"], function(e) {
    var t = e("backbone");
    return t.Model.extend({})
}), define("components/audio/collections/audio_upload", ["require", "backbone", "components/audio/models/audio_upload", "core/utils", "components/image/collections/image_upload"], function(e) {
    var t = (e("backbone"), e("components/audio/models/audio_upload")), i = (e("core/utils"), e("components/image/collections/image_upload"));
    return i.extend({model: t,eventPrefix: "audio"})
}), define("components/player/audio_player", ["jquery"], function(e) {
    var t = function(e, t) {
        this.Audio.init(e, t)
    };
    t.prototype.Audio = {init: function(t, i) {
            var n = this;
            this.opts = i, this.$voice = e(t), this.$wrapper = this.$voice.parent(), this.$stop = this.$voice.find(i.stop), this.$play = this.$voice.find(i.play), this.$second = this.$voice.find(i.second), this.$duration = this.$voice.find(i.duration), this.stop_text = "点击播放", this.voice_src = this.$wrapper.data("voice-src"), this.stopnow = null, n.appendDom()
        },appendDom: function() {
            var t = this, i = null, n = e('<div id="audio_wrapper"><audio id="audio_player" preload="" type="audio/mpeg" src=""></audio></div>').hide();
            0 === e("#audio_wrapper").size() && e("body").append(n), e("#audio_player").attr("src", t.voice_src), i = e("#audio_player").get(0), t.voicePlay(i)
        },voicePlay: function(t) {
            var i = this, n = this.opts, s = "js-audio-playnow";
            e("." + s).find(n.play).hide(), e("." + s).find(n.stop).text(i.stop_text).show(), e("." + s).find(n.second).empty().hide(), e("." + s).find(n.duration).show(), e("." + s).removeClass(s), i.$voice.addClass(s), i.$stop.text("loading..."), i.playerEvent(t)
        },playerEvent: function(t) {
            var i = this, n = null, s = !!navigator.userAgent.match(/AppleWebKit.*Mobile./), o = function() {
                i.$play.hide(), i.$stop.text(i.stop_text).show(), i.$second.empty().hide(), i.$duration.show(), i.$voice.removeClass("js-audio-playnow"), n && clearInterval(n)
            };
            i.stopnow = !1, s && t.play(), t.addEventListener("canplay", function() {
                i.stopnow || (s || (t.play(), i.$second.empty().show(), n && clearInterval(n), i.$second.html("0/" + Math.floor(t.duration)), n = setInterval(function() {
                    i.$second.html(Math.round(t.currentTime) + "/" + Math.floor(t.duration))
                }, 1e3)), i.$stop.text(i.stop_text).hide(), i.$play.show(), i.$duration.hide())
            }), t.addEventListener("ended", function() {
                o()
            }), t.addEventListener("error", function() {
                i.$stop.text("加载失败！")
            }), e("body").on("click", ".js-audio-playnow", function() {
                i.stopnow = !0, t.pause(), o()
            })
        }}, e.fn.extend({audioPlayer: function(i) {
            return this.each(function() {
                var n = e.extend({stop: ".stop",play: ".play",second: ".second",duration: ".duration"}, i);
                new t(this, n)
            })
        }}), e.getAudioDuration = function(t) {
        var i, n, s;
        return n = new e.Deferred, void 0 == t ? (n.reject("音频地址为空"), n) : (i = new Audio, i.src = t, i.oncanplay = function() {
            clearTimeout(s), n.resolve(i.duration)
        }, i.onerror = function() {
            clearTimeout(s), n.reject("音频获取失败")
        }, s = setTimeout(function() {
            n.reject("获取音频信息超时")
        }, 5e3), n)
    }
}), define("components/audio/app", ["require", "core/event", "marionette", "components/audio/views/audio_list_view", "components/audio/collections/audios", "components/audio/views/audio_upload", "components/audio/collections/audio_upload", "components/player/audio_player"], function(e) {
    window.NC = e("core/event");
    var t = e("marionette"), i = e("components/audio/views/audio_list_view"), n = e("components/audio/collections/audios"), s = e("components/audio/views/audio_upload"), o = e("components/audio/collections/audio_upload");
    e("components/player/audio_player");
    var a = new t.Application;
    a.on("initialize:before", function() {
        $("body").append('<div class="js-audio-app-container"></div>'), a.addRegions({container: ".js-audio-app-container",audioUpload: ".js-upload-local-img"})
    });
    var r = new n, l = new o;
    a.addInitializer(function() {
        a.container.show(new i({collection: r})), a.audioUpload.show(new s({collection: l}))
    }), a.on("show:audio", function() {
        a.container.$el.find(".modal").modal("show");
        var e = a._options.tabIndex, t = a._options.onlyUpload, i = a.container.$el.find(".js-modal-tab");
        void 0 != e && i.eq(e).click(), t === !0 && (a.container.$el.find(".module-nav li").eq(0).hide(), i.eq(1).click())
    });
    var c = function(e) {
        a._options && _.isFunction(a._options.callback) && a._options.callback(e), a.container.$el.find(".modal").modal("hide")
    };
    return window.NC.on("audio:upload:success", c), window.NC.on("audio:download:success", c), window.NC.on("audio:choose:success", c), {initialize: function(e) {
            this._initialized || (a.start(), this._initialized = !0), a._options = e, r.multiChoose = e.multiChoose === !1 ? !1 : !0, $("body").off("click", ".voice-player:not(.js-audio-playnow)"), $("body").on("click", ".voice-player:not(.js-audio-playnow)", function() {
                $(this).audioPlayer()
            }), a.trigger("show:audio")
        }}
}), define("text!components/editor_v2/templates/editor.html", [], function() {
    return '<% if(mode !== \'bottom-action\') { %>\n<div class="misc top clearfix">\n    <div class="content-actions clearfix">\n    <% for(var idx = 0, len = flat.length; idx < len; idx++) { %>\n\n        <% if (flat[idx] === \'emotion\') { %>\n        <div class="editor-module insert-emotion">\n            <a class="js-open-emotion" data-action-type="emotion" href="javascript:;">表情</a>\n            <div class="emotion-wrapper">\n                <ul class="emotion-container clearfix"></ul>\n            </div>\n        </div>\n\n        <% } else if (flat[idx] === \'wx_emotion\') { %>\n        <div class="editor-module insert-emotion">\n            <a class="js-open-wx_emotion" data-action-type="emotion" href="javascript:;">表情</a>\n            <div class="emotion-wrapper">\n                <ul class="emotion-container clearfix"></ul>\n            </div>\n        </div>\n\n        <% } else if (flat[idx] === \'shortlink\') { %>\n        <div class="editor-module insert-shortlink">\n            <div class="js-reply-menu dropup hover dropdown--right add-reply-menu">\n                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">\n                    <span class="dropdown-current">插入链接</span>\n                    <i class="caret"></i>\n                </a>\n                <ul class="dropdown-menu">\n                    <li><a data-link-type="Feature" class="js-open-shortlink" href="javascript:;">微杂志及分类</a></li>\n                    <li><a data-link-type="Goods" class="js-open-shortlink" href="javascript:;">商品及分类</a></li>\n                    <li><a data-link-type="Homepage" class="js-open-shortlink" href="javascript:;">店铺主页</a></li>\n                </ul>\n            </div>\n        </div>\n        \n        <% } else { %>\n        <div class="editor-module insert-<%- flat[idx] %>">\n            <a class="js-open-<%- flat[idx] %>" data-action-type="<%- flat[idx] %>" href="javascript:;"><%- i18n[flat[idx]] %></a>\n            <div class="<%- flat[idx] %>-wrapper"></div>\n        </div>\n        <% } %>\n\n    <% } %>\n\n        <% if (thumb.length > 0) { %>\n            <div class="editor-module">\n                <div class="js-reply-menu dropdown hover add-reply-menu">\n                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">\n                        <span class="dropdown-current">其它</span>\n                        <i class="caret"></i>\n                    </a>\n                    <ul class="dropdown-menu">\n                    <% for(var idx = 0, len = thumb.length; idx < len; idx++) { %>\n                        <li><a class="js-open-<%= thumb[idx] %>" data-action-type="<%= thumb[idx] %>" data-complex-mode="true" href="javascript:;"><%= i18n[thumb[idx]] %></a></li>\n                    <% } %>\n                    </ul>\n                </div>\n            </div>\n        <% } %>\n\n    </div>\n</div>\n<% } %>\n\n<div class="content-wrapper">\n    <textarea class="js-txta" cols="50" rows="4"></textarea>\n    <div class="js-picture-container picture-container"></div>\n    <div class="complex-backdrop"><div class="js-complex-content complex-content"></div></div>\n</div>\n\n<div class="misc clearfix">\n    <div class="content-actions clearfix">\n        <div class="word-counter pull-right">还能输入 <i><%=charLimit %></i> 个字</div>\n    </div>\n</div>\n\n\n'
}), define("components/editor_v2/models/data", ["require", "backbone", "core/utils"], function(e) {
    var t = e("backbone"), i = e("core/utils"), n = t.Model.extend({defaults: function() {
            return {type: "text",pics: [],content: ""}
        },i18n: {text: "文本",picture: "图片",articles: "图文",feature: "微杂志",category: "微杂志分类",goods: "商品",tag: "商品标签",homepage: "商家主页",usercenter: "会员主页",music: "音乐"},initialize: function(e, t) {
            var i = this, n = t.modules || [], s = "text," + n.join(",");
            s = s.replace("goods", "goods,tag").replace("feature", "feature,category"), i.modules = s.split(",")
        },validate: function(e) {
            var t = this, n = !0, s = e.type, o = t.modules;
            return o && !_(o).contains(s) ? (n = "此编辑器不支持插入“" + t.i18n[s] + "”类型的内容。", i.errorNotify(n), n) : void 0
        }});
    return n
}), define("components/editor_v2/collections/picture_list", ["require", "backbone", "components/wb_editor_v2/models/picture", "core/utils"], function(e) {
    var t = e("backbone"), i = e("components/wb_editor_v2/models/picture"), n = e("core/utils"), s = t.Collection.extend({model: i,initialize: function(e) {
            var t = this;
            t.maxSize = e.maxSize || 9
        },getPics: function() {
            var e = this;
            return e.pluck("attachment_url")
        },checkMaxsize: function() {
            var e = this, t = e.length < e.maxSize, i = "最多支持 " + e.maxSize + " 张图片。";
            return t || (console.log(i), n.errorNotify(i)), t
        }});
    return s
}), define("components/editor_v2/views/picture", ["require", "backbone", "text!components/wb_editor_v2/templates/picture.html", "core/utils"], function(e) {
    var t = e("backbone"), i = e("text!components/wb_editor_v2/templates/picture.html"), n = (e("core/utils"), t.View.extend({template: _.template(i),tagName: "div",className: "picture-wrapper",events: {"click .js-delete-picture": "deletePicture"},initialize: function() {
            var e = this;
            e.listenTo(e.model, "change", e.render), e.listenTo(e.model, "destroy", e.remove)
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.model.toJSON())), this.$("img").load(function() {
                $(this).parents(".picture").removeClass("loading").off("load")
            }), e
        },deletePicture: function() {
            var e = this, t = e.model.collection;
            e.model.destroy(), t.trigger("update:picture_list")
        }}));
    return n
}), define("text!components/music/templates/layout.html", [], function() {
    return '<div class="modal-header">\n    <a class="close" data-dismiss="modal">×</a>\n    <h3 class="title">音乐素材</h3>\n</div>\n<div class="modal-body js-detail-container">\n\n</div>\n<div class="modal-footer">\n    <a href="javascript:;" class="btn btn-primary js-save-data" data-loading-text="确 定...">确 定</a>\n</div>\n'
}), define("components/validation/validate", ["backbone", "backboneValidate"], function(e) {
    _.extend(e.Validation.patterns, {mobile: /^1\d{10}$/,integer: /^[1-9]\d*$/}), _.extend(e.Validation.messages, {mobile: "请输入正确的手机号码"}), _.extend(e.Validation.callbacks, {valid: function(e, t, i) {
            t.indexOf(".") >= 0 && (t = t.split(".").join("_"));
            var n, s;
            return n = e.$("[" + i + "=" + t + "]"), 0 === n.length ? (n = e.$("[" + i + '^="' + t + '"]'), s = n.parents(".control-group").first()) : s = n.parents(".control-group").first(), s.removeClass("error"), "tooltip" !== n.data("error-style") ? "inline" === n.data("error-style") ? s.find(".help-inline.error-message").remove() : s.find(".help-block.error-message").remove() : n.data("tooltip") ? n.tooltip("hide") : void 0
        },invalid: function(e, t, i, n) {
            t.indexOf(".") >= 0 && (t = t.split(".").join("_"));
            var s, o, a, r;
            return s = e.$("[" + n + "=" + t + "]"), 0 === s.length ? (s = e.$("[" + n + '^="' + t + '"]'), o = s.parents(".control-group").first()) : o = s.parents(".control-group").first(), o.addClass("error"), "tooltip" === s.data("error-style") ? (a = s.data("tooltip-position") || "right", s.tooltip({placement: a,trigger: "manual",title: i}), s.tooltip("show")) : "inline" === s.data("error-style") ? (0 === o.find(".help-inline").length && o.find(".controls").append('<span class="help-inline error-message"></span>'), r = o.find(".help-inline"), r.text(i)) : (0 === o.find(".help-block").length && o.find(".controls").append('<p class="help-block error-message"></p>'), r = o.find(".help-block"), r.text(i))
        }})
}), define("text!components/music/templates/thumb.html", [], function() {
    return '<% if (thumb_attachment_url) { %>\n    <img src="<%= fullfillImage(thumb_attachment_url) %>" width="100" height="100">\n<% } else { %>\n    <a class="js-choose-thumb" href="javascript:;">+添加缩略图</a>\n<% } %>'
}), define("components/music/views/thumb", ["require", "marionette", "components/image/app", "text!components/music/templates/thumb.html", "core/utils"], function(e) {
    var t = e("marionette"), i = e("components/image/app"), n = e("text!components/music/templates/thumb.html"), s = e("core/utils");
    return t.ItemView.extend({template: _.template(n),className: "msg-music-thumb-inner",modelEvents: {"change thumb_attachment_id": "render"},events: {"click .js-choose-thumb": "chooseThumb"},templateHelpers: {fullfillImage: function(e) {
                return s.fullfillImage(e)
            }},chooseThumb: function() {
            i.initialize({multiChoose: !1,maxSize: 128,callback: function(e) {
                    e = e[0], this.model.set({thumb_attachment_id: e.attachment_id,thumb_attachment_url: e.thumb_url})
                }.bind(this)})
        }})
}), define("text!components/music/templates/form.html", [], function() {
    return '<div class="msg-music clearfix">\n    <div class="pull-left msg-music-thumb">\n\n    </div>\n    <div class="pull-left msg-music-input">\n        <div class="control-group">\n            <input type="text" name="title" placeholder="音乐标题" maxLength="20" value="<%= title %>">\n        </div>\n        <div class="control-group">\n            <textarea name="description" cols="30" rows="2" placeholder="音乐描述" maxLength="50"><%= description %></textarea>\n        </div>\n    </div>\n</div>\n<div class="control-group">\n    <div class="controls">\n        <input type="hidden" name="thumb_attachment_id" value="<%= thumb_attachment_id %>">\n    </div>\n</div>\n<div class="control-group">\n    <label class="control-label" for="music_url">普通音质：</label>\n    <div class="controls">\n        <input type="text" id="music_url" placeholder="填写音乐地址" name="music_url" value="<%= music_url %>">\n    </div>\n</div>\n<div class="control-group">\n    <label class="control-label" for="hq_music_url">高清音质：</label>\n    <div class="controls">\n        <input type="text" id="hq_music_url" placeholder="填写音乐地址" name="hq_music_url" value="<%= hq_music_url %>">\n    </div>\n</div>\n'
}), define("components/music/views/form", ["require", "marionette", "components/validation/validate", "components/music/views/thumb", "core/utils", "text!components/music/templates/form.html"], function(e) {
    var t = e("marionette");
    e("components/validation/validate");
    var i = e("components/music/views/thumb"), n = e("core/utils"), s = e("text!components/music/templates/form.html");
    return t.Layout.extend({template: _.template(s),tagName: "form",className: "form-horizontal",ui: {thumb: ".msg-music-thumb"},modelEvents: {"change thumb_attachment_id": "change"},onRender: function() {
            Backbone.Validation.bind(this), this.thumb.show(new i({model: this.model}))
        },regions: {thumb: ".msg-music-thumb"},change: function() {
            this.ui.thumb
        },saveForm: function() {
            var e = this.$el.serializeArray();
            _(e).each(function(e) {
                ["thumb_attachment_id", "thumb_attachment_url"].indexOf(e.name) < 0 && this.model.set(e.name, e.value)
            }.bind(this))
        },submit: function(e) {
            this.saveForm();
            var t = this.model.validate(), i = this;
            t ? _.isFunction(e.always) && e.always() : this.model.save().always(function() {
                _.isFunction(e.always) && e.always()
            }).success(function(e) {
                n.parse(e, {success: function() {
                        i.model.callback(i.model.attributes), i.model.trigger("closeAll")
                    }})
            })
        }})
}), define("components/music/views/layout", ["require", "marionette", "text!components/music/templates/layout.html", "core/utils", "components/music/views/form"], function(e) {
    var t = e("marionette"), i = e("text!components/music/templates/layout.html"), n = (e("core/utils"), e("components/music/views/form"));
    return t.Layout.extend({template: _.template(i),className: "modal hide fade modal-music",ui: {},regions: {detail: ".js-detail-container"},events: {"click .js-save-data": "save"},save: function(e) {
            var t = $(e.target);
            t.button("loading"), this.detail.currentView.submit({always: function() {
                    t.button("reset")
                }})
        },closeAll: function() {
            this.$el.modal("hide")
        },renderToBody: function(e) {
            this.model = e, this.listenTo(this.model, "closeAll", this.closeAll), this.render().$el.appendTo("body"), this.renderDetail(e), this.showModal()
        },renderDetail: function(e) {
            this.detail.show(new n({model: e}))
        },showModal: function() {
            this.$el.modal("show")
        },initialize: function() {
            this.$el.on("hidden", function() {
                this.$el.off("hidden"), this.$el.data("modal", null), this.close(), this.model = null
            }.bind(this))
        }})
}), define("components/music/models/form", ["require", "backbone"], function(e) {
    var t = e("backbone");
    return t.Model.extend({url: _global.url.www + "/showcase/music/detail.json",defaults: function() {
            return {thumb_attachment_id: "",thumb_attachment_url: "",music_url: "",hq_music_url: "",title: "",description: ""}
        },validation: {thumb_attachment_id: {required: !0,msg: "必须添加一个缩略图"},music_url: [{required: !0,msg: "音乐地址不能为空"}, {pattern: "url",msg: "音乐地址必须是一个有效的网址"}],hq_music_url: [{required: !0,msg: "高清音乐地址不能为空"}, {pattern: "url",msg: "音乐地址必须是一个有效的网址"}],title: {required: !0,msg: "音乐标题不能为空"},description: {required: !0,msg: "音乐描述不能为空"}},parse: function(e) {
            return e.data
        },initialize: function(e) {
            this.callback = e.callback
        }})
}), define("components/music/com", ["require", "components/music/views/layout", "components/music/models/form"], function(e) {
    var t = e("components/music/views/layout"), i = e("components/music/models/form");
    return {initialize: function(e) {
            var n = new t, s = new i(e);
            n.renderToBody(s)
        }}
}), define("text!components/editor_v2/templates/complex.html", [], function() {
    return '<% if (type === \'feature\') { %>\n<!-- 微杂志 -->\n<div class="ng ng-single">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">微杂志</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item view-more">\n        <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n            <span class="pull-left">阅读全文</span>\n            <span class="pull-right">&gt;</span>\n        </a>\n    </div>\n</div>\n\n<% } else if (type === \'category\') { %>\n<!-- 微杂志分类 -->\n<div class="ng ng-multiple-mini">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">微杂志分类</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n</div>\n\n<% } else if (type === \'goods\') { %>\n<!-- 商品 -->\n<div class="ng ng-single">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">商 品</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item view-more">\n        <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n            <span class="pull-left">阅读全文</span>\n            <span class="pull-right">&gt;</span>\n        </a>\n    </div>\n</div>\n\n<% } else if (type === \'tag\') { %>\n<!-- 商品标签 -->\n<div class="ng ng-multiple-mini">\n    <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n    <div class="ng-item">\n        <span class="label label-success">分 类</span>\n        <div class="ng-title">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="<%-content.reply.title %>"><%=content.reply.title %></a>\n        </div>\n    </div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n    <div class="ng-item"></div>\n</div>\n\n<% } else if (type === \'articles\') { %>\n<!-- 图文 -->\n    <% if(content.reply && content.reply.length > 1) { %>\n        <div class="ng ng-multiple-mini">\n            <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n            <div class="ng-item">\n                <span class="label label-success">多条图文</span>\n                <div class="ng-title">\n                    <a href="<%=content.reply[0].link %>" target="_blank" class="new-window" title="<%-content.reply[0].title %>"><%=content.reply[0].title %></a>\n                </div>\n            </div>\n            <% for(var idx = 1, len = content.reply.length; idx < len; idx++) { %>\n            <div class="ng-item"></div>\n            <% } %>\n        </div>\n    <% } else { %>\n        <div class="ng ng-single">\n            <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n            <div class="ng-item">\n                <span class="label label-success">图 文</span>\n                <div class="ng-title">\n                    <a href="<%=content.reply[0].url %>" target="_blank" class="new-window" title="<%-content.reply[0].title %>"><%=content.reply[0].title %></a>\n                </div>\n            </div>\n            <div class="ng-item view-more">\n                <a href="<%=content.reply[0].url %>" target="_blank" class="clearfix new-window">\n                    <span class="pull-left">阅读全文</span>\n                    <span class="pull-right">&gt;</span>\n                </a>\n            </div>\n        </div>\n    <% } %>\n\n<% } else if (type === \'homepage\') { %>\n    <!-- 店铺主页 -->\n    <div class="ng ng-single">\n        <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n        <div class="ng-item">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="店铺主页"><span class="label label-success">店铺主页</span></a>\n        </div>\n        <div class="ng-item view-more">\n            <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n                <span class="pull-left">阅读全文</span>\n                <span class="pull-right">&gt;</span>\n            </a>\n        </div>\n    </div>\n<% } else if (type === \'usercenter\') { %>\n    <div class="ng ng-single">\n        <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n        <div class="ng-item">\n            <a href="<%=content.reply.link %>" target="_blank" class="new-window" title="会员主页"><span class="label label-success">会员主页</span></a>\n        </div>\n        <div class="ng-item view-more">\n            <a href="<%=content.reply.link %>" target="_blank" class="clearfix new-window">\n                <span class="pull-left">阅读全文</span>\n                <span class="pull-right">&gt;</span>\n            </a>\n        </div>\n    </div>\n<% } else if (type === \'image\') { %>\n    <div class="ng ng-single ng-image">\n        <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n        <a title="<%=content.reply.title %>" class="picture" target="_blank" href="<%=content.reply.link %>"><img src="<%=content.reply.link %>!100x100.jpg" alt="" /></a>\n    </div>\n<% } else if (type === \'voice\') { %>\n    <div class="voice-wrapper" data-voice-src="<%= content.reply.link%>!8k.mp3">\n        <span class="voice-player">\n            <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n            <span class="stop">点击播放</span>\n            <span class="second"></span>\n            <i class="play" style="display:none;"></i>\n        </span>\n    </div>\n<% } else if (type === \'music\') { %>\n    <div class="voice-wrapper" data-voice-src="<%= content.reply.link %>">\n        <span class="voice-player">\n            <a href="javascript:;" class="close--circle js-delete-complex">&times;</a>\n            <span class="stop">点击播放</span>\n            <span class="second"></span>\n            <i class="play" style="display:none;"></i>\n        </span>\n    </div>\n<% } %>\n'
}), define("components/editor_v2/views/editor", ["require", "jquery", "backbone", "components/modal/modal", "components/image/app", "components/audio/app", "text!components/editor_v2/templates/editor.html", "components/editor_v2/models/data", "components/editor_v2/collections/picture_list", "components/editor_v2/views/picture", "components/wb_emotion/com", "components/weixin_emotion/com", "components/wb_hyperlink/com", "components/homepage/com", "components/usercenter/com", "components/music/com", "text!components/editor_v2/templates/complex.html", "core/utils", "jwerty"], function(e) {
    var t = e("jquery"), i = e("backbone"), n = e("components/modal/modal"), s = e("components/image/app"), o = e("components/audio/app"), a = e("text!components/editor_v2/templates/editor.html"), r = e("components/editor_v2/models/data"), l = e("components/editor_v2/collections/picture_list"), c = e("components/editor_v2/views/picture"), d = e("components/wb_emotion/com"), u = e("components/weixin_emotion/com"), p = e("components/wb_hyperlink/com"), h = e("components/homepage/com"), m = e("components/usercenter/com"), f = e("components/music/com"), g = e("text!components/editor_v2/templates/complex.html"), v = e("core/utils");
    e("jwerty");
    var b = i.View.extend({template: _.template(a),complexTemplate: _.template(g),events: {"click .js-delete-complex": "deleteComplex","keyup .js-txta": "textUx",click: "hideMiscPops"},initialize: function(e) {
            var t = this;
            t.config = {}, t.setConfig(e), t.hideModules = [], t.listenTo(t, "update:counter", t.updateCounter), t.render(), t.init(), t.renderContent()
        },setConfig: function(e) {
            var t = this;
            _(t.config).extend(e);
            var i = t.model, n = i.get("data");
            t.charLimit = i.get("charLimit"), t.picLimit = i.get("picLimit"), t.hasSign = i.get("hasSign"), t.sign = "", t.hasSign && "text" === n.type && (t.sign = n.content)
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.model.toJSON())), e.$txta = e.$el.find(".js-txta"), e.txta = e.$txta[0], e.counter = e.$el.find(".word-counter i"), e.pictureContainer = e.$el.find(".js-picture-container"), e.complexBackdrop = e.$el.find(".complex-backdrop"), e.complexContent = e.$el.find(".js-complex-content"), e.focusTxta(), e
        },init: function() {
            var e = this;
            e.initModules(), e.initEditorData(), e.initShortcuts()
        },renderContent: function() {
            var e = this, t = e.getContent(), i = t.type;
            "text" === i ? (e.switchToText(), e.setText(t.content), e.renderPicture(t.pics)) : e.renderComplex(t)
        },initEditorData: function() {
            var e = this, t = e.model;
            e.editorData = new r(t.get("data"), {modules: t.get("modules")}), e.listenTo(e.editorData, "change", e.renderContent)
        },initShortcuts: function() {
            var e = this;
            e.$txta.bind("keydown", jwerty.event("ctrl+enter", function() {
                e.trigger("ctrl+enter:txta")
            }))
        },initModules: function() {
            var e, t, i = this, n = i.model.get("modules"), s = i.model.get("supportModules"), o = {};
            _.each(n, function(n) {
                _.has(s, n) && (e = s[n], t = i["init" + e], _.isFunction(t) && t.apply(i), o["click .js-open-" + n] = "toggle" + e)
            }), _(i.events).extend(o)
        },textUx: function() {
            var e = this, t = e.$txta.val();
            e.hasRemain(t), e.updateData({type: "text",content: t}, {silent: !0})
        },hasRemain: function(e) {
            var t = this, i = v.wbLength(e), n = t.charLimit - i;
            return t.trigger("update:counter", n), n >= 0 ? !0 : !1
        },updateData: function(e, t) {
            var i = this, n = i.model.get("data");
            _(n).extend(e);
            var s = {validate: !0};
            _(s).extend(t), i.editorData.set(n, s)
        },initEmotion: function() {
            var e = this;
            e.emotionTrigger = e.$el.find(".js-open-emotion"), e.emotionWrapper = e.$el.find(".emotion-wrapper")
        },initWxEmotion: function() {
            var e = this;
            e.emotionTrigger = e.$el.find(".js-open-wx_emotion"), e.emotionWrapper = e.$el.find(".emotion-wrapper")
        },_initEmotion: function() {
            var e = this;
            e.wbEmotion = new d({target: e.emotionTrigger,el: e.emotionWrapper}), e.hideModules.push(e.wbEmotion), e.listenTo(e.wbEmotion, "selected:emotion", e.insertText)
        },_initWxEmotion: function() {
            var e = this;
            e.wxEmotion = new u({target: e.emotionTrigger,el: e.emotionWrapper}), e.hideModules.push(e.wxEmotion), e.listenTo(e.wxEmotion, "selected:emotion", e.insertText)
        },initHyperlink: function() {
            var e = this;
            e.hyperlinkTrigger = e.$el.find(".js-open-hyperlink"), e.hyperlinkWrapper = e.$el.find(".hyperlink-wrapper"), e.wbHyperlink = new p({target: e.hyperlinkTrigger,el: e.hyperlinkWrapper}), e.hideModules.push(e.wbHyperlink), e.listenTo(e.wbHyperlink, "save:hyperlink", e.insertHyperlink)
        },initArticles: function() {
        },initHomepage: function() {
            var e = this;
            e.homepage = new h, e.usercenter = new m
        },initPicture: function() {
            var e = this, t = e.pictureList = new l([]);
            e.listenTo(t, "add", e.addOnePicture), e.listenTo(t, "reset", e.addAllPicture), e.listenTo(t, "update:picture_list", e.updatePics)
        },initMusic: function() {
            console.log("init music")
        },setText: function(e) {
            var t = this;
            t.$txta.val(e).trigger("keyup")
        },insertText: function(e) {
            var t = this;
            console.log("insertText", e), v.insertText(t.txta, e), t.$txta.trigger("keyup")
        },insertHyperlink: function(e) {
            var i = v.getSelectedText(this.txta);
            i && 0 != i.length && (e = '<a href="' + t.trim(e) + '">' + i + "</a>"), this.insertText(e)
        },updateCounter: function(e) {
            var t = this;
            t.counter.html(e), t.counter.toggleClass("error", 0 > e)
        },toggleHyperlink: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.switchToText(), t.hideMiscPops(), t.wbHyperlink.toggle()
        },toggleEmotion: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.wbEmotion || t._initEmotion(), t.switchToText(), t.hideMiscPops(), t.wbEmotion.toggle()
        },toggleWxEmotion: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.wxEmotion || t._initWxEmotion(), t.switchToText(), t.hideMiscPops(), t.wxEmotion.toggle()
        },togglePicture: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.pictureModal = s.initialize({multiChoose: !1,callback: function(e) {
                    e = e[0], console.warn("------------- choosed picture: ", e), t.switchToText(), t.insertPicture(e)
                }})
        },toggleMusic: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            f.initialize({callback: function(e) {
                    t.insertComplex({type: "music",content: {_id: e.id,reply: {title: e.title,link: e.hq_music_url}}})
                }})
        },toggleArticles: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            t.articleModal = n.initialize({type: "news"}).setChooseItemCallback(function(e) {
                console.warn("------------- choosed article: ", e), e.news && e.news.length > 1 && (e.news = e.news.slice(0, 4)), t.insertComplex({type: "articles",content: {_id: e.id,reply: e.news}})
            }), t.articleModal.modal("show")
        },toggleImage: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            s.initialize({multiChoose: !1,maxSize: 128,callback: function(e) {
                    e = e[0], t.insertComplex({type: "image",content: {_id: e.attachment_id,reply: {title: e.attachment_title,link: v.fullfillImgqn(e.attachment_url)}}})
                }})
        },toggleVoice: function(e) {
            e.preventDefault(), e.stopPropagation();
            var t = this;
            o.initialize({multiChoose: !1,callback: function(e) {
                    e = e[0], t.insertComplex({type: "voice",content: {_id: e.attachment_id,reply: {title: e.attachment_title,link: v.fullfillImgqn(e.attachment_url)}}})
                }})
        },toggleShortlink: function(e) {
            var i = this, n = t(e.target), s = n.data("link-type");
            i.homepage || i.initHomepage(), i["toggle" + s]()
        },toggleFeature: function(e) {
            var i = this, s = !1;
            e && (s = t(e.target).data("complex-mode")), i.featureModal = n.initialize({type: "feature"}).setChooseItemCallback(function(e) {
                console.warn("------------- choosed feature: ", e), s ? i.insertComplex({type: e.type,content: {_id: e.id,reply: {title: e.title,link: e.data_url}}}) : (i.switchToText(), i.insertText(" " + e.data_url + " "))
            }), i.featureModal.modal("show")
        },toggleGoods: function(e) {
            var i = this, s = !1;
            e && (s = t(e.target).data("complex-mode")), i.goodsModal = n.initialize({type: "goods"}).setChooseItemCallback(function(e) {
                console.warn("------------- choosed goods: ", e), s ? i.insertComplex({type: e.type,content: {_id: e.id,reply: {title: e.title,link: e.data_url}}}) : (i.switchToText(), i.insertText(" " + e.data_url + " "))
            }), i.goodsModal.modal("show")
        },toggleHomepage: function(e) {
            var i = this, n = !1;
            return e && (n = t(e.target).data("complex-mode")), "homepage" === i.model.get("data").type ? !1 : void i.homepage.get(function(e) {
                n ? i.insertComplex({type: e.type,content: {_id: e.id,reply: {title: e.title,link: e.data_url}}}) : (i.switchToText(), i.insertText(" " + e.data_url + " "))
            })
        },toggleUsercenter: function() {
            var e = this, i = !1;
            return event && (i = t(event.target).data("complex-mode")), "usercenter" === e.model.get("data").type ? !1 : void e.usercenter.get(function(t) {
                i ? e.insertComplex({type: t.type,content: {_id: t.id,reply: {title: t.title,link: t.data_url}}}) : (e.switchToText(), e.insertText(" " + t.data_url + " "))
            })
        },insertComplex: function(e) {
            var t = this;
            t.renderComplex(e), t.updateData(e), t.$txta.focus()
        },renderComplex: function(e) {
            var t = this;
            t.showBackdrop();
            var i = t.complexTemplate(e);
            t.complexContent.empty(), t.complexContent.append(i)
        },insertPicture: function(e) {
            var t = this;
            t.pictureList && (t.picLimit > 1 ? t.pictureList.checkMaxsize() && t.pictureList.add(e) : t.pictureList.reset(e), t.pictureList.trigger("update:picture_list"))
        },addOnePicture: function(e) {
            var t = this, i = new c({model: e});
            t.pictureContainer.append(i.render().el)
        },addAllPicture: function() {
            var e = this;
            e.pictureContainer.empty(), e.pictureList.each(e.addOnePicture, e)
        },updatePics: function() {
            var e = this, t = e.pictureList.getPics();
            e.updateData({pics: t}, {silent: !0})
        },renderPicture: function(e) {
            for (var t = this, i = 0, n = e.length; n > i; i++)
                e[i] = {attachment_url: e[i]};
            t.insertPicture(e)
        },existedComplex: function() {
            var e = this, t = e.editorData.toJSON();
            "text" !== t.type && e.renderComplex(t)
        },showBackdrop: function() {
            this.complexBackdrop.show()
        },hideBackdrop: function() {
            this.complexBackdrop.hide()
        },focusTxta: function() {
            var e = this;
            e.$txta.focus()
        },clearTxta: function() {
            var e = this;
            e.$txta.val(e.hasSign ? e.sign : ""), e.resetData()
        },clearPicture: function() {
            var e = this;
            e.pictureList && e.pictureList.reset([])
        },resetData: function() {
            var e = this;
            e.updateData({type: "text",content: e.sign || "",pics: []})
        },deleteComplex: function() {
            var e = this;
            e.complexContent.empty(), e.hideBackdrop(), e.textUx(), e.focusTxta()
        },switchToText: function() {
            var e = this;
            e.deleteComplex()
        },hideMiscPops: function() {
            var e = this;
            _(e.hideModules).each(function(e) {
                e.hide()
            })
        },clear: function() {
            var e = this;
            e.deleteComplex(), e.clearTxta(), e.clearPicture()
        },getContent: function() {
            var e = this, t = e.editorData.toJSON();
            return "text" === t.type && v.wbLength(t.content) > e.charLimit && (e.flashAnimate(), e.focusTxta()), t
        },flashAnimate: function() {
            var e = this;
            e.$txta.addClass("animated flashError"), window.clearTimeout(e.flashTimer), e.flashTimer = window.setTimeout(function() {
                e.$txta.removeClass("flashError")
            }, 2e3)
        }});
    return b
}), define("components/editor_v2/com", ["require", "backbone", "components/editor_v2/models/editor", "components/editor_v2/views/editor"], function(e) {
    var t = e("backbone"), i = e("components/editor_v2/models/editor"), n = e("components/editor_v2/views/editor"), s = {enter: function(e) {
            var t = {};
            return t = "text" === e.type ? e : "image" == e.type || "voice" == e.type ? {type: e.type,content: {_id: e.media_attachment_id,reply: {link: e.attachment_url_cdn,title: e.title}}} : "music" == e.type ? {type: e.type,content: {_id: e.data_id,reply: {link: e.hq_music_url,title: e.title}}} : "news" == e.data_type ? {type: "articles",content: {_id: e.data_id,reply: [{url: e.url,title: e.title}]}} : {type: e.data_type,content: {_id: e.data_id,reply: {link: e.url,title: e.title}}}
        },out: function(e) {
            var t;
            return t = "text" === e.type ? e : "image" === e.type || "voice" === e.type ? {type: e.type,media_attachment_id: e.content._id,attachment_url_cdn: e.content.reply.link,title: e.content.reply.title} : "music" === e.type ? {type: e.type,data_id: e.content._id,data_type: "music",hq_music_url: e.content.reply.link,title: e.content.reply.title} : "articles" === e.type ? {type: "news",data_type: "news",data_id: e.content._id,url: e.content.reply[0].url,title: e.content.reply[0].title} : {type: "news",data_type: e.type,data_id: e.content._id,url: e.content.reply.link,title: e.content.reply.title}
        }}, o = t.View.extend({initialize: function(e) {
            var t = this, o = t.config = e.config;
            o.data = s.enter(o.data);
            var a = t.editor = new i(o);
            t.editorView = new n({el: t.el,model: a}), t.listenTo(t.editorView, "ctrl+enter:txta", t.shortcuts)
        },render: function() {
            var e = this;
            return e
        },focus: function() {
            var e = this;
            e.editorView.focusTxta()
        },shortcuts: function() {
            var e = this;
            e.trigger("ctrl+enter:txta")
        },updateContent: function(e) {
            e = s.enter(e), this.editorView.updateData(e)
        },getContent: function() {
            var e = this.editorView.getContent();
            return e = s.out(e)
        },insertText: function(e) {
            var t = this;
            t.editorView.insertText(e)
        },clear: function() {
            var e = this;
            e.editorView.clear()
        }});
    return o
}), define("components/pop/rule_reply", ["require", "components/pop/base", "text!components/pop/templates/reply.html", "components/editor_v2/com", "core/utils"], function(e) {
    var t = e("components/pop/base"), i = e("text!components/pop/templates/reply.html"), n = e("components/editor_v2/com"), s = e("core/utils");
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-close": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            var i = this;
            t.prototype.initialize.call(i, e), i.data = e.data
        },render: function() {
            var e = this, t = e.data, i = t.minus;
            e.$el.html(e.template(t)), e.editorEle = e.$el.find(".wb-sender__input");
            var s = ["wx_emotion", "hyperlink", "image", "voice", "music", "articles", "goods", "feature", "homepage", "usercenter"];
            return ["/v2/weixin/autoreply/normal", "/v2/weixin/autoreply/default"].indexOf(window.location.pathname) < 0 && (_global.team_status.weixin_server && _global.team_status.weixin_oldsub || (s = _.without(s, "music"))), i && (s = s.filter(function(e) {
                return !i.some(function(t) {
                    return t == e
                })
            })), e.editor = new n({config: {data: t,charLimit: 300,picLimit: 1,modules: s,thumb: ["goods", "feature", "homepage", "usercenter"]},el: e.editorEle}), e
        },positioning: function() {
            var e = this, t = e.options.className;
            e.el.className = t, e.$el.show(), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center"} : {of: e.target,my: "left center",at: "right center"}), e.editor.focus()
        },setData: function(e) {
            var t = this;
            t.editor.updateContent(e)
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className;
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },triggerCallback: function() {
            var e = this, t = e.editor.getContent();
            this.validateData(t).done(function() {
                e.callback.call(e, t)
            }).fail(function(e) {
                s.errorNotify(e)
            }).always(function() {
                e.hide(), e.editor.clear()
            })
        },validateData: function(e) {
            var t = new $.Deferred;
            if ("voice" == e.type) {
                if (void 0 != $.getAudioDuration)
                    return $.getAudioDuration(e.attachment_url_cdn).then(function(e) {
                        return Math.floor(e) > 61 ? t.reject("音频不能超过60秒") : void 0
                    });
                t.resolve(), console.error("没有引入getAudioDuration函数, 跳过验证")
            } else
                t.resolve();
            return t
        }})
}), define("text!components/pop/templates/rule_method.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-thin">\n    <div class="popover-content">\n        <div class="controls">\n            <label class="radio inline">\n                <input value="1" type="radio" name="send_type" \n                    <% if(sendAll == 1) {%>\n                        checked\n                    <% } %>\n                > 全部发送 \n            </label>\n            <label class="radio inline">\n                <input value="0" type="radio" name="send_type"\n                    <% if(sendAll == 0) {%>\n                        checked\n                    <% } %>\n                > 随机发送一条 \n            </label>\n        </div>\n        \n        <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n        <button type="button" class="btn btn-cancle js-btn-cancel">取消</button>\n    \n    </div>\n</div>'
}), define("components/pop/rule_method", ["backbone", "components/pop/base", "text!components/pop/templates/rule_method.html", "core/utils"], function(e, t, i, n) {
    return t.extend({template: _.template(i),className: function() {
            return this.options.className
        },events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            t.prototype.initialize.call(this, e), this.data = e.data
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e.el.className = e.options.className, e.txt = e.$el.find(".js-txt"), e
        },positioning: function() {
            var e = this;
            e.$el.show().position({of: e.target,my: "center top",at: "center bottom",collision: "none"}), e.txt.focus()
        },setData: function(e) {
            this.data = _.extend(this.data, e), this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data, o = e.className;
            this.options.className = o, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },enterSave: function(e) {
            return e.which === n.keyCode.ENTER ? (this.triggerCallback(), !1) : void 0
        },triggerCallback: function() {
            var e = this, t = {};
            e.$("input[type=radio]").each(function() {
                this.checked && (t.sendAll = this.value)
            }), e.callback(t), e.hide()
        }})
}), define("text!components/pop/templates/change.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-change">\n    <div class="popover-content text-center">\n        <form class="form-inline">\n            <label class="radio">\n                <input type="radio" name="discount" value="1" checked/>参与\n            </label>\n            <label class="radio">\n                <input type="radio" name="discount" value="0"/>不参与\n            </label>\n            <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>\n            <button type="reset" class="btn js-btn-cancel">取消</button>\n        </form>\n    </div>\n</div>\n'
}), define("components/pop/change", ["backbone", "components/pop/base", "text!components/pop/templates/change.html", "core/utils"], function(e, t, i) {
    return t.extend({template: _.template(i),className: "popover right popover-change",events: {"click .js-btn-cancel": "hide","click .js-btn-confirm": "triggerCallback"},initialize: function(e) {
            t.prototype.initialize.call(this, e)
        },render: function() {
            return this.$el.html(this.template({})), this
        },setData: function(e) {
            this.data = e, this.render()
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.data;
            this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.setData(s), this.positioning(), this.show()
        },triggerCallback: function() {
            this.callback({join: $.trim(this.$('[name="discount"]:checked').val())}), this.hide()
        }})
}), define("text!components/pop/templates/apps_qrcode.html", [], function() {
    return '<div class="arrow"></div>\n<div class="popover-inner popover-qrcode">\n    <div class="popover-content">\n        <div class="popover-qrcode-header">\n            <button type="button" class="close js-close">×</button>\n            <div class="popover-qrcode-title">活动二维码</div>    \n        </div>\n        \n        <div class="qrcode-wrap">\n            <img class="qrcode loading" src="http://wap.koudaitong.com/v2/common/url/create?type=<%= type %>&mode=apps&alias=<%= alias %>">\n            <p class="scan-info">扫一扫立即参与活动</p>\n        </div>\n\n        <div class="popover-qrcode-footer">\n            <a href="http://wap.koudaitong.com/v2/common/url/create?type=<%= type %>&mode=apps&alias=<%= alias %>" download="<%= name %>">下载二维码</a>\n            <a href="/v2/weixin/autoreply/scan" class="pull-right">设置带参数二维码</a>\n        </div>\n    </div>\n</div>'
}), define("components/pop/apps_qrcode", ["require", "backbone", "components/pop/base", "text!components/pop/templates/apps_qrcode.html", "core/utils", "underscore"], function(e) {
    var t = (e("backbone"), e("components/pop/base")), i = e("text!components/pop/templates/apps_qrcode.html"), n = e("core/utils"), s = e("underscore");
    return t.extend({template: s.template(i),className: function() {
            return this.options.className
        },initialize: function(e) {
            this.data = e.data, t.prototype.initialize.call(this, e)
        },events: {"click .js-close": "hide"},isShow: function() {
            var e = this.$el.css("display");
            return "none" === e ? !1 : !0
        },positioning: function() {
            var e = this, t = e.options.className;
            e.$el.show(), n.focus(e.$(".js-btn-confirm")), e.$el.position(-1 !== t.indexOf("left") ? {of: e.target,my: "right center",at: "left center",collision: "none"} : -1 !== t.indexOf("bottom") ? {of: e.target,my: "center top",at: "center bottom",collision: "none"} : {of: e.target,my: "left center",at: "right center",collision: "none"}), e.el.className = e.options.className
        },render: function() {
            var e = this;
            return e.$el.html(e.template(e.data)), e.el.className = e.options.className, e
        },reset: function(e) {
            var t = e.callback, i = e.target, n = e.trigger, s = e.className || "popover left";
            this.options.className = s, this.setCallback(t), this.setTarget(i), this.setTrigger(n), this.positioning(), this.show()
        }})
}), define("components/pop/pop", ["require", "backbone", "components/pop/link", "components/pop/confirm", "components/pop/delete", "components/pop/delete_multiple", "components/pop/rule", "components/pop/keyword", "components/pop/reply", "components/pop/normal_reply", "components/pop/url", "components/pop/text", "components/pop/rename", "components/pop/change_category", "components/pop/help_notes", "components/pop/memo", "components/pop/chosen", "components/pop/template", "components/pop/category", "components/pop/msgcat", "components/pop/tag", "components/pop/form_actions_confirm", "components/pop/send_goods", "components/pop/note", "components/pop/cube", "components/pop/timer", "components/pop/fans_tag", "components/pop/fans_level", "components/pop/qrcode", "components/pop/rule_reply", "components/pop/rule_method", "components/pop/change", "components/pop/apps_qrcode"], function(e) {
    var t = (e("backbone"), e("components/pop/link")), i = e("components/pop/confirm"), n = e("components/pop/delete"), s = e("components/pop/delete_multiple"), o = e("components/pop/rule"), a = e("components/pop/keyword"), r = e("components/pop/reply"), l = e("components/pop/normal_reply"), c = e("components/pop/url"), d = e("components/pop/text"), u = e("components/pop/rename"), p = e("components/pop/change_category"), h = e("components/pop/help_notes"), m = e("components/pop/memo"), f = e("components/pop/chosen"), g = e("components/pop/template"), v = e("components/pop/category"), _ = e("components/pop/msgcat"), b = e("components/pop/tag"), y = e("components/pop/form_actions_confirm"), w = e("components/pop/send_goods"), k = e("components/pop/note"), x = e("components/pop/cube"), C = e("components/pop/timer"), j = e("components/pop/fans_tag"), T = e("components/pop/fans_level"), S = e("components/pop/qrcode"), D = e("components/pop/rule_reply"), M = e("components/pop/rule_method"), N = e("components/pop/change"), E = e("components/pop/apps_qrcode"), I = {};
    return {initialize: function(e) {
            var z = e.target = $(e.target), O = e.className, A = e.type, R = e.callback, P = e.trigger = $(e.trigger || z), q = e.data, V = e.content || "", L = e.appendTarget || "body";
            if (I[A])
                return I[A].reset(e), I[A];
            var B;
            switch (A) {
                case "link":
                    B = new t({callback: R,target: z,trigger: P,content: V});
                    break;
                case "text":
                    B = new d({callback: R,target: z,trigger: P});
                    break;
                case "delete":
                    B = new n({callback: R,target: z,className: O || "popover left",trigger: P});
                    break;
                case "confirm":
                    B = new i({callback: R,target: z,className: O || "popover right",trigger: P,data: q});
                    break;
                case "note":
                    B = new k({callback: R,target: z,className: O || "popover right",trigger: P,data: q});
                    break;
                case "timer":
                    B = new C({callback: R,target: z,className: O || "popover top",trigger: P,data: q});
                    break;
                case "delete_multiple":
                    B = new s({callback: R,target: z,trigger: P});
                    break;
                case "url":
                    B = new c({callback: R,target: z,trigger: P,data: q});
                    break;
                case "rule":
                    B = new o({callback: R,target: z,trigger: P,className: O,data: q});
                    break;
                case "keyword":
                    B = new a({callback: R,target: z,trigger: P,data: q});
                    break;
                case "reply":
                    B = new r({callback: R,target: z,trigger: P,className: O,notAutoHide: !0,data: q});
                    break;
                case "normal_reply":
                    B = new l({callback: R,target: z,trigger: P,className: O,data: q});
                    break;
                case "rename":
                    B = new u({callback: R,target: z,className: O || "popover left",trigger: P,data: q});
                    break;
                case "chosen":
                    B = new f({callback: R,target: z,trigger: P,notAutoHide: !0,data: q});
                    break;
                case "change_category":
                    B = new p({callback: R,target: z,trigger: P});
                    break;
                case "change":
                    B = new N({callback: R,target: z,className: O || "popover top",trigger: P});
                    break;
                case "help_notes":
                    B = new h({callback: R,target: z,className: O || "popover bottom",trigger: P});
                    break;
                case "memo":
                    B = new m({callback: R,target: z,trigger: P,className: O || "popover left",data: q});
                    break;
                case "template":
                    B = new g({callback: R,target: z,trigger: P,className: O});
                    break;
                case "category":
                    B = new v({callback: R,target: z,trigger: P,data: q});
                    break;
                case "msgcat":
                    B = new _({callback: R,target: z,trigger: P,data: q});
                    break;
                case "tag":
                    B = new b({callback: R,target: z,trigger: P,data: q});
                    break;
                case "form_actions_confirm":
                    B = new y({callback: R,target: z,appendTarget: L,className: O || "popover top",trigger: P,data: q});
                    break;
                case "send_goods":
                    B = new w({callback: R,target: z,trigger: P,className: "popover left"});
                    break;
                case "cube":
                    B = new x({callback: R,target: z,trigger: P,className: "popover bottom",data: q});
                    break;
                case "fans_tag":
                    B = new j({callback: R,target: z,trigger: P,className: "popover bottom",data: q});
                    break;
                case "fans_level":
                    B = new T({callback: R,target: z,trigger: P,className: "popover bottom",data: q});
                    break;
                case "qrcode":
                    B = new S({callback: R,target: z,trigger: P,className: "popover bottom",data: q});
                    break;
                case "rule_reply":
                    B = new D({callback: R,target: z,trigger: P,className: O,notAutoHide: !0,data: q});
                    break;
                case "rule_method":
                    B = new M({callback: R,target: z,trigger: P,className: O,data: q});
                    break;
                case "apps_qrcode":
                    B = new E({target: z,trigger: P,className: O || "popover left",data: q})
            }
            return B.render().$el.appendTo(L), B.positioning(), "help_notes" !== A && "confirm" !== A && (I[A] = B), B
        }}
}), define("components/help_notes/com", ["require", "jquery", "components/pop/pop"], function(e) {
    var t = e("jquery"), i = e("components/pop/pop"), n = null, s = 1, o = 1, a = 200, r = "", l = 2, c = ".js-intro-popover", d = ".js-intro-popover .popover-inner", u = function(e) {
        var s = "js-intro-popover popover popover-help-notes " + r;
        2 !== l && (s = "js-intro-popover popover popover-intro " + r), o = 0, n && clearTimeout(n), n = setTimeout(function() {
            t(c).remove(), i.initialize({type: "help_notes",target: t(e.target),className: s})
        }, a)
    }, p = function() {
        n && clearTimeout(n), n = setTimeout(function() {
            o && s && t(c).hide()
        }, a)
    };
    t("body").on("mouseover", ".js-help-notes, .js-help-notes-btn-copy", function(e) {
        var i = t(this);
        r = i.data("class") || "bottom", l = i.data("ui-version") || 2, u(e)
    }).on("mouseout", ".js-help-notes, #global-zeroclipboard-html-bridge", function() {
        o = 1, p()
    }), t("body").on("mouseover", d, function() {
        s = 0
    }).on("mouseout", d, function() {
        s = 1, p()
    })
}), define("text!components/notify/templates/notify.html", [], function() {
    return '<div class="alert">\n    <button type="button" class="js-close close" data-dismiss="alert">&times;</button>\n    <div class="notify-cont">\n        <% var wb_total = wb_at_number + wb_tome_number + wb_private_number; %>\n        <% if (wx_number > 0) { %>\n        微信：你有 <a href="<%=wx_url %>"><%=wx_number %></a> 条未读信息<% if (wb_total > 0) { %>；&nbsp;&nbsp;&nbsp;&nbsp;<% } else { %>。<% } %>\n        <% } %>\n    </div>\n    <div class="notify-setting">\n        <% if (audio) { %>\n        <a href="javascript:void(0);" class="js-toggle-audio audio-on">\n            已开启\n        </a>\n        <% } else { %>\n        <a href="javascript:void(0);" class="js-toggle-audio audio-off">\n            已关闭\n        </a>\n        <% } %>\n    </div>\n</div>\n'
}), define("components/notify/notify", ["backbone", "text!components/notify/templates/notify.html"], function(e, t) {
    var i, n = e.View.extend({el: ".js-notify",template: _.template(t),events: {"click .js-close": "close","click .js-toggle-audio": "toggleAudio"},initialize: function() {
            this.status = {wx_url: "",wx_number: 0,wb_at_url: "",wb_at_number: 0,wb_private_url: "",wb_private_number: 0,wb_tome_url: "",wb_tome_number: 0}, this.statekey = "mp-" + window._global.mp_id + "-adm-" + window._global.user_id + "-msg"
        },render: function() {
            this.$el.html(this.template(this.status))
        },close: function() {
            this.$el.removeClass("fadeInUpBig"), window.NC.trigger("notify:close")
        },show: function() {
            this.$el.removeClass("hide"), this.$el.addClass("fadeInUpBig")
        },hide: function() {
            this.$el.removeClass("fadeInUpBig"), this.$el.addClass("hide")
        },setStatus: function(e) {
            _.isObject(e) && (this.status = e), this.canPlayAudio(), this.render(), this.show()
        },clear: function() {
            this.remove()
        },canPlayAudio: function() {
            var e;
            e = window.localStorage ? "1" == window.localStorage.getItem(this.statekey + "-playAudio") ? !0 : !1 : !0, this.status.audio = e
        },toggleAudio: function() {
            window.localStorage && (this.status.audio ? window.localStorage.setItem(this.statekey + "-playAudio", 0) : window.localStorage.setItem(this.statekey + "-playAudio", 1)), this.setStatus()
        }});
    return {initialize: function(e) {
            return _.isUndefined(i) && (i = new n), _.isObject(e) && i.setStatus(e), i
        }}
}), define("components/message/message_bot_lite", ["jquery", "components/notify/notify", "core/utils", "core/event"], function(e, t) {
    var i = e({}), n = {pollUrl: _global.url.www + "/common/message/unread.json",seeUrl: _global.url.v1 + "/messages/my?type=unread",wbAtUrl: _global.url.www + "/sinaweibo/timeline/mentions",wbTomeUrl: _global.url.www + "/sinaweibo/timeline/tome",wbPrivateUrl: _global.url.www + "/sinaweibo/message#type=unread",kdtID: _global.kdt_id,userID: _global.user_id,audioSrc: _global.url.v1_static + "/assets/media/notify.wav",weixin: e(".js-weixin-notify .notify-counter"),weibo: e(".js-weibo-notify .notify-counter"),sidebarContainer: {privateMsg: e(".js-sidebar-private-counter"),tome: e(".js-sidebar-tome-counter"),at: e(".js-sidebar-at-counter")},interval: 6e4};
    return i._init = function(t) {
        i.options = e.extend({}, n, t), (1 == _global.team_status.weixin_server || 1 == _global.team_status.weixin_oldsub) && (i.intervaler = null, i.titleIntervaler = null, i.stateKey = "mp-" + i.options.kdtID + "-adm-" + i.options.userID + "-msg", i.isSupportAudio = !!document.createElement("audio").canPlayType, i.isSupportAudio && (i.notificationAudio = new Audio), i.notifying = !1, i.titleNotify = e("title"), i.originalTitle = i.titleNotify.html(), i.initLocalStorage(), i.notifyState = i.getNotifyState(), i.notifyHandler(), i.fetchMessages())
    }, i.init = function(e) {
        return window._global.js.message_report ? void window.NC.once("finish", function() {
            i._init(e)
        }) : !1
    }, i.initLocalStorage = function() {
        window.localStorage && (i.storage = window.localStorage)
    }, i.getNotifyState = function() {
        return i.storage ? i.storage.getItem(i.stateKey) : void 0
    }, i.setNotifyState = function(e) {
        i.storage && setTimeout(function() {
            i.storage.setItem(i.stateKey, e)
        }, 500)
    }, i.showMessageNumber = function(e, t) {
        if (0 >= t)
            return void e.css("visibility", "hidden");
        var i;
        i = 99 >= t ? t : "99+", e.html(i).css("visibility", "visible")
    }, i.toggleMsg = function(e) {
        _.each(i.options.sidebarContainer, function(t, n) {
            t.length > 0 && i.showMessageNumber(t, e[n])
        })
    }, i.fetchMessages = function() {
        function n() {
            e.ajax({cache: !1,url: i.options.pollUrl,dataType: "html",timeout: i.options.interval}).done(function(s) {
                function o() {
                    i.titleNotify.html(i.notifying ? i.originalTitle : i.titleMsg), i.notifying = !i.notifying, i.titleIntervaler = window.setTimeout(o, 1e3)
                }
                var a;
                try {
                    a = e.parseJSON(s)
                } catch (r) {
                    return
                }
                if (0 !== +a.code)
                    return 10302 === +a.code && (window.location.href = "/v2/account/user/login"), void window.clearTimeout(i.intervaler);
                e(document.documentElement).trigger("poll_complete:message", a.data);
                var l = a.data.weixin, c = a.data.weibo, d = Number(l.total_items), u = Number(c.private_msg), p = Number(c.tome), h = Number(c.mentions), m = u + p + h;
                if (d + m > 0) {
                    var f = (l.latest_message_id, l.total_items);
                    i.getNotifyState() !== s && (t.initialize({wx_number: f,wx_url: i.options.seeUrl,wb_tome_number: c.tome,wb_tome_url: i.options.wbTomeUrl,wb_private_number: c.private_msg,wb_private_url: i.options.wbPrivateUrl,wb_at_number: c.mentions,wb_at_url: i.options.wbAtUrl}), i.titleMsg = "您有未读信息", i.titleNotify.html(i.titleMsg), i.isSupportAudio && "1" === window.localStorage.getItem(i.stateKey + "-playAudio") && i.notifyState !== s && i.playNotificationSound(), i.titleIntervaler ? (i.titleMsg = "您有未读信息", i.notifying = !0) : o()), d > 0 && i.options.weixin.css("visibility", "visible"), m > 0 && i.options.weibo.css("visibility", "visible")
                } else
                    0 >= d && i.options.weixin.css("visibility", "hidden"), 0 >= m && i.options.weibo.css("visibility", "hidden"), t.initialize().hide(), i.titleNotify.html(i.originalTitle), window.clearTimeout(i.titleIntervaler), i.titleIntervaler = null;
                i.setNotifyState(s), i.notifyState = s, i.toggleMsg({tome: p,privateMsg: u,at: h}), i.intervaler = window.setTimeout(n, i.options.interval)
            })
        }
        i.intervaler || n()
    }, i.playNotificationSound = function() {
        try {
            -1 == navigator.platform.toLowerCase().indexOf("linux") && i.notificationAudio.src && -1 != i.notificationAudio.src.indexOf(i.options.audioSrc) || (i.notificationAudio.src = i.options.audioSrc), i.notificationAudio.play()
        } catch (e) {
            console.error(e)
        }
    }, i.stopNotify = function() {
        i.notifying = !1, i.titleNotify.html(i.originalTitle), window.clearTimeout(i.titleIntervaler), i.titleIntervaler = null, i.setNotifyState(i.notifyState)
    }, i.notifyHandler = function() {
        t.initialize();
        window.NC.on("notify:close", i.stopNotify)
    }, i
}), define("core/reqres", ["require", "backbone", "backbone.wreqr"], function(e) {
    {
        var t = e("backbone");
        e("backbone.wreqr")
    }
    return window.RS ? window.RS : (window.RS = new t.Wreqr.RequestResponse, window.RS)
}), define("commons/utils", ["require", "underscore", "core/utils"], function(e) {
    var t = e("underscore"), i = e("core/utils"), n = {};
    return t.extend(n, {fetchSaleProperty: function(e, t) {
            var n = window._global.url.www + "/showcase/paipai/saleproperty.json";
            $.ajax({url: n,type: "GET",dataType: "json",cache: !1,data: e,success: function(e) {
                    0 === e.code ? t(e) : i.errorNotify(e.msg)
                },error: function() {
                    i.errorNotify("加载销售属性失败。")
                },complete: function() {
                }})
        },fnCallCount: function(e) {
            window[e] = "undefined" == typeof window[e] ? 0 : window[e] + 1, console.info("fnName: ", window[e])
        },removeSkuKeyValue: function(e, t) {
            for (var i = 3; i > t; i--) {
                var n = "sku_name_" + i, s = n + "_value";
                e.unset(n, {silent: !0}), e.unset(s, {silent: !0})
            }
        },needConfirm: function(e, t, i) {
            var n = window.confirm(e);
            n ? t && "function" == typeof t && t.apply() : i && "function" == typeof i && i.apply()
        },button: function(e, t, i) {
            return e ? (e = $(e), void e.text(t).prop("disabled", i)) : !1
        },decimalFix: function(e, i) {
            if (!e)
                return "";
            var n = Number(e);
            if (isNaN(n))
                return n;
            if (t.isUndefined(i))
                return n;
            var s = n.toFixed(i);
            return s
        },originValueFix: function(e) {
            var i, n, s, o = e.match(/([^\d\uffe5]*)[\uffe5]?(\d+\.?\d*)(.*)$/), a = e;
            return o && o.length > 2 && (n = o[1], i = +o[2], s = o[3], t.isNumber(i) && (i = i.toFixed(2, 10)), s || (s = ""), a = n + "￥" + i + s), a
        },buyUrlValueFix: function(e) {
            var t = i.urlCheck(e);
            return t
        },booleanConverter: function(e, t) {
            return t = +t
        },numberValueFix: function(e) {
            var t = Number(e), i = isNaN(t) ? e : t.toFixed(2);
            return i
        },showLog: function(e) {
            var t = e.toJSON();
            console.info(JSON.stringify(t))
        },getIdxOfCollection: function(e) {
            var t = e.collection, i = t.indexOf(e);
            return i
        }}), n
}), define("models/goods", ["require", "underscore", "backbone", "core/utils"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = (e("core/utils"), i.Model.extend({defaults: {type: "config",class_type: "0",class_1: "",class_2: "",goods_class: [],cid: "",shop_method: "1",attrs: null,tag: [],shipment: "0",stock: [],hide_stock: "0",total_stock: 0,goods_no: "",title: "",picture: [],picture_height: "320",price: "",origin: "淘价：",buy_url: "",postage: "",quota: "0",province_id: "0",province: "",city_id: "0",city: "",messages: [],sold_time: "0",start_sold_time: "",take_down_time: "",join_level_discount: "1",invoice: "0",warranty: "0",content: ""},url: function() {
            var e = this, t = e.get("id"), i = window._global.url.www;
            return i += "goods" === window._global.goods_type ? "/showcase/goods/goods.json" : "/showcase/material/detail.json", i = i + "?id=" + t
        },initialize: function() {
        },calculateImage: function() {
            var e = this, i = 0;
            t.each(e.get("picture"), function(e) {
                var n = Number(e.width) / 320;
                n = 1 > n ? 1 : n;
                var s = t.isUndefined(e.height) || 0 === Number(e.height) ? 320 : Number(e.height) / n;
                i = s > i ? s : i
            }), i = i > 420 ? 420 : i, 0 === i && (i = 320), e.set("picture_height", i)
        },validation: {class_1: function(e, t, i) {
                return "0" == i.class_type && "" === e ? "必须选择一个商品类目。" : void 0
            },class_2: function(e, t, i) {
                return "0" == i.class_type && "" === e ? "必须选择一个商品类目。" : void 0
            },goods_class: function(e, i, n) {
                return "1" == n.class_type && !e && t.isEmpty(e) ? "必须选择一个商品类目。" : void 0
            },title: {rangeLength: [1, 100],msg: "商品名长度不能少于一个字或者多于100个字"},picture: function(e) {
                return t.isArray(e) && e.length <= 0 ? "商品图至少有一张。" : t.isArray(e) && e.length > 15 ? "商品图片最多支持 15 张。" : void 0
            },postage: function(e) {
                return i.Validation.patterns.number.test(+e) ? void 0 : "邮费必须为数字。"
            },price: function(e, n, s) {
                if (t.isEmpty(s.stock)) {
                    var o = +e;
                    if (!e)
                        return "商品价格不能为空";
                    if (!i.Validation.patterns.number.test(o))
                        return "商品价格必须为数字。";
                    if (0 >= o)
                        return "商品价格必须大于0元。"
                }
            },buy_url: function(e, t, n) {
                if ("0" == n.shop_method) {
                    if (!e)
                        return "购买地址不能为空。";
                    if (!i.Validation.patterns.url.test(e))
                        return "购买地址必须是一个合法网址。"
                }
            },total_stock: function(e, t, n) {
                if ("0" != n.shop_method) {
                    if (!i.Validation.patterns.number.test(+e))
                        return "总库存必须是数字";
                    if (0 > +e)
                        return "总库存不能小于 0"
                }
            },quota: function(e) {
                return i.Validation.patterns.number.test(+e) ? void 0 : "限购数量必须是数字"
            },sold_time: function(e, t, i) {
                return 1 === +e && "" === i.start_sold_time ? "请选择一个开售时间" : void 0
            },take_down_time: function(e, t, i) {
                var n = +new Date, s = +new Date(i.start_sold_time) || 0, o = +new Date(e);
                return "" !== e && 6e4 > o - n ? "下架时间必须大于当前时间至少一分钟" : "1" == i.sold_time && "" !== e && 6e4 > o - s ? "下架时间必须大于定时开售时间至少一分钟" : void 0
            },messages: function(e, t, i) {
                if ("1" == i.shop_method) {
                    var n = e.length;
                    if (n > 20)
                        return "留言最多 20 个。";
                    for (var s = n - 1; s >= 0; s--) {
                        if ("" === $.trim(e[s].name))
                            return "留言名称不能为空";
                        if (e[s].name.length > 5)
                            return "留言名称不能多于5个字。"
                    }
                }
            },tag: function(e) {
                if (t.isArray(e)) {
                    for (var i = e.length - 1; i >= 0; i--)
                        e[i] = +e[i];
                    var n = t.difference(e, window._global.defaultTag);
                    return n.length > 10 ? "商品标签最多 10 个。" : void 0
                }
            }}}));
    return n
}), define("models/app", ["require", "underscore", "backbone", "core/utils"], function(e) {
    var t = (e("underscore"), e("backbone")), i = e("core/utils"), n = t.Model.extend({defaults: {data: [],is_display: 0},url: function() {
            var e = this, t = e.get("id"), i = window._global.url.www;
            return i += "goods" === window._global.goods_type ? "/showcase/goods/goods.json" : "/showcase/material/detail.json", i = i + "?id=" + t
        },initialize: function() {
        },parse: function(e) {
            return 0 === e.code ? e.data : (i.errorNotify(e.msg), !1)
        }});
    return n
}), define("models/class_info", ["require", "underscore", "backbone"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = i.Model.extend({defaults: {class_type: "0",class_1: "",class_2: "",goods_class: [],cid: "",shop_method: "1"},initialize: function() {
            var e = this, i = e.get("cid"), n = e.get("goods_class");
            if (!n)
                return !1;
            var s = t(n).last();
            !i && s && e.set("cid", s.cid, {silent: !0})
        },validation: {class_1: function(e, t, i) {
                return "0" == i.class_type && "" === e ? "必须选择一个商品品类。" : void 0
            },class_2: function(e, t, i) {
                return "0" == i.class_type && "" === e ? "必须选择一个商品品类。" : void 0
            },goods_class: function(e, i, n) {
                return "1" == n.class_type && t.isEmpty(e) ? "必须选择一个综合类目。" : void 0
            }}});
    return n
}), define("text!templates/class_info.html", [], function() {
    return '<div class="js-class-block control-group class-block">\n    <label class="<% if (class_type == \'1\') { %>js-blur-label<% } %> radio inline">\n        <input <% if (class_type == \'1\') { %>disabled<% } %> type="radio" name="class_type" value="0" <% if (class_type == \'0\') { %>checked<% } %>><strong>使用模糊类目</strong>\n        <p>适合实体商户、非常规商品。商品只上架到口袋通微店铺，如有同步其它店铺（如拍拍店铺）的需求，请使用综合类目。</p>\n    </label>\n    <div class="js-blur-class controls <% if (class_type != \'0\') { %>hide<% } %>">\n        <input type="hidden" class="input-medium js-class-1" name="class_1" value="<%= class_1 %>">\n        <input type="hidden" class="input-medium js-class-2" name="class_2" value="<%= class_2 %>">\n    </div>\n</div>\n<div class="js-class-block control-group class-block">\n    <label class="<% if (class_type == \'0\' && shop_method == \'0\') { %>js-composite-label<% } %> radio inline">\n        <input <% if (class_type == \'0\' && shop_method == \'0\') { %>disabled<% } %> type="radio" name="class_type" value="1" <% if (class_type == \'1\') { %>checked<% } %>><strong>使用综合类目(推荐)</strong>\n        <p>如果您想同步商品到您的其它店铺（如拍拍店铺），请使用综合类目。<em>已经是综合类目商品，不能切换为模糊类目。</em></p>\n    </label>\n    <div id="recent-class-region" class="js-recent-used-class recent-used-class <% if (class_type != \'1\') { %>hide<% } %>">\n        <div class="dropdown hover dropdown-right">\n            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">\n                选择最近使用的类目\n                <i class="caret"></i>\n            </a>\n            <ul class="js-recent-list dropdown-menu">\n            </ul>\n        </div>\n    </div>\n    <div class="js-composite-class controls <% if (class_type != \'1\') { %>hide<% } %>">\n        <div id="class-group-region1" class="class-group"></div>\n        <div id="class-group-region2" class="class-group"></div>\n        <div id="class-group-region3" class="class-group"></div>\n        <div id="class-group-region4" class="class-group"></div>\n        <div class="class-path-name"><span>你当前选择的类目是：</span><span class="js-path-name">未选择</span></div>\n        <input type="hidden" name="goods_class" />\n    </div>\n</div>\n'
}), define("models/class_item", ["require", "underscore", "backbone"], function(e) {
    var t = (e("underscore"), e("backbone")), i = t.Model.extend({idAttribute: "cid",defaults: {cid: "",name: "",parent_id: "",has_child: "0",pinyin: "",active: 0},initialize: function() {
        },toggleActive: function(e) {
            var t = this;
            t.set("active", e)
        }});
    return i
}), define("collections/class_list", ["require", "underscore", "backbone", "core/utils", "models/class_item"], function(e) {
    var t = (e("underscore"), e("backbone")), i = e("core/utils"), n = e("models/class_item"), s = t.Collection.extend({url: function() {
        },model: n,initialize: function() {
        },inactiveOther: function() {
            var e = this, t = e.findCurrentActive();
            return t ? void t.toggleActive(0) : !1
        },findCurrentActive: function() {
            var e = this, t = e.findWhere({active: 1});
            return t
        },parse: function(e) {
            return 0 === e.code ? e.data : (i.errorNotify(e.msg), !1)
        },findNotMatched: function(e) {
            var t = this;
            e = e.toLowerCase();
            var i = t.filter(function(t) {
                var i = t.get("name").toLowerCase(), n = t.get("pinyin").toLowerCase(), s = -1 === i.indexOf(e) && -1 === n.indexOf(e);
                return s
            });
            return i
        }});
    return s
}), define("text!templates/class_item.html", [], function() {
    return '<a title="<%-name %>" href="javascript:;" data-class-id="<%=cid %>">\n    <%=name %>\n    <% if(has_child === \'1\') { %><span>&gt;</span><% } %>\n</a>\n'
}), define("views/class_item", ["require", "underscore", "backbone", "marionette", "core/event", "text!templates/class_item.html"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = (e("core/event"), e("text!templates/class_item.html")), s = i.ItemView.extend({tagName: "li",className: "",template: t.template(n),events: {click: "onClicked"},modelEvents: {change: "render","view:hide": "hideSelf"},initialize: function() {
        },onRender: function() {
            var e = this;
            e.toggleActive()
        },toggleActive: function() {
            var e = this, t = !!e.model.get("active");
            e.$el.toggleClass("active", t)
        },onShow: function() {
        },onClicked: function(e) {
            var t = this;
            e.preventDefault();
            var i = t.model;
            if (i.get("active"))
                return !1;
            var n = i.get("cid");
            console.log(n);
            var s = i.collection;
            s.inactiveOther(), t.model.toggleActive(1), s.trigger("class_item:choosed", t.model)
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        }});
    return s
}), define("text!templates/class_group.html", [], function() {
    return '<div class="class-search">\n    <input type="text" class="js-txt-search txt-search" placeholder="请输入类目名称" />\n    <button type="button" class="btn">搜</button>\n</div>\n<ul class="js-class-item-container"></ul>\n'
}), define("views/class_group", ["require", "underscore", "backbone", "marionette", "core/event", "core/utils", "views/class_item", "text!templates/class_group.html"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = e("core/event"), s = (e("core/utils"), e("views/class_item")), o = e("text!templates/class_group.html"), a = i.CompositeView.extend({tagName: "div",className: "class-group-inner",template: t.template(o),itemView: s,itemViewContainer: ".js-class-item-container",ui: {container: ".js-class-item-container",searchTxt: ".js-txt-search"},events: {"input @ui.searchTxt": "onSearchTxtInput"},initialize: function(e) {
            var t = this;
            t.setConfig(e), t.groupId = e.groupId, t.initDataUrl(), t.listenTo(t.collection, "data:refresh", t.refreshData), t.listenTo(t.collection, "data:empty", t.emptyData), t.listenTo(t.collection, "class_item:choosed", t.onClassItemChoosed)
        },onRender: function() {
        },onShow: function() {
            var e = this;
            e.toggleShow()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initDataUrl: function() {
            var e = this, t = window._global.url.www, i = t + "/showcase/paipai/category.json";
            e.dataUrl = i
        },refreshData: function(e, t) {
            var i = this;
            i.emptyItemContainer();
            var n = {parent_id: e || "0",cid: t || "0"};
            i.fetchData(n)
        },emptyItemContainer: function() {
            var e = this;
            e.ui.container.empty()
        },fetchData: function(e) {
            var t = this;
            t.abortOldFetch(), t.xhr = t.collection.fetch({url: t.dataUrl,data: e,cache: !0,reset: !0,success: function(e) {
                    console.log(JSON.stringify(e)), t.toggleShow()
                },error: function() {
                }})
        },abortOldFetch: function() {
            var e = this, t = e.xhr;
            t && t.readyState > 0 && t.readyState < 4 && t.abort()
        },emptyData: function() {
            var e = this;
            e.collection.reset([]), e.hideSelf()
        },onClassItemChoosed: function(e) {
            var t = this, i = e.toJSON();
            n.trigger("class_group:update", t.groupId, i)
        },onSearchTxtInput: function() {
            var e = this, t = e.ui.searchTxt.val();
            t = $.trim(t), e.localSearch(t)
        },localSearch: function(e) {
            var t = this;
            t.showAllItems(), t.hideNotMatchedItems(e)
        },showAllItems: function() {
            var e = this;
            e.children.each(function(e) {
                e.showSelf()
            })
        },hideNotMatchedItems: function(e) {
            var i = this, n = i.collection.findNotMatched(e);
            t(n).each(function(e) {
                var t = i.children.findByModel(e);
                t.hideSelf()
            })
        },toggleShow: function() {
            var e = this, t = e.collection.size();
            t > 0 ? e.showSelf() : e.hideSelf()
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        }});
    return a
}), define("core/cache", ["require", "underscore"], function(e) {
    var t = (e("underscore"), {data: {},get: function(e) {
            var t = this, i = t.data[e];
            return i
        },set: function(e, t) {
            var i = this;
            i.data[e] = t
        },"delete": function(e) {
            var t = this, i = t.get(e);
            return i ? (delete t.data[e], i) : "CACHE_NOT_FOUND"
        },list: function() {
            var e = this;
            console.log(JSON.stringify(e.data)), console.info(e.data)
        },gc: function() {
        }});
    return window.CA ? window.CA : (window.CA = t, t)
}), define("text!templates/recent_class_item.html", [], function() {
    return '<a title="<%-text %>" href="javascript:;">\n    <%=text %>\n</a>\n'
}), define("views/recent_class_item", ["require", "underscore", "backbone", "marionette", "core/event", "text!templates/recent_class_item.html"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = e("core/event"), s = e("text!templates/recent_class_item.html"), o = i.ItemView.extend({tagName: "li",className: "",template: t.template(s),events: {click: "onClicked"},modelEvents: {},initialize: function() {
        },onRender: function() {
        },serializeData: function() {
            var e = this, t = {};
            return this.model && (t = this.model.toJSON(), t = e.processData(t)), t
        },processData: function(e) {
            var i = t(e.class_data).pluck("name"), n = i.join(">>");
            return e.text = n, e
        },onShow: function() {
        },onClicked: function(e) {
            var t = this;
            e.preventDefault();
            var i = t.model.get("class_data");
            n.trigger("recent_class:choosed", i)
        }});
    return o
}), define("text!templates/recent_class.html", [], function() {
    return '<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">\n    选择最近使用的类目\n    <i class="caret"></i>\n</a>\n<ul class="js-recent-list dropdown-menu">\n</ul>\n'
}), define("models/recent_class_item", ["require", "underscore", "backbone"], function(e) {
    var t = (e("underscore"), e("backbone")), i = t.Model.extend({idAttribute: "cid",defaults: {class_data: []},initialize: function() {
        }});
    return i
}), define("collections/recent_class_list", ["require", "underscore", "backbone", "core/utils", "models/recent_class_item"], function(e) {
    var t = (e("underscore"), e("backbone")), i = e("core/utils"), n = e("models/recent_class_item"), s = t.Collection.extend({url: function() {
            var e = window._global.url.www;
            return e + "/showcase/paipai/recentUsedCategory.json"
        },model: n,initialize: function() {
        },parse: function(e) {
            return 0 === e.code ? e.data : (i.errorNotify(e.msg), !1)
        }});
    return s
}), define("views/recent_class", ["require", "underscore", "backbone", "marionette", "core/cache", "core/event", "core/utils", "views/recent_class_item", "text!templates/recent_class.html", "collections/recent_class_list"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = e("core/cache"), s = (e("core/event"), e("core/utils"), e("views/recent_class_item")), o = e("text!templates/recent_class.html"), a = e("collections/recent_class_list"), r = i.CompositeView.extend({tagName: "div",className: "dropdown hover dropdown-right",template: t.template(o),itemView: s,itemViewContainer: ".js-recent-list",collectionEvents: {reset: "toggleShow"},initialize: function(e) {
            var t = this;
            t.setConfig(e), t.initDataList()
        },onRender: function() {
        },onShow: function() {
            var e = this;
            e.toggleShow()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initDataList: function() {
            var e = this, i = n.set("recent_class") || [];
            e.collection = new a(i), t.isEmpty(i) && e.fetchData()
        },fetchData: function() {
            var e = this;
            e.collection.fetch({cache: !1,reset: !0,success: function(t) {
                    console.log(JSON.stringify(t)), e.cacheRecentClass()
                },error: function() {
                }})
        },cacheRecentClass: function() {
            var e = this, t = e.collection.toJSON();
            n.set("recent_class", t)
        },toggleShow: function() {
            var e = this, t = e.collection.size();
            t > 0 ? e.showSelf() : e.hideSelf()
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        }});
    return r
}), define("views/class_info", ["require", "underscore", "backbone", "marionette", "components/validation/validate", "core/reqres", "core/event", "select2", "core/utils", "commons/utils", "text!templates/class_info.html", "collections/class_list", "views/class_group", "views/recent_class"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = (e("components/validation/validate"), e("core/reqres"), e("core/event")), o = (e("select2"), e("core/utils")), a = (e("commons/utils"), e("text!templates/class_info.html")), r = e("collections/class_list"), l = e("views/class_group"), c = e("views/recent_class"), d = n.Layout.extend({tagName: "div",className: "",template: t.template(a),ui: {classBlock: ".js-class-block",classSelect1: ".js-class-1",classSelect2: ".js-class-2",classTypeRadio: 'input[name="class_type"]',blurLabel: ".js-blur-label",blurClass: ".js-blur-class",compositeLabel: ".js-composite-label",compositeClass: ".js-composite-class",recentUsedClass: ".js-recent-used-class",pathName: ".js-path-name"},events: {"click @ui.blurLabel": "onBlurLabelClick","click @ui.compositeLabel": "onCompositeLabelClick","change @ui.classTypeRadio": "onClassTypeRadioChange"},regions: {classGroupRegion1: "#class-group-region1",classGroupRegion2: "#class-group-region2",classGroupRegion3: "#class-group-region3",classGroupRegion4: "#class-group-region4",recentClassRegion: "#recent-class-region"},maxGroups: 4,initialize: function(e) {
            var t = this;
            t.setConfig(e), t.goodsClass = [], t.classNames = [], i.Validation.bind(t), t.listenTo(t.model, "change:class_type", t.onClassTypeChanged), t.listenTo(t.model, "change:class_1", t.updateClass2), t.listenTo(s, "class_group:update", t.updateClassGroups), t.listenTo(s, "recent_class:choosed", t.showChoosedRecentClass)
        },onClose: function() {
        },onRender: function() {
        },onShow: function() {
            var e = this;
            e.initClassSelect(), e.initClassGroups(), e.showCurrentGoodsClass(), e.initRecentClass(), e.setupValidation()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },setupValidation: function() {
            var e = this;
            i.Validation.bind(e), e.listenTo(e.model, "change", e.validateModel)
        },initClassSelect: function() {
            var e = this, t = e.model.get("class_type");
            "0" == t && (e.initClass1(), e.initClass2())
        },getClass1: function() {
            var e = window._global.defaultClass, i = [];
            return t.each(e, function(e) {
                var t = {};
                t.id = e.id, t.text = e.name, i.push(t)
            }), i
        },getClass2: function(e) {
            if (!e)
                return [];
            var i = window._global.defaultClass, n = t.where(i, {id: e})[0], s = [];
            return t.each(n.list, function(e) {
                var t = {};
                t.id = e.id, t.text = e.name, s.push(t)
            }), s
        },initClass1: function() {
            var e = this, t = e.ui.classSelect1, i = e.model.get("class_1") || void 0;
            t.select("destroy"), t.off("select2-selecting").select2({placeholder: "一级类目",data: e.getClass1()}).on("select2-selecting", function(t) {
                e.model.set("class_1", t.val), e.ui.classSelect2.select2("open")
            }).select2("val", i)
        },initClass2: function() {
            var e = this, t = e.ui.classSelect2, i = e.model.get("class_2") || void 0;
            t.select("destroy"), t.off("select2-selecting").select2({placeholder: "二级类目",data: e.getClass2(e.model.get("class_1"))}).on("select2-selecting", function(t) {
                e.model.set("class_2", t.val)
            }).select2("val", i)
        },updateClass2: function(e) {
            var t = this;
            e.set("class_2", ""), t.initClass2()
        },initClassGroups: function() {
            for (var e = this, t = 1; t <= e.maxGroups; t++) {
                var i = new r([]), n = new l({groupId: t,collection: i});
                e["classList" + t] = i, e["classGroupView" + t] = n, e["classGroupRegion" + t].show(n)
            }
        },showCurrentGoodsClass: function() {
            var e = this, i = e.model.get("goods_class") || [];
            t.isEmpty(i) ? e.initFirstGroup() : e.showGoodsClass(i)
        },initRecentClass: function() {
            var e = this, t = new c;
            e.recentClassRegion.show(t)
        },initFirstGroup: function() {
            var e = this;
            e.tiggerListEvent(1, "refresh")
        },updateClassGroups: function(e, t) {
            var i = this, n = "0";
            t && (n = t.cid);
            var s = t.has_child;
            i.tiggerClassEvent(e, n, s), "0" == s ? (i.updateCid(t.cid), i.updateClassData()) : (i.updateCid(""), i.resetClassData()), i.updateClassPath(), i.updateClassModel()
        },tiggerClassEvent: function(e, t, i) {
            var n = this;
            if (e >= n.maxGroups)
                return !1;
            var s = e + 1;
            for ("1" == i && n.tiggerListEvent(s, "refresh", t); s <= n.maxGroups; )
                n.tiggerListEvent(s, "empty"), s += 1
        },showChoosedRecentClass: function(e) {
            var t = this;
            t.showGoodsClass(e), t.reverseUpdateData(e)
        },showGoodsClass: function(e) {
            for (var t, i, n = this, s = 0, o = 0, a = e.length; a > o; )
                i = e[o], console.warn(i), s = i.parent_id, t = i.cid, o += 1, n.tiggerListEvent(o, "refresh", s, t);
            for (; o < n.maxGroups; )
                o += 1, n.tiggerListEvent(o, "empty")
        },reverseUpdateData: function(e) {
            var i = this;
            i.resetClassData(), i.goodsClass = e, i.classNames = t(e).pluck("name");
            var n = t(e).last();
            n && i.updateCid(n.cid), i.updateClassPath(), i.updateClassModel()
        },tiggerListEvent: function(e, t, i, n) {
            var s = this, o = s["classList" + e];
            return o ? void o.trigger("data:" + t, i, n) : !1
        },updateClassPath: function() {
            var e = this, t = e.classNames.join(">>") || "未选择";
            e.ui.pathName.html(t)
        },updateClassData: function() {
            var e = this;
            e.resetClassData();
            for (var t, i, n = "", s = 1; s <= e.maxGroups; s++)
                t = e["classList" + s], 0 !== t.size() && (i = t.findCurrentActive(), i && (e.goodsClass.push({cid: i.get("cid"),name: i.get("name"),parent_id: i.get("parent_id"),has_child: i.get("has_child")}), n = i.get("name"), e.classNames.push(n)))
        },resetClassData: function() {
            var e = this;
            e.goodsClass = [], e.classNames = []
        },updateCid: function(e) {
            var t = this;
            t.model.set("cid", e, {silent: !0})
        },updateClassModel: function() {
            var e = this;
            e.model.set({goods_class: e.goodsClass})
        },onBlurLabelClick: function(e) {
            e.preventDefault(), o.errorNotify("综合类目商品，不能切换为模糊类目。")
        },onCompositeLabelClick: function(e) {
            e.preventDefault(), o.errorNotify("使用模糊类目的外部购买商品，不能切换为综合类目。")
        },onClassTypeRadioChange: function(e) {
            var t = this, i = $(e.target), n = i.val();
            t.model.set({class_type: n})
        },onClassTypeChanged: function(e, t) {
            var i = this;
            i.toggleClassShow(t), i.resetValidate()
        },toggleClassShow: function(e) {
            var t = this;
            "0" == e ? (t.ui.blurClass.removeClass("hide"), t.ui.compositeClass.addClass("hide"), t.ui.recentUsedClass.addClass("hide")) : (t.closeSelect2(), t.ui.blurClass.addClass("hide"), t.ui.compositeClass.removeClass("hide"), t.ui.recentUsedClass.removeClass("hide"))
        },closeSelect2: function() {
            var e = this;
            e.ui.classSelect1.select2("close"), e.ui.classSelect2.select2("close")
        },resetValidate: function() {
            var e = this;
            e.ui.classBlock.removeClass("error")
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        }});
    return d
}), define("text!templates/step1.html", [], function() {
    return '<div id="class-info-region" class="goods-info-group"></div>\n<div class="app-actions">\n    <div class="form-actions ta-c">\n        <button data-next-step="2" class="btn btn-primary js-switch-step">下一步</button>\n    </div>\n</div>\n'
}), define("views/step1", ["require", "underscore", "backbone", "marionette", "vendor/nprogress", "core/reqres", "core/event", "core/utils", "commons/utils", "models/class_info", "views/class_info", "text!templates/step1.html"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = (e("vendor/nprogress"), e("core/reqres")), o = (e("core/event"), e("core/utils"), e("commons/utils"), e("models/class_info")), a = e("views/class_info"), r = e("text!templates/step1.html"), l = n.Layout.extend({tagName: "form",className: "form-horizontal fm-goods-info",template: t.template(r),regions: {classInfoRegion: "#class-info-region"},initialize: function() {
            var e = this;
            e.setAttrsResp()
        },onRender: function() {
            var e = this;
            e.$el.attr("novalidate", !0)
        },onShow: function() {
            var e = this;
            e.initModules()
        },initModules: function() {
            var e = this;
            e.initClassInfo(), e.setupValidation(), e.setupClassInfoResp(), e.setupHasChangedResp(), e.setupValidationResp()
        },setupValidation: function() {
            var e = this;
            i.Validation.bind(e), e.listenTo(e.model, "change", e.validateModel)
        },setAttrsResp: function() {
            var e = this, t = e.model;
            s.setHandler("class_attr:get", function(e) {
                var i = t.get(e);
                return i
            })
        },setupValidationResp: function() {
            var e = this;
            s.setHandler("step1:validate", function() {
                e.classInfoModel.validate();
                var t = e.classInfoModel.isValid();
                return t
            })
        },setupClassInfoResp: function() {
            var e = this;
            s.setHandler("class_info:get", function(t) {
                var i;
                return t ? i = e.classInfoModel.get(t) : (i = e.classInfoModel.toJSON(), e.pureData(i)), i
            })
        },setupHasChangedResp: function() {
            var e = this;
            s.setHandler("class_info:has_changed", function() {
                var t = e.classInfoModel.hasChanged();
                return t
            })
        },pureData: function(e) {
            "0" == e.class_type ? e.goods_class = [] : (e.class_1 = "", e.class_2 = "")
        },validateModel: function(e) {
            e.validate(e.changed)
        },initClassInfo: function() {
            var e = this, i = e.model.toJSON(), n = t(i).pick("class_type", "class_1", "class_2", "goods_class", "cid", "shop_method");
            e.classInfoModel = new o(n);
            var s = e.classInfoView = new a({model: e.classInfoModel});
            e.classInfoRegion.show(s)
        }});
    return l
}), function(e) {
    "function" == typeof define && define.amd ? define("backbone.modelbinder", ["underscore", "jquery", "backbone"], e) : e(_, jQuery, Backbone)
}(function(e, t, i) {
    if (!i)
        throw "Please include Backbone.js before Backbone.ModelBinder.js";
    return i.ModelBinder = function() {
        e.bindAll.apply(e, [this].concat(e.functions(this)))
    }, i.ModelBinder.SetOptions = function(e) {
        i.ModelBinder.options = e
    }, i.ModelBinder.VERSION = "1.0.5", i.ModelBinder.Constants = {}, i.ModelBinder.Constants.ModelToView = "ModelToView", i.ModelBinder.Constants.ViewToModel = "ViewToModel", e.extend(i.ModelBinder.prototype, {bind: function(e, i, n, s) {
            this.unbind(), this._model = e, this._rootEl = i, this._setOptions(s), this._model || this._throwException("model must be specified"), this._rootEl || this._throwException("rootEl must be specified"), n ? (this._attributeBindings = t.extend(!0, {}, n), this._initializeAttributeBindings(), this._initializeElBindings()) : this._initializeDefaultBindings(), this._bindModelToView(), this._bindViewToModel()
        },bindCustomTriggers: function(e, t, i, n, s) {
            this._triggers = i, this.bind(e, t, n, s)
        },unbind: function() {
            this._unbindModelToView(), this._unbindViewToModel(), this._attributeBindings && (delete this._attributeBindings, this._attributeBindings = void 0)
        },_setOptions: function(t) {
            this._options = e.extend({boundAttribute: "name"}, i.ModelBinder.options, t), this._options.modelSetOptions || (this._options.modelSetOptions = {}), this._options.modelSetOptions.changeSource = "ModelBinder", this._options.changeTriggers || (this._options.changeTriggers = {"": "change","[contenteditable]": "blur"}), this._options.initialCopyDirection || (this._options.initialCopyDirection = i.ModelBinder.Constants.ModelToView)
        },_initializeAttributeBindings: function() {
            var t, i, n, s, o;
            for (t in this._attributeBindings) {
                for (i = this._attributeBindings[t], e.isString(i) ? n = {elementBindings: [{selector: i}]} : e.isArray(i) ? n = {elementBindings: i} : e.isObject(i) ? n = {elementBindings: [i]} : this._throwException("Unsupported type passed to Model Binder " + n), s = 0; s < n.elementBindings.length; s++)
                    o = n.elementBindings[s], o.attributeBinding = n;
                n.attributeName = t, this._attributeBindings[t] = n
            }
        },_initializeDefaultBindings: function() {
            var e, i, n, s, o;
            for (this._attributeBindings = {}, i = t("[" + this._options.boundAttribute + "]", this._rootEl), e = 0; e < i.length; e++)
                n = i[e], s = t(n).attr(this._options.boundAttribute), this._attributeBindings[s] ? this._attributeBindings[s].elementBindings.push({attributeBinding: this._attributeBindings[s],boundEls: [n]}) : (o = {attributeName: s}, o.elementBindings = [{attributeBinding: o,boundEls: [n]}], this._attributeBindings[s] = o)
        },_initializeElBindings: function() {
            var e, i, n, s, o, a, r;
            for (e in this._attributeBindings)
                for (i = this._attributeBindings[e], n = 0; n < i.elementBindings.length; n++)
                    if (s = i.elementBindings[n], o = "" === s.selector ? t(this._rootEl) : t(s.selector, this._rootEl), 0 === o.length)
                        this._throwException("Bad binding found. No elements returned for binding selector " + s.selector);
                    else
                        for (s.boundEls = [], a = 0; a < o.length; a++)
                            r = o[a], s.boundEls.push(r)
        },_bindModelToView: function() {
            this._model.on("change", this._onModelChange, this), this._options.initialCopyDirection === i.ModelBinder.Constants.ModelToView && this.copyModelAttributesToView()
        },copyModelAttributesToView: function(t) {
            var i, n;
            for (i in this._attributeBindings)
                (void 0 === t || -1 !== e.indexOf(t, i)) && (n = this._attributeBindings[i], this._copyModelToView(n))
        },copyViewValuesToModel: function() {
            var e, i, n, s, o, a;
            for (e in this._attributeBindings)
                for (i = this._attributeBindings[e], n = 0; n < i.elementBindings.length; n++)
                    if (s = i.elementBindings[n], this._isBindingUserEditable(s))
                        if (this._isBindingRadioGroup(s))
                            a = this._getRadioButtonGroupCheckedEl(s), a && this._copyViewToModel(s, a);
                        else
                            for (o = 0; o < s.boundEls.length; o++)
                                a = t(s.boundEls[o]), this._isElUserEditable(a) && this._copyViewToModel(s, a)
        },_unbindModelToView: function() {
            this._model && (this._model.off("change", this._onModelChange), this._model = void 0)
        },_bindViewToModel: function() {
            e.each(this._options.changeTriggers, function(e, i) {
                t(this._rootEl).delegate(i, e, this._onElChanged)
            }, this), this._options.initialCopyDirection === i.ModelBinder.Constants.ViewToModel && this.copyViewValuesToModel()
        },_unbindViewToModel: function() {
            this._options && this._options.changeTriggers && e.each(this._options.changeTriggers, function(e, i) {
                t(this._rootEl).undelegate(i, e, this._onElChanged)
            }, this)
        },_onElChanged: function(e) {
            var i, n, s, o;
            for (i = t(e.target)[0], n = this._getElBindings(i), s = 0; s < n.length; s++)
                o = n[s], this._isBindingUserEditable(o) && this._copyViewToModel(o, i)
        },_isBindingUserEditable: function(e) {
            return void 0 === e.elAttribute || "text" === e.elAttribute || "html" === e.elAttribute
        },_isElUserEditable: function(e) {
            var t = e.attr("contenteditable");
            return t || e.is("input") || e.is("select") || e.is("textarea")
        },_isBindingRadioGroup: function(e) {
            var i, n, s = e.boundEls.length > 0;
            for (i = 0; i < e.boundEls.length; i++)
                if (n = t(e.boundEls[i]), "radio" !== n.attr("type")) {
                    s = !1;
                    break
                }
            return s
        },_getRadioButtonGroupCheckedEl: function(e) {
            var i, n;
            for (i = 0; i < e.boundEls.length; i++)
                if (n = t(e.boundEls[i]), "radio" === n.attr("type") && n.attr("checked"))
                    return n;
            return void 0
        },_getElBindings: function(e) {
            var t, i, n, s, o, a, r = [];
            for (t in this._attributeBindings)
                for (i = this._attributeBindings[t], n = 0; n < i.elementBindings.length; n++)
                    for (s = i.elementBindings[n], o = 0; o < s.boundEls.length; o++)
                        a = s.boundEls[o], a === e && r.push(s);
            return r
        },_onModelChange: function() {
            var e, t;
            for (e in this._model.changedAttributes())
                t = this._attributeBindings[e], t && this._copyModelToView(t)
        },_copyModelToView: function(e) {
            var n, s, o, a, r, l;
            for (r = this._model.get(e.attributeName), n = 0; n < e.elementBindings.length; n++)
                for (s = e.elementBindings[n], o = 0; o < s.boundEls.length; o++)
                    a = s.boundEls[o], a._isSetting || (l = this._getConvertedValue(i.ModelBinder.Constants.ModelToView, s, r), this._setEl(t(a), s, l))
        },_setEl: function(e, t, i) {
            t.elAttribute ? this._setElAttribute(e, t, i) : this._setElValue(e, i)
        },_setElAttribute: function(t, n, s) {
            switch (n.elAttribute) {
                case "html":
                    t.html(s);
                    break;
                case "text":
                    t.text(s);
                    break;
                case "enabled":
                    t.prop("disabled", !s);
                    break;
                case "displayed":
                    t[s ? "show" : "hide"]();
                    break;
                case "hidden":
                    t[s ? "hide" : "show"]();
                    break;
                case "css":
                    t.css(n.cssAttribute, s);
                    break;
                case "class":
                    var o = this._model.previous(n.attributeBinding.attributeName), a = this._model.get(n.attributeBinding.attributeName);
                    e.isUndefined(o) && e.isUndefined(a) || (o = this._getConvertedValue(i.ModelBinder.Constants.ModelToView, n, o), t.removeClass(o)), s && t.addClass(s);
                    break;
                default:
                    t.attr(n.elAttribute, s)
            }
        },_setElValue: function(e, t) {
            if (e.attr("type"))
                switch (e.attr("type")) {
                    case "radio":
                        e.prop("checked", e.val() === t);
                        break;
                    case "checkbox":
                        e.prop("checked", !!t);
                        break;
                    case "file":
                        break;
                    default:
                        e.val(t)
                }
            else
                e.is("input") || e.is("select") || e.is("textarea") ? e.val(t || (0 === t ? "0" : "")) : e.text(t || (0 === t ? "0" : ""))
        },_copyViewToModel: function(e, n) {
            var s, o, a;
            n._isSetting || (n._isSetting = !0, s = this._setModel(e, t(n)), n._isSetting = !1, s && e.converter && (o = this._model.get(e.attributeBinding.attributeName), a = this._getConvertedValue(i.ModelBinder.Constants.ModelToView, e, o), this._setEl(t(n), e, a)))
        },_getElValue: function(e, t) {
            switch (t.attr("type")) {
                case "checkbox":
                    return t.prop("checked") ? !0 : !1;
                default:
                    return void 0 !== t.attr("contenteditable") ? t.html() : t.val()
            }
        },_setModel: function(e, t) {
            var n = {}, s = this._getElValue(e, t);
            return s = this._getConvertedValue(i.ModelBinder.Constants.ViewToModel, e, s), n[e.attributeBinding.attributeName] = s, this._model.set(n, this._options.modelSetOptions)
        },_getConvertedValue: function(e, t, i) {
            return t.converter ? i = t.converter(e, i, t.attributeBinding.attributeName, this._model, t.boundEls) : this._options.converter && (i = this._options.converter(e, i, t.attributeBinding.attributeName, this._model, t.boundEls)), i
        },_throwException: function(e) {
            if (!this._options.suppressThrows)
                throw e;
            console && console.error && console.error(e)
        }}), i.ModelBinder.CollectionConverter = function(t) {
        if (this._collection = t, !this._collection)
            throw "Collection must be defined";
        e.bindAll(this, "convert")
    }, e.extend(i.ModelBinder.CollectionConverter.prototype, {convert: function(e, t) {
            return e === i.ModelBinder.Constants.ModelToView ? t ? t.id : void 0 : this._collection.get(t)
        }}), i.ModelBinder.createDefaultBindings = function(e, i, n, s) {
        var o, a, r, l, c = {};
        for (o = t("[" + i + "]", e), a = 0; a < o.length; a++)
            if (r = o[a], l = t(r).attr(i), !c[l]) {
                var d = {selector: "[" + i + '="' + l + '"]'};
                c[l] = d, n && (c[l].converter = n), s && (c[l].elAttribute = s)
            }
        return c
    }, i.ModelBinder.combineBindings = function(t, i) {
        return e.each(i, function(e, i) {
            var n = {selector: e.selector};
            e.converter && (n.converter = e.converter), e.elAttribute && (n.elAttribute = e.elAttribute), t[i] = t[i] ? [t[i], n] : n
        }), t
    }, i.ModelBinder
}), define("models/attr_item", ["require", "underscore", "backbone"], function(e) {
    var t = (e("underscore"), e("backbone")), i = t.Model.extend({idAttribute: "pid",defaults: {cid: "",pid: "",name: "",value: "",is_sale: "0",is_necessary: "0",type: "UT_RADIO",order_flag: "",option: []},actionText: {UT_CHECKBOX: "选择",UT_RADIO: "选择",UT_TEXT: "填写"},initialize: function() {
        },validation: {value: function(e, t, i) {
                return "1" == i.is_necessary && "" === e ? "请" + this.actionText[i.type] + i.name : void 0
            }}});
    return i
}), define("collections/attr_list", ["require", "underscore", "backbone", "core/utils", "models/attr_item"], function(e) {
    var t = (e("underscore"), e("backbone")), i = (e("core/utils"), e("models/attr_item")), n = t.Collection.extend({model: i});
    return n
}), define("text!templates/attr_item.html", [], function() {
    return '<div class="control-group">\n    <label class="control-label" for="attr-item-<%=pid %>"><% if(is_necessary == \'1\') { %><em class="required">*</em><% } %><%-name %></label>\n    <div class="controls">\n        <input name="value" id="attr-item-<%=pid %>" class="js-attr-input input-medium" type="text" value="<%=value %>" />\n    </div>\n</div>\n'
}), define("views/attr_item", ["require", "underscore", "backbone", "marionette", "components/validation/validate", "core/event", "commons/utils", "backbone.modelbinder", "text!templates/attr_item.html"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = (e("components/validation/validate"), e("core/event"), e("commons/utils"));
    e("backbone.modelbinder");
    var o = e("text!templates/attr_item.html"), a = n.ItemView.extend({tagName: "li",className: "",template: t.template(o),ui: {attrInput: ".js-attr-input"},events: {},_modelBinder: void 0,initialize: function() {
            var e = this;
            e._modelBinder = new i.ModelBinder, e.listenTo(e.model, "change", s.showLog)
        },onClose: function() {
            var e = this;
            e._modelBinder.unbind()
        },onRender: function() {
        },onShow: function() {
            var e = this, t = e.model.get("type");
            "UT_TEXT" === t ? e.initDataBindings() : e.initSelect2(t), e.setupValidation()
        },initSelect2: function(e) {
            var t = this, i = t.model.get("value") || void 0, n = "UT_CHECKBOX" === e;
            t.ui.attrInput.select2({placeholder: "请选择",data: t.getSelect2Data(),multiple: n,allowClear: !0}).on("select2-close", function() {
                var e = t.ui.attrInput.select2("val");
                t.model.set("value", e)
            }).on("select2-clearing", function() {
                t.model.set("value", "")
            }).select2("val", i)
        },getSelect2Data: function() {
            var e = this, i = e.model.get("option"), n = [];
            return t.each(i, function(e) {
                var t = {};
                t.id = e.option_id, t.text = e.option_value, n.push(t)
            }), n
        },initDataBindings: function() {
            var e = this, t = {value: ".js-attr-input"};
            e._modelBinder.bind(e.model, e.el, t)
        },setupValidation: function() {
            var e = this;
            i.Validation.bind(e), e.listenTo(e.model, "change:value", e.validateModel)
        },validateModel: function(e) {
            e.validate(e.changed)
        }});
    return a
}), define("views/attr_empty", ["require", "underscore", "backbone", "marionette"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = i.ItemView.extend({tagName: "li",className: "",template: t.template("无")});
    return n
}), define("text!templates/attrs_edit.html", [], function() {
    return '<ul class="attributes-list">\n</ul>\n'
}), define("views/attrs_edit", ["require", "underscore", "backbone", "marionette", "components/validation/validate", "core/reqres", "core/event", "core/utils", "commons/utils", "views/attr_item", "views/attr_empty", "text!templates/attrs_edit.html"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = (e("components/validation/validate"), e("core/reqres")), s = (e("core/event"), e("core/utils"), e("commons/utils"), e("views/attr_item")), o = e("views/attr_empty"), a = e("text!templates/attrs_edit.html"), r = i.CollectionView.extend({tagName: "ul",className: "attributes-list",template: t.template(a),itemView: s,emptyView: o,ui: {},events: {},collectionEvents: {change: "reverseUpdate"},initialize: function(e) {
            var t = this;
            t.setConfig(e), t.parentModel = e.parentModel, t.setupValidateResp()
        },onRender: function() {
        },onShow: function() {
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },setupValidateResp: function() {
            var e = this;
            e.errorLen = 0, n.setHandler("attrs:validate", function() {
                return e.validateAttrs(), e.errorLen
            })
        },validateAttrs: function() {
            var e = this;
            e.errorLen = 0, e.collection.each(function(t) {
                var i = t.validate();
                i && (e.errorLen += 1)
            })
        },reverseUpdate: function() {
            var e = this, t = e.collection.toJSON();
            e.cleanAttrsOption(t), e.parentModel.set({attrs: t}, {silent: !0})
        },cleanAttrsOption: function(e) {
            for (var t = 0, i = e.length; i > t; t++)
                delete e[t].option
        }});
    return r
}), define("text!templates/base_info.html", [], function() {
    return '<div class="info-group-title vbox">\n    <div class="group-inner">基本信息</div>\n</div>\n<div class="info-group-cont vbox">\n    <div class="group-inner">\n        <div class="control-group">\n            <label class="control-label">商品类目：</label>\n            <div class="controls">\n                <div class="js-goods-class static-value">\n                    <% if(goods_class) { %>\n                        <%-goods_class %>\n                    <% } else { %>\n                        无 <a style="margin-left: 20px;" data-next-step="1" class="js-switch-step" href="javascript:;">去选择商品品类</a>\n                    <% } %>\n                </div>\n                <input type="hidden" class="input-medium" name="class_2" value="<%= class_2 %>">\n                <input type="hidden" class="input-medium" name="goods_class" value="<%= goods_class %>">\n            </div>\n        </div>\n        <% if(class_type === \'0\') { %>\n        <div class="control-group">\n            <label class="control-label">购买方式：</label>\n            <div class="controls">\n                <label class="radio inline">\n                    <input type="radio" name="shop_method" value="1" <% if (shop_method == \'1\') { %>checked<% } %>>在口袋通购买\n                </label>\n                <label class="radio inline">\n                    <input type="radio" name="shop_method" value="0" <% if (shop_method == \'0\') { %>checked<% } %>>链接到外部购买\n                </label>\n            </div>\n        </div>\n        <% } %>\n        <% if(class_type == \'1\') { %>\n        <div class="js-attrs-edit control-group">\n            <label class="control-label">商品属性：</label>\n            <div class="js-attrs-controls controls">\n                <div id="attrs-edit-region" class="attributes">\n                </div>\n            </div>\n        </div>\n        <% } %>\n        <div class="control-group">\n            <label class="control-label">商品分组：</label>\n            <div class="controls">\n                <select class="js-tag chosen-select" name="tag" data-selected-id="<%- tag %>" multiple data-placeholder="选择商品分组">\n                </select>\n                <p class="help-inline">\n                    <a class="js-refresh-tag" href="javascript:;">刷新</a>\n                    <span>|</span>\n                    <a class="new_window" target="_blank" href="/v2/showcase/tag#create">新建分组</a>\n                    <span>|</span>\n                    <a class="new_window" target="_blank" href="http://wap.koudaitong.com/v2/showcase/feature?alias=djbj0hb7">帮助</a>\n                </p>\n                <p class="help-desc js-tag-desc hide">\n                    使用“列表中隐藏”分组，商品将不出现在商品列表中\n                </p>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label">商品类型：</label>\n            <div class="controls">\n                <label class="radio inline">\n                    <input type="radio" name="shipment" value="0" <% if (shipment == \'0\') { %>checked<% } %>>实物商品\n                    <span class="gray">（物流发货）</span>\n                </label>\n                <label class="radio inline">\n                    <input type="radio" name="shipment" value="1" disabled <% if (shipment == \'1\') { %>checked<% } %>>虚拟商品\n                    <span class="gray">（无需物流）</span>\n                </label>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("views/base_info", ["require", "underscore", "backbone", "marionette", "components/validation/validate", "core/event", "commons/utils", "backbone.modelbinder", "chosen", "collections/attr_list", "views/attrs_edit", "text!templates/base_info.html"], function(e) {
    {
        var t = e("underscore"), i = e("backbone"), n = e("marionette");
        e("components/validation/validate"), e("core/event"), e("commons/utils")
    }
    e("backbone.modelbinder"), e("chosen");
    var s = e("collections/attr_list"), o = e("views/attrs_edit"), a = e("text!templates/base_info.html"), r = n.Layout.extend({tagName: "div",className: "goods-info-group-inner",template: t.template(a),ui: {shopMethodRadio: '[name="shop_method"]',attrsEdit: ".js-attrs-edit",goodsClass: ".js-goods-class",tagSelect: ".js-tag",tagDesc: ".js-tag-desc"},regions: {attrsEditRegion: "#attrs-edit-region"},events: {"change @ui.shopMethodRadio": "onShopMethodRadioChange"},_modelBinder: void 0,initialize: function(e) {
            var t = this;
            t.setConfig(e), t.attrsOptions = e.attrsOptions, i.Validation.bind(t), t._modelBinder = new i.ModelBinder, t.listenTo(t.model, "change:goods_class", t.updateGoodsClass), t.listenTo(t.model, "change:tag", t.onTagChange)
        },onClose: function() {
            var e = this;
            e._modelBinder.unbind()
        },onRender: function() {
        },serializeData: function() {
            var e = this, t = {};
            return this.model && (t = this.model.toJSON(), t = e.processData(t)), t
        },processData: function(e) {
            var t = this;
            return e.goods_class = t.generateGoodsClass(e), e
        },generateGoodsClass: function(e) {
            var i = this;
            e || (e = i.model.toJSON());
            var n = [], s = "";
            return n = "0" == e.class_type ? i.getClassNameArr(e) : t(e.goods_class).pluck("name") || [], s = n.join(">>")
        },getClassNameArr: function(e) {
            var i = [];
            if (!e.class_1 || !e.class_2)
                return i;
            var n = window._global.defaultClass, s = t(n).findWhere({id: e.class_1});
            if (!s)
                return i;
            i.push(s.name);
            var o = t(s.list).findWhere({id: e.class_2});
            return o ? (i.push(o.name), i) : i
        },onShow: function() {
            var e = this;
            e.initAttrsEdit(), e.initTagChosen(), e.initDataBindings()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },updateGoodsClass: function() {
            var e = this, t = e.generateGoodsClass();
            e.ui.goodsClass.html(t)
        },initTagChosen: function() {
            var e = this, t = e.ui.tagSelect, i = t.chosen({no_results_text: "木有找到这个分类：",width: "200px"});
            e.$(".js-refresh-tag").on("click", function(n) {
                var s = e.model.get("tag");
                $.get(window._global.url.www + "/showcase/tag/option", {selected: s}, function(n) {
                    t.html(n), i.trigger("chosen:updated"), e.model.trigger("change:tag")
                }), n.preventDefault()
            }).trigger("click"), t.trigger("chosen:updated")
        },initDataBindings: function() {
            var e = this, t = {tag: {selector: '[name="tag"]',converter: e.tagConverter},shipment: '[name="shipment"]'};
            e._modelBinder.bind(e.model, e.el, t)
        },onShopMethodRadioChange: function(e) {
            var t = this;
            e.preventDefault();
            var i = $(e.target), n = i.val();
            t.model.set("shop_method", n)
        },initAttrsEdit: function() {
            var e = this, t = e.attrsOptions || [];
            e.attrsList = new s(t), e.attrsEditView = new o({parentModel: e.model,collection: e.attrsList}), e.attrsEditRegion.show(e.attrsEditView)
        },onTagChange: function() {
            var e = this, t = e._findDefaultOption().toString(), i = e.model.get("tag") || "";
            i.indexOf(t) >= 0 ? e.ui.tagDesc.removeClass("hide") : e.ui.tagDesc.addClass("hide")
        },_findDefaultOption: function() {
            var e = this, t = e.ui.tagSelect.find("option");
            if (e._defaultTagId)
                return e._defaultTagId;
            for (var i = "", n = 0; n < t.length; n++) {
                var s = t[n], o = $.trim(s.innerText);
                if ("列表中隐藏" === o) {
                    i = s.value;
                    break
                }
            }
            return e._defaultTagId = i, i
        },tagConverter: function(e, t) {
            return null === t ? [] : t
        },onShopMethodChanged: function(e, t) {
            var i = this;
            "0" == t ? i.ui.attrsEdit.hide() : i.ui.attrsEdit.show()
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        }});
    return r
}), define("text!templates/goods_info.html", [], function() {
    return '<div class="info-group-title vbox">\n    <div class="group-inner">商品信息</div>\n</div>\n<div class="info-group-cont vbox">\n    <div class="group-inner">\n        <div class="control-group">\n            <label class="control-label"><em class="required">*</em>商品名：</label>\n            <div class="controls">\n                <input class="input-xxlarge" type="text" name="title" value="<%= title %>" maxlength="100" />\n                <span class="autoread-goods">\n                    <a href="javascript: ;" class="js-autoread-goods">快速导入淘宝商品信息</a>\n                </span>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label"><em class="required">*</em>价格：</label>\n            <div class="controls">\n                <div class="input-prepend">\n                    <span class="add-on">￥</span>\n                    <input type="text" name="price" value="<%= price %>" class="input-small" />\n                </div>\n                <input type="text" class="input-small" placeholder="淘价：" name="origin" value="<%= origin %>">\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label"><em class="required">*</em>商品图：</label>\n            <div class="controls">\n                <input type="hidden" name="picture" />\n                <div class="picture-list">\n                    <ul class="js-picture-list app-image-list clearfix">\n                    <% _.each(picture, function(item, index) { %>\n                        <%= picTemplate({\'item\': item}) %>\n                    <% }); %>\n                    <li>\n                        <a href="javascript:;" class="add-goods js-add-picture">+加图</a>\n                    </li>\n                    </ul>\n                </div>\n                <p class="help-desc">建议尺寸：640 x 640 像素</p>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label">\n                <em class="js-buy-url-required required <% if(shop_method != \'0\') { %>hide<% } %>">*</em>\n                外部购买地址：\n            </label>\n            <div class="controls">\n                <input type="text" name="buy_url" value="<%= buy_url %>" class="input-xxlarge js-buy-url">\n                <a style="display: none;" href="javascript:;" class="js-help-notes circle-help">?</a>\n                <p class="help-desc">当用户购买环境不支持微信或微博支付时会跳转到此地址</p>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("text!templates/picture.html", [], function() {
    return '<li class="sort">\n    <a href="<%= Utils.fullfillImage(item.url) %>" target="_blank">\n        <img src="<%= Utils.fullfillImage(item.url, \'!100x100.jpg\') %>" />\n    </a>\n    <a class="js-delete-picture close-modal small hide">×</a>\n</li>\n'
}), define("views/goods_info", ["require", "underscore", "backbone", "marionette", "components/pop/pop", "core/event", "core/reqres", "components/validation/validate", "core/utils", "commons/utils", "backbone.modelbinder", "jqueryui", "components/image/app", "text!templates/goods_info.html", "text!templates/picture.html"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = e("components/pop/pop"), o = e("core/event"), a = e("core/reqres"), r = (e("components/validation/validate"), e("core/utils")), l = e("commons/utils");
    e("backbone.modelbinder"), e("jqueryui");
    var c = e("components/image/app"), d = e("text!templates/goods_info.html"), u = e("text!templates/picture.html"), p = n.Layout.extend({tagName: "div",className: "goods-info-group-inner",template: t.template(d),picTemplate: t.template(u),ui: {autoread: ".js-autoread-goods",priceTxt: '[name="price"]',originPriceTxt: '[name="origin"]',pictureContainer: ".js-picture-list",addPicture: ".js-add-picture",buyUrlRequired: ".js-buy-url-required"},events: {"click @ui.autoread": "autoreadGoods","click @ui.addPicture": "addPicture","click .js-delete-picture": "deletePicture",'blur [name="origin"]': "onOriginBlur","blur .js-buy-url": "checkUrl"},_modelBinder: void 0,pictureMaxSize: 15,initialize: function(e) {
            var t = this;
            t.setConfig(e), i.Validation.bind(t), t._modelBinder = new i.ModelBinder({modelSetOptions: {validate: !0}}), t.listenTo(t.model, "change", l.showLog), t.listenTo(t.model, "change:shop_method", t.onShopMethodChanged), t.listenTo(t.model, "picture:reset", t.renderAllPicture), t.listenTo(o, "stock_module:hide", t.hideStockModule), t.listenTo(o, "stock_module:show", t.showStockModule)
        },onClose: function() {
            var e = this;
            e._modelBinder.unbind()
        },serializeData: function() {
            var e = this, t = {};
            return this.model && (t = this.model.toJSON(), t = e.processData(t)), t
        },processData: function(e) {
            var t = this, i = Number(e.price).toFixed(2) + "";
            return e.price = i, e.picTemplate = t.picTemplate, e
        },onRender: function() {
            var e = this;
            e.bindUIElements()
        },onShow: function() {
            var e = this;
            e.initSortable(), e.initDataBindings()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initDataBindings: function() {
            var e = this, t = {title: '[name="title"]',price: {selector: '[name="price"]',converter: e.priceConverter},buy_url: {selector: '[name="buy_url"]',converter: e.buyUrlConverter},origin: {selector: '[name="origin"]',converter: e.originConverter}};
            e._modelBinder.bind(e.model, e.el, t)
        },originConverter: function(e, t) {
            return t = l.originValueFix(t)
        },priceConverter: function(e, t) {
            var i = l.decimalFix(t, 2);
            return i
        },buyUrlConverter: function(e, t) {
            return t = l.buyUrlValueFix(t)
        },onOriginBlur: function() {
            var e = this, t = e.ui.originPriceTxt, i = t.val(), n = l.originValueFix(i);
            return i === n ? !1 : void t.val(n)
        },checkPictureMaxSize: function(e) {
            var t = this, i = e.length < t.pictureMaxSize;
            return i
        },addPicture: function() {
            var e = this;
            c.initialize({callback: function(i) {
                    t.isArray(i) ? t.each(i, function(t) {
                        e._addSinglePicture.call(e, t)
                    }) : e._addSinglePicture.call(e, i), e.model.calculateImage()
                }})
        },renderAllPicture: function() {
            var e = this;
            e.ui.pictureContainer.find("li.sort").remove();
            var i = e.model.get("picture");
            t(i).each(function(t) {
                e.renderPicture(t)
            })
        },renderPicture: function(e) {
            var t = this;
            t.ui.pictureContainer.find("li:not(.sort)").before(t.picTemplate({item: e}))
        },_addSinglePicture: function(e) {
            var t = this, i = t.model.get("picture");
            if (!t.checkPictureMaxSize(i))
                return r.errorNotify("商品图片最多支持 " + t.pictureMaxSize + " 张"), !1;
            var n = {url: e.attachment_url,id: e.attachment_id,width: e.width,height: e.height};
            i.push(n), t.model.set("picture", i), t.renderPicture(n)
        },initSortable: function() {
            var e = this;
            e.$(".picture-list").sortable({items: ".sort",cursor: "move",start: function(e, t) {
                    t.item.data("startPos", t.item.index())
                },stop: function(i, n) {
                    var s = n.item.data("startPos"), o = n.item.index();
                    if (s !== o) {
                        var a = [];
                        t.each(e.model.get("picture"), function(e, t, i) {
                            a.push(s > o ? t === o ? i[s] : t > o && s >= t ? i[t - 1] : e : t === o ? i[s] : o > t && t >= s ? i[t + 1] : e)
                        }), e.model.set("picture", a)
                    }
                },update: function() {
                }})
        },deletePicture: function(e) {
            var t = this, i = $(e.target), n = i.parent("li").index(), s = this.model.get("picture");
            s.splice(n, 1), i.parents("li.sort").first().remove(), t.model.calculateImage()
        },checkUrl: function(e) {
            var t = $(e.target);
            e.stopPropagation(), e.stopImmediatePropagation();
            var i = $.trim(t.val());
            i = l.buyUrlValueFix(i), t.val(i)
        },autoreadGoods: function(e) {
            var t = this, i = $(e.target);
            s.initialize({type: "link",target: i,content: "请粘贴 淘宝/天猫 的单品地址",callback: function(e) {
                    r.successNotify("正在读取商品，请稍等。", void 0, {fade: !1});
                    var i = $.trim(e);
                    t.grabGoodsData(i)
                }})
        },grabGoodsData: function(e) {
            var t = this, i = window._global.url.img + "/tao/detail", n = {url: encodeURI(e),mp_id: window._global.kdt_id};
            $.ajax({url: i,type: "GET",dataType: "jsonp",timeout: 6e4,cache: !1,data: n,success: function(i) {
                    "success" == i.status ? (t.updateGoodsModel(e, i), r.clearNotify()) : r.errorNotify("抓取商品信息出错啦:(")
                },error: function() {
                    r.errorNotify("抓取商品信息出错啦:(")
                },complete: function() {
                }})
        },updateGoodsModel: function(e, i) {
            var n = this, s = {title: i.title,buy_url: e};
            if (0 !== i.price && (s.price = i.price), n.model.set(s), t.isEmpty(i.attachment))
                return !1;
            var o = [];
            t.each(i.attachment, function(e) {
                o.push({url: e.attachment_url,id: e.attachment_id})
            }), n.model.set({picture: o}), n.model.trigger("picture:reset")
        },getData: function() {
            var e = this, t = e.model.toJSON();
            return t
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        },hideStockModule: function() {
            var e = this, t = e.model.get("shop_method");
            return "0" == t ? !1 : void e.ui.priceTxt.prop("readonly", !1)
        },showStockModule: function() {
            var e = this;
            e.ui.priceTxt.prop("readonly", !0)
        },onShopMethodChanged: function(e, t) {
            var i = this;
            "0" == t ? (i.ui.buyUrlRequired.removeClass("hide"), i.ui.priceTxt.prop("readonly", !1)) : (i.ui.buyUrlRequired.addClass("hide"), i.resetPrice())
        },resetPrice: function() {
            var e = this, t = a.request("stock_module:is_show");
            if (!t)
                return !1;
            e.ui.priceTxt.prop("readonly", !0);
            var i = a.request("min_price:get");
            e.ui.priceTxt.val(i)
        }});
    return p
}), define("models/sku_item", ["require", "underscore", "backbone"], function(e) {
    var t = (e("underscore"), e("backbone")), i = t.Model.extend({idAttribute: "_id",defaults: {text: "自定义",editable: "1",list: []},initialize: function() {
        }});
    return i
}), define("collections/sku_list", ["require", "underscore", "backbone", "models/sku_item"], function(e) {
    var t = (e("underscore"), e("backbone")), i = e("models/sku_item"), n = t.Collection.extend({url: function() {
        },model: i,checkIsExist: function(e) {
            var t = this, i = t.where({text: e}), n = i.length > 0;
            return n
        }});
    return n
}), define("models/sku", ["require", "underscore", "backbone"], function(e) {
    var t = (e("underscore"), e("backbone")), i = t.Model.extend({defaults: {group_list: null},initialize: function() {
        }});
    return i
}), define("models/sku_atom", ["require", "underscore", "backbone"], function(e) {
    var t = (e("underscore"), e("backbone")), i = t.Model.extend({idAttribute: "id",defaults: {text: ""},initialize: function() {
        }});
    return i
}), define("collections/sku_atom_list", ["require", "underscore", "backbone", "core/utils", "core/reqres", "models/sku_atom"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("core/utils"), s = e("core/reqres"), o = e("models/sku_atom"), a = i.Collection.extend({url: function() {
        },model: o,initialize: function(e, t) {
            var i = this;
            i.choosable = t.choosable || {}, i.customCount = 0, i.maxCount = t.choosable.count || 0, i.listenTo(i, "add remove reset", i.updateCustomCount)
        },updateCustomCount: function() {
            var e = this, i = 0, n = e.choosable.data || [];
            e.each(function(e) {
                var s = t(n).findWhere({option_value: e.get("text")});
                s || (i += 1)
            }), e.customCount = i
        },checkChooseable: function(e) {
            var t = this;
            return t.checkIsExist(e) ? !1 : t.checkCustomLimit(e) ? !1 : !0
        },checkIsExist: function(e) {
            var t = this, i = t.get(e.id);
            return i ? (n.errorNotify("已经添加了相同的规格值。"), !0) : !1
        },checkCustomLimit: function(e) {
            var i = this, o = s.request("goods_attr:get", "class_type");
            if ("0" == o)
                return !1;
            var a = i.choosable.data || [];
            if (t.isEmpty(a))
                return !1;
            var r = t(a).findWhere({option_value: e.text});
            if (!r) {
                if (0 === i.maxCount)
                    return n.errorNotify("该商品不支持『" + e.text + "』规格，且不支持自定义项。"), !0;
                if (i.customCount === i.maxCount)
                    return n.errorNotify("该规格最多添加个 " + i.maxCount + " 自定义项。"), !0
            }
            return !1
        }});
    return a
}), define("text!templates/sku_atom.html", [], function() {
    return '<span data-atom-id="<%=id %>"><%=text %></span>\n<div class="close-modal small js-remove-sku-atom">×</div>\n'
}), define("views/sku_atom", ["require", "underscore", "backbone", "marionette", "core/event", "core/reqres", "commons/utils", "chosen", "text!templates/sku_atom.html"], function(e) {
    {
        var t = e("underscore"), i = (e("backbone"), e("marionette")), n = (e("core/event"), e("core/reqres"));
        e("commons/utils")
    }
    e("chosen");
    var s = e("text!templates/sku_atom.html"), o = i.ItemView.extend({tagName: "div",className: "sku-atom",template: t.template(s),ui: {},events: {"click .js-remove-sku-atom": "removeSkuAtom"},initialize: function(e) {
            var t = this;
            t.setConfig(e)
        },onClose: function() {
        },onRender: function() {
        },serializeData: function() {
            var e = this, t = {};
            return this.model && (t = this.model.toJSON(), t = e.processData(t)), t
        },processData: function(e) {
            var t = n.request("goods_attr:get", "class_type"), i = "0" == t;
            return e.editable = i, e
        },onShow: function() {
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },removeSkuAtom: function() {
            var e = this;
            event.preventDefault(), event.stopPropagation();
            var t = e.model.collection;
            t.remove(e.model)
        },editSkuAtom: function(e) {
            e.preventDefault()
        }});
    return o
}), define("text!templates/sku_atom_list.html", [], function() {
    return '<div class="js-sku-atom-list sku-atom-list"></div>\n<a href="javascript:;" class="js-add-sku-atom add-sku">+添加</a>\n'
}), define("views/sku_atom_list", ["require", "underscore", "backbone", "marionette", "core/reqres", "core/event", "components/pop/pop", "core/utils", "commons/utils", "chosen", "views/sku_atom", "text!templates/sku_atom_list.html"], function(e) {
    {
        var t = e("underscore"), i = (e("backbone"), e("marionette")), n = e("core/reqres"), s = (e("core/event"), e("components/pop/pop"));
        e("core/utils"), e("commons/utils")
    }
    e("chosen");
    var o = e("views/sku_atom"), a = e("text!templates/sku_atom_list.html"), r = i.CompositeView.extend({template: t.template(a),itemView: o,itemViewContainer: ".js-sku-atom-list",ui: {addSkuAtom: ".js-add-sku-atom"},events: {"click @ui.addSkuAtom": "onAddSkuAtomClick"},collectionEvents: {reset: "render"},initialize: function(e) {
            var t = this;
            t.setConfig(e), t.parentModel = e.parentModel
        },onClose: function() {
        },onRender: function() {
        },onShow: function() {
        },toggleOpts: function(e) {
            var t = this;
            t.ui.addSkuAtom.toggle(e)
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },onAddSkuAtomClick: function(e) {
            var t = this;
            e.preventDefault();
            var i = $(e.target), o = t.parentModel.get("id"), a = t.parentModel.get("text"), r = n.request("atom_data:get", a);
            t.atomPop = s.initialize({type: "chosen",target: i,data: {id: o,atomData: r},callback: function(e) {
                    t.onChooseSkuAtom(e)
                }})
        },onChooseSkuAtom: function(e) {
            var i = this;
            if (t.isEmpty(e))
                return !1;
            var n = i.collection;
            t.each(e, function(e) {
                return n.checkChooseable(e) ? void n.add(e) : !1
            })
        }});
    return r
}), define("text!templates/sku_color.html", [], function() {
    return '<tr>\n<% _.each(color_groups, function(item, i) { %>\n    <td>\n        <h4 class="c-color-<%=i %>"><%=item.text %></h4>\n        <ul>\n            <% _.each(item.list, function(atom, j) { %>\n            <li>\n                <label class="checkbox inline" for="color_atom_<%=atom.id %>">\n                    <input class="js-color-chk" data-color-id="<%=atom.id %>" data-color-text="<%=atom.color %>" type="checkbox" id="color_atom_<%=atom.id %>">\n                    <%-atom.color %>\n                </label>\n            </li>\n            <% }); %>\n        </ul>\n    </td>\n    <% if((i + 1)%5 === 0) { %>\n    </tr><tr>\n    <% } %>\n<% }); %>\n</tr>\n<!-- <tr>\n    <td colspan="5">\n        <h4>自定义</h4>\n        <div id="custom-color-region"></div>\n    </td>\n</tr> -->\n'
}), define("views/sku_color", ["require", "underscore", "backbone", "core/reqres", "core/event", "components/pop/pop", "marionette", "views/sku_atom", "text!templates/sku_color.html", "collections/sku_atom_list", "views/sku_atom_list"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("core/reqres"), e("core/event"), e("components/pop/pop"), e("marionette")), n = e("views/sku_atom"), s = e("text!templates/sku_color.html"), o = (e("collections/sku_atom_list"), e("views/sku_atom_list")), a = i.Layout.extend({tagName: "table",className: "table-sku-color",template: t.template(s),itemView: n,itemViewContainer: ".js-sku-atom-list",ui: {customColorList: ".js-custom-list",addCustomColor: ".js-add-custom-color",customColorContainer: "tr:last-of-type"},regions: {customColorRegion: "#custom-color-region"},events: {"change .js-color-chk": "reverseUpdate","click @ui.addCustomColor": "onAddCustomColorClicked"},initialize: function(e) {
            var t = this;
            t.setConfig(e), t.initColorData()
        },onShow: function() {
            var e = this;
            e.checkedColor()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initColorData: function() {
            var e = this, t = window._global.color || {};
            e.colorGroups = t
        },initCustomColorView: function() {
            var e = this;
            e.customListView = new o({parentModel: e.model,collection: e.collection})
        },serializeData: function() {
            var e = this, t = {};
            return e.model && (t = this.model.toJSON(), t = e.processData(t)), t
        },processData: function(e) {
            var t = this;
            return e.color_groups = t.colorGroups, e
        },onRender: function() {
        },checkedColor: function() {
            var e = this, i = e.model.get("list"), n = [];
            t(i).each(function(t) {
                var i = 'input[data-color-text="' + t.text + '"]', s = e.$el.find(i);
                s.length > 0 ? s.prop("checked", !0) : n.push(t)
            }), e.customAtom = n
        },reverseUpdate: function(e) {
            var t = this;
            e.preventDefault();
            var i = $(e.target), n = i.prop("checked"), s = i.data("color-id"), o = i.data("color-text"), a = t.collection.get(s);
            n ? t.collection.add({id: s,text: o}) : a && t.collection.remove(a)
        }});
    return a
}), define("text!templates/sku_item.html", [], function() {
    return '<h3 class="sku-group-title">\n    <% if(editable == \'1\') { %>\n    <input type="hidden" name="sku_name" value="<%=id %>" class="js-sku-name">\n    <a class="js-remove-sku-group remove-sku-group">&times;</a>\n    <% } else { %>\n    <%-text %>\n    <% } %>\n</h3>\n<div class="js-sku-atom-container sku-group-cont"></div>\n'
}), define("views/sku_item", ["require", "underscore", "backbone", "marionette", "core/event", "core/reqres", "select2", "core/utils", "commons/utils", "backbone.modelbinder", "models/sku", "collections/sku_atom_list", "views/sku_atom_list", "views/sku_color", "text!templates/sku_item.html", "text!templates/sku_color.html"], function(e) {
    {
        var t = e("underscore"), i = (e("backbone"), e("marionette")), n = e("core/event"), s = e("core/reqres"), o = (e("select2"), e("core/utils"));
        e("commons/utils")
    }
    e("backbone.modelbinder");
    var a = (e("models/sku"), e("collections/sku_atom_list")), r = e("views/sku_atom_list"), l = e("views/sku_color"), c = e("text!templates/sku_item.html"), d = e("text!templates/sku_color.html"), u = i.Layout.extend({tagName: "div",className: "sku-sub-group",template: t.template(c),colorTemplate: t.template(d),ui: {skuNameEle: ".js-sku-name",skuGroupRemove: ".js-remove-sku-group",skuAtomContainer: ".js-sku-atom-container"},regions: {skuAtomRegion: ".js-sku-atom-container"},events: {"click @ui.skuGroupRemove": "removeSkuGroup"},initialize: function(e) {
            var t = this;
            t.setConfig(e), t.choosable = {}, t.skuNameList = s.request("sku_name_list:get") || []
        },onClose: function() {
            var e = this;
            e.skuAtomListView && e.skuAtomListView.close()
        },serializeData: function() {
            var e = this, t = {};
            return this.model && (t = this.model.toJSON(), t = e.processData(t)), t
        },processData: function(e) {
            return e.id || (e.id = -1), e
        },onRender: function() {
        },onShow: function() {
            var e = this;
            e.initSkuName(), e.initChoosable(), e.initSkuAtomListView()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initSkuName: function() {
            var e = this, t = e.model.get("editable");
            return t ? void e.initSkuNameSelect2() : !1
        },initSkuNameSelect2: function() {
            var e = this, i = s.request("select2_config:get") || {};
            t(i).extend({data: e.skuNameList}), e.ui.skuNameEle.select2(i).on("select2-opening", function() {
                n.trigger("chosen:hide")
            }).on("select2-selecting", function(t) {
                var i = t.object;
                e.selectSkuName(i, e, t)
            })
        },initSkuAtomListView: function() {
            var e = this, t = e.model, i = t.get("id");
            if (!i || -1 == i)
                return !1;
            var n = e.model.get("list") || [];
            e.skuAtomList = new a(n, {choosable: e.choosable}), e.listenTo(e.skuAtomList, "all", e.reverseUpdate);
            {
                var s = e.classType, o = t.get("text");
                t.get("editable")
            }
            "0" != s && "颜色" === o ? e.initColorListView() : e.initOtherListView()
        },initColorListView: function() {
            var e = this, t = new l({model: e.model,collection: e.skuAtomList});
            e.skuAtomRegion.show(t)
        },initOtherListView: function() {
            var e = this;
            e.skuAtomListView = new r({parentModel: e.model,collection: e.skuAtomList}), e.skuAtomRegion.show(e.skuAtomListView)
        },reverseUpdate: function() {
            var e = this, t = e.skuAtomList.toJSON();
            e.model.set({list: t}), e.model.collection.trigger("item:list:change")
        },selectSkuName: function(e, t, i) {
            var n = this, s = n.model.collection;
            return s.checkIsExist(e.text) ? (o.errorNotify("规格名不能相同。"), i.preventDefault(), i.stopPropagation(), !1) : (n.model.set("id", e.id), void (-1 == e.id ? n.createSkuName(e) : n.updateAtomListView(e)))
        },createSkuName: function(e) {
            var t = this, i = window._global.url.www + "/showcase/WCGoodsSkuTree/skuTree.json", n = {text: e.text};
            $.ajax({url: i,type: "POST",dataType: "json",timeout: 8e3,cache: !1,data: n,success: function(e) {
                    0 === e.code ? t.onCreateSkuNameSuccess(e.data, n) : o.errorNotify(e.msg || "新增规格类型失败。")
                },error: function() {
                },complete: function() {
                }})
        },onCreateSkuNameSuccess: function(e, t) {
            var i = this, n = {id: Number(e),text: t.text};
            i.model.set(n), i.ui.skuNameEle.select2("data", n), i.ui.skuNameEle.select2("close"), window._global.skuTree.push({_id: n.id,text: n.text}), i.updateAtomListView(t)
        },updateAtomListView: function(e) {
            var t = this;
            t.skuAtomListView || t.initSkuAtomListView(), t.updateChoosable(), t.resetAtomListData(e)
        },initChoosable: function() {
            var e = this;
            e.updateChoosable()
        },getChoosable: function() {
            var e = this, i = {}, n = {}, o = e.model.get("id"), a = s.request("goods_property:get") || [];
            return o && !t.isEmpty(a) && (i = t(a).findWhere({class_id: o})), n = e.processChoosable(i)
        },processChoosable: function(e) {
            var i = {data: [],count: 0};
            return !e || t.isEmpty(e.option) ? i : (t(e.option).each(function(e) {
                -1 === e.option_value.indexOf("自定义") ? i.data.push(e) : i.count += 1
            }), i)
        },updateChoosable: function() {
            var e = this;
            e.choosable = e.getChoosable()
        },resetAtomListData: function(e) {
            var i = this;
            t(e).extend({list: []}), i.model.set(e), i.skuAtomList.reset([])
        },removeSkuGroup: function(e) {
            var t = this;
            e.preventDefault(), e.stopPropagation();
            var i = t.model.collection;
            i.remove(t.model)
        }});
    return u
}), define("views/sku_empty", ["require", "underscore", "backbone", "marionette"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = i.ItemView.extend({tagName: "div",className: "sku-sub-group",template: t.template('<h3 class="sku-group-title">没有商品规格</h3>')});
    return n
}), define("text!templates/sku.html", [], function() {
    return '<div class="js-sku-list-container"></div>\n<div class="js-sku-group-opts sku-sub-group">\n    <h3 class="sku-group-title">\n        <button type="button" class="js-add-sku-group btn">添加规格项目</button>\n    </h3>\n</div>\n'
}), define("views/sku", ["require", "underscore", "backbone", "marionette", "core/event", "core/reqres", "core/utils", "commons/utils", "backbone.modelbinder", "chosen", "models/sku_item", "views/sku_item", "views/sku_empty", "text!templates/sku.html"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = e("core/event"), s = e("core/reqres"), o = (e("core/utils"), e("commons/utils"));
    e("backbone.modelbinder"), e("chosen");
    var a = e("models/sku_item"), r = e("views/sku_item"), l = (e("views/sku_empty"), e("text!templates/sku.html")), c = i.CompositeView.extend({tagName: "div",className: "sku-group",template: t.template(l),itemView: r,itemViewContainer: ".js-sku-list-container",ui: {skuGroupOpts: ".js-sku-group-opts",skuGroupAdd: ".js-add-sku-group"},events: {"click @ui.skuGroupAdd": "addSkuGroup"},collectionEvents: {"add remove": "checkAndReverseUpdate"},initialize: function(e) {
            var t = this;
            t.setConfig(e), t.goodsModel = e.goodsModel, t.editable = "0" == t.goodsModel.get("class_type"), t.initCustomCount(), t.initSkuName(), t.listenTo(n, "sku_name:change", t.onSkuNameChange), t.listenTo(t.collection, "item:list:change", t.reverseUpdate)
        },onRender: function() {
            var e = this;
            e.toggleSkuGroupOpts()
        },onShow: function() {
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initCustomCount: function() {
            var e = this, t = e.goodsModel.get("class_type");
            e.maxSize = 3, e.customMaxSize = "0" == t ? e.maxSize : 1
        },initSkuName: function() {
            var e = this;
            e.initSelect2Config(), e.initSkuNameList()
        },initSelect2Config: function() {
            var e = {multiple: !1,placeholder: "请选择...",createSearchChoice: function(e, t) {
                    return 0 === $(t).filter(function() {
                        return 0 === this.text.localeCompare(e)
                    }).length ? {id: -1,text: e} : void 0
                },maximumInputLength: 4,width: 100};
            s.setHandler("select2_config:get", function() {
                return e
            })
        },initSkuNameList: function() {
            var e = this;
            e.skuNameList = [];
            var i = window._global.skuTree;
            t.each(i, function(t) {
                e.skuNameList.push({id: t._id,text: t.text || t.name})
            }), s.setHandler("sku_name_list:get", function() {
                return e.skuNameList
            })
        },addSkuGroup: function() {
            var e = this, t = new a;
            e.collection.add(t)
        },onAfterItemAdded: function(e) {
            var t = this;
            return t.isEmpty() ? !1 : void e.ui.skuNameEle.select2("open")
        },checkMaxSize: function() {
            var e = this, t = e.collection.size(), i = e.collection.where({editable: "1"}), n = i.length || 0, s = n < e.customMaxSize && t < e.maxSize;
            return s
        },toggleSkuGroupOpts: function() {
            var e = this, t = e.checkMaxSize();
            t ? e.ui.skuGroupOpts.show() : e.ui.skuGroupOpts.hide()
        },checkAndReverseUpdate: function() {
            var e = this;
            e.toggleSkuGroupOpts(), e.reverseUpdate()
        },reverseUpdate: function() {
            var e = this, i = e.goodsModel, n = e.collection.toJSON();
            t(n).each(function(e, t) {
                var n = t + 1, s = "sku_name_" + n, o = s + "_value", a = "undefined" == typeof e.editable ? "1" : e.editable;
                i.set(s, {id: e.id,text: e.text,editable: a}, {silent: !0}), i.set(o, e.list, {silent: !0})
            }), e.removeUnnecessaryKeys(n)
        },removeUnnecessaryKeys: function(e) {
            var t = this, i = e.length;
            o.removeSkuKeyValue(t.goodsModel, i)
        }});
    return c
}), define("text!templates/stock.html", [], function() {
    return ""
}), define("text!templates/thead.html", [], function() {
    return '<thead>\n    <tr>\n        <% _.each(thead, function(item, index) {\n            if(item.list && item.list.length > 0) { %>\n            <th class="ta-c"><%=item.text %></th>\n        <% }}); %>\n        <th class="th-price">价格（元）</th>\n        <th class="th-stock">库存</th>\n        <th class="th-code">商品编码</th>\n        <th class="ta-r">销量</th>\n    </tr>\n</thead>\n'
}), define("text!templates/tbody.html", [], function() {
    return '<tbody>\n    <% _.each(tbody, function(item, idx1) { %>\n    <tr>\n        <% _.each(item.list, function(atom, idx2) { %>\n        <td class="ta-c"><%=atom.text %></td>\n        <% }); %>\n        <td><input type="text" name="" class="input-mini"></td>\n        <td><input type="text" name="" class="input-mini"></td>\n        <td><input type="text" name="" class="input-small"></td>\n        <td class="ta-r">12345</td>\n    </tr>\n    <% }); %>\n</tbody>\n'
}), define("text!templates/td.html", [], function() {
    return '<td>\n    <input type="text" name="sku_price" class="js-price input-mini" value="<%=price %>" />\n</td>\n<td><input type="text" name="stock_num" class="js-stock-num input-mini" value="<%=stock_num %>" /></td>\n<td><input type="text" name="code" class="js-code input-small" value="<%=code %>" /></td>\n<td class="ta-r"><%=sold_num || 0 %></td>\n'
}), define("views/stock", ["require", "underscore", "backbone", "marionette", "core/event", "core/reqres", "commons/utils", "backbone.modelbinder", "text!templates/stock.html", "text!templates/thead.html", "text!templates/tbody.html", "text!templates/td.html"], function(e) {
    {
        var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = e("core/event"), o = e("core/reqres");
        e("commons/utils")
    }
    e("backbone.modelbinder");
    var a = e("text!templates/stock.html"), r = e("text!templates/thead.html"), l = e("text!templates/tbody.html"), c = e("text!templates/td.html"), d = n.ItemView.extend({tagName: "table",className: "table-sku-stock",template: t.template(a),theadTemplate: t.template(r),tbodyTemplate: t.template(l),tdTemplate: t.template(c),events: {"blur .js-price": "onAtomPriceBlur","input .js-price": "onAtomPriceInput","input .js-stock-num": "onAtomStockInput","input .js-code": "reverseUpdateStock"},initialize: function(e) {
            var t = this;
            t.skuList = e.skuList, t.setConfig(e), t.stockBackup = t.model.get("stock"), t._modelBinder = new i.ModelBinder, t.setupStockResp(), t.setupPriceResp(), t.setupValidateResp(), t.listenTo(t.skuList, "add", t.rebuildStockData), t.listenTo(t.skuList, "remove", t.rebuildStockData), t.listenTo(t.skuList, "change", t.updateStockData)
        },onShow: function() {
            var e = this;
            e.showStockView()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },setupValidateResp: function() {
            var e = this;
            e.errorLen = 0, o.setHandler("stock:validate", function() {
                var t = e.model.get("shop_method");
                return "0" == t ? e.resetValidate() : e.validateStockData(), e.errorLen
            })
        },setupStockResp: function() {
            var e = this;
            e.isStockModulShow = !1, o.setHandler("stock_module:is_show", function() {
                return e.isStockModulShow
            })
        },setupPriceResp: function() {
            var e = this;
            e.minPrice = .01, o.setHandler("min_price:get", function() {
                return e.minPrice
            })
        },rebuildStockData: function() {
            var e = this, t = e.generateStockData();
            e.model.set({stock: t}, {silent: !0}), e.showStockView()
        },updateStockData: function() {
            var e = this, t = e.generateStockData();
            t = e.recoverBackup(t), e.model.set({stock: t}, {silent: !0}), e.showStockView()
        },generateStockData: function() {
            var e = this, i = [], n = e.skuList.toJSON();
            return t(n).each(function(n, s) {
                i = t.isEmpty(i) ? e.initSkuData(n, s) : e.appendSkuInfo(i, n, s)
            }), i
        },recoverBackup: function(e) {
            var i = this, n = i.stockBackup, s = i.getFilterKeys();
            return t(e).each(function(i, o) {
                var a = t(i).pick(s), r = t(n).findWhere(a);
                r && (e[o].stock_num = r.stock_num, e[o].price = r.price, e[o].code = r.code, e[o].sold_num = r.sold_num)
            }), e
        },getFilterKeys: function() {
            for (var e = this, t = [], i = e.skuList.size(), n = 1; i >= n; n++) {
                var s = "v" + n + "_id";
                t.push(s)
            }
            return t
        },updateStockBackup: function(e) {
            var t = this;
            t.stockBackup = e
        },initSkuData: function(e, i) {
            var n = [], s = {};
            return t(e.list).each(function(t) {
                var o = {id: 0,price: "",stock_num: 0,sold_num: "",code: ""}, a = i + 1;
                o["k" + a + "_id"] = e.id, o["k" + a] = e.text, o["v" + a + "_id"] = t.id, o["v" + a] = t.text;
                var r = "v" + a + "_id";
                s[r] = t.id, n.push(o)
            }), n
        },appendSkuInfo: function(e, i, n) {
            if (t.isEmpty(i.list))
                return e;
            var s = [], o = {};
            return t(e).each(function(e) {
                t(i.list).each(function(a) {
                    var r = {};
                    t(e).each(function(e, t) {
                        r[t] = e
                    });
                    var l = n + 1;
                    r["k" + l + "_id"] = i.id, r["k" + l] = i.text, r["v" + l + "_id"] = a.id, r["v" + l] = a.text;
                    var c = "v" + l + "_id";
                    o[c] = a.id, s.push(r)
                })
            }), s
        },showStockView: function() {
            var e = this, i = e.skuList;
            return 0 === i.size() ? (e.isStockModulShow = !1, s.trigger("stock_module:hide"), !1) : (e.skuData = e.generateSkuData(), t(e.skuData).isEmpty() ? (e.isStockModulShow = !1, s.trigger("stock_module:hide"), !1) : void e.renderTable())
        },renderTable: function() {
            var e = this, t = "";
            t += e.renderHeader(), t += e.renderBody(), e.$el.html(t), s.trigger("stock_module:show"), e.isStockModulShow = !0
        },renderHeader: function() {
            var e = this, t = {thead: e.skuList.toJSON()}, i = e.theadTemplate(t);
            return i
        },renderBody: function() {
            var e = this, i = e.skuData;
            if (t.isEmpty(i))
                return !1;
            e.combine = e.calcCombine(i);
            var n = e.generateRows();
            return n
        },generateRows: function() {
            var e = this;
            return e.TableHtml = "<tbody>", e.outputTrTag = !1, e.trIndex = 0, e.printRow(0, e.outputTrTag), e.TableHtml += "</tbody>", e.TableHtml
        },generateSkuData: function() {
            var e = this, t = [];
            return e.skuList.each(function(e) {
                var i = e.get("list");
                i && i.length > 0 && t.push(i)
            }), t
        },calcCombine: function(e) {
            for (var t = [], i = e.length, n = 0; i > n; n++) {
                t[n] = 1;
                for (var s = n + 1; i > s; s++)
                    t[n] = t[n] * e[s].length
            }
            return t
        },printRow: function(e, t) {
            var i = this, n = i.skuData, s = n.length;
            if (e === s)
                return !1;
            {
                var o = n[e];
                i.skuList.toJSON()
            }
            o.forEach(function(n) {
                t || (i.TableHtml += "<tr>", t = !0);
                var o = '<td data-atom-id="' + n.id + '" rowspan="' + i.combine[e] + '">' + n.text + "</td>";
                if (i.TableHtml += o, e === s - 1) {
                    var a = i.getAtomStockData(i.trIndex);
                    i.TableHtml += i.tdTemplate(a) + "</tr>", i.trIndex += 1, t = !1
                }
                var r = e + 1;
                i.printRow(r, t)
            })
        },getAtomStockData: function(e) {
            var t = this, i = t.model.get("stock"), n = i[e];
            return n = n || {price: "",stock_num: "",sold_num: 0,code: ""}
        },reverseUpdateStock: function(e) {
            var t = this, i = $(e.target), n = i.attr("name");
            n = "sku_price" === n ? "price" : n;
            var s = $.trim(i.val()), o = t.$("tr"), a = o.index(i.parents("tr")) - 1, r = t.model.get("stock");
            r[a] || (r = t.generateStockData(t.skuList)), r[a][n] = s, t.updateStockBackup(r), t.model.set({stock: r}, {silent: !0})
        },onAtomPriceBlur: function(e) {
            var t = this, i = $(e.target), n = Number(i.val());
            i.val(n.toFixed(2, 10)), t.validAtomPrice(i)
        },validAtomPrice: function(e) {
            var t = this, i = e.val(), n = e.parents("td"), s = n.find(".error-message"), o = t.validatePrice(i);
            o ? (0 === s.length ? (s = $('<div class="error-message"></div>'), n.append(s.html(o))) : s.html(o), n.addClass("manual-valid-error"), t.errorLen += 1) : (s.remove(), n.removeClass("manual-valid-error", function() {
                t.errorLen -= 1
            }))
        },validAllPrice: function() {
            var e = this;
            e.$(".js-price").each(function(t, i) {
                var n = $(i);
                e.validAtomPrice(n)
            })
        },validatePrice: function(e) {
            if (!e)
                return "价格不能为空";
            var t = Number(e);
            return isNaN(t) ? "请输入一个数字" : .01 > t ? "价格最低为 0.01" : !1
        },resetValidate: function() {
            var e = this;
            e.$(".manual-valid-error").removeClass("manual-valid-error"), e.$(".error-message").hide(), e.errorLen = 0
        },validateStockData: function() {
            var e = this;
            e.errorLen = 0, e.isStockModulShow && e.validAllPrice()
        },onAtomPriceInput: function(e) {
            var t = this;
            t.updatePrice(e), t.reverseUpdateStock(e)
        },onAtomStockInput: function(e) {
            var t = this;
            t.updateTotalStock(e), t.reverseUpdateStock(e)
        },calcPrice: function() {
            var e = this, i = [], n = e.$(".js-price");
            n.each(function(e, n) {
                var s = $.trim($(n).val());
                if (t.isEmpty(s))
                    return !1;
                var o = Number(s);
                return isNaN(o) ? !1 : void i.push(o)
            });
            var s = t.isEmpty(i) ? 0 : t.min(i);
            return s
        },updatePrice: function() {
            var e = this, t = e.calcPrice();
            t = Number(t).toFixed(2, 10) + "", e.minPrice = t, e.model.set({price: t})
        },calcTotalStock: function() {
            var e = this, t = 0, i = e.$(".js-stock-num");
            return i.each(function(e, i) {
                var n = Number($(i).val());
                t += n
            }), t
        },updateTotalStock: function() {
            var e = this, t = e.calcTotalStock();
            e.model.set({total_stock: t})
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        }});
    return d
}), define("text!templates/sku_stock_info.html", [], function() {
    return '<div class="info-group-title vbox">\n    <div class="group-inner">库存/规格</div>\n</div>\n<div class="info-group-cont vbox">\n    <div class="group-inner">\n        <div class="js-goods-sku control-group">\n            <label class="control-label">商品规格：</label>\n            <div id="sku-region" class="controls">\n            </div>\n        </div>\n        <div class="js-goods-stock control-group">\n            <label class="control-label">商品库存：</label>\n            <div id="stock-region" class="controls sku-stock">\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label"><em class="required">*</em>总库存：</label>\n            <div class="controls">\n                <input type="text" class="input-small" name="total_stock" value="<%=total_stock %>" />\n                <label class="checkbox inline">\n                    <input type="checkbox" name="hide_stock" value="<%= hide_stock %>" <% if (hide_stock == \'1\') { %> checked<% } %>>页面不显示商品库存\n                </label>\n                <p class="help-desc">总库存为 0 时，会上架到『已售罄的商品』列表里</p>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label">商家编码：</label>\n            <div class="controls">\n                <input type="text" class="input-small" name="goods_no" value="<%=goods_no %>" />\n                <a style="display: none;" href="javascript:;" class="js-help-notes circle-help">?</a>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("views/sku_stock_info", ["require", "underscore", "backbone", "marionette", "core/event", "components/validation/validate", "commons/utils", "backbone.modelbinder", "chosen", "collections/sku_list", "views/sku", "views/stock", "text!templates/sku_stock_info.html"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = e("core/event"), o = (e("components/validation/validate"), e("commons/utils"));
    e("backbone.modelbinder"), e("chosen");
    var a = e("collections/sku_list"), r = e("views/sku"), l = e("views/stock"), c = e("text!templates/sku_stock_info.html"), d = n.Layout.extend({tagName: "div",className: "goods-info-group-inner",template: t.template(c),ui: {goodsSkuBlock: ".js-goods-sku",goodsStockBlock: ".js-goods-stock",totalStockTxt: '[name="total_stock"]'},events: {},regions: {skuRegion: "#sku-region",stockRegion: "#stock-region"},_modelBinder: void 0,initialize: function(e) {
            var t = this;
            t.setConfig(e), t._modelBinder = new i.ModelBinder, t.listenTo(t.model, "change:shop_method", t.onShopMethodChanged), t.listenTo(s, "stock_module:hide", t.hideStockModule), t.listenTo(s, "stock_module:show", t.showStockModule), t.listenTo(s, "sku_stock:update", t.updateSkuStock)
        },onClose: function() {
            var e = this;
            e._modelBinder.unbind()
        },onRender: function() {
            var e = this, t = e.model.get("shop_method");
            "0" == t && e.hideSelf()
        },onShow: function() {
            var e = this;
            e.initDataBindings(), e.initSkuView(), e.initStockView()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initDataBindings: function() {
            var e = this, t = {total_stock: '[name="total_stock"]',hide_stock: {selector: '[name="hide_stock"]',converter: o.booleanConverter},goods_no: '[name="goods_no"]'};
            e._modelBinder.bind(e.model, e.el, t)
        },initSkuView: function() {
            var e = this;
            o.fnCallCount("initSkuView");
            var t = e.getSkuData() || [], i = e.skuList = new a(t);
            e.skuView = new r({collection: i,goodsModel: e.model}), e.skuRegion.show(e.skuView)
        },getSkuData: function() {
            for (var e = this, t = "sku_name_", i = [], n = e.model.toJSON(), s = 1; 6 > s; s++) {
                var o = t + s, a = o + "_value", r = n[o], l = n[a] || [];
                if (r) {
                    var c = "undefined" == typeof r.editable ? "1" : r.editable;
                    i.push({id: r.id,text: r.text,editable: c,list: l})
                }
            }
            return i
        },initStockView: function() {
            var e = this;
            e.stockView = new l({model: e.model,skuList: e.skuList}), e.stockRegion.show(e.stockView)
        },updateSkuStock: function() {
            var e = this, t = e.getSkuData() || [];
            e.skuList.reset(t), e.model.set("stock", [])
        },openStockEdit: function() {
            var e = this;
            e.ui.goodsStockBlock.removeClass("hide")
        },closeStockEdit: function() {
            var e = this;
            e.ui.goodsStockBlock.addClass("hide")
        },onShopMethodChanged: function(e, t) {
            var i = this;
            "0" == t ? i.hideSelf() : i.showSelf()
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        },hideStockModule: function() {
            var e = this;
            e.ui.goodsStockBlock.hide(), e.ui.totalStockTxt.prop("readonly", !1)
        },showStockModule: function() {
            var e = this;
            e.ui.goodsStockBlock.show(), e.ui.totalStockTxt.prop("readonly", !0)
        }});
    return d
}), define("components/paipai_region/region_data", ["require"], function() {
    function e() {
        return window._regionMap || (window._regionMap = {0: ["北京", 0, {4e4: ["北京市", 1, {40004: ["延庆县"],40005: ["密云县"],40008: ["通州区"],41e3: ["东城区"],41001: ["西城区"],41004: ["朝阳区"],41005: ["丰台区"],41006: ["石景山区"],41007: ["海淀区"],41008: ["门头沟区"],41009: ["房山区"],41010: ["顺义区"],41011: ["昌平区"],41012: ["大兴区"],41013: ["怀柔区"],41014: ["平谷区"]}]}],1: ["天津", 0, {100: ["天津市", 1, {101: ["宝坻区"],102: ["静海县"],103: ["武清区"],104: ["宁河县"],105: ["蓟县"],41015: ["和平区"],41016: ["河东区"],41017: ["河西区"],41018: ["南开区"],41019: ["河北区"],41020: ["红桥区"],41024: ["东丽区"],41025: ["西青区"],41026: ["津南区"],41027: ["北辰区"],42296: ["滨海新区"]}]}],2: ["上海", 0, {200: ["上海市", 1, {203: ["青浦区"],204: ["嘉定区"],205: ["宝山区"],206: ["奉贤区"],208: ["松江区"],210: ["金山区"],41294: ["黄浦区"],41296: ["徐汇区"],41297: ["长宁区"],41298: ["静安区"],41299: ["普陀区"],41300: ["闸北区"],41301: ["虹口区"],41302: ["杨浦区"],41303: ["闵行区"],41304: ["浦东新区"],41305: ["崇明县"]}]}],3: ["重庆", 0, {300: ["重庆市", 1, {2101: ["涪陵区"],2102: ["北碚区"],2103: ["江北区"],2107: ["永川市"],2131: ["綦江区"],2132: ["长寿区"],2136: ["江津市"],2137: ["合川区"],2138: ["潼南县"],2139: ["铜梁县"],2141: ["荣昌县"],2142: ["大足县"],41788: ["万州区"],41789: ["渝中区"],41790: ["大渡口区"],41791: ["沙坪坝区"],41792: ["九龙坡区"],41793: ["南岸区"],41795: ["渝北区"],41796: ["巴南区"],41797: ["黔江区"],41798: ["璧山县"],41799: ["梁平县"],41800: ["城口县"],41801: ["丰都县"],41802: ["垫江县"],41803: ["武隆县"],41804: ["忠县"],41805: ["开县"],41806: ["云阳县"],41807: ["奉节县"],41808: ["巫山县"],41809: ["巫溪县"],41810: ["石柱土家族自治县"],41811: ["秀山土家族苗族自治县"],41812: ["酉阳土家族苗族自治县"],41813: ["彭水苗族土家族自治县"],41814: ["南川市"]}]}],4: ["河北", 0, {400: ["石家庄市", 0, {444: ["藁城市"],445: ["栾城县"],446: ["正定县"],448: ["井陉县"],4131: ["元氏县"],4132: ["新乐市"],4133: ["无极县"],4134: ["深泽县"],4135: ["辛集市"],4137: ["赵县"],4138: ["赞皇县"],4139: ["高邑县"],4140: ["平山县"],4141: ["灵寿县"],4142: ["行唐县"],41028: ["长安区"],41029: ["桥东区"],41030: ["桥西区"],41031: ["新华区"],41032: ["井陉矿区"],41033: ["裕华区"],41034: ["晋州市"],41035: ["鹿泉市"]}],401: ["邯郸市", 0, {402: ["武安市"],403: ["临漳县"],404: ["磁县"],405: ["涉县"],406: ["成安县"],407: ["永年县"],408: ["鸡泽县"],409: ["曲周县"],411: ["馆陶县"],412: ["大名县"],413: ["魏县"],414: ["广平县"],415: ["肥乡县"],41041: ["邯山区"],41042: ["丛台区"],41043: ["复兴区"],41044: ["峰峰矿区"],41045: ["邯郸县"],41046: ["邱县"]}],459: ["保定市", 0, {417: ["容城县"],418: ["易县"],419: ["涞源县"],420: ["博野县"],421: ["安国市"],422: ["定州市"],423: ["曲阳县"],424: ["唐县"],425: ["阜平县"],449: ["徐水县"],451: ["雄县"],452: ["安新县"],453: ["高阳县"],454: ["蠡县"],455: ["望都县"],456: ["顺平县"],457: ["满城县"],458: ["清苑县"],4143: ["涞水县"],4144: ["定兴县"],4145: ["涿州市"],41050: ["新市区"],41051: ["北市区"],41052: ["南市区"],41053: ["高碑店市"]}],461: ["张家口市", 0, {442: ["尚义县"],443: ["康保县"],460: ["宣化县"],462: ["怀安县"],463: ["万全县"],464: ["张北县"],465: ["崇礼县"],466: ["赤城县"],467: ["怀来县"],468: ["涿鹿县"],469: ["蔚县"],470: ["阳原县"],41054: ["桥东区"],41055: ["桥西区"],41056: ["宣化区"],41057: ["下花园区"],41058: ["沽源县"]}],472: ["承德市", 0, {471: ["兴隆县"],473: ["承德县"],474: ["隆化县"],475: ["围场满族蒙古族自治县"],476: ["平泉县"],477: ["宽城满族自治县"],478: ["丰宁满族自治县"],479: ["滦平县"],41059: ["双桥区"],41060: ["双滦区"],41061: ["鹰手营子矿区"]}],490: ["唐山市", 0, {480: ["滦南县"],481: ["丰南区"],482: ["唐海县"],483: ["遵化市"],484: ["滦县"],485: ["乐亭县"],486: ["丰润区"],487: ["玉田县"],488: ["迁西县"],489: ["迁安市"],2203: ["路南区"],41036: ["路北区"],41037: ["古冶区"],41038: ["开平区"]}],491: ["廊坊市", 0, {492: ["永清县"],493: ["霸州市"],494: ["大城县"],495: ["文安县"],496: ["固安县"],497: ["香河县"],498: ["大厂回族自治县"],499: ["三河市"],41065: ["安次区"],41066: ["广阳区"]}],4108: ["沧州市", 0, {4100: ["黄骅市"],4101: ["海兴县"],4102: ["盐山县"],4103: ["孟村回族自治县"],4104: ["南皮县"],4105: ["东光县"],4106: ["吴桥县"],4107: ["泊头市"],4109: ["青县"],4110: ["河间市"],4111: ["肃宁县"],4112: ["任丘市"],4113: ["献县"],41062: ["新华区"],41063: ["运河区"],41064: ["沧县"]}],4114: ["衡水市", 0, {4115: ["饶阳县"],4116: ["武强县"],4117: ["武邑县"],4118: ["阜城县"],4119: ["景县"],4120: ["故城县"],4121: ["枣强县"],4124: ["安平县"],41067: ["桃城区"],41068: ["冀州市"],41069: ["深州市"]}],4125: ["邢台市", 0, {426: ["临西县"],427: ["内丘县"],428: ["临城县"],429: ["柏乡县"],430: ["宁晋县"],431: ["隆尧县"],432: ["巨鹿县"],433: ["新河县"],434: ["南宫市"],435: ["清河县"],436: ["威县"],437: ["广宗县"],438: ["平乡县"],439: ["南和县"],440: ["任县"],441: ["沙河市"],41047: ["桥东区"],41048: ["桥西区"],41049: ["邢台县"]}],4130: ["秦皇岛市", 0, {4126: ["青龙满族自治县"],4127: ["昌黎县"],4128: ["卢龙县"],4129: ["抚宁县"],4146: ["北戴河区"],41039: ["海港区"],41040: ["山海关区"]}]}],5: ["河南", 0, {500: ["郑州市", 0, {501: ["上街区"],585: ["荥阳市"],5105: ["新郑市"],5114: ["登封市"],5126: ["中牟县"],5130: ["巩义市"],41529: ["中原区"],41530: ["二七区"],41531: ["管城回族区"],41532: ["金水区"],41533: ["惠济区"],41534: ["新密市"]}],502: ["安阳市", 0, {503: ["安阳县"],576: ["汤阴县"],578: ["内黄县"],579: ["滑县"],41551: ["文峰区"],41552: ["北关区"],41553: ["殷都区"],41554: ["龙安区"],41555: ["林州市"]}],504: ["新乡市", 0, {505: ["新乡县"],508: ["延津县"],509: ["原阳县"],510: ["获嘉县"],511: ["卫辉市"],512: ["辉县市"],513: ["长垣县"],41559: ["红旗区"],41560: ["卫滨区"],41561: ["凤泉区"],41562: ["牧野区"],41563: ["封丘县"]}],506: ["商丘市", 0, {561: ["虞城县"],562: ["夏邑县"],563: ["永城市"],564: ["柘城县"],565: ["宁陵县"],567: ["民权县"],41579: ["梁园区"],41580: ["睢阳区"],41581: ["睢县"]}],515: ["许昌市", 0, {516: ["许昌县"],517: ["长葛市"],518: ["鄢陵县"],519: ["禹州市"],590: ["襄城县"],41571: ["魏都区"]}],521: ["平顶山市", 0, {520: ["舞钢市"],589: ["郏县"],591: ["叶县"],592: ["鲁山县"],593: ["宝丰县"],594: ["汝州市"],41547: ["新华区"],41548: ["卫东区"],41549: ["石龙区"],41550: ["湛河区"]}],522: ["信阳市", 0, {5106: ["罗山县"],5107: ["息县"],5108: ["潢川县"],5109: ["光山县"],5110: ["新县"],5111: ["淮滨县"],5112: ["固始县"],5113: ["商城县"],41582: ["浉河区"],41583: ["平桥区"]}],524: ["南阳市", 0, {560: ["西峡县"],5115: ["淅川县"],5116: ["方城县"],5117: ["社旗县"],5118: ["唐河县"],5119: ["新野县"],5120: ["邓州市"],5121: ["镇平县"],5122: ["南召县"],5123: ["桐柏县"],5124: ["内乡县"],41577: ["宛城区"],41578: ["卧龙区"]}],528: ["开封市", 0, {526: ["兰考县"],527: ["杞县"],529: ["开封县"],530: ["通许县"],531: ["尉氏县"],41535: ["龙亭区"],41536: ["顺河回族区"],41537: ["鼓楼区"],41538: ["禹王台区"],41539: ["金明区"]}],534: ["洛阳市", 0, {532: ["汝阳县"],533: ["嵩县"],535: ["孟津县"],536: ["偃师市"],537: ["伊川县"],538: ["宜阳县"],539: ["新安县"],541: ["洛宁县"],41540: ["老城区"],41541: ["西工区"],41542: ["廛河回族区"],41543: ["涧西区"],41544: ["吉利区"],41545: ["洛龙区"],41546: ["栾川县"]}],544: ["济源市"],546: ["焦作市", 0, {542: ["温县"],543: ["沁阳市"],545: ["修武县"],548: ["博爱县"],549: ["武陟县"],41564: ["解放区"],41565: ["中站区"],41566: ["马村区"],41567: ["山阳区"],41568: ["孟州市"]}],551: ["驻马店市", 0, {550: ["泌阳县"],552: ["遂平县"],553: ["汝南县"],554: ["确山县"],555: ["西平县"],556: ["上蔡县"],557: ["平舆县"],558: ["新蔡县"],559: ["正阳县"],41585: ["驿城区"]}],571: ["鹤壁市", 0, {568: ["浚县"],569: ["淇县"],41556: ["鹤山区"],41557: ["山城区"],41558: ["淇滨区"]}],572: ["漯河市", 0, {587: ["舞阳县"],588: ["郾城区"],41572: ["源汇区"],41573: ["召陵区"],41574: ["临颍县"]}],584: ["濮阳市", 0, {580: ["台前县"],581: ["范县"],582: ["南乐县"],583: ["清丰县"],41569: ["华龙区"],41570: ["濮阳县"]}],596: ["周口市", 0, {595: ["鹿邑县"],597: ["西华县"],598: ["淮阳县"],599: ["郸城县"],5100: ["沈丘县"],5101: ["项城市"],5102: ["扶沟县"],5103: ["太康县"],5104: ["商水县"],41584: ["川汇区"]}],5131: ["三门峡市", 0, {5125: ["卢氏县"],5127: ["渑池县"],5128: ["义马市"],5129: ["灵宝市"],41575: ["湖滨区"],41576: ["陕县"]}]}],6: ["黑龙江", 0, {600: ["哈尔滨市", 0, {612: ["通河县"],613: ["阿城区"],614: ["尚志市"],615: ["延寿县"],616: ["双城市"],645: ["依兰县"],667: ["呼兰区"],671: ["宾县"],672: ["木兰县"],673: ["五常市"],674: ["巴彦县"],679: ["方正县"],41227: ["道里区"],41228: ["南岗区"],41229: ["道外区"],41230: ["香坊区"],41232: ["平房区"],41233: ["松北区"]}],601: ["齐齐哈尔市", 0, {622: ["克山县"],623: ["拜泉县"],624: ["依安县"],625: ["龙江县"],626: ["讷河市"],627: ["泰来县"],629: ["甘南县"],630: ["富裕县"],669: ["克东县"],41234: ["龙沙区"],41235: ["建华区"],41236: ["铁锋区"],41237: ["昂昂溪区"],41238: ["富拉尔基区"],41239: ["碾子山区"],41240: ["梅里斯达斡尔族区"]}],602: ["牡丹江市", 0, {634: ["林口县"],635: ["穆棱市"],636: ["东宁县"],637: ["海林市"],638: ["宁安市"],639: ["绥芬河市"],41287: ["东安区"],41288: ["阳明区"],41289: ["爱民区"],41290: ["西安区"]}],603: ["佳木斯市", 0, {643: ["桦川县"],644: ["汤原县"],648: ["桦南县"],668: ["富锦市"],670: ["抚远县"],676: ["同江市"],41280: ["向阳区"],41281: ["前进区"],41282: ["东风区"],41283: ["郊区"]}],604: ["绥化市", 0, {617: ["肇东市"],651: ["明水县"],652: ["庆安县"],653: ["海伦市"],654: ["安达市"],657: ["绥棱县"],658: ["望奎县"],659: ["兰西县"],660: ["青冈县"],41293: ["北林区"]}],605: ["黑河市", 0, {662: ["北安市"],663: ["孙吴县"],664: ["逊克县"],666: ["嫩江县"],41291: ["爱辉区"],41292: ["五大连池市"]}],610: ["伊春市", 0, {677: ["嘉荫县"],678: ["铁力市"],41264: ["伊春区"],41265: ["南岔区"],41266: ["友好区"],41267: ["西林区"],41268: ["翠峦区"],41269: ["新青区"],41270: ["美溪区"],41271: ["金山屯区"],41272: ["五营区"],41273: ["乌马河区"],41274: ["汤旺河区"],41275: ["带岭区"],41276: ["乌伊岭区"],41277: ["红星区"],41278: ["上甘岭区"]}],611: ["大庆市", 0, {621: ["林甸县"],655: ["肇州县"],656: ["肇源县"],41258: ["萨尔图区"],41259: ["龙凤区"],41260: ["让胡路区"],41261: ["红岗区"],41262: ["大同区"],41263: ["杜尔伯特蒙古族自治县"]}],618: ["鸡西市", 0, {632: ["鸡东县"],633: ["密山市"],640: ["虎林市"],41241: ["鸡冠区"],41242: ["恒山区"],41243: ["滴道区"],41244: ["梨树区"],41245: ["城子河区"],41246: ["麻山区"]}],619: ["鹤岗市", 0, {646: ["萝北县"],41247: ["向阳区"],41248: ["工农区"],41249: ["南山区"],41250: ["兴安区"],41251: ["东山区"],41252: ["兴山区"],41253: ["绥滨县"]}],620: ["双鸭山市", 0, {631: ["友谊县"],647: ["宝清县"],649: ["饶河县"],650: ["集贤县"],41254: ["尖山区"],41255: ["岭东区"],41256: ["四方台区"],41257: ["宝山区"]}],641: ["七台河市", 0, {642: ["勃利县"],41284: ["新兴区"],41285: ["桃山区"],41286: ["茄子河区"]}],680: ["大兴安岭地区", 0, {606: ["呼玛县"],607: ["漠河县"],608: ["塔河县"]}]}],7: ["吉林", 0, {700: ["长春市", 0, {710: ["双阳区"],711: ["德惠市"],712: ["农安县"],713: ["九台市"],714: ["榆树市"],41206: ["南关区"],41207: ["宽城区"],41208: ["朝阳区"],41209: ["二道区"],41210: ["绿园区"]}],701: ["吉林市", 0, {715: ["永吉县"],716: ["磐石市"],717: ["桦甸市"],718: ["蛟河市"],719: ["舒兰市"],41211: ["昌邑区"],41212: ["龙潭区"],41213: ["船营区"],41214: ["丰满区"]}],703: ["四平市", 0, {727: ["公主岭市"],728: ["双辽市"],729: ["伊通满族自治县"],730: ["梨树县"],41215: ["铁西区"],41216: ["铁东区"]}],704: ["通化市", 0, {732: ["通化县"],733: ["梅河口市"],734: ["集安市"],735: ["柳河县"],736: ["辉南县"],41220: ["东昌区"],41221: ["二道江区"]}],705: ["白城市", 0, {740: ["洮南市"],741: ["镇赉县"],743: ["通榆县"],744: ["大安市"],41225: ["洮北区"]}],706: ["辽源市", 0, {747: ["东丰县"],41217: ["龙山区"],41218: ["西安区"],41219: ["东辽县"]}],707: ["松原市", 0, {708: ["前郭尔罗斯蒙古族自治县"],742: ["扶余县"],745: ["长岭县"],746: ["乾安县"],41224: ["宁江区"]}],748: ["白山市", 0, {731: ["临江市"],737: ["靖宇县"],738: ["长白朝鲜族自治县"],739: ["抚松县"],41222: ["八道江区"],41223: ["江源县"]}],41226: ["延边朝鲜族自治州", 0, {702: ["延吉市"],720: ["汪清县"],721: ["和龙市"],722: ["安图县"],723: ["敦化市"],724: ["图们市"],725: ["珲春市"],726: ["龙井市"]}]}],8: ["辽宁", 0, {800: ["沈阳市", 0, {801: ["辽中县"],802: ["新民市"],41137: ["和平区"],41138: ["沈河区"],41139: ["大东区"],41140: ["皇姑区"],41141: ["铁西区"],41142: ["苏家屯区"],41143: ["东陵区"],41145: ["于洪区"],41146: ["康平县"],41147: ["法库县"],42252: ["沈北新区"]}],806: ["铁岭市", 0, {803: ["昌图县"],804: ["开原市"],805: ["西丰县"],41197: ["银州区"],41198: ["清河区"],41199: ["铁岭县"],41200: ["调兵山市"]}],807: ["大连市", 0, {808: ["庄河市"],809: ["长海县"],812: ["瓦房店市"],41148: ["中山区"],41149: ["西岗区"],41150: ["沙河口区"],41151: ["甘井子区"],41152: ["旅顺口区"],41153: ["金州区"],41154: ["普兰店市"]}],813: ["鞍山市", 0, {814: ["海城市"],815: ["台安县"],816: ["岫岩满族自治县"],41155: ["铁东区"],41156: ["铁西区"],41157: ["立山区"],41158: ["千山区"]}],817: ["抚顺市", 0, {819: ["新宾满族自治县"],41159: ["新抚区"],41160: ["东洲区"],41161: ["望花区"],41162: ["顺城区"],41163: ["抚顺县"],41164: ["清原满族自治县"]}],820: ["本溪市", 0, {822: ["桓仁满族自治县"],41165: ["平山区"],41166: ["溪湖区"],41167: ["明山区"],41168: ["南芬区"],41169: ["本溪满族自治县"]}],824: ["丹东市", 0, {823: ["凤城市"],825: ["宽甸满族自治县"],41170: ["元宝区"],41171: ["振兴区"],41172: ["振安区"],41173: ["东港市"]}],831: ["锦州市", 0, {827: ["北镇市"],829: ["黑山县"],830: ["义县"],41174: ["古塔区"],41175: ["凌河区"],41176: ["太和区"],41177: ["凌海市"]}],834: ["营口市", 0, {41178: ["站前区"],41179: ["西市区"],41180: ["鲅鱼圈区"],41181: ["老边区"],41182: ["盖州市"],41183: ["大石桥市"]}],835: ["阜新市", 0, {836: ["彰武县"],41184: ["海州区"],41185: ["新邱区"],41186: ["太平区"],41187: ["清河门区"],41188: ["细河区"],41189: ["阜新蒙古族自治县"]}],838: ["辽阳市", 0, {839: ["辽阳县"],840: ["灯塔市"],41190: ["白塔区"],41191: ["文圣区"],41192: ["宏伟区"],41193: ["弓长岭区"],41194: ["太子河区"]}],843: ["朝阳市", 0, {841: ["喀喇沁左翼蒙古族自治县"],842: ["朝阳县"],844: ["建平县"],845: ["北票市"],846: ["凌源市"],41201: ["双塔区"],41202: ["龙城区"]}],847: ["盘锦市", 0, {848: ["大洼县"],849: ["盘山县"],41195: ["双台子区"],41196: ["兴隆台区"]}],854: ["葫芦岛市", 0, {850: ["兴城市"],851: ["绥中县"],852: ["建昌县"],41203: ["连山区"],41204: ["龙港区"],41205: ["南票区"]}]}],9: ["山东", 0, {900: ["济南市", 0, {925: ["长清区"],933: ["章丘市"],959: ["商河县"],961: ["济阳县"],997: ["平阴县"],41480: ["历下区"],41481: ["市中区"],41482: ["槐荫区"],41483: ["天桥区"],41484: ["历城区"]}],901: ["青岛市", 0, {935: ["胶南市"],936: ["胶州市"],937: ["平度市"],938: ["莱西市"],939: ["即墨市"],41485: ["市南区"],41486: ["市北区"],41487: ["四方区"],41488: ["黄岛区"],41489: ["崂山区"],41490: ["李沧区"],41491: ["城阳区"]}],903: ["淄博市", 0, {902: ["桓台县"],946: ["高青县"],9101: ["沂源县"],41492: ["淄川区"],41493: ["张店区"],41494: ["博山区"],41495: ["临淄区"],41496: ["周村区"]}],904: ["德州市", 0, {924: ["庆云县"],934: ["夏津县"],953: ["齐河县"],954: ["陵县"],955: ["平原县"],956: ["武城县"],957: ["宁津县"],958: ["乐陵市"],960: ["临邑县"],962: ["禹城市"],41524: ["德城区"]}],905: ["烟台市", 0, {940: ["招远市"],941: ["莱阳市"],942: ["海阳市"],963: ["长岛县"],964: ["莱州市"],965: ["龙口市"],966: ["蓬莱市"],967: ["栖霞市"],968: ["牟平区"],41504: ["芝罘区"],41505: ["福山区"],41506: ["莱山区"]}],906: ["潍坊市", 0, {969: ["寿光市"],972: ["昌邑市"],973: ["高密市"],974: ["诸城市"],976: ["安丘市"],977: ["临朐县"],978: ["青州市"],998: ["昌乐县"],41507: ["潍城区"],41508: ["寒亭区"],41509: ["坊子区"],41510: ["奎文区"]}],907: ["济宁市", 0, {979: ["梁山县"],982: ["曲阜市"],983: ["兖州市"],984: ["邹城市"],985: ["微山县"],986: ["鱼台县"],987: ["金乡县"],988: ["嘉祥县"],992: ["泗水县"],995: ["汶上县"],41511: ["市中区"],41512: ["任城区"]}],908: ["泰安市", 0, {991: ["新泰市"],993: ["宁阳县"],994: ["东平县"],996: ["肥城市"],41513: ["泰山区"],41514: ["岱岳区"]}],909: ["临沂市", 0, {989: ["苍山县"],999: ["平邑县"],9100: ["蒙阴县"],9102: ["沂水县"],9103: ["沂南县"],9105: ["莒南县"],9107: ["郯城县"],9108: ["费县"],41520: ["兰山区"],41521: ["罗庄区"],41522: ["河东区"],41523: ["临沭县"]}],910: ["威海市", 0, {911: ["荣成市"],913: ["文登市"],914: ["乳山市"],41515: ["环翠区"]}],912: ["菏泽市", 0, {916: ["巨野县"],917: ["定陶县"],918: ["成武县"],919: ["单县"],920: ["曹县"],921: ["东明县"],922: ["鄄城县"],923: ["郓城县"],41528: ["牡丹区"]}],915: ["日照市", 0, {975: ["五莲县"],9104: ["莒县"],41516: ["东港区"],41517: ["岚山区"]}],926: ["聊城市", 0, {927: ["临清市"],929: ["东阿县"],930: ["阳谷县"],931: ["莘县"],932: ["冠县"],9109: ["高唐县"],41525: ["东昌府区"],41526: ["茌平县"]}],944: ["滨州市", 0, {945: ["博兴县"],947: ["邹平县"],948: ["惠民县"],949: ["无棣县"],950: ["沾化县"],951: ["阳信县"],41527: ["滨城区"]}],970: ["东营市", 0, {943: ["利津县"],952: ["广饶县"],971: ["垦利县"],41502: ["东营区"],41503: ["河口区"]}],980: ["枣庄市", 0, {981: ["滕州市"],41497: ["市中区"],41498: ["薛城区"],41499: ["峄城区"],41500: ["台儿庄区"],41501: ["山亭区"]}],990: ["莱芜市", 0, {41518: ["莱城区"],41519: ["钢城区"]}]}],10: ["内蒙古", 1, {1e3: ["呼和浩特市", 0, {1003: ["武川县"],1005: ["托克托县"],1041: ["和林格尔县"],1055: ["清水河县"],41104: ["新城区"],41105: ["回民区"],41106: ["玉泉区"],41107: ["赛罕区"],41108: ["土默特左旗"]}],1008: ["包头市", 0, {1007: ["土默特右旗"],1009: ["固阳县"],1043: ["达尔罕茂明安联合旗"],41109: ["东河区"],41110: ["昆都仑区"],41111: ["青山区"],41112: ["石拐区"],41113: ["白云矿区"],41114: ["九原区"]}],1011: ["乌海市", 0, {41115: ["海勃湾区"],41116: ["海南区"],41117: ["乌达区"]}],1013: ["通辽市", 0, {1057: ["开鲁县"],1058: ["库伦旗"],1059: ["奈曼旗"],1060: ["扎鲁特旗"],1061: ["科尔沁左翼中旗"],1063: ["霍林郭勒市"],41121: ["科尔沁区"],41122: ["科尔沁左翼后旗"]}],1014: ["赤峰市", 0, {1015: ["阿鲁科尔沁旗"],1016: ["敖汉旗"],1017: ["宁城县"],1018: ["翁牛特旗"],1019: ["巴林左旗"],1020: ["巴林右旗"],1021: ["林西县"],1022: ["克什克腾旗"],1023: ["喀喇沁旗"],41118: ["红山区"],41119: ["元宝山区"],41120: ["松山区"]}],41123: ["鄂尔多斯市", 0, {1024: ["东胜区"],1067: ["达拉特旗"],1068: ["伊金霍洛旗"],1069: ["准格尔旗"],1070: ["杭锦旗"],1071: ["乌审旗"],1072: ["鄂托克旗"],1073: ["鄂托克前旗"]}],41124: ["呼伦贝尔市", 0, {1001: ["海拉尔区"],1002: ["鄂温克族自治旗"],1028: ["牙克石市"],1029: ["扎兰屯市"],1030: ["鄂伦春自治旗"],1031: ["陈巴尔虎旗"],1032: ["新巴尔虎左旗"],1033: ["新巴尔虎右旗"],1038: ["满洲里市"],1042: ["阿荣旗"],41125: ["莫力达瓦达斡尔族自治旗"],41126: ["额尔古纳市"],41127: ["根河市"]}],41128: ["巴彦淖尔市", 0, {1025: ["临河区"],1026: ["杭锦后旗"],1075: ["五原县"],1076: ["乌拉特前旗"],1077: ["乌拉特中旗"],1078: ["乌拉特后旗"],41129: ["磴口县"]}],41130: ["乌兰察布市", 0, {1012: ["集宁区"],1044: ["四子王旗"],1046: ["化德县"],1047: ["丰镇市"],1048: ["卓资县"],1049: ["商都县"],1050: ["兴和县"],1051: ["察哈尔右翼前旗"],1052: ["察哈尔右翼中旗"],1053: ["察哈尔右翼后旗"],1054: ["凉城县"]}],41131: ["兴安盟", 0, {1040: ["乌兰浩特市"],1056: ["科尔沁右翼中旗"],1064: ["突泉县"],1065: ["扎赉特旗"],1066: ["阿尔山市"],41132: ["科尔沁右翼前旗"]}],41133: ["锡林郭勒盟", 0, {1027: ["锡林浩特市"],1039: ["二连浩特市"],1080: ["多伦县"],1081: ["太仆寺旗"],1082: ["苏尼特左旗"],1083: ["苏尼特右旗"],1084: ["镶黄旗"],1085: ["东乌珠穆沁旗"],1086: ["西乌珠穆沁旗"],1087: ["正镶白旗"],1088: ["正蓝旗"],41134: ["阿巴嘎旗"]}],41135: ["阿拉善盟", 0, {1045: ["阿拉善左旗"],1079: ["阿拉善右旗"],41136: ["额济纳旗"]}]}],11: ["江苏", 0, {1100: ["南京市", 0, {1108: ["高淳县"],1109: ["溧水县"],1110: ["六合区"],1111: ["江宁区"],41306: ["玄武区"],41307: ["白下区"],41308: ["秦淮区"],41309: ["建邺区"],41310: ["鼓楼区"],41311: ["下关区"],41312: ["浦口区"],41313: ["栖霞区"],41314: ["雨花台区"]}],1101: ["无锡市", 0, {1103: ["江阴市"],1134: ["宜兴市"],41315: ["崇安区"],41316: ["南长区"],41317: ["北塘区"],41318: ["锡山区"],41319: ["惠山区"],41320: ["滨湖区"]}],1104: ["镇江市", 0, {1105: ["丹徒区"],1130: ["丹阳市"],1131: ["扬中市"],1175: ["句容市"],41350: ["京口区"],41351: ["润州区"]}],1106: ["苏州市", 0, {1135: ["常熟市"],1136: ["张家港市"],1137: ["吴江市"],1138: ["昆山市"],1139: ["太仓市"],41331: ["沧浪区"],41332: ["平江区"],41333: ["金阊区"],41334: ["虎丘区"],41335: ["吴中区"],41336: ["相城区"]}],1112: ["南通市", 0, {1113: ["通州市"],1114: ["如皋市"],1115: ["如东县"],1140: ["海门市"],1141: ["启东市"],1144: ["海安县"],41337: ["崇川区"],41338: ["港闸区"]}],1116: ["扬州市", 0, {1117: ["邗江区"],1148: ["江都市"],1150: ["宝应县"],1151: ["高邮市"],1174: ["仪征市"],41348: ["广陵区"]}],1118: ["盐城市", 0, {1153: ["东台市"],1154: ["大丰市"],1155: ["射阳县"],1156: ["滨海县"],1157: ["响水县"],1158: ["阜宁县"],1159: ["建湖县"],41346: ["亭湖区"],41347: ["盐都区"]}],1119: ["徐州市", 0, {1120: ["铜山县"],1160: ["睢宁县"],1162: ["新沂市"],1163: ["沛县"],1164: ["丰县"],41321: ["鼓楼区"],41322: ["云龙区"],41324: ["贾汪区"],41325: ["泉山区"],41326: ["邳州市"]}],1123: ["淮安市", 0, {1165: ["涟水县"],1170: ["盱眙县"],1171: ["金湖县"],1172: ["洪泽县"],41342: ["清河区"],41343: ["楚州区"],41344: ["淮阴区"],41345: ["清浦区"]}],1124: ["连云港市", 0, {1125: ["赣榆县"],1126: ["灌云县"],1127: ["东海县"],1173: ["灌南县"],41339: ["连云区"],41340: ["新浦区"],41341: ["海州区"]}],1128: ["常州市", 0, {1129: ["武进区"],1132: ["金坛市"],1133: ["溧阳市"],41327: ["天宁区"],41328: ["钟楼区"],41329: ["戚墅堰区"],41330: ["新北区"]}],1145: ["泰州市", 0, {1146: ["靖江市"],1149: ["兴化市"],1152: ["泰兴市"],41352: ["海陵区"],41353: ["高港区"],41354: ["姜堰市"]}],1167: ["宿迁市", 0, {1166: ["沭阳县"],1168: ["泗阳县"],1169: ["泗洪县"],41355: ["宿城区"],41356: ["宿豫区"]}]}],12: ["安徽", 0, {1200: ["合肥市", 0, {1207: ["肥西县"],1208: ["长丰县"],1209: ["肥东县"],41383: ["瑶海区"],41384: ["庐阳区"],41385: ["蜀山区"],41386: ["包河区"],42283: ["巢湖市"],42284: ["庐江县"]}],1205: ["滁州市", 0, {1201: ["天长市"],1202: ["来安县"],1203: ["定远县"],1206: ["全椒县"],1257: ["凤阳县"],41415: ["琅琊区"],41416: ["南谯区"],41417: ["明光市"]}],1210: ["蚌埠市", 0, {1211: ["固镇县"],1212: ["五河县"],1213: ["怀远县"],41391: ["龙子湖区"],41392: ["蚌山区"],41393: ["禹会区"],41394: ["淮上区"]}],1214: ["芜湖市", 0, {1215: ["芜湖县"],1216: ["南陵县"],1217: ["繁昌县"],41387: ["镜湖区"],41388: ["弋江区"],41389: ["鸠江区"],41390: ["三山区"],42285: ["无为县"]}],1218: ["淮南市", 0, {1219: ["凤台县"],41395: ["大通区"],41396: ["田家庵区"],41397: ["谢家集区"],41398: ["八公山区"],41399: ["潘集区"]}],1220: ["马鞍山市", 0, {1221: ["当涂县"],41400: ["金家庄区"],41401: ["花山区"],41402: ["雨山区"],42286: ["和县"],42287: ["含山县"]}],1227: ["安庆市", 0, {1222: ["岳西县"],1223: ["怀宁县"],1224: ["枞阳县"],1225: ["望江县"],1226: ["潜山县"],1228: ["宿松县"],1229: ["太湖县"],1230: ["桐城市"],41410: ["迎江区"],41411: ["大观区"],41412: ["宜秀区"]}],1232: ["宿州市", 0, {1231: ["萧县"],1233: ["灵璧县"],1234: ["泗县"],41422: ["埇桥区"],41423: ["砀山县"]}],1236: ["阜阳市", 0, {1271: ["阜南县"],1275: ["界首市"],1276: ["颍上县"],1277: ["太和县"],1278: ["临泉县"],41418: ["颍州区"],41419: ["颍东区"],41420: ["颍泉区"]}],1239: ["黄山市", 0, {1237: ["黟县"],1238: ["歙县"],1240: ["祁门县"],1242: ["休宁县"],1281: ["屯溪区"],41413: ["黄山区"],41414: ["徽州区"]}],1253: ["淮北市", 0, {1252: ["濉溪县"],41403: ["杜集区"],41404: ["相山区"],41405: ["烈山区"]}],1254: ["铜陵市", 0, {41406: ["铜官山区"],41407: ["狮子山区"],41408: ["郊区"],41409: ["铜陵县"]}],1266: ["六安市", 0, {1256: ["舒城县"],1265: ["金寨县"],1267: ["寿县"],1268: ["霍邱县"],1269: ["霍山县"],41425: ["金安区"],41426: ["裕安区"]}],1270: ["亳州市", 0, {1272: ["蒙城县"],1273: ["涡阳县"],1274: ["利辛县"],41427: ["谯城区"]}],1282: ["宣城市", 0, {1258: ["宣州区"],1260: ["广德县"],1261: ["郎溪县"],1262: ["宁国市"],1263: ["绩溪县"],1264: ["旌德县"],41429: ["泾县"]}],41428: ["池州市", 0, {1248: ["东至县"],1249: ["贵池区"],1250: ["青阳县"],1251: ["石台县"]}]}],13: ["山西", 0, {1300: ["太原市", 0, {1336: ["阳曲县"],1337: ["清徐县"],1338: ["古交市"],41070: ["小店区"],41071: ["迎泽区"],41072: ["杏花岭区"],41073: ["尖草坪区"],41074: ["万柏林区"],41075: ["晋源区"],41076: ["娄烦县"]}],1301: ["大同市", 0, {1341: ["天镇县"],1343: ["阳高县"],1344: ["浑源县"],1346: ["广灵县"],1347: ["灵丘县"],1348: ["左云县"],41077: ["城区"],41078: ["矿区"],41079: ["南郊区"],41080: ["新荣区"],41081: ["大同县"]}],1306: ["忻州市", 0, {1303: ["原平市"],1304: ["代县"],1349: ["偏关县"],1350: ["静乐县"],1351: ["定襄县"],1352: ["五台县"],1353: ["岢岚县"],1354: ["河曲县"],1355: ["保德县"],1356: ["宁武县"],1357: ["神池县"],1358: ["五寨县"],1391: ["繁峙县"],41095: ["忻府区"]}],1318: ["阳泉市", 0, {1320: ["平定县"],41082: ["城区"],41083: ["矿区"],41084: ["郊区"],41085: ["盂县"]}],1322: ["长治市", 0, {1363: ["平顺县"],1364: ["黎城县"],1366: ["襄垣县"],1367: ["武乡县"],1368: ["沁县"],1369: ["沁源县"],1370: ["屯留县"],1371: ["长子县"],1372: ["潞城市"],41086: ["长治县"],41087: ["壶关县"],42289: ["城区"],42290: ["郊区"]}],1323: ["晋城市", 0, {1359: ["高平市"],1360: ["阳城县"],1361: ["沁水县"],1362: ["陵川县"],41088: ["城区"],41089: ["泽州县"]}],1324: ["临汾市", 0, {1365: ["侯马市"],1383: ["大宁县"],1384: ["曲沃县"],1385: ["翼城县"],1386: ["洪洞县"],1387: ["霍州市"],1388: ["汾西县"],1389: ["蒲县"],1390: ["隰县"],1392: ["乡宁县"],1393: ["吉县"],1394: ["浮山县"],1395: ["古县"],41096: ["尧都区"],41097: ["襄汾县"],41098: ["安泽县"],41099: ["永和县"]}],1326: ["运城市", 0, {1302: ["芮城县"],1307: ["垣曲县"],1308: ["平陆县"],1310: ["万荣县"],1311: ["稷山县"],1312: ["河津市"],1313: ["新绛县"],1314: ["闻喜县"],1315: ["夏县"],1316: ["绛县"],1317: ["永济市"],41093: ["盐湖区"],41094: ["临猗县"]}],1339: ["朔州市", 0, {1305: ["山阴县"],1340: ["右玉县"],1342: ["怀仁县"],1345: ["应县"],41090: ["朔城区"],41091: ["平鲁区"]}],41092: ["晋中市", 0, {1321: ["榆次区"],1373: ["灵石县"],1374: ["昔阳县"],1375: ["和顺县"],1376: ["左权县"],1377: ["榆社县"],1378: ["寿阳县"],1379: ["太谷县"],1380: ["祁县"],1381: ["平遥县"],1382: ["介休市"]}],41100: ["吕梁市", 0, {1325: ["离石区"],1327: ["方山县"],1328: ["临县"],1329: ["汾阳市"],1330: ["文水县"],1331: ["交城县"],1332: ["孝义市"],1333: ["交口县"],1334: ["中阳县"],1335: ["兴县"],41101: ["柳林县"],41102: ["石楼县"],41103: ["岚县"]}]}],14: ["陕西", 0, {1400: ["西安市", 0, {1401: ["长安区"],1402: ["户县"],1403: ["周至县"],1405: ["临潼区"],1406: ["高陵县"],42050: ["新城区"],42051: ["碑林区"],42052: ["莲湖区"],42053: ["灞桥区"],42054: ["未央区"],42055: ["雁塔区"],42056: ["阎良区"],42057: ["蓝田县"]}],1407: ["咸阳市", 0, {1408: ["兴平市"],1409: ["武功县"],1410: ["永寿县"],1411: ["乾县"],1445: ["彬县"],1446: ["三原县"],1447: ["泾阳县"],1448: ["礼泉县"],1492: ["淳化县"],1493: ["长武县"],1494: ["旬邑县"],42064: ["秦都区"],42065: ["杨凌区"],42066: ["渭城区"]}],1417: ["延安市", 0, {1412: ["延川县"],1413: ["子长县"],1414: ["延长县"],1416: ["黄陵县"],1418: ["富县"],1419: ["黄龙县"],1420: ["宜川县"],1421: ["洛川县"],1422: ["志丹县"],1423: ["甘泉县"],1424: ["安塞县"],42068: ["宝塔区"],42069: ["吴起县"]}],1425: ["榆林市", 0, {1449: ["吴堡县"],1450: ["子洲县"],1451: ["佳县"],1452: ["府谷县"],1453: ["米脂县"],1454: ["绥德县"],1455: ["定边县"],1456: ["靖边县"],1457: ["神木县"],1465: ["清涧县"],42071: ["榆阳区"],42072: ["横山县"]}],1426: ["渭南市", 0, {1427: ["华县"],1458: ["大荔县"],1459: ["华阴市"],1460: ["潼关县"],1461: ["富平县"],1462: ["澄城县"],1463: ["韩城市"],1464: ["蒲城县"],1490: ["合阳县"],1491: ["白水县"],42067: ["临渭区"]}],1429: ["安康市", 0, {1472: ["镇坪县"],1473: ["岚皋县"],1474: ["宁陕县"],1475: ["汉阴县"],1476: ["旬阳县"],1477: ["白河县"],1478: ["紫阳县"],1479: ["石泉县"],42073: ["汉滨区"],42074: ["平利县"]}],1430: ["汉中市", 0, {1431: ["南郑县"],1480: ["留坝县"],1481: ["城固县"],1482: ["洋县"],1483: ["佛坪县"],1484: ["西乡县"],1485: ["镇巴县"],1486: ["宁强县"],1487: ["勉县"],1488: ["略阳县"],42070: ["汉台区"]}],1432: ["宝鸡市", 0, {1434: ["凤翔县"],1435: ["岐山县"],1436: ["扶风县"],1437: ["眉县"],1438: ["太白县"],1439: ["凤县"],1440: ["麟游县"],1441: ["千阳县"],1489: ["陇县"],42061: ["渭滨区"],42062: ["金台区"],42063: ["陈仓区"]}],1442: ["铜川市", 0, {1444: ["宜君县"],42058: ["王益区"],42059: ["印台区"],42060: ["耀州区"]}],42075: ["商洛市", 0, {1428: ["商州区"],1466: ["丹凤县"],1467: ["柞水县"],1468: ["镇安县"],1469: ["山阳县"],1470: ["洛南县"],1471: ["商南县"]}]}],15: ["甘肃", 0, {1500: ["兰州市", 0, {1502: ["榆中县"],1503: ["永登县"],1504: ["皋兰县"],1505: ["红古区"],42076: ["城关区"],42077: ["七里河区"],42078: ["西固区"],42079: ["安宁区"]}],1506: ["定西市", 0, {1529: ["陇西县"],1530: ["漳县"],1531: ["通渭县"],1532: ["岷县"],1533: ["临洮县"],1534: ["渭源县"],42091: ["安定区"]}],1507: ["平凉市", 0, {1536: ["静宁县"],1537: ["泾川县"],1538: ["灵台县"],1539: ["崇信县"],1540: ["华亭县"],1541: ["庄浪县"],42087: ["崆峒区"]}],1509: ["武威市", 0, {1549: ["民勤县"],1550: ["天祝藏族自治县"],1551: ["古浪县"],42085: ["凉州区"]}],1510: ["张掖市", 0, {1554: ["山丹县"],1555: ["高台县"],1556: ["肃南裕固族自治县"],1557: ["民乐县"],1558: ["临泽县"],42086: ["甘州区"]}],1511: ["酒泉市", 0, {1563: ["玉门市"],1564: ["安西县"],1565: ["敦煌市"],1566: ["金塔县"],1567: ["肃北蒙古族自治县"],42088: ["肃州区"],42089: ["阿克塞哈萨克族自治县"]}],1535: ["白银市", 0, {1559: ["平川区"],1560: ["靖远县"],1561: ["景泰县"],1562: ["会宁县"],42081: ["白银区"]}],1542: ["庆阳市", 0, {1508: ["西峰区"],1543: ["宁县"],1544: ["镇原县"],1545: ["环县"],1546: ["合水县"],1547: ["正宁县"],1548: ["华池县"],42090: ["庆城县"]}],1552: ["金昌市", 0, {1553: ["永昌县"],42080: ["金川区"]}],1568: ["嘉峪关市"],1580: ["天水市", 0, {1512: ["甘谷县"],1569: ["武山县"],1570: ["张家川回族自治县"],1571: ["清水县"],42082: ["秦州区"],42083: ["北道区"],42084: ["秦安县"]}],42092: ["陇南市", 0, {1513: ["武都区"],1572: ["成县"],1573: ["康县"],1574: ["文县"],1576: ["西和县"],1577: ["礼县"],1578: ["徽县"],1579: ["两当县"],42093: ["宕昌县"]}],42094: ["临夏回族自治州", 0, {1514: ["临夏市"],1515: ["永靖县"],1516: ["和政县"],1517: ["东乡族自治县"],1518: ["康乐县"],1519: ["广河县"],1520: ["积石山保安族东乡族撒拉族自治县"],42095: ["临夏县"]}],42096: ["甘南藏族自治州", 0, {1522: ["夏河县"],1523: ["临潭县"],1524: ["舟曲县"],1525: ["碌曲县"],1526: ["玛曲县"],1527: ["卓尼县"],1528: ["迭部县"],42097: ["合作市"]}]}],16: ["浙江", 0, {1600: ["杭州市", 0, {1620: ["余杭区"],1621: ["萧山区"],1622: ["富阳市"],1623: ["桐庐县"],1624: ["建德市"],1625: ["淳安县"],1626: ["临安市"],41357: ["上城区"],41358: ["下城区"],41359: ["江干区"],41360: ["拱墅区"],41361: ["西湖区"],41362: ["滨江区"]}],1601: ["湖州市", 0, {1627: ["德清县"],1628: ["安吉县"],1629: ["长兴县"],41373: ["吴兴区"],41374: ["南浔区"]}],1602: ["嘉兴市", 0, {1630: ["嘉善县"],1631: ["平湖市"],1632: ["海盐县"],1633: ["海宁市"],1634: ["桐乡市"],41371: ["南湖区"],41372: ["秀洲区"]}],1603: ["宁波市", 0, {1605: ["镇海区"],1635: ["象山县"],1636: ["宁海县"],1637: ["奉化市"],1638: ["余姚市"],1639: ["慈溪市"],41363: ["海曙区"],41364: ["江东区"],41365: ["江北区"],41366: ["北仑区"],41367: ["鄞州区"]}],1606: ["绍兴市", 0, {1607: ["绍兴县"],1640: ["上虞市"],1642: ["新昌县"],1643: ["诸暨市"],1680: ["嵊州市"],41375: ["越城区"]}],1609: ["温州市", 0, {1655: ["永嘉县"],1656: ["乐清市"],1657: ["洞头县"],1658: ["瑞安市"],1659: ["平阳县"],1660: ["苍南县"],1661: ["泰顺县"],1662: ["文成县"],41368: ["鹿城区"],41369: ["龙湾区"],41370: ["瓯海区"]}],1611: ["丽水市", 0, {1663: ["缙云县"],1664: ["青田县"],1665: ["云和县"],1666: ["庆元县"],1667: ["龙泉市"],1668: ["遂昌县"],1669: ["松阳县"],1670: ["景宁畲族自治县"],41382: ["莲都区"]}],1612: ["金华市", 0, {1671: ["浦江县"],1672: ["义乌市"],1673: ["东阳市"],1674: ["永康市"],1675: ["武义县"],1676: ["兰溪市"],1677: ["磐安县"],41376: ["婺城区"],41377: ["金东区"]}],1614: ["衢州市", 0, {1616: ["江山市"],1617: ["常山县"],1618: ["开化县"],1619: ["龙游县"],41378: ["柯城区"],41379: ["衢江区"]}],1644: ["舟山市", 0, {1645: ["岱山县"],1646: ["嵊泗县"],1678: ["定海区"],41380: ["普陀区"]}],1681: ["台州市", 0, {1608: ["临海市"],1648: ["三门县"],1649: ["黄岩区"],1650: ["温岭市"],1651: ["玉环县"],1652: ["仙居县"],1653: ["天台县"],1654: ["椒江区"],41381: ["路桥区"]}]}],17: ["江西", 0, {1700: ["南昌市", 0, {1701: ["南昌县"],1704: ["新建县"],1705: ["进贤县"],1706: ["安义县"],41458: ["东湖区"],41459: ["西湖区"],41460: ["青云谱区"],41461: ["湾里区"],41462: ["青山湖区"]}],1703: ["新余市", 0, {1702: ["分宜县"],41472: ["渝水区"]}],1707: ["九江市", 0, {1708: ["湖口县"],1709: ["星子县"],1710: ["修水县"],1711: ["瑞昌市"],1712: ["德安县"],1713: ["彭泽县"],1714: ["永修县"],1715: ["庐山区"],1716: ["都昌县"],1717: ["武宁县"],41470: ["浔阳区"],41471: ["九江县"],42291: ["共青城市"]}],1719: ["上饶市", 0, {1718: ["上饶县"],1720: ["广丰县"],1735: ["婺源县"],1736: ["德兴市"],1737: ["鄱阳县"],1738: ["弋阳县"],1739: ["玉山县"],1785: ["万年县"],1786: ["余干县"],1787: ["横峰县"],41478: ["信州区"],41479: ["铅山县"]}],1722: ["宜春市", 0, {1750: ["宜丰县"],1751: ["上高县"],1752: ["樟树市"],1753: ["奉新县"],1754: ["靖安县"],1755: ["高安市"],1756: ["万载县"],1757: ["丰城市"],1758: ["铜鼓县"],41477: ["袁州区"]}],1723: ["吉安市", 0, {1724: ["吉安县"],1729: ["新干县"],1759: ["井冈山市"],1760: ["吉水县"],1761: ["泰和县"],1762: ["安福县"],1763: ["永新县"],1765: ["万安县"],1766: ["永丰县"],1767: ["峡江县"],1768: ["遂川县"],41475: ["吉州区"],41476: ["青原区"]}],1726: ["赣州市", 0, {1725: ["赣县"],1769: ["于都县"],1770: ["兴国县"],1771: ["宁都县"],1772: ["石城县"],1773: ["瑞金市"],1774: ["会昌县"],1775: ["大余县"],1776: ["上犹县"],1777: ["崇义县"],1778: ["信丰县"],1779: ["龙南县"],1780: ["定南县"],1781: ["全南县"],1782: ["安远县"],1783: ["寻乌县"],1784: ["南康市"],41474: ["章贡区"]}],1727: ["景德镇市", 0, {1734: ["乐平市"],41463: ["昌江区"],41464: ["珠山区"],41465: ["浮梁县"]}],1728: ["萍乡市", 0, {1764: ["莲花县"],41466: ["安源区"],41467: ["湘东区"],41468: ["上栗县"],41469: ["芦溪县"]}],1731: ["鹰潭市", 0, {1732: ["贵溪市"],1733: ["余江县"],41473: ["月湖区"]}],1788: ["抚州市", 0, {1721: ["临川区"],1740: ["资溪县"],1741: ["广昌县"],1742: ["东乡县"],1743: ["金溪县"],1744: ["崇仁县"],1745: ["宜黄县"],1746: ["乐安县"],1747: ["南城县"],1748: ["南丰县"],1749: ["黎川县"]}]}],18: ["湖北", 0, {1800: ["武汉市", 0, {1802: ["汉阳区"],1803: ["黄陂区"],1804: ["新洲区"],41586: ["江岸区"],41587: ["江汉区"],41588: ["硚口区"],41589: ["武昌区"],41590: ["青山区"],41591: ["洪山区"],41592: ["东西湖区"],41593: ["汉南区"],41594: ["蔡甸区"],41595: ["江夏区"]}],1805: ["襄阳市", 0, {1806: ["襄州区"],1820: ["枣阳市"],1822: ["宜城市"],1824: ["保康县"],1825: ["谷城县"],1826: ["老河口市"],41608: ["襄城区"],41609: ["樊城区"],41610: ["南漳县"]}],1807: ["鄂州市", 0, {41611: ["梁子湖区"],41612: ["华容区"],41613: ["鄂城区"]}],1808: ["孝感市", 0, {1829: ["大悟县"],1830: ["汉川市"],1831: ["应城市"],1832: ["云梦县"],1833: ["安陆市"],41617: ["孝南区"],41618: ["孝昌县"]}],1809: ["黄冈市", 0, {1835: ["麻城市"],1836: ["红安县"],1837: ["浠水县"],1838: ["罗田县"],1839: ["英山县"],1841: ["黄梅县"],1842: ["武穴市"],41620: ["黄州区"],41621: ["团风县"],41622: ["蕲春县"]}],1811: ["黄石市", 0, {1810: ["大冶市"],1843: ["阳新县"],41596: ["黄石港区"],41597: ["西塞山区"],41598: ["下陆区"],41599: ["铁山区"]}],1812: ["咸宁市", 0, {1844: ["通山县"],1845: ["崇阳县"],1846: ["通城县"],1848: ["嘉鱼县"],41623: ["咸安区"],41624: ["赤壁市"]}],1815: ["宜昌市", 0, {1857: ["远安县"],1858: ["当阳市"],1859: ["枝江市"],1860: ["宜都市"],1861: ["长阳土家族自治县"],1862: ["五峰土家族自治县"],1864: ["兴山县"],41602: ["西陵区"],41603: ["伍家岗区"],41604: ["点军区"],41605: ["猇亭区"],41606: ["夷陵区"],41607: ["秭归县"]}],1818: ["十堰市", 0, {1872: ["郧县"],1873: ["丹江口市"],1874: ["房县"],1876: ["竹山县"],1877: ["竹溪县"],1878: ["郧西县"],41600: ["茅箭区"],41601: ["张湾区"]}],1819: ["荆门市", 0, {1855: ["钟祥市"],1856: ["京山县"],41614: ["东宝区"],41615: ["掇刀区"],41616: ["沙洋县"]}],1821: ["随州市", 0, {1834: ["广水市"],41625: ["曾都区"],42292: ["随县"]}],1828: ["仙桃市"],1849: ["天门市"],1850: ["潜江市"],1875: ["神农架林区"],1879: ["荆州市", 0, {1813: ["江陵县"],1814: ["沙市区"],1827: ["洪湖市"],1851: ["监利县"],1852: ["石首市"],1853: ["公安县"],1854: ["松滋市"],41619: ["荆州区"]}],41626: ["恩施土家族苗族自治州", 0, {1817: ["恩施市"],1865: ["建始县"],1866: ["巴东县"],1867: ["鹤峰县"],1868: ["宣恩县"],1869: ["来凤县"],1870: ["咸丰县"],1871: ["利川市"]}]}],19: ["湖南", 0, {1900: ["长沙市", 0, {1901: ["长沙县"],1905: ["望城县"],1906: ["宁乡县"],1907: ["浏阳市"],41627: ["芙蓉区"],41628: ["天心区"],41629: ["岳麓区"],41630: ["开福区"],41631: ["雨花区"]}],1902: ["岳阳市", 0, {1903: ["岳阳县"],1904: ["临湘市"],1930: ["湘阴县"],1931: ["华容县"],1932: ["平江县"],1933: ["汨罗市"],41650: ["岳阳楼区"],41651: ["云溪区"],41652: ["君山区"]}],1908: ["湘潭市", 0, {1909: ["韶山市"],1910: ["湘乡市"],41638: ["雨湖区"],41639: ["岳塘区"],41640: ["湘潭县"]}],1911: ["株洲市", 0, {1912: ["株洲县"],1955: ["茶陵县"],1957: ["醴陵市"],41632: ["荷塘区"],41633: ["芦淞区"],41634: ["石峰区"],41635: ["天元区"],41636: ["攸县"],41637: ["炎陵县"]}],1913: ["衡阳市", 0, {1914: ["衡阳县"],1915: ["衡南县"],1958: ["耒阳市"],1959: ["常宁市"],1960: ["衡东县"],1961: ["衡山县"],1962: ["祁东县"],41641: ["珠晖区"],41642: ["雁峰区"],41643: ["石鼓区"],41644: ["蒸湘区"],41645: ["南岳区"]}],1916: ["郴州市", 0, {1963: ["资兴市"],1964: ["桂东县"],1965: ["汝城县"],1966: ["临武县"],1967: ["嘉禾县"],1968: ["安仁县"],1969: ["桂阳县"],1970: ["永兴县"],1971: ["宜章县"],41660: ["北湖区"],41661: ["苏仙区"]}],1918: ["常德市", 0, {1917: ["桃源县"],1919: ["汉寿县"],1972: ["石门县"],1973: ["澧县"],1974: ["津市市"],1975: ["安乡县"],1976: ["临澧县"],41653: ["武陵区"],41654: ["鼎城区"]}],1920: ["益阳市", 0, {1921: ["桃江县"],1977: ["安化县"],1978: ["南县"],41657: ["资阳区"],41658: ["赫山区"],41659: ["沅江市"]}],1922: ["娄底市", 0, {1979: ["双峰县"],1980: ["冷水江市"],1981: ["新化县"],1982: ["涟源市"],41665: ["娄星区"]}],1925: ["邵阳市", 0, {1923: ["邵东县"],1924: ["新邵县"],1926: ["邵阳县"],1994: ["新宁县"],1995: ["城步苗族自治县"],1996: ["绥宁县"],1998: ["洞口县"],1999: ["隆回县"],41646: ["双清区"],41647: ["大祥区"],41648: ["北塔区"],41649: ["武冈市"]}],1927: ["永州市", 0, {1934: ["冷水滩区"],1935: ["东安县"],1936: ["祁阳县"],1937: ["新田县"],1938: ["宁远县"],1939: ["蓝山县"],1940: ["江华瑶族自治县"],1941: ["江永县"],1942: ["道县"],1943: ["双牌县"],41662: ["零陵区"]}],1928: ["怀化市", 0, {1929: ["麻阳苗族自治县"],1944: ["芷江侗族自治县"],1946: ["溆浦县"],1947: ["通道侗族自治县"],1948: ["靖州苗族侗族自治县"],1949: ["会同县"],1950: ["新晃侗族自治县"],1951: ["辰溪县"],1952: ["沅陵县"],1953: ["洪江市"],41663: ["鹤城区"],41664: ["中方县"]}],19100: ["张家界市", 0, {1985: ["桑植县"],1993: ["慈利县"],41655: ["永定区"],41656: ["武陵源区"]}],41666: ["湘西土家族苗族自治州", 0, {1983: ["凤凰县"],1984: ["吉首市"],1987: ["龙山县"],1988: ["永顺县"],1989: ["保靖县"],1990: ["花垣县"],1991: ["古丈县"],1992: ["泸溪县"]}]}],20: ["贵州", 0, {2e3: ["贵阳市", 0, {2022: ["清镇市"],2023: ["修文县"],2025: ["开阳县"],41918: ["南明区"],41919: ["云岩区"],41920: ["花溪区"],41921: ["乌当区"],41922: ["白云区"],41923: ["小河区"],41924: ["息烽县"]}],2001: ["遵义市", 0, {2010: ["赤水市"],2011: ["习水县"],2012: ["仁怀市"],2013: ["遵义县"],2014: ["绥阳县"],2015: ["湄潭县"],2016: ["凤冈县"],2017: ["务川仡佬族苗族自治县"],2056: ["余庆县"],41927: ["红花岗区"],41928: ["汇川区"],41929: ["桐梓县"],41930: ["正安县"],41931: ["道真仡佬族苗族自治县"]}],2002: ["安顺市", 0, {2018: ["平坝县"],2019: ["紫云苗族布依族自治县"],2020: ["镇宁布依族苗族自治县"],2021: ["普定县"],41932: ["西秀区"],41933: ["关岭布依族苗族自治县"]}],2007: ["六盘水市", 0, {2051: ["六枝特区"],2052: ["盘县"],41925: ["钟山区"],41926: ["水城县"]}],41934: ["铜仁市", 0, {2041: ["玉屏侗族自治县"],2042: ["石阡县"],41935: ["江口县"],41936: ["思南县"],41937: ["印江土家族苗族自治县"],41938: ["德江县"],41939: ["沿河土家族自治县"],41940: ["松桃苗族自治县"],41941: ["万山特区"],42294: ["碧江区"]}],41942: ["黔西南布依族苗族自治州", 0, {2008: ["兴义市"],41943: ["兴仁县"],41944: ["普安县"],41945: ["晴隆县"],41946: ["贞丰县"],41947: ["望谟县"],41948: ["册亨县"],41949: ["安龙县"]}],41950: ["毕节市", 0, {2043: ["威宁彝族回族苗族自治县"],2044: ["赫章县"],2045: ["纳雍县"],2046: ["黔西县"],2047: ["大方县"],2048: ["金沙县"],2049: ["织金县"],42293: ["七星关区"]}],41951: ["黔东南苗族侗族自治州", 0, {2004: ["凯里市"],2034: ["黄平县"],2035: ["施秉县"],2036: ["镇远县"],2037: ["天柱县"],2038: ["锦屏县"],2039: ["黎平县"],2040: ["从江县"],2050: ["剑河县"],2053: ["雷山县"],2054: ["台江县"],41952: ["三穗县"],41953: ["岑巩县"],41954: ["榕江县"],41955: ["麻江县"],41956: ["丹寨县"]}],41957: ["黔南布依族苗族自治州", 0, {2003: ["都匀市"],2026: ["贵定县"],2027: ["福泉市"],2028: ["瓮安县"],2029: ["三都水族自治县"],2030: ["独山县"],2031: ["平塘县"],2032: ["惠水县"],2033: ["龙里县"],41958: ["荔波县"],41959: ["罗甸县"],41960: ["长顺县"]}]}],21: ["四川", 0, {2100: ["成都市", 0, {2108: ["温江区"],2128: ["金堂县"],2129: ["双流县"],2135: ["新津县"],2145: ["蒲江县"],2146: ["郫县"],2147: ["新都区"],2149: ["都江堰市"],2151: ["大邑县"],2152: ["邛崃市"],41815: ["锦江区"],41816: ["青羊区"],41817: ["金牛区"],41818: ["武侯区"],41819: ["成华区"],41820: ["龙泉驿区"],41821: ["青白江区"],41822: ["彭州市"],41823: ["崇州市"]}],2105: ["攀枝花市", 0, {2143: ["米易县"],2144: ["盐边县"],41829: ["东区"],41830: ["西区"],41831: ["仁和区"]}],2106: ["自贡市", 0, {2130: ["荣县"],41824: ["自流井区"],41825: ["贡井区"],41826: ["大安区"],41827: ["沿滩区"],41828: ["富顺县"]}],2109: ["绵阳市", 0, {2153: ["平武县"],2155: ["安县"],2156: ["江油市"],2157: ["梓潼县"],2165: ["三台县"],2168: ["盐亭县"],41841: ["涪城区"],41842: ["游仙区"],41843: ["北川羌族自治县"]}],2110: ["南充市", 0, {2162: ["西充县"],2170: ["南部县"],2171: ["阆中市"],2173: ["营山县"],2174: ["蓬安县"],2175: ["仪陇县"],41859: ["顺庆区"],41860: ["高坪区"],41861: ["嘉陵区"]}],2111: ["达州市", 0, {2180: ["宣汉县"],2181: ["开江县"],2182: ["万源市"],2183: ["大竹县"],2184: ["渠县"],21116: ["达县"],41873: ["通川区"]}],2114: ["泸州市", 0, {41832: ["江阳区"],41833: ["纳溪区"],41834: ["龙马潭区"],41835: ["泸县"],41836: ["合江县"],41837: ["叙永县"],41838: ["古蔺县"]}],2115: ["宜宾市", 0, {2116: ["宜宾县"],41863: ["翠屏区"],41864: ["南溪县"],41865: ["江安县"],41866: ["长宁县"],41867: ["高县"],41868: ["珙县"],41869: ["筠连县"],41870: ["兴文县"],41871: ["屏山县"]}],2117: ["内江市", 0, {41850: ["市中区"],41851: ["东兴区"],41852: ["威远县"],41853: ["资中县"],41854: ["隆昌县"]}],2118: ["乐山市", 0, {2188: ["峨眉山市"],2190: ["井研县"],2193: ["沐川县"],2197: ["马边彝族自治县"],2198: ["犍为县"],21100: ["夹江县"],21101: ["金口河区"],41855: ["市中区"],41856: ["沙湾区"],41857: ["五通桥区"],41858: ["峨边彝族自治县"]}],2120: ["雅安市", 0, {2192: ["宝兴县"],2199: ["石棉县"],21108: ["名山县"],21109: ["荥经县"],21110: ["汉源县"],21111: ["天全县"],21112: ["芦山县"],41874: ["雨城区"]}],2122: ["德阳市", 0, {2123: ["中江县"],2124: ["绵竹市"],2125: ["广汉市"],2126: ["什邡市"],41839: ["旌阳区"],41840: ["罗江县"]}],2127: ["广元市", 0, {2158: ["剑阁县"],2160: ["旺苍县"],2161: ["青川县"],2172: ["苍溪县"],41844: ["利州区"],41845: ["元坝区"],41846: ["朝天区"]}],2163: ["遂宁市", 0, {2166: ["蓬溪县"],2167: ["射洪县"],41847: ["船山区"],41848: ["安居区"],41849: ["大英县"]}],2178: ["广安市", 0, {2169: ["华蓥市"],2176: ["岳池县"],2177: ["武胜县"],2185: ["邻水县"],41872: ["广安区"]}],2187: ["眉山市", 0, {2189: ["仁寿县"],2191: ["洪雅县"],2194: ["彭山县"],2195: ["青神县"],2196: ["丹棱县"],41862: ["东坡区"]}],21115: ["巴中市", 0, {2179: ["平昌县"],21114: ["通江县"],41875: ["巴州区"],41876: ["南江县"]}],21119: ["资阳市", 0, {2186: ["乐至县"],41877: ["雁江区"],41878: ["安岳县"],41879: ["简阳市"]}],41880: ["阿坝藏族羌族自治州", 0, {2121: ["马尔康县"],21113: ["汶川县"],21117: ["九寨沟县"],41881: ["理县"],41882: ["茂县"],41883: ["松潘县"],41884: ["金川县"],41885: ["小金县"],41886: ["黑水县"],41887: ["壤塘县"],41888: ["阿坝县"],41889: ["若尔盖县"],41890: ["红原县"]}],41891: ["甘孜藏族自治州", 0, {21118: ["康定县"],41892: ["泸定县"],41893: ["丹巴县"],41894: ["九龙县"],41895: ["雅江县"],41896: ["道孚县"],41897: ["炉霍县"],41898: ["甘孜县"],41899: ["新龙县"],41900: ["德格县"],41901: ["白玉县"],41902: ["石渠县"],41903: ["色达县"],41904: ["理塘县"],41905: ["巴塘县"],41906: ["乡城县"],41907: ["稻城县"],41908: ["得荣县"]}],41909: ["凉山彝族自治州", 0, {2119: ["西昌市"],2154: ["宁南县"],2159: ["盐源县"],21102: ["会理县"],21103: ["会东县"],21104: ["冕宁县"],21105: ["德昌县"],21106: ["雷波县"],21107: ["普格县"],41910: ["木里藏族自治县"],41911: ["布拖县"],41912: ["金阳县"],41913: ["昭觉县"],41914: ["喜德县"],41915: ["越西县"],41916: ["甘洛县"],41917: ["美姑县"]}]}],22: ["云南", 0, {2200: ["昆明市", 0, {2202: ["禄劝彝族苗族自治县"],2204: ["晋宁县"],2205: ["呈贡县"],2206: ["富民县"],2207: ["嵩明县"],2208: ["安宁市"],2209: ["宜良县"],2248: ["寻甸回族彝族自治县"],2249: ["东川区"],41961: ["五华区"],41962: ["盘龙区"],41963: ["官渡区"],41964: ["西山区"],41965: ["石林彝族自治县"]}],2201: ["昭通市", 0, {2250: ["盐津县"],2251: ["绥江县"],2252: ["水富县"],2253: ["镇雄县"],2254: ["鲁甸县"],2255: ["大关县"],2256: ["巧家县"],2257: ["彝良县"],22112: ["永善县"],22113: ["威信县"],41970: ["昭阳区"]}],2217: ["曲靖市", 0, {2214: ["马龙县"],2215: ["师宗县"],2216: ["富源县"],2258: ["陆良县"],2259: ["罗平县"],2260: ["宣威市"],2261: ["会泽县"],41966: ["麒麟区"],41967: ["沾益县"]}],2218: ["保山市", 0, {2219: ["昌宁县"],22103: ["腾冲县"],22104: ["龙陵县"],22105: ["施甸县"],41969: ["隆阳区"]}],2228: ["玉溪市", 0, {2221: ["江川县"],2222: ["元江哈尼族彝族傣族自治县"],2223: ["通海县"],2224: ["易门县"],2225: ["澄江县"],2226: ["峨山彝族自治县"],2227: ["华宁县"],2229: ["新平彝族傣族自治县"],41968: ["红塔区"]}],2234: ["普洱市", 0, {2235: ["镇沅彝族哈尼族拉祜族自治县"],2241: ["墨江哈尼族自治县"],2264: ["澜沧拉祜族自治县"],2265: ["景谷傣族彝族自治县"],2266: ["江城哈尼族彝族自治县"],2267: ["景东彝族自治县"],2268: ["西盟佤族自治县"],2269: ["孟连傣族拉祜族佤族自治县"],2270: ["普洱哈尼族彝族自治县"],41973: ["思茅区"]}],2243: ["临沧市", 0, {2236: ["镇康县"],2237: ["永德县"],2240: ["耿马傣族佤族自治县"],2244: ["沧源佤族自治县"],2245: ["凤庆县"],2246: ["云县"],2247: ["双江拉祜族佤族布朗族傣族自治县"],41974: ["临翔区"]}],22115: ["丽江市", 0, {22116: ["宁蒗彝族自治县"],22117: ["华坪县"],22118: ["永胜县"],41971: ["古城区"],41972: ["玉龙纳西族自治县"]}],22124: ["西双版纳傣族自治州", 0, {2263: ["勐海县"],2271: ["景洪市"],2272: ["勐腊县"]}],41975: ["楚雄彝族自治州", 0, {2231: ["楚雄市"],2232: ["姚安县"],2233: ["双柏县"],2288: ["永仁县"],2289: ["禄丰县"],2290: ["大姚县"],2291: ["南华县"],2292: ["牟定县"],2293: ["武定县"],2294: ["元谋县"]}],41976: ["红河哈尼族彝族自治州", 0, {2213: ["个旧市"],2273: ["元阳县"],2274: ["石屏县"],2275: ["弥勒县"],2276: ["红河县"],2277: ["开远市"],2278: ["蒙自县"],2279: ["建水县"],2280: ["河口瑶族自治县"],2281: ["泸西县"],2282: ["屏边苗族自治县"],22120: ["金平苗族瑶族傣族自治县"],22123: ["绿春县"]}],41977: ["文山壮族苗族自治州", 0, {2220: ["文山县"],2242: ["麻栗坡县"],2262: ["砚山县"],2284: ["广南县"],2285: ["富宁县"],2286: ["马关县"],2287: ["西畴县"],41978: ["丘北县"]}],41979: ["大理白族自治州", 0, {2210: ["宾川县"],2211: ["弥渡县"],2212: ["大理市"],2295: ["南涧彝族自治县"],2296: ["剑川县"],2297: ["鹤庆县"],2298: ["祥云县"],2299: ["漾濞彝族自治县"],22100: ["洱源县"],22101: ["永平县"],22102: ["巍山彝族回族自治县"],22114: ["云龙县"]}],41980: ["德宏傣族景颇族自治州", 0, {22106: ["梁河县"],22107: ["盈江县"],22109: ["瑞丽市"],22110: ["陇川县"],22111: ["潞西市"]}],41981: ["怒江傈僳族自治州", 0, {2238: ["福贡县"],2239: ["兰坪白族普米族自治县"],22121: ["泸水县"],41982: ["贡山独龙族怒族自治县"]}],41983: ["迪庆藏族自治州", 0, {41984: ["香格里拉县"],41985: ["德钦县"],41986: ["维西傈僳族自治县"]}]}],23: ["新疆", 1, {2300: ["乌鲁木齐市", 0, {42137: ["天山区"],42138: ["沙依巴克区"],42139: ["新市区"],42140: ["水磨沟区"],42141: ["头屯河区"],42142: ["达坂城区"],42144: ["乌鲁木齐县"],42250: ["米东区"]}],2301: ["克拉玛依市", 0, {42145: ["独山子区"],42146: ["克拉玛依区"],42147: ["白碱滩区"],42148: ["乌尔禾区"]}],2302: ["石河子市"],42149: ["吐鲁番地区", 0, {2305: ["吐鲁番市"],42150: ["鄯善县"],42151: ["托克逊县"]}],42152: ["哈密地区", 0, {2304: ["哈密市"],42153: ["巴里坤哈萨克自治县"],42154: ["伊吾县"]}],42155: ["昌吉回族自治州", 0, {42156: ["昌吉市"],42157: ["阜康市"],42159: ["呼图壁县"],42160: ["玛纳斯县"],42161: ["奇台县"],42162: ["吉木萨尔县"],42163: ["木垒哈萨克自治县"]}],42164: ["博尔塔拉蒙古自治州", 0, {42165: ["博乐市"],42166: ["精河县"],42167: ["温泉县"]}],42168: ["巴音郭楞蒙古自治州", 0, {42169: ["库尔勒市"],42170: ["轮台县"],42171: ["尉犁县"],42172: ["若羌县"],42173: ["且末县"],42174: ["焉耆回族自治县"],42175: ["和静县"],42176: ["和硕县"],42177: ["博湖县"]}],42178: ["阿克苏地区", 0, {42179: ["阿克苏市"],42180: ["温宿县"],42181: ["库车县"],42182: ["沙雅县"],42183: ["新和县"],42184: ["拜城县"],42185: ["乌什县"],42186: ["阿瓦提县"],42187: ["柯坪县"]}],42188: ["克孜勒苏柯尔克孜自治州", 0, {42189: ["阿图什市"],42190: ["阿克陶县"],42191: ["阿合奇县"],42192: ["乌恰县"]}],42193: ["喀什地区", 0, {2303: ["喀什市"],42194: ["疏附县"],42195: ["疏勒县"],42196: ["英吉沙县"],42197: ["泽普县"],42198: ["莎车县"],42199: ["叶城县"],42200: ["麦盖提县"],42201: ["岳普湖县"],42202: ["伽师县"],42203: ["巴楚县"],42204: ["塔什库尔干塔吉克自治县"]}],42205: ["和田地区", 0, {42206: ["和田市"],42207: ["和田县"],42208: ["墨玉县"],42209: ["皮山县"],42210: ["洛浦县"],42211: ["策勒县"],42212: ["于田县"],42213: ["民丰县"]}],42214: ["伊犁哈萨克自治州", 0, {2306: ["伊宁市"],42215: ["奎屯市"],42216: ["伊宁县"],42217: ["察布查尔锡伯自治县"],42218: ["霍城县"],42219: ["巩留县"],42220: ["新源县"],42221: ["昭苏县"],42222: ["特克斯县"],42223: ["尼勒克县"]}],42224: ["塔城地区", 0, {42225: ["塔城市"],42226: ["乌苏市"],42227: ["额敏县"],42228: ["沙湾县"],42229: ["托里县"],42230: ["裕民县"],42231: ["和布克赛尔蒙古自治县"]}],42232: ["阿勒泰地区", 0, {42233: ["阿勒泰市"],42234: ["布尔津县"],42235: ["富蕴县"],42236: ["福海县"],42237: ["哈巴河县"],42238: ["青河县"],42239: ["吉木乃县"]}],42240: ["阿拉尔市"],42241: ["图木舒克市"],42242: ["五家渠市"]}],24: ["宁夏", 1, {2400: ["银川市", 0, {2401: ["贺兰县"],2402: ["永宁县"],2412: ["灵武市"],42123: ["兴庆区"],42124: ["西夏区"],42125: ["金凤区"]}],2404: ["石嘴山市", 0, {2403: ["平罗县"],42126: ["大武口区"],42127: ["惠农区"]}],2406: ["吴忠市", 0, {2407: ["青铜峡市"],2409: ["盐池县"],42128: ["利通区"],42129: ["同心县"],42295: ["红寺堡区"]}],2408: ["固原市", 0, {42130: ["原州区"],42131: ["西吉县"],42132: ["隆德县"],42133: ["泾源县"],42134: ["彭阳县"]}],2411: ["中卫市", 0, {2410: ["中宁县"],42135: ["沙坡头区"],42136: ["海原县"]}]}],25: ["青海", 0, {2500: ["西宁市", 0, {2501: ["大通回族土族自治县"],2512: ["湟中县"],2514: ["湟源县"],42098: ["城东区"],42099: ["城中区"],42100: ["城西区"],42101: ["城北区"]}],2528: ["海东地区", 0, {2502: ["平安县"],2511: ["乐都县"],2513: ["互助土族自治县"],2515: ["民和回族土族自治县"],2516: ["循化撒拉族自治县"],2517: ["化隆回族自治县"]}],42102: ["海北藏族自治州", 0, {2508: ["门源回族自治县"],2529: ["海晏县"],42103: ["祁连县"],42104: ["刚察县"]}],42105: ["黄南藏族自治州", 0, {2503: ["同仁县"],2510: ["河南蒙古族自治县"],2518: ["尖扎县"],2519: ["泽库县"]}],42106: ["海南藏族自治州", 0, {2504: ["共和县"],2526: ["贵德县"],42107: ["同德县"],42108: ["兴海县"],42109: ["贵南县"]}],42110: ["果洛藏族自治州", 0, {2505: ["玛沁县"],42111: ["班玛县"],42112: ["甘德县"],42113: ["达日县"],42114: ["久治县"],42115: ["玛多县"]}],42116: ["玉树藏族自治州", 0, {2506: ["玉树县"],42117: ["杂多县"],42118: ["称多县"],42119: ["治多县"],42120: ["囊谦县"],42121: ["曲麻莱县"]}],42122: ["海西蒙古族藏族自治州", 0, {2507: ["德令哈市"],2509: ["格尔木市"],2521: ["乌兰县"],2522: ["都兰县"],2523: ["天峻县"]}]}],26: ["西藏", 1, {2600: ["拉萨市", 0, {41987: ["城关区"],41988: ["林周县"],41989: ["当雄县"],41990: ["尼木县"],41991: ["曲水县"],41992: ["堆龙德庆县"],41993: ["达孜县"],41994: ["墨竹工卡县"]}],2602: ["山南", 0, {42001: ["乃东县"],42002: ["扎囊县"],42003: ["贡嘎县"],42004: ["桑日县"],42005: ["琼结县"],42006: ["曲松县"],42007: ["措美县"],42008: ["洛扎县"],42009: ["加查县"],42010: ["隆子县"],42011: ["错那县"],42012: ["浪卡子县"]}],2615: ["阿里地区", 0, {2614: ["措勤县"],42037: ["普兰县"],42038: ["札达县"],42039: ["噶尔县"],42040: ["日土县"],42041: ["革吉县"],42042: ["改则县"]}],41995: ["昌都地区", 0, {2604: ["昌都县"],2605: ["江达县"],2606: ["芒康县"],2607: ["八宿县"],2608: ["洛隆县"],2609: ["丁青县"],41996: ["贡觉县"],41997: ["类乌齐县"],41998: ["察雅县"],41999: ["左贡县"],42e3: ["边坝县"]}],42013: ["日喀则地区", 0, {2601: ["日喀则市"],42014: ["南木林县"],42015: ["江孜县"],42016: ["定日县"],42017: ["萨迦县"],42018: ["拉孜县"],42019: ["昂仁县"],42020: ["谢通门县"],42021: ["白朗县"],42022: ["仁布县"],42023: ["康马县"],42024: ["定结县"],42025: ["仲巴县"],42026: ["亚东县"],42027: ["吉隆县"],42028: ["聂拉木县"],42029: ["萨嘎县"],42030: ["岗巴县"]}],42031: ["那曲地区", 0, {2610: ["巴青县"],2611: ["比如县"],2612: ["那曲县"],2613: ["班戈县"],2616: ["索县"],42032: ["嘉黎县"],42033: ["聂荣县"],42034: ["安多县"],42035: ["申扎县"],42036: ["尼玛县"]}],42043: ["林芝地区", 0, {2603: ["林芝县"],42044: ["工布江达县"],42045: ["米林县"],42046: ["墨脱县"],42047: ["波密县"],42048: ["察隅县"],42049: ["朗县"]}]}],27: ["广西", 1, {2700: ["南宁市", 0, {2702: ["武鸣县"],2703: ["宾阳县"],2704: ["横县"],2706: ["邕宁区"],2748: ["上林县"],2752: ["马山县"],2756: ["隆安县"],41736: ["兴宁区"],41737: ["青秀区"],41738: ["江南区"],41739: ["西乡塘区"],41740: ["良庆区"]}],2701: ["防城港市", 0, {2787: ["上思县"],2788: ["防城区"],41757: ["港口区"],41758: ["东兴市"]}],2712: ["柳州市", 0, {2707: ["融安县"],2708: ["三江侗族自治县"],2709: ["柳城县"],2710: ["融水苗族自治县"],2713: ["鹿寨县"],2714: ["柳江县"],41741: ["城中区"],41742: ["鱼峰区"],41743: ["柳南区"],41744: ["柳北区"]}],2722: ["桂林市", 0, {2716: ["平乐县"],2717: ["永福县"],2718: ["恭城瑶族自治县"],2719: ["阳朔县"],2720: ["临桂县"],2721: ["灵川县"],2763: ["灌阳县"],2764: ["兴安县"],2765: ["全州县"],2766: ["龙胜各族自治县"],2767: ["资源县"],41745: ["秀峰区"],41746: ["叠彩区"],41747: ["象山区"],41748: ["七星区"],41749: ["雁山区"],41750: ["荔浦县"]}],2723: ["梧州市", 0, {2724: ["苍梧县"],2725: ["藤县"],2726: ["蒙山县"],2727: ["岑溪市"],41751: ["万秀区"],41752: ["蝶山区"],41753: ["长洲区"]}],2733: ["玉林市", 0, {2729: ["北流市"],2730: ["陆川县"],2731: ["博白县"],2732: ["容县"],41764: ["玉州区"],41765: ["兴业县"]}],2734: ["百色市", 0, {2768: ["田阳县"],2769: ["乐业县"],2770: ["靖西县"],2771: ["田东县"],2772: ["平果县"],2773: ["德保县"],2774: ["那坡县"],2775: ["田林县"],2776: ["隆林各族自治县"],2777: ["西林县"],2778: ["凌云县"],41766: ["右江区"]}],2735: ["钦州市", 0, {2785: ["灵山县"],2786: ["浦北县"],41759: ["钦南区"],41760: ["钦北区"]}],2736: ["河池市", 0, {2738: ["罗城仫佬族自治县"],2739: ["南丹县"],2740: ["环江毛南族自治县"],2743: ["都安瑶族自治县"],2744: ["东兰县"],2746: ["天峨县"],2747: ["巴马瑶族自治县"],41768: ["金城江区"],41769: ["凤山县"],41770: ["大化瑶族自治县"],41771: ["宜州市"]}],2741: ["北海市", 0, {2742: ["合浦县"],41754: ["海城区"],41755: ["银海区"],41756: ["铁山港区"]}],2750: ["崇左市", 0, {2749: ["凭祥市"],2751: ["宁明县"],2753: ["龙州县"],2754: ["大新县"],2755: ["天等县"],2757: ["扶绥县"],41773: ["江洲区"]}],2758: ["来宾市", 0, {2711: ["金秀瑶族自治县"],2759: ["忻城县"],2760: ["合山市"],2761: ["武宣县"],2762: ["象州县"],41772: ["兴宾区"]}],2779: ["贺州市", 0, {2728: ["昭平县"],2780: ["钟山县"],2781: ["富川瑶族自治县"],41767: ["八步区"]}],2784: ["贵港市", 0, {2782: ["桂平市"],2783: ["平南县"],41761: ["港北区"],41762: ["港南区"],41763: ["覃塘区"]}]}],28: ["广东", 0, {2800: ["深圳市", 0, {41678: ["罗湖区"],41679: ["福田区"],41680: ["南山区"],41681: ["宝安区"],41682: ["龙岗区"],41683: ["盐田区"]}],2801: ["广州市", 0, {2813: ["番禺区"],2815: ["从化市"],2816: ["增城市"],41667: ["荔湾区"],41668: ["越秀区"],41669: ["海珠区"],41670: ["天河区"],41671: ["白云区"],41672: ["黄埔区"],41673: ["花都区"],41674: ["南沙区"],41675: ["萝岗区"]}],2802: ["珠海市", 0, {2847: ["斗门区"],41684: ["香洲区"],41685: ["金湾区"]}],2803: ["中山市"],2804: ["汕头市", 0, {2845: ["澄海区"],2846: ["南澳县"],2873: ["潮阳区"],41686: ["龙湖区"],41687: ["金平区"],41688: ["濠江区"],41689: ["潮南区"]}],2805: ["汕尾市", 0, {41708: ["城区"],41709: ["海丰县"],41710: ["陆河县"],41711: ["陆丰市"]}],2809: ["茂名市", 0, {41698: ["茂南区"],41699: ["茂港区"],41700: ["电白县"],41701: ["高州市"],41702: ["化州市"],41703: ["信宜市"]}],2810: ["东莞市"],2817: ["江门市", 0, {2818: ["鹤山市"],2819: ["新会区"],2820: ["台山市"],2821: ["开平市"],2822: ["恩平市"],41691: ["蓬江区"],41692: ["江海区"]}],2823: ["韶关市", 0, {2824: ["乐昌市"],2825: ["仁化县"],2826: ["始兴县"],2827: ["翁源县"],2828: ["南雄市"],2829: ["新丰县"],2830: ["乳源瑶族自治县"],2831: ["曲江区"],41676: ["武江区"],41677: ["浈江区"]}],2833: ["惠州市", 0, {2832: ["博罗县"],2834: ["惠阳区"],2835: ["惠东县"],2836: ["龙门县"],41706: ["惠城区"]}],2837: ["梅州市", 0, {2838: ["梅县"],2839: ["蕉岭县"],2840: ["大埔县"],2841: ["丰顺县"],2842: ["五华县"],2843: ["兴宁市"],2844: ["平远县"],41707: ["梅江区"]}],2848: ["佛山市", 0, {2811: ["顺德区"],2849: ["南海区"],2850: ["三水区"],2851: ["高明区"],41690: ["禅城区"]}],2853: ["肇庆市", 0, {2852: ["高要市"],2854: ["封开县"],2855: ["德庆县"],2856: ["怀集县"],2857: ["广宁县"],2858: ["四会市"],41704: ["端州区"],41705: ["鼎湖区"]}],2859: ["湛江市", 0, {2861: ["遂溪县"],2862: ["廉江市"],2863: ["吴川市"],2864: ["徐闻县"],41693: ["赤坎区"],41694: ["霞山区"],41695: ["坡头区"],41696: ["麻章区"],41697: ["雷州市"]}],2865: ["河源市", 0, {2806: ["紫金县"],2812: ["龙川县"],2866: ["东源县"],2867: ["连平县"],2868: ["和平县"],41712: ["源城区"]}],2869: ["潮州市", 0, {41723: ["湘桥区"],41724: ["潮安县"],41725: ["饶平县"]}],2870: ["阳江市", 0, {41713: ["江城区"],41714: ["阳西县"],41715: ["阳东县"],41716: ["阳春市"]}],2871: ["揭阳市", 0, {41726: ["榕城区"],41727: ["揭东县"],41728: ["揭西县"],41729: ["惠来县"],41730: ["普宁市"]}],2872: ["清远市", 0, {2807: ["佛冈县"],2808: ["英德市"],41717: ["清城区"],41718: ["阳山县"],41719: ["连山壮族瑶族自治县"],41720: ["连南瑶族自治县"],41721: ["清新县"],41722: ["连州市"]}],2874: ["云浮市", 0, {41731: ["云城区"],41732: ["新兴县"],41733: ["郁南县"],41734: ["云安县"],41735: ["罗定市"]}]}],29: ["福建", 0, {2900: ["福州市", 0, {2903: ["罗源县"],2904: ["连江县"],2905: ["长乐市"],2906: ["福清市"],2907: ["平潭县"],2908: ["永泰县"],2909: ["闽清县"],41430: ["鼓楼区"],41431: ["台江区"],41432: ["仓山区"],41433: ["马尾区"],41434: ["晋安区"],41435: ["闽侯县"]}],2901: ["厦门市", 0, {2911: ["同安区"],41436: ["思明区"],41437: ["海沧区"],41438: ["湖里区"],41439: ["集美区"],41440: ["翔安区"]}],2920: ["宁德市", 0, {2912: ["福安市"],2913: ["柘荣县"],2914: ["福鼎市"],2915: ["霞浦县"],2916: ["古田县"],2917: ["屏南县"],2918: ["周宁县"],2919: ["寿宁县"],41457: ["蕉城区"]}],2921: ["莆田市", 0, {2923: ["仙游县"],41441: ["城厢区"],41442: ["涵江区"],41443: ["荔城区"],41444: ["秀屿区"]}],2924: ["泉州市", 0, {2925: ["石狮市"],2926: ["晋江市"],2927: ["惠安县"],2928: ["南安市"],2929: ["安溪县"],2930: ["永春县"],2931: ["德化县"],41447: ["鲤城区"],41448: ["丰泽区"],41449: ["洛江区"],41450: ["泉港区"],41451: ["金门县"]}],2932: ["漳州市", 0, {2933: ["长泰县"],2934: ["龙海市"],2935: ["漳浦县"],2936: ["东山县"],2938: ["诏安县"],2939: ["平和县"],2940: ["南靖县"],2941: ["华安县"],41452: ["芗城区"],41453: ["龙文区"],41454: ["云霄县"]}],2942: ["龙岩市", 0, {2943: ["漳平市"],2944: ["永定县"],2945: ["上杭县"],2946: ["武平县"],2947: ["长汀县"],2948: ["连城县"],41456: ["新罗区"]}],2949: ["三明市", 0, {2950: ["沙县"],2951: ["尤溪县"],2952: ["大田县"],2953: ["永安市"],2954: ["清流县"],2955: ["宁化县"],2956: ["明溪县"],2957: ["建宁县"],2958: ["泰宁县"],2959: ["将乐县"],41445: ["梅列区"],41446: ["三元区"]}],2960: ["南平市", 0, {2902: ["建阳市"],2961: ["浦城县"],2962: ["松溪县"],2963: ["政和县"],2964: ["建瓯市"],2965: ["顺昌县"],2966: ["邵武市"],2967: ["光泽县"],2968: ["武夷山市"],41455: ["延平区"]}]}],30: ["海南", 0, {3e3: ["海口市", 0, {41774: ["秀英区"],41775: ["龙华区"],41776: ["琼山区"],41777: ["美兰区"]}],3001: ["三亚市"],3003: ["琼海市"],3004: ["儋州市"],3005: ["文昌市"],3006: ["东方市"],3008: ["万宁市"],3009: ["定安县"],3010: ["屯昌县"],3011: ["澄迈县"],3012: ["临高县"],41778: ["五指山市"],41779: ["白沙黎族自治县"],41780: ["昌江黎族自治县"],41781: ["乐东黎族自治县"],41782: ["陵水黎族自治县"],41783: ["保亭黎族苗族自治县"],41784: ["琼中黎族苗族自治县"],41785: ["西沙群岛"],41786: ["南沙群岛"],41787: ["中沙群岛的岛礁及其海域"]}],31: ["台湾", 1, {3100: ["台北市"],3101: ["高雄市"],3102: ["台中市"],3107: ["台南市"],3115: ["南投县"],42243: ["金门县"]}],32: ["香港", 1, {3200: ["香港", 1]}],33: ["澳门", 1, {3300: ["澳门", 1]}],42245: ["海外", 1, {42246: ["海外", 1]}]}), window._regionMap
    }
    return e
}), define("components/paipai_region/com", ["require", "components/paipai_region/region_data"], function(e) {
    function t(e) {
        return "string" == typeof e ? document.getElementById(e) : e
    }
    function i(e, t, i) {
        var n = new Option(t, i);
        return e.options[e.options.length] = n, n
    }
    function n(e) {
        function n() {
            o(), c(), a(), r(), l()
        }
        function o() {
            if (d.initValue)
                for (var e in p) {
                    if (e.toString() === d.initValue.toString())
                        return void (d.provId = d.initValue);
                    if ("object" == typeof p[e][2]) {
                        var t = e;
                        for (var i in p[e][2]) {
                            if (i.toString() === d.initValue.toString())
                                return d.provId = t, void (d.cityId = i);
                            var n = p[e][2][i][2];
                            if ("object" == typeof n) {
                                var s = i;
                                for (var o in n)
                                    if (o.toString() === d.initValue.toString())
                                        return d.provId = t, d.cityId = s, void (d.areaId = o)
                            }
                        }
                    }
                }
        }
        function a() {
            var e = d.provinceHander;
            if (e) {
                if (e.options.length > 0)
                    return;
                i(e, "请选择省", "");
                for (var t in p)
                    i(e, p[t][0], t);
                d.provId && (e.value = d.provId)
            }
        }
        function r() {
            var e = d.cityHander;
            if (e) {
                if (!d.provId)
                    return;
                var t = p[d.provId][2];
                e.options.length = 0, i(e, "请选择市", "");
                for (var n in t)
                    i(e, t[n][0], n);
                d.cityId && (e.value = d.cityId)
            }
        }
        function l() {
            var e = d.areaHander;
            if (e && d.area) {
                var t;
                if (!d.cityId)
                    return void (e[0].options.length = 0);
                t = p[d.provId][2][d.cityId][2], e.options.length = 0, i(e, "请选择地区", "");
                for (var n in t)
                    i(e, t[n][0], n);
                d.areaId && e.val(d.areaId)
            }
        }
        function c() {
            function e() {
                var e = this.getAttribute("vprovince"), i = this.getAttribute("vcity"), s = this.getAttribute("varea"), o = this.getAttribute("stype"), a = this.value, r = a;
                d.onSelect(this, o), "province" === o && "" === a && (d.onSelectBlankProv(this), t(i) ? t(i).options.length = 0 : "", s && t(s) ? t(s).options.length = 0 : ""), "city" === o && "" === a && (d.onSelectBlankCity(this), r = t(e).value), d.provId = "", d.cityId = "", d.areaId = "", d.initValue = r, n()
            }
            d.cityHander && (d.provinceHander.onchange = "", d.provinceHander.onchange = e), d.areaHander && (d.cityHander.onchange = "", d.cityHander.onchange = e)
        }
        var d = {province: "",city: "",area: "",provId: "",cityId: "",areaId: "",initValue: "",onSelect: function() {
                return !0
            },onSelectBlankProv: function() {
                return !0
            },onSelectBlankCity: function() {
                return !0
            }};
        for (var u in e)
            d[u] = e[u];
        d.provinceHander = "" === d.province ? null : t(d.province), d.cityHander = "" === d.city ? null : t(d.city), d.areaHander = "" === d.area ? null : t(d.area), d.provinceHander && (d.provinceHander.setAttribute("stype", "province"), d.provinceHander.setAttribute("vprovince", d.province), d.provinceHander.setAttribute("vcity", d.city), d.provinceHander.setAttribute("varea", d.area)), d.cityHander && (d.cityHander.setAttribute("stype", "city"), d.cityHander.setAttribute("vprovince", d.province), d.cityHander.setAttribute("vcity", d.city), d.cityHander.setAttribute("varea", d.area)), d.areaHander && (d.cityHander.setAttribute("stype", "area"), d.cityHander.setAttribute("vprovince", d.province), d.cityHander.setAttribute("vcity", d.city), d.cityHander.setAttribute("varea", d.area)), d.initaddress = n;
        var p = s();
        return n(), d
    }
    var s = e("components/paipai_region/region_data");
    return n
}), define("text!templates/other_info.html", [], function() {
    return '<div class="info-group-title vbox">\n    <div class="group-inner">物流/其它</div>\n</div>\n<div class="info-group-cont vbox">\n    <div class="group-inner">\n        <div class="control-group">\n            <label class="control-label"><em class="required">*</em>运费设置：</label>\n            <div class="controls">\n                <label class="radio">\n                    <input type="radio" name="" value="0" checked>统一邮费\n                    <div class="input-prepend">\n                        <span class="add-on">￥</span>\n                        <input type="text" name="postage" value="<%= postage %>" class="input-small js-postage">\n                    </div>\n                </label>\n                <label class="radio">\n                    <input type="radio" name="" value="1" disabled><span class="gray">运费模版（即将支持）</span>\n                </label>\n            </div>\n        </div>\n\n        <% if(class_type != \'0\') { %>\n        <div class="control-group">\n            <label class="control-label">商品所在地：</label>\n            <div class="controls">\n                <select class="js-location input-medium" name="province_id" id="js-province"></select>\n                <select class="js-location input-medium" name="city_id" id="js-city"></select>\n            </div>\n        </div>\n        <% } %>\n\n        <div class="control-group">\n            <label class="control-label">每人限购：</label>\n            <div class="controls">\n                <input type="text" name="quota" value="0" class="input-small js-quota">\n                <span class="gray">0 代表不限购</span>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label">要求留言：</label>\n            <div class="controls">\n                <input type="hidden" name="messages" />\n                <div id="messages-region"></div>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label">开售时间：</label>\n            <div class="controls">\n                <label class="radio">\n                    <input type="radio" name="sold_time" value="0" <% if (sold_time == \'0\') { %>checked<% } %>>立即开售\n                </label>\n                <label class="radio" for="sold_time">\n                    <input type="radio" id="sold_time" name="sold_time" value="1" <% if (sold_time == \'1\') { %>checked<% } %>>定时开售\n                    <input id="start_sold_time" name="start_sold_time" readonly class="input-medium js-sold-time <% if (sold_time == \'0\') { %>v-hide<% } %>" type="text" value="<% if (sold_time == \'1\') { %><%= start_sold_time %><% } %>">\n                </label>\n            </div>\n        </div>\n        <!-- <div class="control-group">\n            <label class="control-label">停售时间：</label>\n            <div class="controls">\n                <input name="take_down_time" readonly class="input-medium" type="text" value="<%=take_down_time %>">\n            </div>\n        </div> -->\n        <div class="control-group">\n            <label class="control-label">会员折扣：</label>\n            <div class="controls">\n                <label class="checkbox inline">\n                    <input type="checkbox" name="join_level_discount" value="1" <% if (join_level_discount == \'1\') { %>checked<% } %>>参加会员折扣\n                </label>\n                <a style="display: none;" href="javascript:;" class="js-help-notes circle-help">?</a>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label">发票：</label>\n            <div class="controls">\n                <label class="radio inline">\n                    <input type="radio" name="invoice" value="0" <% if (invoice == \'0\') { %>checked<% } %> >无\n                </label>\n                <label class="radio inline">\n                    <input type="radio" name="invoice" value="1" <% if (invoice == \'1\') { %>checked<% } %> >有\n                </label>\n            </div>\n        </div>\n        <div class="control-group">\n            <label class="control-label">保修：</label>\n            <div class="controls">\n                <label class="radio inline">\n                    <input type="radio" name="warranty" value="0" <% if (warranty == \'0\') { %>checked<% } %>>无\n                </label>\n                <label class="radio inline">\n                    <input type="radio" name="warranty" value="1" <% if (warranty == \'1\') { %>checked<% } %>>有\n                </label>\n            </div>\n        </div>\n    </div>\n</div>\n'
}), define("text!templates/messages.html", [], function() {
    return '<div class="js-message-container message-container"></div>\n<div class="message-add">\n    <a href="javascript:;" class="js-add-message control-action">+ 添加字段</a>\n    <a style="display: none;" href="javascript:;" class="js-help-notes circle-help">?</a>\n</div>\n'
}), define("models/message_item", ["require", "underscore", "backbone"], function(e) {
    var t = (e("underscore"), e("backbone")), i = t.Model.extend({defaults: {name: "留言",type: "text",multiple: "0",required: "1"}});
    return i
}), define("text!templates/message_item.html", [], function() {
    return '<input type="text" name="name" value="<%= name %>" class="input-mini message-input" maxlength="5">\n<select class="input-small message-input" name="type">\n    <option value="text" <% if (type == \'text\') { %>selected<% } %>>文本格式</option>\n    <option value="tel" <% if (type == \'tel\') { %>selected<% } %>>数字格式</option>\n    <option value="email" <% if (type == \'email\') { %>selected<% } %>>邮件格式</option>\n</select>\n<label class="checkbox inline message-input">\n    <input type="checkbox" name="multiple" value="<%= multiple %>" <% if (multiple == 1) { %> checked<% } %> <% if (type != \'text\') { %>disabled<% } %>>多行\n</label>\n<label class="checkbox inline message-input">\n    <input type="checkbox" name="required" value="<%= required %>" <% if (required == 1) { %> checked<% } %>>必填\n</label>\n<a href="javascript:;" class="js-remove-message remove-message">删除</a>\n'
}), define("views/message_item", ["require", "underscore", "backbone", "marionette", "core/event", "commons/utils", "text!templates/message_item.html"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = e("core/event"), o = e("commons/utils"), a = e("text!templates/message_item.html"), r = n.ItemView.extend({tagName: "div",className: "message-item",template: t.template(a),ui: {typeSelect: "select",multipleChk: '[name="multiple"]'},events: {"click .js-remove-message": "removeMessage","change @ui.typeSelect": "onTypeChange"},_modelBinder: void 0,initialize: function(e) {
            var t = this;
            t.setConfig(e), t._modelBinder = new i.ModelBinder({modelSetOptions: {validate: !0}}), t.listenTo(t.model, "change", t.modelChanged)
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },onRender: function() {
        },onShow: function() {
            var e = this;
            e.initDataBindings()
        },modelChanged: function() {
            s.trigger("message_item:change")
        },initDataBindings: function() {
            var e = this, t = {name: '[name="name"]',type: '[name="type"]',multiple: {selector: '[name="multiple"]',converter: o.booleanConverter},required: {selector: '[name="required"]',converter: o.booleanConverter}};
            e._modelBinder.bind(e.model, e.el, t)
        },removeMessage: function(e) {
            var t = this;
            e.preventDefault(), e.stopPropagation();
            var i = t.model.collection;
            i.remove(t.model)
        },onTypeChange: function() {
            var e = this, t = e.ui.multipleChk, i = e.ui.typeSelect.val(), n = "text" !== i, s = {disabled: n};
            n && (s.checked = !1), t.prop(s).trigger("change")
        }});
    return r
}), define("views/messages", ["require", "underscore", "backbone", "marionette", "core/event", "core/utils", "text!templates/messages.html", "models/message_item", "views/message_item"], function(e) {
    var t = e("underscore"), i = (e("backbone"), e("marionette")), n = e("core/event"), s = e("core/utils"), o = e("text!templates/messages.html"), a = e("models/message_item"), r = e("views/message_item"), l = i.CompositeView.extend({template: t.template(o),itemView: r,itemViewContainer: ".js-message-container",events: {"click .js-add-message": "addMessage"},collectionEvents: {"add remove reset": "reverseUpdate"},maxSize: 20,initialize: function() {
            var e = this;
            e.listenTo(n, "message_item:change", e.reverseUpdate)
        },reverseUpdate: function() {
            var e = this, t = e.collection.toJSON();
            n.trigger("messages:update", t)
        },addMessage: function(e) {
            var t = this;
            if (e.preventDefault(), !t.checkMaxSize())
                return s.errorNotify("留言最多支持 20 组。"), !1;
            var i = new a;
            t.collection.add(i)
        },checkMaxSize: function() {
            var e = this, t = e.collection.size(), i = t < e.maxSize;
            return i
        }});
    return l
}), define("views/other_info", ["require", "underscore", "backbone", "marionette", "core/event", "components/validation/validate", "commons/utils", "components/paipai_region/com", "backbone.modelbinder", "jqueryui", "datetimepicker", "text!templates/other_info.html", "views/messages"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = e("core/event"), o = (e("components/validation/validate"), e("commons/utils")), a = e("components/paipai_region/com");
    e("backbone.modelbinder"), e("jqueryui"), e("datetimepicker");
    var r = e("text!templates/other_info.html"), l = e("views/messages"), c = n.Layout.extend({tagName: "div",className: "goods-info-group-inner",template: t.template(r),ui: {citySelect: "#js-city",postageTxt: ".js-postage",soldTimeRadio: 'input[name="sold_time"]',startSoldTxt: 'input[name="start_sold_time"]',takeDownTxt: 'input[name="take_down_time"]'},events: {"change @ui.soldTimeRadio": "showTimepicker"},regions: {messagesRegion: "#messages-region"},_modelBinder: void 0,initialize: function(e) {
            var t = this;
            t.setConfig(e), t._modelBinder = new i.ModelBinder, t.listenTo(t.model, "change:shop_method", t.onShopMethodChange)
        },onClose: function() {
            var e = this;
            e._modelBinder.unbind()
        },onRender: function() {
            var e = this, t = e.model.get("shop_method");
            "0" == t && e.hideSelf()
        },onShow: function() {
            var e = this;
            e.initMessages(), e.initTimepicker(), e.initTakeDownTime(), e.initDataBindings(), e.initLocation()
        },setConfig: function(e) {
            var i = this;
            i.settings = {}, t(i.settings).extend(e)
        },initLocation: function() {
            var e = this, t = e.model.get("class_type");
            if ("0" == t)
                return !1;
            var i = e.model.get("city_id");
            a({province: "js-province",city: "js-city",initValue: i})
        },initMessages: function() {
            var e = this, t = e.model.get("messages");
            e.messageList = new i.Collection(t);
            var n = new l({collection: e.messageList,parentModel: e.model});
            e.messagesRegion.show(n), e.listenTo(s, "messages:update", e.updateMessages)
        },updateMessages: function(e) {
            var t = this;
            t.model.set({messages: e})
        },initDataBindings: function() {
            var e = this, i = {postage: {selector: '[name="postage"]',converter: e.postageConverter},quota: '[name="quota"]',join_level_discount: {selector: '[name="join_level_discount"]',converter: o.booleanConverter},invoice: '[name="invoice"]',warranty: '[name="warranty"]'};
            "0" != e.model.get("class_type") && t(i).extend({province_id: {selector: '[name="province_id"]',converter: e.provinceConverter.bind(e)},city_id: {selector: '[name="city_id"]',converter: e.cityConverter.bind(e)}}), e._modelBinder.bind(e.model, e.el, i)
        },postageConverter: function(e, t) {
            return t = o.numberValueFix(t)
        },provinceConverter: function(e, t, i, n, s) {
            var o = this;
            if (t) {
                var a = $(s[0]), r = a.find("option:selected"), l = r.text();
                n.set("province", l, {silent: !0}), o.ui.citySelect.trigger("change")
            }
            return t
        },cityConverter: function(e, t, i, n, s) {
            if (t) {
                var o = $(s[0]), a = o.find("option:selected"), r = a.text();
                n.set("city", r, {silent: !0})
            }
            return t
        },onPostageTxtBlur: function(e) {
            var t = $(e.target), i = t.val(), n = o.numberValueFix(i);
            t.val(n)
        },initTimepicker: function() {
            var e = this, t = e.ui.startSoldTxt, i = +new Date, n = new Date(i + 6e4);
            t.datetimepicker({dateFormat: "yy-mm-dd",timeFormat: "HH:mm:ss",minDate: n,showSecond: !0,onSelect: function(i) {
                    e.model.set({start_sold_time: i}), t.siblings("input").trigger("click")
                }.bind(e)})
        },showTimepicker: function(e) {
            var t = this, i = t.ui.startSoldTxt, n = $(e.target), s = n.val();
            t.model.set({sold_time: s}, {silent: !0}), "1" == s && (i.removeClass("v-hide"), t.ui.startSoldTxt.datetimepicker("show"))
        },initTakeDownTime: function() {
            {
                var e = this, t = e.ui.takeDownTxt, i = +new Date;
                new Date(i + 6e4)
            }
            t.datetimepicker({dateFormat: "yy-mm-dd",timeFormat: "HH:mm:ss",minDate: new Date,showSecond: !0,onSelect: function(t) {
                    e.model.set({take_down_time: t})
                }.bind(e)})
        },onShopMethodChange: function(e, t) {
            var i = this;
            "0" == t ? i.hideSelf() : i.showSelf()
        },getData: function() {
            var e = this, t = e.model.toJSON();
            return t
        },showSelf: function() {
            var e = this;
            e.$el.show()
        },hideSelf: function() {
            var e = this;
            e.$el.hide()
        }});
    return c
}), define("text!templates/step2.html", [], function() {
    return '<div id="base-info-region" class="goods-info-group"></div>\n<div id="sku-info-region" class="goods-info-group"></div>\n<div id="goods-info-region" class="goods-info-group"></div>\n<div id="other-info-region" class="goods-info-group"></div>\n<div class="app-actions">\n    <div class="form-actions ta-c">\n        <button data-next-step="3" class="btn btn-primary js-switch-step">下一步</button>\n    </div>\n</div>\n'
}), define("views/step2", ["require", "underscore", "backbone", "marionette", "vendor/nprogress", "core/cache", "core/reqres", "core/event", "core/utils", "commons/utils", "views/base_info", "views/goods_info", "views/sku_stock_info", "views/other_info", "text!templates/step2.html"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = (e("vendor/nprogress"), e("core/cache")), o = e("core/reqres"), a = e("core/event"), r = (e("core/utils"), e("commons/utils")), l = e("views/base_info"), c = e("views/goods_info"), d = e("views/sku_stock_info"), u = e("views/other_info"), p = e("text!templates/step2.html"), h = n.Layout.extend({tagName: "form",className: "form-horizontal fm-goods-info",template: t.template(p),regions: {baseInfoRegion: "#base-info-region",skuStockInfoRegion: "#sku-info-region",goodsInfoRegion: "#goods-info-region",otherInfoRegion: "#other-info-region"},initialize: function() {
            var e = this;
            e.setupPropertyResp()
        },onRender: function() {
            var e = this;
            e.$el.attr("novalidate", !0)
        },onShow: function() {
            var e = this;
            e.initStep2()
        },init: function() {
            var e = this;
            e.initModules(), e.setupAtomBaseDataResp(), e.setupValidation(), e.model.isNew() && a.trigger("sku_stock:update")
        },initModules: function() {
            var e = this;
            e.initBaseInfo(), e.initGoodsInfo(), e.initSkuInfo(), e.initOtherInfo()
        },setupPropertyResp: function() {
            var e = this;
            e.propertyData = {}, o.setHandler("goods_property:get", function() {
                return e.propertyData
            })
        },setupValidation: function() {
            var e = this;
            i.Validation.bind(e), e.listenTo(e.model, "change", e.validateModel)
        },validateModel: function(e) {
            e.validate(e.changed)
        },initStep2: function() {
            var e = this, t = e.model.get("class_type");
            "0" == t ? e.init() : "1" == t && e.loadDataAndInit()
        },fixCid: function() {
            var e = this, t = e.model.get("cid"), i = e.model.get("goods_class");
            t || (t = e.getCid(i) || ""), e.model.set("cid", t, {silent: !0})
        },loadDataAndInit: function() {
            var e = this, t = e.model.get("cid");
            if (!t)
                return e.init(), !1;
            var i = {cid: t}, n = "goods_property_" + t, o = s.get(n);
            o ? e.loadDataSuccess(o) : e.fetchData(i)
        },getCid: function(e) {
            if (!e || t.isEmpty(e))
                return !1;
            var i = t(e).last(), n = i.cid;
            return n
        },cachePropertyData: function(e, t) {
            var i = "goods_property_" + e;
            s.set(i, t)
        },loadDataSuccess: function(e) {
            var t = this;
            t.updateSkuAtomBaseData(e), t.mergeData(e), t.init()
        },setupAtomBaseDataResp: function() {
            var e = this;
            e.atomBaseData = e.atomBaseData || {}, o.setHandler("atom_data:get", function(t) {
                var i = e.atomBaseData[t] || {};
                return i
            })
        },updateSkuAtomBaseData: function(e) {
            var i = this, n = t(e).where({is_sale: "1"}), s = {};
            t(n).each(function(e) {
                if (-1 !== e.name.indexOf("自定义"))
                    return !1;
                var i = [];
                t(e.option).each(function(e) {
                    var t = e.option_value || "";
                    -1 === t.indexOf("自定义") && i.push({id: -1,text: t})
                }), s[e.name] = i
            }), i.atomBaseData = s
        },getDefaultSkuNames: function(e) {
            var i = t(e).where({is_sale: "1"}), n = {}, s = 1;
            return t(i).each(function(e) {
                if (!e.class_id)
                    return !1;
                var t = "sku_name_" + s, i = t + "_value";
                n[t] = {id: e.class_id,text: e.name,editable: "0"}, n[i] = null, s += 1
            }), n
        },getGoodsAttrs: function(e) {
            var i = t(e).where({is_sale: "0"});
            return i
        },mergeData: function(e) {
            var t = this;
            t.attrsOptions = t.getGoodsAttrs(e);
            var i = t.model.previous("goods_class"), n = t.getCid(i), s = t.model.hasChanged("goods_class");
            if (t.model.isNew())
                t.mergeSkuData(e);
            else if (s && n)
                return t.clearModelAttrsValue(), t.clearModelSkuValue(), t.mergeSkuData(e), !1;
            t.mergeAttrsData(e)
        },mergeSkuData: function(e) {
            var i = this, n = {}, s = i.getDefaultSkuNames(e);
            t(n).extend(s), i.model.set(n)
        },mergeAttrsData: function() {
            var e = this;
            e.setAttrsDataValue()
        },clearModelSkuValue: function() {
            var e = this;
            r.removeSkuKeyValue(e.model, 0)
        },clearModelAttrsValue: function() {
            var e = this;
            e.model.set({attrs: null})
        },setAttrsDataValue: function() {
            var e = this, i = e.model.get("attrs");
            return !i || t.isEmpty(i) ? !1 : void t(e.attrsOptions).each(function(e) {
                var n = e.pid, s = t(i).findWhere({pid: n});
                s && (e.value = s.value)
            })
        },fetchData: function(e) {
            var t = this;
            r.fetchSaleProperty(e, function(i) {
                var n = i.data;
                t.propertyData = n, t.cachePropertyData(e.cid, n), t.loadDataSuccess(n)
            })
        },initBaseInfo: function() {
            var e = this, t = e.baseInfoView = new l({model: e.model,attrsOptions: e.attrsOptions});
            e.baseInfoRegion.show(t)
        },initGoodsInfo: function() {
            var e = this, t = e.goodsInfoView = new c({model: e.model});
            e.goodsInfoRegion.show(t)
        },initSkuInfo: function() {
            var e = this, t = e.skuStockInfoView = new d({model: e.model});
            e.skuStockInfoRegion.show(t)
        },initOtherInfo: function() {
            var e = this, t = e.otherInfoView = new u({model: e.model});
            e.otherInfoRegion.show(t)
        }});
    return h
}), define("text!templates/app.html", [], function() {
    return '<ul class="ui-nav-tab">\n    <li data-next-step="1" class="js-switch-step js-step-1"><a href="javascript:;">1.选择商品品类</a></li>\n    <li data-next-step="2" class="js-switch-step js-step-2"><a href="javascript:;">2.编辑基本信息</a></li>\n    <li data-next-step="3" class="js-switch-step js-step-3"><a href="javascript:;">3.编辑商品详情</a></li>\n</ul>\n<div id="step-content-region">\n</div>\n'
}), define("views/app_new", ["require", "underscore", "backbone", "marionette", "vendor/nprogress", "core/reqres", "core/event", "core/utils", "commons/utils", "models/goods", "models/app", "views/step1", "views/step2", "text!templates/app.html"], function(e) {
    var t = e("underscore"), i = e("backbone"), n = e("marionette"), s = e("vendor/nprogress"), o = e("core/reqres"), a = e("core/event"), r = e("core/utils"), l = e("commons/utils"), c = e("models/goods"), d = e("models/app"), u = e("views/step1"), p = e("views/step2"), h = e("text!templates/app.html"), m = n.Layout.extend({tagName: "div",className: "goods-edit-area",template: t.template(h),regions: {stepContentRegion: "#step-content-region"},ui: {tip: ".js-tip",switchTabs: "li.js-switch-step",switchBtn: "button.js-switch-step",switchLink: "a.js-switch-step"},events: {"click @ui.switchTabs": "switchStep","click @ui.switchBtn": "switchStep","click @ui.switchLink": "switchStep"},modelEvents: {},initialize: function() {
            var e = this;
            e.setServerUrl(), e.ajaxFlag = !1, e.listenTo(a, "goods:create", e.openCreateView), e.listenTo(a, "goods:edit", e.openEditView)
        },onRender: function() {
            var e = this;
            e.bindUIElements()
        },onShow: function() {
            var e = this;
            e.appModel = new d
        },setServerUrl: function() {
            var e = this, t = window._global.url.www;
            t += "goods" === window._global.goods_type ? "/showcase/goods/goods.json" : "/showcase/material/save.json", e.goodsUrl = t
        },showTip: function() {
            var e = this, t = e.model.get("class_type");
            "0" != t && e.ui.tip.show()
        },updateCurrentStep: function(e) {
            var t = this;
            t.currentStep = e
        },openView: function(e, t) {
            var i = this;
            i.updateCurrentStep(t);
            var n = i.model.isNew();
            n ? i["showStep" + i.currentStep + "View"]() : i.fetchAppData(e)
        },openCreateView: function(e) {
            var t = this;
            s.done(), t.appModel.set({id: null}), t.model = new c, (!e || e > 2) && (e = 1), t.openView(null, e), t.initModules()
        },openEditView: function(e, t) {
            var i = this;
            return e ? (i.model = new c({id: e}), t = t || 2, void i.openView(e, t)) : (r.errorNotify("请输入一个正确的商品ID。"), !1)
        },initModules: function() {
            var e = this;
            return e.initFlag ? !1 : (e.setAttrsResp(), e.setupValidation(), void (e.initFlag = !0))
        },setupValidation: function() {
            var e = this;
            i.Validation.bind(e), e.listenTo(e.model, "change", e.validateModel)
        },setAttrsResp: function() {
            var e = this, t = e.model;
            o.setHandler("goods_attr:get", function(e) {
                var i = t.get(e);
                return i
            })
        },validateModel: function(e) {
            e.validate(e.changed)
        },fetchAppData: function(e) {
            var t = this;
            r.clearNotify(), t.appModel.set("id", e), t.appModel.fetch({success: function(e, i) {
                    t.fetchSuccess(i)
                },error: function() {
                    t.fetchError()
                }})
        },fetchSuccess: function(e) {
            var t = this;
            s.done(), t.appData = e.data, t.goodsData = e.data.data[0], t.model.set(t.goodsData), t["showStep" + t.currentStep + "View"](), t.initModules()
        },fetchError: function() {
            r.errorNotify("获取商品数据失败。")
        },switchNavTab: function(e) {
            var t = this;
            t.ui.switchTabs.removeClass("active");
            var i = t.ui.switchTabs.filter(".js-step-" + e);
            i.addClass("active")
        },showStep1View: function() {
            var e = this;
            e.step1View || (e.step1View = new u({model: e.model})), console.warn(JSON.stringify(e.model)), e.switchNavTab(1), e.stepContentRegion.show(e.step1View)
        },showStep2View: function() {
            var e = this;
            e.step2View || (e.step2View = new p({model: e.model})), e.switchNavTab(2), e.stepContentRegion.show(e.step2View)
        },showStep3View: function(e) {
            window.location.href = e
        },toStep1: function() {
            var e = this;
            e.prepareToStep1()
        },toStep2: function() {
            var e = this, t = o.request("step1:validate");
            if (!t)
                return e.errorHandler(2), !1;
            var i = o.request("class_info:get", "cid"), n = e.model.get("cid"), s = e.model.get("sku_name_1");
            i !== n && s ? l.needConfirm("修改商品的综合类目信息需要重新编辑商品规格和库存信息，是否继续？", function() {
                e.prepareToStep2()
            }, null) : e.prepareToStep2()
        },toStep3: function() {
            var e = this, t = e.model.validate();
            console.info(t);
            var i = o.request("attrs:validate"), n = o.request("stock:validate");
            if (i || n > 0 || !e.model.isValid())
                return e.errorHandler(3), !1;
            var s = e.collectStep2Data();
            e.saveStep2Data(s)
        },prepareToStep1: function() {
            var e = this;
            e.showStep1View(), e.appNavigate(1)
        },prepareToStep2: function() {
            var e = this, t = o.request("class_info:has_changed");
            t && e.updateGoodsModel(), e.showStep2View(), e.appNavigate(2)
        },prepareToStep3: function(e) {
            var t = this, i = window._global.url.www + "/showcase/goods/detail#id=" + e.id;
            t.showStep3View(i)
        },collectStep2Data: function() {
            var e = this, t = e.getBaseData();
            return t.data[0] = e.model.toJSON(), t.data = JSON.stringify(t.data), t
        },getBaseData: function() {
            var e = this, t = e.appModel.toJSON();
            return t
        },saveStep2Data: function(e) {
            var t = this, i = t.$("button.js-switch-step");
            $.ajax({url: t.goodsUrl,type: "POST",dataType: "json",timeout: 8e3,cache: !1,data: e,beforeSend: function() {
                    t.ajaxFlag = !0, l.button(i, "保存中...", !0)
                },success: function(e) {
                    0 === e.code ? t.prepareToStep3(e.data) : (r.errorNotify(e.msg), l.button(i, "下一步", !1))
                },error: function() {
                    l.button(i, "下一步", !1)
                },complete: function() {
                    t.ajaxFlag = !1
                }})
        },switchStep: function(e) {
            var t = this;
            if (e.preventDefault(), t.ajaxFlag)
                return !1;
            var i = $(e.currentTarget), n = i.data("next-step");
            t["toStep" + n]()
        },appNavigate: function(e, t) {
            var n = this;
            n.updateCurrentStep(e);
            var s = n.processHash(e);
            t = "undefined" == typeof t ? !1 : t, i.history.navigate(s, {trigger: t})
        },processHash: function(e) {
            var t = i.history.fragment, n = r.deparam(t);
            return n.step = e, t = $.param(n)
        },errorHandler: function(e) {
            var t = this;
            return console.log(t.currentStep, e), e - t.currentStep > 1 ? (r.errorNotify("请一步步填写页面所需的信息。"), !1) : void t.focusFirstError()
        },focusFirstError: function() {
            var e = $(".error-message:eq(0)").parents(".control-group");
            if (0 === e.length)
                return !1;
            var t = e.offset(), i = t.top - 10;
            $(window).scrollTop(i)
        },updateGoodsModel: function() {
            var e = this, i = {}, n = o.request("class_info:get");
            t(i).extend(n), e.model.set(i)
        }});
    return m
}), define("app", ["require", "underscore", "backbone", "marionette", "vendor/nprogress", "core/utils", "views/app_new"], function(e) {
    var t = (e("underscore"), e("backbone"), e("marionette")), i = (e("vendor/nprogress"), e("core/utils"));
    window.Utils = i;
    var n = e("views/app_new"), s = new t.Application;
    return s.addRegions({appRegion: "#app-region"}), s.addInitializer(function(e) {
        var t = new n(e);
        s.appRegion.show(t)
    }), s
}), define("routers/router", ["require", "common", "underscore", "backbone", "marionette"], function(e) {
    var t = (e("common"), e("underscore"), e("backbone"), e("marionette")), i = t.AppRouter.extend({appRoutes: {"": "createGoods","id=:id&step=:step": "editGoods","id=:id": "editGoods","step=:step": "createGoods"}});
    return i
}), define("controllers/router", ["require", "underscore", "backbone", "marionette", "core/event"], function(e) {
    var t = (e("underscore"), e("backbone"), e("marionette"), e("core/event"));
    return {createGoods: function(e) {
            e = e ? Number(e) : "", t.trigger("goods:create", e)
        },editGoods: function(e, i) {
            i = i ? Number(i) : "", t.trigger("goods:edit", e, i)
        }}
}), define("main", ["require", "common", "backbone", "vendor/nprogress", "bootstrap", "components/help_notes/com", "components/message/message_bot_lite", "app", "routers/router", "controllers/router"], function(e) {
    var t = (e("common"), e("backbone")), i = e("vendor/nprogress");
    e("bootstrap"), e("components/help_notes/com");
    var n = e("components/message/message_bot_lite"), s = e("app"), o = e("routers/router"), a = e("controllers/router");
    return {initialize: function(e) {
            n.init(), i.start(), s.start(e), window.router = new o({controller: a}), t.history.start()
        }}
});
