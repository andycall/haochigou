define(["jquery","underscore","shop_cart/shop_cart"],function(a,b,c){a("#favor_food").on("click",".add_btn",function(b){var d=a(b.target).parents("li");console.log(d);var e=d.data("good_id"),f=d.data("shop_id");c.add(e,f)}),console.log("goods_collection loaded")});