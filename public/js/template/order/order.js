require.config({
    baseUrl: "/js/lib/",
    shim: {
        underscore: {
            exports: "_"
        }
    },
    paths: {
        shop: "../template/shop",
        order: "../template/order",
        footer: "../widget/footer",
        content_header: "../widget/content_header",
        deliver_place: "../widget/deliver_place",
        deliver_time: "../widget/deliver_time",
        good_basket: "../widget/good_basket",
        pay_method: "../widget/pay_method",
        userBar: "../widget/userBar",
        cart_topbar: "../widget/cart_topbar",
        shop_cart: "../widget/shop_cart"
    }
}), // 加载项目所需的所有依赖项
define([ "userBar/userBar", //"footer/footer",
//"content_header/content_header",
"deliver_place/deliver_place", //"deliver_time/deliver_time",
"good_basket/good_basket", //"pay_method/pay_method",
"cart_topbar/cart_topbar" ], function() {
    console.log("init");
});