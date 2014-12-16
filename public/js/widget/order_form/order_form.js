define([ "jquery", "personal/port", "jquery-ui" ], function($, port) {
    console.log("order form loaded"), function() {
        var d = [];
        for (i = -24; 25 > i; i++) d[i] = 0 == i ? 1 : 5 * i;
        var e = [ "正点", "5分钟", "10分钟", "15分钟", "20分钟", "25分钟", "30分钟", "35分钟", "40分钟", "45分钟", "50分钟", "55分钟", "1小时", "1小时5分钟", "1小时10分钟", "1小时15分钟", "1小时20分钟", "1小时25分钟", "1小时30分钟", "1小时35分钟", "1小时40分钟", "1小时45分钟", "1小时50分钟", "1小时55分钟", "2小时" ];
        for (e[-24] = "提前2小时", i = -23; -12 > i; i++) e[i] = "提前1小时" + 5 * (-i - 12) + "分钟";
        for (e[-12] = "提前1小时", i = -11; 0 > i; i++) e[i] = "提前" + 5 * -i + "分钟";
        $(".slider").slider({
            range: "min",
            min: 2,
            max: 100,
            value: 100,
            slide: function(g, h) {
                {
                    var l = parseInt($(this).attr("max")), j = parseInt($(this).attr("min"));
                    $(this).attr("rel");
                }
                $(this).slider("option", "disabled", !1), $(this).slider("option", "min", j), $(this).slider("option", "max", l), 
                $(this).slider("option", "value", l), $(this).parents(".content").find(".col_value span").html(e[h.value - 1]), 
                $(this).parents(".content").find(".col_btn").css("display", "block");
            }
        });
    }(), $.fn.serializeObject = function() {
        var o = {}, a = this.serializeArray();
        return $.each(a, function() {
            void 0 != o[this.name] ? (o[this.name].push || (o[this.name] = [ o[this.name] ]), 
            o[this.name].push(this.value || "")) : o[this.name] = this.value || "";
        }), o;
    }, $(".order_radio").on("change", function() {
        $(this).parents(".order_content").children(".order_comment").css("display", "block");
    }), $(".order_comment_cancel").on("click", function() {
        var order_content_form = $(this).parents(".order_content").find(".order_content_form")[0] || null;
        $(this).parent().css("display", "none"), order_content_form && order_content_form.reset();
    }), $(".order_content .order_comment_save").on("click", function() {
        var p = document.createElement("p"), order_form = $(this).parents(".order_form"), order_content = $(this).parents(".order_content"), shop_id = order_form.data("shop_id"), deal_id = order_form.data("deal_id"), text = order_content.find(".text").val(), order_radio = order_content.find(".order_content_form").serializeObject()["service-rate"];
        switch (order_radio) {
          case "1":
            p.innerHTML = "已点评，不满意";
            break;

          case "2":
            p.innerHTML = "已点评，一般般";
            break;

          default:
            p.innerHTML = "已点评，满意";
        }
        $.ajax({
            url: port.commentRestaurant,
            type: "POST",
            data: {
                shop_id: shop_id,
                deal_id: deal_id,
                deal_satisfied: order_radio,
                deal_satisfied_comment: text
            },
            success: function(res) {
                "true" == res.success ? (order_content.remove(), order_content.parent().append(p)) : alert("评论失败，请重新评论!");
            }
        });
    }), $(".rating_comment .comment div").on("click", function() {
        var rating_comment = $(this).parents(".rating_comment");
        return rating_comment.find(".order_comment").css("display", "block"), !1;
    }).on("mouseover", star = function() {
        $(this).parents(".comment").find("div").removeClass("mouseover"), $(this).addClass("mouseover");
        var rating_text = $(this).parents(".rating_comment").find(".rating_text");
        switch (this.innerText) {
          case "1":
            rating_text.html("差评");
            break;

          case "2":
            rating_text.html("差点意思");
            break;

          case "3":
            rating_text.html("一般般");
            break;

          case "4":
            rating_text.html("有点滋味");
            break;

          default:
            rating_text.html("我的最爱");
        }
        return !1;
    }), $(".rating_comment .order_comment_save").on("click", function() {
        var rating_comment = $(this).parents(".rating_comment"), order_form = $(this).parents(".order_form"), shop_id = order_form.data("shop_id"), deal_id = order_form.data("deal_id"), goods_id = rating_comment.data("goods_id"), goods_level = rating_comment.find(".mouseover")[0].innerText[0], text = rating_comment.find(".text").val(), $_this = $(this);
        $.ajax({
            url: port.commentCate,
            type: "POST",
            data: {
                shop_id: shop_id,
                deal_id: deal_id,
                goods_id: goods_id,
                goods_level: goods_level,
                deal_satisfied_comment: text
            },
            success: function(res) {
                "true" == res.success ? ($_this.parents(".rating_comment").removeClass("rating_comment"), 
                $_this.parents(".comment").find("div").off("mouseover", star)) : alert("评论失败，请重新评论!");
            }
        });
    }), $(".col_btn").on("click", function() {
        var $_this = $(this), p = document.createElement("p"), content = $_this.parents(".content"), order_form = $(this).parents(".order_form"), shop_id = order_form.data("shop_id"), deal_id = order_form.data("deal_id"), deal_speed = content.find(".col_value span").html();
        p.innerHTML = "已点评， 时间： " + deal_speed, $.ajax({
            url: port.commentSpeed,
            type: "POST",
            data: {
                shop_id: shop_id,
                deal_id: deal_id,
                deal_speed: deal_speed
            },
            success: function(res) {
                "true" == res.success ? (content.find(".content_speed").remove(), content.append(p)) : alert("评论失败，请重新评论!");
            }
        });
    });
});