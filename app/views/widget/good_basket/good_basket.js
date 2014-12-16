define(['jquery', 'underscore', 'shop/port'], function($, _, port){

    function Cart(opts){
        for(var i in opts){
            this[i] = opts[i];
        }
        //init
        this.itemList = [];
    }

    Cart.prototype.find = function(id, callback){
        if(typeof id == 'object')
            id = id.id;
        for(var i = 0, len = this.itemList.length; i < len; i++ ){
            if(this.itemList[i].id == id){
                if(callback) callback(this.itemList[i]);
                return this.itemList[i];
            }
        }
        if(callback) callback(null);
        return false;
    }

    Cart.prototype.add = function(item, cb){
        if(!this.find(item.id)){
            this.itemList.push(item);
            cb && cb(item);
            return true;
        }
        return false;
    }

    Cart.prototype.setCount = function(id, callback){
        var item = this.find(id);
        if(!item) return callback(null);
        callback(item);
    }

    Cart.prototype.del = function(id){
        if(typeof id == 'object')
            id = id.id;
        for(var i = 0, len = this.itemList.length; i < len; i++ ){
            if(this.itemList[i].id == id) {
                return this.itemList.splice(i, 1);
            }

        }
        return false;
    }

    Cart.prototype.getTotalPrice = function(){
        var total = 0;
        this.itemList.forEach(function(item){
            var count = item.count || 0;
            total += parseInt(item.price * count);
        });
        return total;
    }

    Cart.prototype.getTotalCount = function(){
        var total = 0;
        this.itemList.forEach(function(item){
            var count = item.count || 0;
            total += parseInt(count);
        });
        return total;
    }

    Cart.prototype.empty = function(cb){
        this.itemList = [];
        cb && cb();
    }

    var cart = new Cart();

    cart.init = function(){
        $('.cgroup-item').each(function(i, item){
            var self = $(item);
            var obj = {
                id: self.data('good_id'),
                price: self.find('.cdish-price').html(),
                count: self.find('.cdish-qty').val(),
                title: self.find('.cdish-name').html()
            };
            cart.itemList.push(obj);
        });
    };



    /**
     * 把 cart 里的信息更新到 dom 上, itemTotaCount, itemTotalPrice
     */
    function refresh(){
        $('#total_count_basket').html(cart.getTotalCount());
        $('#total_price_basket').html(cart.getTotalPrice());
        $('.cgroup-item').each(function(i, item){
            var self = $(item);
            var id = self.data('good_id');
            cart.find(id, function(item){
                if(item && item.count)
                    self.find('.cdish-qty').val(item.count);
            });
        });
    }

    function cartAdd(id, shop_id) {
        cart.find(id, function(item){
            if(item){
                cartSetCount(id, shop_id, item.count + 1);
            }else{
                $.ajax({
                    url: port['cartAdd'],
                    type: "post",
                    data: {
                        good_id: id,
                        shop_id: shop_id
                    },
                    success: function(res){
                        if('true' == res.success){
                            var data = res.data;
                            cart.add({
                                id: data.addedItem['goods_id'],
                                price: data.addedItem['goods_price'],
                                count: data.addedItem['goods_count'],
                                title: data.addedItem['goods_name'],
                                shop_id: data['shop_id'],
                                domLi: null
                            }, function(item){
                                var t = _.template($('#tpl-cgroup-item').html())({data: {
                                    title: item.title,
                                    good_id: id,
                                    shop_id: shop_id,
                                    price: item.price,
                                    count: item.count
                                }});
                                $('#cgroup-list').append(t);
                            });
                        }else{
                            alert(res.info);
                        }
                    }

                });
            }
        });
        refreshCart();
    }

    function cartDel(id, shop_id){
        $.ajax({
            url: port['cartDel'],
            type: 'POST',
            data: {
                good_id: id,
                shop_id: shop_id
            },
            success: function(res){
                if('true' == res.success){
                    cart.del(id);
                    $('.cgroup-item[data-good_id='+ id +']').remove();
                }else{
                    alert('网络错误!');
                }
            }
        });
        refresh();
    }

    function cartSetCount(id, shop_id, count){
        cart.find(id, function(item){
            if(!item){
                alert('请先添加这个商品!');
            }else{
                $.ajax({
                    url: port['cartSetCount'],
                    type: "post",
                    data: {
                        good_id: id,
                        shop_id: shop_id,
                        count: count
                    },
                    success: function(res){
                        if('true' == res.success){
                            item.count = count;
                            refresh();
                        }else{
                            alert('网络错误!');
                        }
                    }
                });
            }
        })
    }

    $('#cgroup-list').on('click', '.dec_btn', function(e){
        var pnt = $(e.target).parents('li');
        var id = pnt.data('good_id'),
            shop_id = pnt.data('shop_id'),
            count = parseInt(pnt.find('.cdish-qty').val());
        if(count <= 1){
            cartDel(id, shop_id);
        }else
        cartSetCount(id, shop_id, count - 1);
    }).on('click', '.inc_btn', function(e){
        var pnt = $(e.target).parents('li');
        var id = pnt.data('good_id'),
            shop_id = pnt.data('shop_id'),
            count = parseInt(pnt.find('.cdish-qty').val());
        cartSetCount(id, shop_id, count + 1);
    }).on('click', '.del', function(e){
        var pnt = $(e.target).parents('li');
        var id = pnt.data('good_id'),
            shop_id = pnt.data('shop_id');
        cartDel(id, shop_id);
    }).on('keyup', '.cdish-qty', function(e){
        var self = $(e.target);
        var pnt = self.parents('li');
        var id = pnt.data('good_id'),
            shop_id = pnt.data('shop_id'),
            count = parseInt(self.val());
        if(!count) {
            cartDel(id, shop_id);
        }else{
            cartSetCount(id, shop_id, count);
        }
    });

    cart.init(); //LOADING!
	console.log("good basket loaded");
});