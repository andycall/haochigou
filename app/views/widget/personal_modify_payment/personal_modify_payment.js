define(["jquery", "personal/port"], function($, port){
/*
 *@include "短信获取验证码"
*/

 var $smsBtn = $(".sms-btn");

    //短信验证码
    $smsBtn.on("click",function(){
        getAuth({ 
            'auth_way'  : 'sms',
            'timestemp' : new Date().getTime(),   //时间戳
            'userId' : $(".user-id").attr("data-user-id")         //用户唯一id
        });
    });

    //验证码ajax请求
    function getAuth(data,callback){
       console.log( port);
        $.post(port["smsAuth"], data, function(res){
            if( typeof res != 'object' ){
                try{
                    res = $.parseJSON(res);
                }catch(err){
                    alert("服务器数据异常，稍后再试");
                    return ;
                }
            }

            if( res.success == "true"){
                alert("短信已经发送，请注意接收验证码");
                    
                //计时禁止连续发送30秒
                $smsBtn.attr("disabled","disabled");

                var count     = 60,
                    orginText = $smsBtn.text();

                var authTimer = setInterval(function(){
                    $smsBtn.text( (count--) + "秒后可再发送");

                    if(count < 1 ){
                        $smsBtn.text(orginText).removeAttr("disabled");
                        clearInterval(authTimer);
                    }
                },1000);
            }else if( !res.success && res.errMsg){
                alert(res.errMsg);
            }
        });
    }




});