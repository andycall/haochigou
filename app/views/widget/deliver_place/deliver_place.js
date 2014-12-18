define(['jquery','order/port'], function($,port){

	/*
	 * @include "修改地址"
	*/

	var $authWrapper  = $(".js-sms-auth-wrapper"),   //短信框
        $uMask      = $(".u-mask"),                  //弹出罩
        $reallyForm = $(".js-save-bottom");          //表单下部


	//打开编辑地址
	$(".js-open-edit").on("click", function(){
		$(".u-mask").show();
		$(".js-cmodal-wrapper").show();
	}); 
	$(".js-exit-edit").on("click", function(){
		$(".u-mask").hide();
		$(".js-cmodal-wrapper").hide();
	});

	//选择时间
	var $timePeanel = $(".js-select-time"),
	    $onPeanel   = $timePeanel.find(".ctime-toggle"),
		$orderTimes = $timePeanel.find(".ctime-dropdown"),
		$itemTime   = $timePeanel.find(".ctime-item");

    //打开时间面板
	$onPeanel.on("click", function(ev){
		ev.stopPropagation();
		$orderTimes.addClass('on');
	});

	//选择时间
	$itemTime.on('click', function(){
		var $thisVal = $(this).text();

		$onPeanel.text($thisVal);
		$reallyForm.find(".order-time").val($thisVal);

	});

	//关闭时间选择面板
	$(document.body).on("click", function(){
		$orderTimes.removeClass('on');
	});


	//切换支付方式
	$(".cpayment-choice").on("click", function(){
		var $this = $(this);
 
		if( !$this.hasClass("ui_disabled") && !$this.hasClass("ui_selected") ){ 
			$(".cpayment-choice").removeClass("ui_selected");
			$this.addClass('ui_selected');
			$reallyForm.find(".order-way").val( $this.attr("data-pay-way") );
		}
	});

	//短信验证关闭
	$(".js-exit-auth").on("click", function(){
		$authWrapper.hide();
		$uMask.hide();
	});
             
    var authInfo = {
     "csrf_token" : $reallyForm.find('input[name="_token"]').val()

    };   //验证发送附带信息

    //保存
    $(".js-save-edit").on("click", function(ev){

	     if(!checkForm()){
	      	ev.preventDefault();
	     }else{
		    $(".js-show-addr-info").find(".current_addr").text(authInfo.addr).end().find('.current_name').text(authInfo.name).end().find(".current_tel").text(authInfo.phone).end().find(".current_bkTel").text(authInfo.bkTel);
		    $(".u-mask").hide();
             alert(12467);
            $(".js-cmodal-wrapper").hide();
	     }
    });

	//验证必填项
	function checkForm(){
		var $modifyAdress = $(".js-adress-modify");
		var $telInput  = $modifyAdress.find(".tel"),
		    $nameInput = $modifyAdress.find(".name"),
		    $addrInput = $modifyAdress.find(".addr"),
		    $bkTel     = $modifyAdress.find(".bk");

		$modifyAdress.find("input").on("focus", function(){
			$(this).css("border-color", "#bbb");
		});

		var flag = true;

		var regTel    = /^[\d]{11}$/,            //验证手机电话
		    regBkTel  = /^[\d]{6,15}/,             //验证固定电话
		    regMust   = /^[\S\s]+$/i;           //必填
                        
                        //验证姓名
		if(  !regMust.test( $nameInput.val() ) ){
			$nameInput.css("border-color", "red");

			flag = false;
		}else{
			$nameInput.css("border-color", "#bbb");
			$reallyForm.find(".user-name").val(  $nameInput.val() ) //添加姓名
			authInfo.name = $nameInput.val();
		}
                        
                        //验证电话
		if( !regTel.test( $telInput.val() ) ){
			$telInput.css("border-color", "red");

			flag = false;
		}else{
			$telInput.css("border-color", "#bbb");
			authInfo.phone = $telInput.val();
			$reallyForm.find(".user-tel").val( $telInput.val() );  //添加电话号码
		}

		//验证地址
		if( !regMust.test( $addrInput.val() ) ){
			$addrInput.css("border-color", "red");

			flag = false;
		}else{
			$addrInput.css("border-color", "#bbb");
			$reallyForm.find(".user-addr").val( $addrInput.val() );   //添加地址
			authInfo.addr = $addrInput.val();
		}
        
        //固定电话
		if(  regBkTel.test( $bkTel.val() ) ){
			$reallyForm.find(".user-bkTel").val( $bkTel.val() );   //添加固定电话
			authInfo.bkTel = $bkTel.val();
		}

		if( !flag ){
			return false;
		}else{
			$(".u-mask").hide();
			$(".js-cmodal-wrapper").hide();
			return true;
		}
	}
           
     //短信验证框打开
	//,发送验证码请求到服务器
	$reallyForm.on("submit", function(ev){
		ev.preventDefault();

        //验证地址
		if( !checkForm() ){
			$(".js-cmodal-wrapper").show();
			$(".u-mask").show();
			return false;
		}

		//验证送餐时间
		if( $reallyForm.find(".order-time").val() == "" ){
			$timePeanel.find(".ui-err-notice").show();
			return false;
		}else{
			$timePeanel.find(".ui-err-notice").hide();
		}

		uRequestAuthAjax({
			'success' : function(res){
				$authWrapper.show();
	            $uMask.show();
	            $reallyForm.find(".user-auth").val(res.auth);
			},
			'failed'  : function(res){
				if( res.errMsg ){  //失败且有错误信息
					$authWrapper.hide();
	                $uMask.hide();
					alert( res.Msg );
				}else{    //默认错误信息
					alert("验证码发送失败, 请重试！！");
				}

			}
		});
		
	});

	//重复发送ajax
	$(".js-repeat-send-auth").on("click", function(){
		var $this = $(this);

		uRequestAuthAjax({
			'success' : function(res){
	            $reallyForm.find(".user-auth").val(res.auth);
                
                //计时禁止短时间内请求验证码
                $this.attr("disabled", "disabled");
	            var count = 60;
	            var disabledTimer = setInterval(function(){
	            	$this.text( (count--) + '秒后可重新发送');
	            	if(count < 0){
	            		clearInterval(disabledTimer);
	            		$this.text('重新发送');
	            		$this.removeAttribute("disabled");
	            	}
	            },1000);

			},
			'failed'  : function(res){
				if( res.errMsg ){  //失败且有错误信息
					alert( res.errMsg );
				}else{    //默认错误信息
					alert("验证码发送失败, 请重试！！");
				}

			}
		});
	});
    
    //发送ajax
	function uRequestAuthAjax(callback){
		authAjax(port['orderAuth'],     //地址
		{
			'type'   : 'sms',        
			'phone'  : authInfo.phone,
			'csrf_token' :  authInfo.csrf_token
		},callback);
	}

	//验证码ajax
	function authAjax(url,data, callback){
		$.post(url, data, function(res){
			if(typeof res != "object"){
				try{
					res = $.parseJSON(res);
				}catch(err){
					alert("服务器数据错误！！！");
					return ;
				}
			}
            console.log(callback);

			if( res.success){ //成功
				callback.success(res);

			}else{                      //失败
				callback.failed(res);
			}
		});

	}

	//发送用户所填短信验证码
	$(".js-send-confirm-auth").on("click", function(){
		authAjax(port['confirmAuth'],  //地址
		{
			'auth' : $('.js-confirm-auth').val(),
			'csrf_token' :  authInfo.csrf_token
		},
		{
			'success' : function(res){   //验证码正确
				if( res.successMsg ){
					alert( res.successMsg )
				}else{
					alert("验证码正确，稍后会为您送来!!!");
				}
				$authWrapper.hide();
	            $uMask.hide();

	            $reallyForm[0].submit();  //成功后提交

			},
			'failed'  : function(res){   //验证码错误
				if( res.errMsg ){
					alert(errMsg);
				}else{
					alert("验证码错误!!!!,请重填!!");
				}
			}
		});
	});

});
