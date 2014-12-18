<div id="tabs">
  <ul>
    <li><a href="#tabs-1">注册邮箱</a></li>
    <li><a href="#tabs-2">已绑定手机</a></li>
  </ul>
  <div id="tabs-1">
    {{Form::open(array("id" => "form"  ,"method" => "POST", "url" => $find_email))}}
        <section>
            <input  class="tooltips"  type="text" placeholder="注册邮箱" title="sdsd" name="register_email" id="register_email" />
            <div class="u-error-tip f-dn">请填写正确的邮箱</div>
        </section>

        <section class="ui-helper-clearfix">
            <input class="tooltips"  type="text" placeholder="验证码" name="auth_code" id="auth_code" />
            <img class="auth_image" src="{{$auth_image}}" alt=""/>
            <div class="u-error-tip f-dn">请填写正确的验证码</div>
        </section>

        <section>
            <input  class="tooltips" type="text" placeholder="新密码" name="new_psw"  id="new_psw" />
            <div class="u-error-tip f-dn">密码填写错误</div>
        </section>

        <section>
            <input  class="tooltips" type="text" placeholder="确认密码" name="repeat_psw"  id="repeat_psw" />
            <div class="u-error-tip f-dn">2次密码不相同</div>
        </section>

        <section>
            <input class="tooltips form_submit" type="submit" id="email_submit" />
        </section>
    {{Form::close()}}
  </div>
  <div id="tabs-2">
    {{Form::open(array("id" => "form1" ,"method" => "POST", "url" => $find_phone))}}
        <section>
            <input  class="tooltips" type="text" placeholder="手机号码" name="register_phone"  id="register_phone" />
            <div class="u-error-tip f-dn">请填写正确的手机号码</div>
        </section>

        <section>
            <input  class="tooltips" type="text" placeholder="验证码" name="auth_sms" id="auth_sms" />
            <div class="u-error-tip f-dn">请填写正确的验证码</div>
            <button class="send_sms">发送验证码</button>
        </section>

        <section>
            <input  class="tooltips" type="text" placeholder="新密码" name="new_psw"  id="new_psw" />
            <div class="u-error-tip f-dn">密码填写错误</div>
        </section>

        <section>
            <input  class="tooltips" type="text" placeholder="确认密码" name="repeat_psw"  id="repeat_psw" />
            <div class="u-error-tip f-dn">2次密码不相同</div>
        </section>

        <section>
            <input  class="tooltips form_submit" type="submit" id="email_submit"  />
        </section>
    {{Form::close()}}
  </div>
</div>


@section("css")
    @parent
    {{HTML::style("/css/widget/find_password/find_password.css")}}
@stop
