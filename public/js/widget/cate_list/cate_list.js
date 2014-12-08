define([ "jquery", "shop_cart/shop_cart" ], function($, cart) {
    function getListTop(list) {
        var arr = [];
        return list.each(function() {
            arr.push($(this).offset());
        }), arr;
    }
    console.log("cate list loaded");
    // 计算滚动方向
    var ready_tmp, CaculateDirection = function() {
        var direction = 1, scrollTmp = 0;
        return function(scrollTop) {
            return direction = scrollTop > scrollTmp ? 1 : -1, scrollTmp = scrollTop, direction;
        };
    }(), menu_toolbar = $(".menu_toolbar"), menu_offset = menu_toolbar.offset(), toolBar_toggle = $(".toolBar_toggle"), drop_down_menu = $(".drop_down_menu"), classify_sec = $(".classify_sec"), sec_title = $(".sec_title"), toolbar_text = $(".toolbar_text").find("span"), shop_id = $(".res_info_header").data("shop_id"), scrollIndex = 0, // 状态保存
    ready_status = !1;
    // 是否需要运行切换
    $(window).scrollTop() >= menu_offset.top && (menu_toolbar.css({
        position: "fixed",
        top: 0
    }), toolBar_toggle.fadeIn(300)), toolBar_toggle.on("click", function() {
        drop_down_menu.toggle();
    }), // 购物车
    $(".cate_view").on("click", ".rst-d-act-add", function() {
        var good_id = $(this).parents(".menu_list_block").data("good_id");
        return console.log(good_id), cart.add(good_id, shop_id), !1;
    }), $(window).on("scroll", function() {
        var // 是否可以切换fixed
        nextPosition, prevPosition, target, scrollTop = $(window).scrollTop(), positionArr = getListTop(classify_sec), direction = CaculateDirection(scrollTop), isReady = scrollTop >= menu_offset.top;
        if (isReady != ready_tmp && (ready_status = !0, ready_tmp = isReady), isReady && ready_status ? (menu_toolbar.css({
            position: "fixed",
            top: 0
        }), toolBar_toggle.fadeIn(300), ready_status = !1) : !isReady && ready_status && (menu_toolbar.css({
            position: "absolute",
            top: "auto"
        }), toolBar_toggle.fadeOut(300), ready_status = !1), isReady && 1 === direction) {
            if (scrollIndex + 1 >= positionArr.length) return;
            nextPosition = positionArr[scrollIndex + 1], scrollTop + 10 > nextPosition.top && (target = sec_title.eq(scrollIndex + 1).find("span").html(), 
            scrollIndex++, toolbar_text.html(target));
        } else if (isReady && -1 === direction) {
            if (0 > scrollIndex - 1) return;
            prevPosition = positionArr[scrollIndex], scrollTop + 10 < prevPosition.top && (target = sec_title.eq(scrollIndex - 1).find("span").html(), 
            scrollIndex--, toolbar_text.html(target));
        }
    });
});