define([ "jquery", "timer", "find_password_template/port", "JSON" ], function($, Timer, port) {
    $("#tabs").tabs(), $.prototype.serializeObject = function() {
        var obj = new Object();
        return $.each(this.serializeArray(), function(index, param) {
            param.name in obj || (obj[param.name] = param.value);
        }), obj;
    };
    var RegObj = {
        register_email: /(?:^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$)|(?:^[\d]{6,16})$/,
        register_phone: /\d{11}/,
        auth_code: /\w+/,
        auth_sms: /\d+/,
        new_psw: /\w+/,
        repeat_psw: /\w+/
    }, flag = !0, send_sms = $(".send_sms"), sms_flag = !0;
    $("input").on("blur", function() {
        $("div.u-error-tip").hide();
    }), send_sms.on("click", function(e) {
        e.preventDefault();
        var data = $("#form1").serializeObject(), phoneNumber = data.register_phone;
        if (sms_flag) {
            if (!RegObj.register_phone.test(phoneNumber)) return $("#register_phone").parent().find("div.u-error-tip").show(), 
            !1;
            var timer = new Timer(function(e) {
                send_sms.html("剩余时间:" + e + "秒");
            });
            return timer.start(30), sms_flag = !1, timer.time_by_second(0, function() {
                sms_flag = !0, send_sms.html("发送验证码");
            }), $.ajax({
                url: port.sms_auth,
                type: "POST",
                data: JSON.encode({
                    "register_phone ": data.register_phone,
                    timestemp: new Date().getTime()
                }),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                async: !1
            }).done(function() {}).fail(function() {
                alert("服务器错误！");
            }), !1;
        }
    }), $(".auth_image").on("click", function(e) {
        e.preventDefault(), $.ajax({
            url: port.image_auth,
            type: "POST",
            data: JSON.encode({}),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: !1
        }).done(function(data) {
            var imgSrc = data.nextSrc + "?version=" + 1e3 * Math.random();
            $(".auth_image").attr("src", imgSrc);
        }).fail(function(data) {
            alert(data.errMsg ? data.errMsg : "服务器错误！");
        });
    }), $("form").on("submit", function(e) {
        var target = e.target, data = $(target).serializeObject();
        return flag = !0, $.each(data, function(name, value) {
            if ("_token" != name && !RegObj[name].test(value)) {
                {
                    $("input[name='" + name + "'").parent().find("div.u-error-tip").show();
                }
                flag = !1;
            }
        }), data.new_psw != data.repeat_psw && (flag = !1, $("input[name='repeat_psw']").parent().find("div.u-error-tip").show()), 
        flag ? ("form" == target.id && ($("#tabs").hide(), $(".success_info").show()), !1) : !1;
    });
});