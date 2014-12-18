// 登录注册ajax 接口

define(function(){
	return {
		"image_auth" : "/image_auth",   //图片验证码
		"sms_auth"  :         "/sms_auth",     //短信验证码
		"error_msg_after_email_send" : ""   // 如果是邮箱尚未通过， 则消息出现在这里
	}
});