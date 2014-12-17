define([ "jquery", "underscore" ], function($, _) {
    console.log("userBar loaded");
    var $sForm = $(".tb-search-form"), $sInput = $(".tb-search-input"), $iLoading = $(".icon-loading"), $iClear = $(".icon-clear"), $sResult = $(".search-result");
    return $sInput.on("focus", function() {
        $sForm.css({
            background: "#FFF"
        });
    }).on("keydown", function() {
        $.ajax("/userBarSearch", {
            type: "POST",
            data: {
                string: $sInput.val()
            },
            beforeSend: function() {
                $iClear.addClass("hide"), $iLoading.removeClass("hide");
            },
            success: function(res) {
                if ("object" != typeof res && (res = $.parseJSON(res)), 1 == res.success) {
                    var data = res.data, _tpl = _.template($("#tpl-tb-search").html())({
                        data: data
                    });
                    $sResult.html(_tpl).show(), $iLoading.addClass("hide"), $iClear.removeClass("hide");
                } else alert("搜索异常!");
            }
        }), $iClear.on("click", function() {
            $sForm.css({
                background: ""
            }), $sResult.hide(), $iLoading.addClass("hide"), $iClear.addClass("hide"), $sInput.val("");
        });
    }), $(".icon-cart").on("click", function() {
        return "block" == $(".tb-cart-dropdown-wrapper").css("display") ? $(".tb-cart-dropdown-wrapper").hide() : void $.ajax("/userBarCart", {
            beforeSend: function() {
                $(".tb-cart-dropdown-wrapper").show(), $(".tb-msg-dropdown-wrapper").hide(), $(".tb-user-dropdown").hide(), 
                $iClear.click();
            },
            success: function(res) {
                var _tpl, data = res.data;
                _tpl = res && "true" == res.success && 0 !== data.goods.length ? _.template($("#tpl-tb-cart").html())({
                    data: data
                }) : _.template($("#tpl-tb-cart-empty").html())(), $(".tb-cart-dropdown").html(_tpl);
            },
            error: function() {
                var _tpl = _.template($("#tpl-tb-cart-empty").html())();
                $(".tb-cart-dropdown").html(_tpl);
            }
        });
    }), $(".icon-msg").on("click", function() {
        return "block" == $(".tb-msg-dropdown-wrapper").css("display") ? $(".tb-msg-dropdown-wrapper").hide() : void $.ajax("/userBarMsg", {
            beforeSend: function() {
                $(".tb-msg-dropdown-wrapper").show(), $(".tb-cart-dropdown-wrapper").hide(), $(".tb-user-dropdown").hide(), 
                $iClear.click();
            },
            success: function(res) {
                if ("true" == res.success) {
                    var _tpl, data = res.data;
                    _tpl = (data.goods && data.goods.length, _.template($("#tpl-tb-msg-empty").html())()), 
                    $(".tb-msg-dropdown").html(_tpl);
                }
            },
            error: function() {
                var _tpl = _.template($("#tpl-tb-msg-empty").html())();
                $(".tb-msg-dropdown").html(_tpl);
            }
        });
    }), $(".tb-username").on("click", function() {
        return "block" == $(".tb-user-dropdown").css("display") ? $(".tb-user-dropdown").hide() : ($(".tb-cart-dropdown-wrapper").hide(), 
        $(".tb-msg-dropdown-wrapper").hide(), $iClear.click(), void $(".tb-user-dropdown").show());
    }), {};
});