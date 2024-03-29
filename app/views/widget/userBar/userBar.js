define(['jquery', 'underscore'], function($, _){
	console.log("userBar loaded");
    var $sForm = $(".tb-search-form"), $sInput = $(".tb-search-input"), $iLoading = $(".icon-loading"), $iClear = $('.icon-clear'), $sResult = $('.search-result');
    var keyuptimer;
    $sInput.on("focus", function() {
        $sForm.css({
            background: "#FFF"
        });
    }).on("keyup", function() {
        clearTimeout(keyuptimer);
        keyuptimer = setTimeout(function(){
            $.ajax("/userBarSearch", {
                type: "POST",
                data: {
                    string: $sInput.val()
                },
                beforeSend: function () {
                    $iClear.addClass("hide");
                    $iLoading.removeClass("hide");
                },
                success: function (res) {
                    if(typeof res != 'object')
                        res = $.parseJSON(res);
                    if (res.success == true) {
                        if(res.data.length){//有搜出东西来
                            var data = res.data,
                                _tpl = _.template($('#tpl-tb-search').html())({data: data});
                        }else{
                            var _tpl = _.template($('#tpl-tb-search-empty').html())();
                        }
                        $sResult.html(_tpl).show();
                        $iLoading.addClass("hide");
                        $iClear.removeClass('hide');

                    } else {
                        alert('搜索异常!');
                    }
                }

            });
        }, 200);

        $iClear.on("click", function () {
            $sForm.css({
                background: ""
            });
            $sResult.hide();
            $iLoading.addClass('hide');
            $iClear.addClass('hide');
            $sInput.val('');
        });
    });

    $('.icon-cart').on('click', function(){
        if($('.tb-cart-dropdown-wrapper').css('display') == 'block'){
            return $('.tb-cart-dropdown-wrapper').hide();
        }
        $.ajax('/userBarCart', {
            beforeSend: function () {
                $('.tb-cart-dropdown-wrapper').show();
                $('.tb-msg-dropdown-wrapper').hide();
                $('.tb-user-dropdown').hide();
                $iClear.click();
            },

            success: function(res){
                var data = res.data;
                var _tpl;
                if(res && "true" == res.success && 0 !== data.goods.length){
                    _tpl = _.template($('#tpl-tb-cart').html())({data: data});
                }else{
                    _tpl = _.template($('#tpl-tb-cart-empty').html())();
                }
                $('.tb-cart-dropdown').html(_tpl);
            },

            error: function(){
                var _tpl = _.template($('#tpl-tb-cart-empty').html())();
                $('.tb-cart-dropdown').html(_tpl);
            }

        });
    });

    $('.icon-msg').on('click', function(){
        if($('.tb-msg-dropdown-wrapper').css('display') == 'block'){
            return $('.tb-msg-dropdown-wrapper').hide();
        }
        $.ajax('/userBarMsg', {
            beforeSend: function () {
                $('.tb-msg-dropdown-wrapper').show();
                $('.tb-cart-dropdown-wrapper').hide();
                $('.tb-user-dropdown').hide();
                $iClear.click();
            },

            success: function(res){
                if("true" == res.success){
                    var data = res.data;
                    var _tpl;
                    if(!data.goods || !data.goods.length){
                        _tpl = _.template($('#tpl-tb-msg-empty').html())();
                    }else{
                        _tpl = _.template($('#tpl-tb-msg-empty').html())();
                    }
                    $('.tb-msg-dropdown').html(_tpl);
                }else{

                }
            },

            error: function(){
                var _tpl = _.template($('#tpl-tb-msg-empty').html())();
                $('.tb-msg-dropdown').html(_tpl);
            }
        });
    });

    $('.tb-username').on('click', function(){
        if($('.tb-user-dropdown').css('display') == 'block'){
            return $('.tb-user-dropdown').hide();
        }
        $('.tb-cart-dropdown-wrapper').hide();
        $('.tb-msg-dropdown-wrapper').hide();
        $iClear.click();
        $('.tb-user-dropdown').show();
    });

    return {

    };
});

