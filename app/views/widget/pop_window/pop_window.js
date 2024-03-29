define(['jquery', "shop/port"], function($, port){

	/*
	 *@include "左侧评论打开与关闭"
	 *@include "ajax获取评论并显示出来" 
     *@include "收藏"
	*/

    //跟踪侧边栏商品信息
    var goodInfo = {
        "goods_name"  : "", //名称
        "goods_id"    : "", //商品id
        "goods_price" : "", //价格
        "shop_id"    : $(".pop_window .pop_inner").attr("data-shop-id")  //商家id
    };

    /*------------------------------------------
    *           侧边栏打开关闭及ajax获取信息评论
    *-------------------------------------------
    */
	
    var $popWindow  = $(".pop_window"),
        $windowMask = $(".u-mask");

    //打开左侧框
    $(".js-open-pop-window").on("click",function(){
    	var $this = $(this);

    	$popWindow.css("left","0px");
    	$windowMask.show();

    	var data = {
    		"goods_id"  :  $this.parents(".js-get-good-id").attr("data-good_id")
    	};

        goodInfo.goods_id    = data.goods_id;
        goodInfo.goods_name  = $this.parents(".menu_sec_status").siblings(".menu_sec_info").find(".menu_sec_desc").text();
        goodInfo.goods_price = $this.parents(".menu_sec_status").siblings(".menu_sec_action").find(".symbol-rmb").text();

    	ajaxGetConmments(data);
    });

    //关闭左侧框
    $(document).on("click",".js-close-pop-window, .u-mask",function(){
    	$popWindow.css("left","-400px");
    	$windowMask.hide();
    });

	//ajax
	function ajaxGetConmments(data){
        console.log(data);
		$.post(port['getComments'], data, function(res){

            if( typeof res != "object" ){

                try{
                    res = $.parseJSON(res);
                }catch(err){
                    alert("服务器数据错误");
                    return ;
                }

            }

            //请求成功后
            if(res.success == "true"){
                showConmments(res.data);
            }else{
                if(res.errMsg){
                    alert(res.errMsg);
                }
            }
        });
	}

	//ajax获取成功后的操作 将数据填进dom中
	function showConmments(data){
        //保存商品名称
        data.good_name = goodInfo.goods_name;
        //获取模板填数据
        console.log(data);
		var temp = _.template( $("#drawer-temp").html() )(data);

        //渲染
        $(".pop_inner").html(temp);
	}

    /*---------------------------------------------
     *          商品收藏
     *---------------------------------------------
    */
    
    var listsWrapper = $(".rst-aside-menu-list"); //列表

    //收藏商品
    $(".pop_window").on("click", ".u-favor", function(){
        var $this = $(this);

        //感应
        $this.toggleClass("on");
        
        if($this.hasClass('on')){
           collectAjax(goodInfo); //追加到列表
        }else{
            delCollectAjax(goodInfo); //移除
        }
    });
    //hmphmphmp
    $(".favor_btn").on("click", function(ev){
        var $this = $(this);

        $this.toggleClass("on");

        goodInfo.goods_id = $this.parents(".js-get-good-id").attr("data-good_id");
        goodInfo.goods_name = $this.parents(".menu_sec_title").siblings(".menu_sec_desc").attr("title");

        if($this.hasClass('on')){
            collectAjax(goodInfo); //追加到列表
        }else{
            delCollectAjax(goodInfo); //移除
        }
    });
    
    //收藏商品ajax
    function collectAjax(data){
        console.log(data);
        $.post(port['goodFavor'], data, function(res){
            if( typeof res != "object" ){

                try{
                    res = $.parseJSON(res);
                }catch(err){
                    alert("服务器数据错误");
                    $(".pop_window .u-favor").toggleClass("on");

                    return ;
                }

            }
            if( res.success == "true"){
                var itemFavor    = $(".rst-aside-dish-item").eq(0).clone(true);

                itemFavor.attr({
                    'data-good-id' : goodInfo.goods_id,
                    'data-shop-id' : goodInfo.shop_id
                }); //设置id

                itemFavor.find(".food_name").text(goodInfo.goods_name);
                itemFavor.find(".symbol-rmb").text(goodInfo.goods_price);

                listsWrapper.find(".rst-aside-dish-item").eq(0).before(itemFavor); //追加
            }else{
                if(res.errMsg){
                    alert(res.errMsg);
                }
            }
        });
    }
    
    //取消收藏商品ajax
    function delCollectAjax(data){
        console.log(data);
        $.post(port['delGoodFavor'], data, function(res){
            if( typeof res != "object" ){

                try{
                    res = $.parseJSON(res);
                }catch(err){
                    alert("服务器数据错误");
                    $(".pop_window .u-favor").toggleClass("on");

                    return ;
                }

            }

            if( res.success == "true"){

                listsWrapper.find(".rst-aside-dish-item").each(function(i,$ele){
                    $ele = $($ele);
                    if( ( $ele.attr("data-good-id") == data.goods_id ) && ( $ele.attr("data-shop-id") == data.shop_id ) && ( $ele.find(".food_name").text() == data.goods_name ) ){
                        $ele.remove(); //移除
                    }
                })

            }else{
                if(res.errMsg){
                    alert(res.errMsg);
                }
            }
        });
    }



    /*------------------------------------
    *           有内容的评价显示控件(待定)
    *-------------------------------------
    */
    $(".pop_window").on("click", "#btn-check", function(){

    });

});