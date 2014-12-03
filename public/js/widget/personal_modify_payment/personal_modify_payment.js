define([ "jquery", "personal/port" ], function($, port) {
    //验证码ajax请求
    function getAuth(data) {
        console.log(port), $.post(port.smsAuth, data, function(res) {
            if ("object" != typeof res) try {
                res = $.parseJSON(res);
            } catch (err) {
                return void alert("服务器数据异常，稍后再试");
            }
            if ("true" == res.success) {
                alert("短信已经发送，请注意接收验证码"), //计时禁止连续发送30秒
                $smsBtn.attr("disabled", "disabled");
                var count = 60, orginText = $smsBtn.text(), authTimer = setInterval(function() {
                    $smsBtn.text(count-- + "秒后可再发送"), 1 > count && ($smsBtn.text(orginText).removeAttr("disabled"), 
                    clearInterval(authTimer));
                }, 1e3);
            } else !res.success && res.errMsg && alert(res.errMsg);
        });
    }
    /*
 *@include "短信获取验证码"
*/
    var $smsBtn = $(".sms-btn");
    //短信验证码
    $smsBtn.on("click", function() {
        getAuth({
            auth_way: "sms",
            timestemp: new Date().getTime(),
            //时间戳
            userId: $(".user-id").attr("data-user-id")
        });
    });
});