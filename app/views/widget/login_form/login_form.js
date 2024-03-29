define(['jquery','login/port', 'loginPort'], function($, port, loginPort){
    //登陆模块

    /*
     * @inlude "切换登陆方式"
     * @inlude "验证表单"
     * @include "ajax 登陆"
     * @include "验证码点击切换/发送验证码"
    */

     var $smsBtn = $(".sms-btn");

    //图片验证码
    $(".captcha-img").on("click",function(){
        getAuth({
            'auth_way' : 'image',
            'auth_port': port.image_auth
        });
    });
    //短信验证码
    $smsBtn.on("click",function(){
        if( !/^[\d]{11}$/.test($("#user-mobile").val() ) ){
            $("#login-user-mobile").find(".u-error-tip").show();
            return ;
        }
        getAuth({
            'auth_port' : port.sms_auth,     //短信验证port
            'auth_way'  : 'sms',               //短信类型
            'timestemp' : new Date().getTime(),   //时间戳
            'telNumber' : $("#user-mobile").val()
        });
    });

    //验证码ajax请求
    function getAuth(data,callback){
        $.post( data.auth_port, data, function(res){
            if( typeof res != 'object' ){
                try{
                    res = $.parseJSON(res);
                }catch(err){
                    alert("服务器数据异常，稍后再试");
                    return ;
                }
            }
            if( res.success ){
                if(res.nextSrc){
                    $(".captcha-img").attr("src", res.nextSrc+'?t='+Math.random()*1000);
                }else{d
                    alert("短信已经发送，请注意接收验证码");
                    
                    //计时禁止连续发送30秒
                    $smsBtn.attr("disabled","disabled");

                    var count     = 30,
                        orginText = $smsBtn.text();

                    var authTimer = setInterval(function(){
                        $smsBtn.text( (count--) + "秒后可再发送");

                        if(count < 1 ){
                            $smsBtn.text(orginText).removeAttr("disabled");
                            clearInterval(authTimer);
                        }
                    },1000);
                }
            }else if( !res.success && res.errMsg){
                alert(res.errMsg);
            }else{
                alert("发送错误");
            }
        });
    }

    //输入框绑定事件,每次获得焦点时隐藏提示
    $("#login-form input").on('focus',function(){
        $(".u-error-tip").hide();
    });
     
    //切换登陆方式
    //记录以哪种方式登陆(默认以正常方式登陆)
    var loginWay = "normal";

    //切换按钮
    var $switchMobile = $("#switch-mobile"),
         $switchNormal = $("#switch-normal");

    //切换容器
    var $mobileLoginW = $(".js-mobile-wapper"),
         $normalLoginW = $(".js-normal-wapper");

    //$switchMobile  ==> $mobileLoginW
    //switchNormal   ==> $normalLoginW

    //切换到mobile
    $switchMobile.on("click",function(){
        $(this).hide();
        $switchNormal.show();

        $mobileLoginW.show();
        $normalLoginW.hide();

        loginWay = "mobile";

        //隐藏所有错误提示
        $(".u-error-tip").hide();

    });
    
    //切换到normal
    $switchNormal.on("click",function(){
        $(this).hide();
        $switchMobile.show();

        $normalLoginW.show();
        $mobileLoginW.hide();

        loginWay = "normal";

        //隐藏所有错误提示
        $(".u-error-tip").hide();
    });

    var $divUserEmail   = $("#login-user-name"),
        $divUserPwd  = $("#login-user-pwd"),
        $divUserTel    = $("#login-user-mobile"),
        $divAuth1      = $("#login-user-auth1"),
        $divAuth2      = $("#login-user-auth2");

    //表单验证     
    function checkLogin(data){
        //先隐藏原来的errtip
        $(".u-error-tip").hide();

        var regEmail = /(?:^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$)|(?:^[\d]{6,16})$/, //邮箱验证或电话号码
            regTel       = /^[\d]{11}$/; //手机号码目前只支持11位

        //normal err tip
        var $errPwd         = $divUserPwd.find(".u-error-tip"),
            $errUserName = $divUserEmail.find(".u-error-tip"),
            $errAuth          = $divAuth1.find(".u-error-tip");
        
        if( loginWay == 'normal'){
            //验证邮箱
            if( !regEmail.test(data.user_email) ){
                $errUserName.show();
                return false;
            }else{
                $errUserName.hide();
            }
            
            //密码没有输入
            if( data.user_psw.length < 1 ){
                $errPwd.show();
                return false;
            }else{
                $errPwd.hide();
            }

            //没有输入验证码
            if( data.user_auth.length != 4 ){
                $errAuth.show();
                return false;
            }else{
                $errAuth.hide();
            }

        }else if( loginWay == 'mobile'){
            //电话号码没有输入  user_email 此时存的是电话号码
            if( !regTel.test(data.user_email) ){
                $divUserTel.find(".u-error-tip").show();
                return false;
            }

            //没有输入验证码
            if( data.user_auth.length < 1 ){
                $divAuth2.find(".u-error-tip").show();
                return false;
            }
        }

        return true;
    }
    
    //ajax
    function ajaxForm(data){
        $.ajax({
            url      : port['login'],
            type     :  'post',
            dataType :  'json',
            data     :  data,

            success : function(res){
                if( typeof res != 'object' ){
                    try{
                        res = $.parseJSON(res);
                    }catch(err){
                        alert("服务器异常，稍后再试");
                        return;
                    }
                }
                if(res.success){
                    alert("登陆成功!");
                    location.href = loginPort['jump_port'];
                }
                if(res.errMsg.inputMsg){
                    alert(res.errMsg.inputMsg);
                }else if(res.errMsg.otherMsg){
                    alert(res.errMsg.otherMsg);
                }else{
                    alert("登陆失败!!!");
                }
            }
        });
    }
    
    //显示表单的错误
    function showInputError($id,msg){
        var $tip = $id.find(".u-error-tip");

        if(msg){
            $tip.text(msg);
        }

        $tip.show();
    }

    //表单提交
    $("#login-form").on("submit",function(ev){
        ev.preventDefault();

        var data = {
            'login_way' : loginWay,                                  //登陆方式
            'user_psw'  : $divUserPwd.find("input").val(),          //密码

            'user_remember' : (function(){
                   if( $("#auto-login")[0].checked == true )return true;
                   return false;
            }()),                                                        //记住密码自动登录

            'user_email' : (function(){

                if( loginWay == 'normal' ){return $divUserEmail.find("input").val();}
                else if( loginWay == 'mobile') {return $divUserTel.find("input").val();}

            })(),                                                       //登陆用户名 || 邮箱||电话号码

            'user_auth' : (function(){

                if( loginWay == 'normal' ){return $divAuth1.find("input").val();}
                else if( loginWay == 'mobile') {return $divAuth2.find("input").val();}

            }())                                                          //验证码

        };
        
        if( !checkLogin(data) ){
            return false;
        }
        
        ajaxForm(data);
        //保险起见
        return false;
    });

});