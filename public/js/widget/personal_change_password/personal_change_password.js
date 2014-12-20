define([ "jquery" ], function($) {
    function check() {
        var oriPwd = $("#oriPwd").val(), newPwd = $("#newPwd").val(), newPwdRepeat = $("#verifyPwd").val();
        return newPwd != newPwdRepeat ? (alert("两次输入的密码不同!请检查!"), !1) : oriPwd ? newPwd && newPwdRepeat ? !0 : (alert("密码不得为空!请检查!"), 
        !1) : (alert("原密码不得为空!请检查!"), !1);
    }
    $(".form-horizontal").on("submit", check), //$('#changePwd').on('click', sendVerify);
    //function sendVerify(e){
    //    var oriPwd = $('#oriPwd').val();
    //    var newPwd = $('#newPwd').val();
    //    var newPwdRepeat = $('#verifyPwd').val();
    //
    //    if(newPwd === newPwdRepeat){
    //        $.ajax({
    //            url: "/takeaway/public/ajax_change_phone",
    //            type: "POST",
    //            data: {
    //                original_password: oriPwd,
    //                new_password: newPwdRepeat
    //            },
    //            success: function(res){
    //                if(res.success == 'true'){
    //                    alert('密码修改成功!');
    //                    window.location = window.location;
    //                }else{
    //                    alert('密码错误!');
    //                }
    //            }
    //        });
    //    }else{
    //        alert('两次密码不一致!请检查!');
    //    }
    //}
    console.log("personal change password loaded");
});