/**
 * 商家页面Ajax 接口
 */
define(function() {
    return {
        cartSetCount: "/cartSetCount",
        //购物车设置商品数量
        cartClear: "/cartClear",
        //购物车清空
        cartAdd: "/addtocart",
        //购物车根据 id 添加商品
        cartDel: "/cartDel",
        //购物车根据 id 删除商品
        cartInit: "/cartInit",
        //购物车初始化
        shopFavor: "/collectshop",
        //收藏店铺
        delShopFavor: "/cancelshop",
        //取消收藏商品
        goodFavor: "/collectmenu",
        //收藏商品
        delGoodFavor: "/cancelmenu",
        //取消收藏商品
        getComments: "/goods_comments"
    };
});