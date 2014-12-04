define(['jquery','personal/port', "jquery-ui"], function($, port){
	console.log("order form loaded");

    (function() {
        var d = [];
        for (i = -24; i < 25; i++) {
            if (i == 0) {
                d[i] = 1
            } else {
                d[i] = 5 * i
            }
        }
        var e = ["正点", "5分钟", "10分钟", "15分钟", "20分钟", "25分钟", "30分钟", "35分钟", "40分钟", "45分钟", "50分钟", "55分钟", "1小时", "1小时5分钟", "1小时10分钟", "1小时15分钟", "1小时20分钟", "1小时25分钟", "1小时30分钟", "1小时35分钟", "1小时40分钟", "1小时45分钟", "1小时50分钟", "1小时55分钟", "2小时"];
        e[-24] = "提前2小时";
        for (i = -23; i < -12; i++) {
            e[i] = "提前1小时" + (-i - 12) * 5 + "分钟"
        }
        e[-12] = "提前1小时";
        for (i = -11; i < 0; i++) {
	        e[i] = "提前" + (-i) * 5 + "分钟"
        }
        $(".slider").slider({range: "min",min: 2,max: 100,value: 100,slide: function(g, h) {
            var l = parseInt($(this).attr("max"));
            var j = parseInt($(this).attr("min"));
            var k = $(this).attr("rel");
                $(this).slider("option", "disabled", false);
            $(this).slider("option", "min", j);
            $(this).slider("option", "max", l);
            $(this).slider("option", "value", l);
            $(this).parents(".content").find(".col_value span").html(e[h.value - 1]);
            $(this).parents(".content").find(".col_btn").css("display","block");
        }});
    })();

    $.fn.serializeObject = function(){
        var o = {};
        var a = this.serializeArray();

        $.each(a, function(){
            if( o[ this.name ] != undefined){
                if(! o[ this.name ].push){
                    o[ this.name ] = [o[this.name]];
                }
                o[this.name].push( this.value || "")
            }
            else{
                o[this.name] = this.value || "";
            }
        });
        return o;
    };

    $(".order_radio").on('change', function(){
        $(this).parents(".order_content").children(".order_comment").css("display","block");
    });

    $(".order_comment_cancel").on('click', function(){
        var order_content_form = $(this).parents(".order_content").find(".order_content_form")[0] || null;
        $(this).parent().css("display","none");
        if(order_content_form){
            order_content_form.reset();
        }
    });

    $(".order_content .order_comment_save").on('click',function(){

        var p = document.createElement("p");
        var order_form = $(this).parents(".order_form");
        var order_content = $(this).parents(".order_content");

        var shop_id = order_form.data('shop_id');
        var deal_id = order_form.data('deal_id');
        var text = order_content.find('.text').val();
        var order_radio = order_content.find('.order_content_form').serializeObject()['service-rate'];

        switch (order_radio)
        {
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
            url: port["commentRestaurant"],
            type: "POST",
            data: {
                shop_id: shop_id,
                deal_id: deal_id,
                deal_satisfied: order_radio,
                deal_satisfied_comment: text
            },
            success: function(res){
                if(res.success == 'true'){
                    order_content.remove();
                    order_content.parent().append(p);
                }else{
                    alert('评论失败，请重新评论!');
                }
            }
        });

    });


    $(".rating_comment .comment div").on('click',function(){

        var rating_comment = $(this).parents(".rating_comment");

        rating_comment.find(".order_comment").css("display","block");

        return false;

    }).on('mouseover', star = function(){
        $(this).parents(".comment").find("div").removeClass("mouseover");

        $(this).addClass("mouseover");


        var rating_text = $(this).parents(".rating_comment").find(".rating_text");
        switch (this.innerText)
        {
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

        return false;
    });

    $(".rating_comment .order_comment_save").on("click",function(){

        var rating_comment = $(this).parents(".rating_comment");
        var order_form = $(this).parents(".order_form");

        var shop_id = order_form.data('shop_id');
        var deal_id = order_form.data('deal_id');
        var goods_id = rating_comment.data('goods_id');
        var goods_level = rating_comment.find(".mouseover")[0].innerText[0];
        var text = rating_comment.find('.text').val();
        var $_this = $(this);

        $.ajax({
            url: port["commentCate"],
            type: "POST",
            data: {
                shop_id: shop_id,
                deal_id: deal_id,
                goods_id: goods_id,
                goods_level: goods_level,
                deal_satisfied_comment: text
            },
            success: function(res){
                if(res.success == 'true'){
                    $_this.parents(".rating_comment").removeClass("rating_comment");
                    $_this.parents(".comment").find("div").off('mouseover',star);
                }else{
                    alert('评论失败，请重新评论!');
                }
            }
        });

    });

    $(".col_btn").on('click',function(){

        var $_this = $(this);
        var p = document.createElement("p");
        var content = $_this.parents(".content");
        var order_form = $(this).parents(".order_form");

        var shop_id = order_form.data('shop_id');
        var deal_id = order_form.data('deal_id');
        var deal_speed = content.find(".col_value span").html();

        p.innerHTML = "已点评， 时间： " + deal_speed;

        $.ajax({
            url: port["commentSpeed"],
            type: "POST",
            data: {
                shop_id: shop_id,
                deal_id: deal_id,
                deal_speed: deal_speed,
            },
            success: function(res){
                if(res.success == 'true'){
                    content.find(".content_speed").remove();
                    content.append(p);
                }else{
                    alert('评论失败，请重新评论!');
                }
            }
        });
    });

});
