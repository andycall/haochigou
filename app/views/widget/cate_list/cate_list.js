define(['jquery' , 'tools/Sizer', "shop_cart/shop_cart"], function($, Sizer, cart){
	console.log("cate list loaded");

	function getListTop(list){
		var arr = [];

		list.each(function(index, value){
			var data = $(this).offset();
			data['classify_id'] = $(this).data("classify_id");
			arr.push(data);
		});
		return arr;
	}




	// 计算滚动方向
	var CaculateDirection = (function(){
		var direction = 1,
			scrollTmp = 0;

		return function(scrollTop){
			if(scrollTop > scrollTmp){
				direction = 1;
			}
			else{
				direction = -1;
			}
			scrollTmp = scrollTop;

			return direction;
		}
	}());

	var d               = document,
		menu_toolbar    = $(".menu_toolbar"),
		menu_offset     = menu_toolbar.offset(),
		toolBar_toggle  = $(".toolBar_toggle"),
		drop_down_menu  = $(".drop_down_menu"),
		classify_sec    = $(".classify_sec"),
		sec_title       = $(".sec_title"),
		toolbar         = $(".toolbar_text"),
		toolbar_text    = toolbar.find("span"),
		shop_id         = $(".res_info_header").data("shop_id"),
		scrollIndex     = 0,
		positionArr = getListTop(classify_sec),
 		ready_tmp,               // 状态保存
		ready_status    = false, // 是否需要运行切换
		good_list = {},
		cate_view  = $(".cate_view"),
		menu_list = $(".menu_list"),
		original_list = cate_view.html();


	(function(){
		$.each(classify_sec, function(index){
			var classify = $(this),
				title = classify.find(".sec_title").attr("title"),
				menu_list = classify.find('.menu_list_block');

			good_list[index] = [];

			$.each(menu_list, function(){
				var list_block = $(this),
					good_list_obj = {};

				good_list_obj['good_id'] = list_block.data("good_id");
				good_list_obj['good_price'] = list_block.data('good_price');
				good_list_obj['good_level'] = list_block.data("good_level");
				good_list_obj['good_sails'] = list_block.data("good_sails");
				good_list_obj['HTML_string'] = list_block[0].cloneNode(true);

				good_list[index].push(good_list_obj);
			});
		});
	})();

	$(".tool_item").on('click', function(e){
		var _item = $('.tool_item'),
			target = $(e.currentTarget),
			_icon = $(".tool_item i"),
			icon = target.find("i"),
			menu_list = $(".menu_list");

		_item.removeClass("ui_on");

		target.toggleClass("ui_on");

		_icon.removeClass("glyph-sort-up");
		_icon.addClass("glyph-sort-down");


		if(icon.hasClass('glyph-sort-up') && !target.hasClass("ui_on")){
			icon.removeClass('glyph-sort-up');
			icon.addClass("glyph-sort-down");
		}
		else{
			icon.removeClass("glyph-sort-down");
			icon.addClass("glyph-sort-up");
		}


		var label = target.data("target");


		if(label == "default") {
			cate_view.html(original_list);
		}
		else {
			$.each(good_list, function(name, value){
				value.sort(function(a, b){
					return b[label] - a[label];
				});

				menu_list.eq(name).html("");

				$.each(value, function(){
					menu_list.eq(name).append(this['HTML_string']);
				});
			});
		}


		return false;
	});




	if($(window).scrollTop() >= menu_offset.top){
		menu_toolbar.css({
			"position" : "fixed",
			"top" : 0
		});
		toolBar_toggle.fadeIn(300);
	}

	toolBar_toggle.on('click', function(){
		drop_down_menu.toggle();
	});


	function windowScroll(id){
		positionArr.forEach(function(value, index){
			if(value['classify_id'] == id){
				$("body").animate({
					scrollTop : value['top']
				});
			}
		});
	}

	$(".category_list").on('click', function(e){
		var id = $(e.currentTarget).find('a').data('cateid');
		windowScroll(id);
	});

	$(".cate_item").on('click', function(e){
		var id = $(e.currentTarget).data('classify_id');
		windowScroll(id);
		return false;
	});

	// 购物车
	cate_view.on('click', '.rst-d-act-add', function(){
		var good_id = $(this).parents('.menu_list_block').data("good_id");
		cart.add(good_id, shop_id);
		return false;

	});

	$(window).on('scroll', function(e){
		var scrollTop = $(window).scrollTop(),
			direction = CaculateDirection(scrollTop),
			isReady = scrollTop >= menu_offset.top, // 是否可以切换fixed
			nextPosition, prevPosition,
			target,
			target_id;


		if(isReady != ready_tmp){
			ready_status = true;
			ready_tmp = isReady;
		}

		if(isReady && ready_status){
			menu_toolbar.css({
				"position" : "fixed",
				"top" : 0
			});
			toolBar_toggle.fadeIn(300);
			ready_status = false;
		}
		else if(!isReady && ready_status){
			menu_toolbar.css({
				"position" : "absolute",
				"top"    : "auto"
			});
			toolBar_toggle.fadeOut(300);
			drop_down_menu.fadeOut(300);
			ready_status = false;
		}


		if(isReady && direction === 1){
			if(scrollIndex + 1 >= positionArr.length) return;
			nextPosition = positionArr[scrollIndex+1];
			if(scrollTop + 10 > nextPosition.top){
				target = sec_title.eq(scrollIndex + 1).find("span").html();
				target_id = classify_sec.eq(scrollIndex + 1).data("classify_id");
				scrollIndex++;
				toolbar_text.html(target);
				toolbar.attr("data-classify_id", target_id);
			}
		}
		else if(isReady && direction === -1){
			if(scrollIndex - 1 < 0) return;
			prevPosition = positionArr[scrollIndex];
			if(scrollTop + 10 < prevPosition.top){
				target = sec_title.eq(scrollIndex - 1).find('span').html();
				target_id = classify_sec.eq(scrollIndex - 1).data("classify_id");
				scrollIndex--;
				toolbar_text.html(target);
				toolbar.attr("data-classify_id", target_id);
			}
		}
	});



});