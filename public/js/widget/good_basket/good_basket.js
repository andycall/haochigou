define([ "jquery", "underscore", "shop/port" ], function($, _, port) {
    function Cart(opts) {
        for (var i in opts) this[i] = opts[i];
        //init
        this.itemList = [];
    }
    /**
     * 把 cart 里的信息更新到 dom 上, itemTotaCount, itemTotalPrice
     */
    function refresh() {
        $("#total_count_basket").html(cart.getTotalCount()), $("#total_price_basket").html(cart.getTotalPrice()), 
        $(".cgroup-item").each(function(i, item) {
            var self = $(item), id = self.data("good_id");
            cart.find(id, function(item) {
                item && item.count && (self.find(".cdish-qty").val(item.count), self.find(".cdish-total").html(item.count * item.price));
            });
        });
    }
    function cartDel(id, shop_id) {
        $.ajax({
            url: port.cartDel,
            type: "POST",
            data: {
                good_id: id,
                shop_id: shop_id
            },
            success: function(res) {
                "true" == res.success ? (cart.del(id), $(".cgroup-item[data-good_id=" + id + "]").remove()) : alert("网络错误!");
            }
        }), refresh();
    }
    function cartSetCount(id, shop_id, count) {
        cart.find(id, function(item) {
            item ? $.ajax({
                url: port.cartSetCount,
                type: "post",
                data: {
                    good_id: id,
                    shop_id: shop_id,
                    count: count
                },
                success: function(res) {
                    "true" == res.success ? (item.count = count, refresh()) : alert("网络错误!");
                }
            }) : alert("请先添加这个商品!");
        });
    }
    Cart.prototype.find = function(id, callback) {
        "object" == typeof id && (id = id.id);
        for (var i = 0, len = this.itemList.length; len > i; i++) if (this.itemList[i].id == id) return callback && callback(this.itemList[i]), 
        this.itemList[i];
        return callback && callback(null), !1;
    }, Cart.prototype.add = function(item, cb) {
        return this.find(item.id) ? !1 : (this.itemList.push(item), cb && cb(item), !0);
    }, Cart.prototype.setCount = function(id, callback) {
        var item = this.find(id);
        return item ? void callback(item) : callback(null);
    }, Cart.prototype.del = function(id) {
        "object" == typeof id && (id = id.id);
        for (var i = 0, len = this.itemList.length; len > i; i++) if (this.itemList[i].id == id) return this.itemList.splice(i, 1);
        return !1;
    }, Cart.prototype.getTotalPrice = function() {
        var total = 0;
        return this.itemList.forEach(function(item) {
            var count = item.count || 0;
            total += parseInt(item.price * count);
        }), total;
    }, Cart.prototype.getTotalCount = function() {
        var total = 0;
        return this.itemList.forEach(function(item) {
            var count = item.count || 0;
            total += parseInt(count);
        }), total;
    }, Cart.prototype.empty = function(cb) {
        this.itemList = [], cb && cb();
    };
    var cart = new Cart();
    cart.init = function() {
        $(".cgroup-item").each(function(i, item) {
            var self = $(item), obj = {
                id: self.data("good_id"),
                price: self.find(".cdish-price").html(),
                count: self.find(".cdish-qty").val(),
                title: self.find(".cdish-name").html()
            };
            cart.itemList.push(obj);
        });
    }, $("#cgroup-list").on("click", ".dec_btn", function(e) {
        var pnt = $(e.target).parents("li"), id = pnt.data("good_id"), shop_id = pnt.data("shop_id"), count = parseInt(pnt.find(".cdish-qty").val());
        1 >= count ? cartDel(id, shop_id) : cartSetCount(id, shop_id, count - 1);
    }).on("click", ".inc_btn", function(e) {
        var pnt = $(e.target).parents("li"), id = pnt.data("good_id"), shop_id = pnt.data("shop_id"), count = parseInt(pnt.find(".cdish-qty").val());
        cartSetCount(id, shop_id, count + 1);
    }).on("click", ".del", function(e) {
        var pnt = $(e.target).parents("li"), id = pnt.data("good_id"), shop_id = pnt.data("shop_id");
        cartDel(id, shop_id);
    }).on("keyup", ".cdish-qty", function(e) {
        var self = $(e.target), pnt = self.parents("li"), id = pnt.data("good_id"), shop_id = pnt.data("shop_id"), count = parseInt(self.val());
        count ? cartSetCount(id, shop_id, count) : cartDel(id, shop_id);
    }), cart.init(), //LOADING!
    console.log("good basket loaded");
});