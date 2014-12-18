define([ "jquery", "shop/port" ], function($, port) {
    //收藏ajax
    function shopFavorAjax(data) {
        $.post(port.shopFavor, data, function(res) {
            if (console.log(res), "object" != typeof res) try {
                res = $.parseJSON(res);
            } catch (err) {
                alert("服务器数据错误！！！");
            }
            //失败的话
            "true" == res.succes && (alert(res.errMsg ? res.errMsg : "收藏失败!"), $favorBar.toggleClass("on"), 
            //取消变红
            $favorStatus.text("收藏餐厅"));
        });
    }
    //取消收藏ajax
    function delShopFavor(data) {
        $.post(port.delShopFavor, data, function(res) {
            if ("object" != typeof res) try {
                res = $.parseJSON(res);
            } catch (err) {
                alert("服务器数据错误！！！");
            }
            //失败的话
            res.success && (alert(res.errMsg ? res.errMsg : "取消收藏失败!"), $favorBar.toggleClass("on"), 
            //取消 不 变红
            $favorStatus.text("已收藏"));
        });
    }
    console.log("shop collection bar loaded");
    /*
 *  @include "侧边栏收藏按钮"
*/
    var $this = $(".js-fav-shop"), $favorBar = $this.find(".glyph"), //红心
    $favorStatus = $this.find(".status");
    //状态
    $(".js-fav-shop").on("click", function() {
        //商家信息
        var shopInfo = {
            shop_id: $(".res_info .res_info_header").attr("data-shop_id"),
            shop_name: ""
        };
        //按钮变红 || 取消变红
        $favorBar.toggleClass("on"), $favorBar.hasClass("on") ? (//如果收藏
        $favorStatus.text("已收藏"), shopFavorAjax(shopInfo)) : (//如果取消收藏
        $favorStatus.text("收藏餐厅"), delShopFavor(shopInfo));
    });
});