define(["jquery"],function(a){function b(){var b=a("#oriPhone").val(),d=a("#newPhone").val(),e=/^\d{11}$/;e.test(b)&&e.test(d)?a.ajax({url:"/takeaway/public/ajax_change_phone",type:"POST",data:{original_phone:b,new_phone:d},success:function(a){"true"==a.success?(alert("验证码已发送, 请注意查收."),c()):alert("原手机号错误!")}}):alert("请填写正确的手机号码.")}function c(){function b(){return c--,0==c?(a("#sendVerifyCode").html("发送验证码"),a("#sendVerifyCode").attr("disabled",!1)):(a("#sendVerifyCode").html("等待"+c+"秒重新获取验证码"),void setTimeout(b,1e3))}a("#sendVerifyCode").attr("disabled","disabled");var c=61;b()}window.$=a,a("#sendVerifyCode").on("click",b),console.log("personal change phone loaded")});