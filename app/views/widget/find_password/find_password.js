define(['jquery', 'timer', 'find_password_template/port',  "JSON"], function($,  Timer, port){
	$( "#tabs" ).tabs();

	$.prototype.serializeObject=function(){
		var obj= new Object();
		$.each(this.serializeArray(),function(index,param){
			if(!(param.name in obj)){
				obj[param.name]=param.value;
			}
		});
		return obj;
	};


	var RegObj = {
			register_email : /(?:^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$)|(?:^[\d]{6,16})$/,
			register_phone : /\d{11}/,
			auth_code : /\w+/,
			auth_sms : /\d+/,
			new_psw : /\w+/,
			repeat_psw : /\w+/
		},
		flag = true,
		send_sms = $(".send_sms"),
		sms_flag = true;


	$("input").on('blur', function(){
		$("div.u-error-tip").hide();
	});

	send_sms.on('click', function(e){

		e.preventDefault();
		var data = $("#form1").serializeObject(),
			phoneNumber = data['register_phone'];

		if(! sms_flag) return;

		if(! RegObj['register_phone'].test(phoneNumber)){
			$("#register_phone").parent().find("div.u-error-tip").show();
			return false;
		}

		var timer = new Timer(function(e){
			send_sms.html("剩余时间:" + e + "秒");
		});

		timer.start(30);

		sms_flag = false;

		timer.time_by_second(0, function(){
			sms_flag = true;
			send_sms.html("发送验证码");
		});

		$.ajax({
			url: port['sms_auth'],
			type: 'POST',
			data: JSON.encode({ "register_phone " : data['register_phone'], timestemp : new Date().getTime()}),
			contentType: 'application/json; charset=utf-8',
			dataType: 'json',
			async: false
		})
			.done(function(){

			})
			.fail(function(){
				alert("服务器错误！");
			});


		return false;
	});

	$('.auth_image').on('click', function(e){
		e.preventDefault();

		$.ajax({
			url : port['image_auth'],
			type : "POST",
			data : JSON.encode({}),
			contentType: 'application/json; charset=utf-8',
			dataType: 'json',
			async: false
		})
			.done(function(data){
				var imgSrc = data['nextSrc'] + "?version=" + Math.random() * 1000;
				$('.auth_image').attr("src" , imgSrc);
			})
			.fail(function(data){
				if(data['errMsg']){
					alert(data['errMsg']);
				}
				else{
					alert('服务器错误！');
				}
			});
	});


	$('form').on('submit', function(e){
		var target = e.target;
		var data = $(target).serializeObject();
		flag = true;
		$.each(data, function(name, value){
			if(name == "_token") return;
			if(! RegObj[name].test(value) ){
				var error = $("input[name='" + name + "'").parent().find("div.u-error-tip").show();
				flag = false;
			}
		});

		if(data['new_psw'] != data['repeat_psw']) {
			flag = false;
			$("input[name='repeat_psw']").parent().find("div.u-error-tip").show();
		}

		if(! flag) return false;


		if(target.id == 'form'){
			$('#tabs').hide();
			$('.success_info').show();
			return false;
		}

	});
});