/**
 * 商家页面Ajax 接口
 */
define(function() {
    return {
        cartSetCount: "./cartSetCount",
        //购物车设置商品数量
        cartClear: "./cartClear",
        //购物车清空
        cartAdd: "./cartAdd",
        //购物车根据 id 添加商品
        cartDel: "./cartDel",
        //购物车根据 id 删除商品
        cartInit: "./cartInit",
        //购物车初始化
        shopFavor: "/shopFavor",
        //收藏店铺
        delShopFavor: "/delShopFavor"
    };
});