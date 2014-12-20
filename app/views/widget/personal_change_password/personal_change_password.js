define(['jquery'], function($){
    $('.form-horizontal').on('submit', check);
    function check(e){
        var oriPwd = $('#oriPwd').val();
        var newPwd = $('#newPwd').val();
        var newPwdRepeat = $('#verifyPwd').val();
        if(newPwd != newPwdRepeat){
            alert('两次输入的密码不同!请检查!');
            return false;
        }
        if(!oriPwd){
            alert('原密码不得为空!请检查!');
            return false;
        }
        if(!newPwd || !newPwdRepeat){
            alert('密码不得为空!请检查!');
            return false;
        }
        return true;
    }
    //$('#changePwd').on('click', sendVerify);
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