{{Form::open(array("id" => "form"  ,"method" => "POST", "url" => $find_email))}}
        <h2>重置密码</h2>
        <br/>
         <section>
            <input  class="tooltips" type="text" placeholder="新密码" name="new_psw"  id="new_psw" />
            <div class="u-error-tip f-dn">密码填写错误</div>
         </section>

         <section>
            <input  class="tooltips" type="text" placeholder="确认密码" name="repeat_psw"  id="repeat_psw" />
            <div class="u-error-tip f-dn">2次密码不相同</div>
         </section>
         <section class="ui-helper-clearfix">
            <input class="tooltips"  type="text" placeholder="验证码" name="auth_code" id="auth_code" />
            <img class="auth_image" src="{{$auth_image}}" alt=""/>
            <div class="u-error-tip f-dn">请填写正确的验证码</div>
         </section>

         <section>
            <input class="tooltips form_submit" type="submit" id="email_submit" />
         </section>
    {{Form::close()}}

@section("css")
    @parent
    {{HTML::style("/css/widget/find_password_change/find_password_change.css")}}
@stop
