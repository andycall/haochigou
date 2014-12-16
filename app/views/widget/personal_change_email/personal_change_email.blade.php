<div class="content-header">
    <h2>更换邮箱</h2>
</div>
<div class="content-inner">
    <div class="email_navigation">

    </div>
    <div class="alert">
        点击获取验证码，我们会发送一封邮件到您绑定的邮箱。请将邮件中的验证码输入下方的输入框内完成验证。如果长时间未收到邮件请等待60秒后再次点击获取验证码。
    </div>
    {{ Form::open(array("class" => "form-horizontal", "method" => "post")); }}


    {{ Form::close();  }}
</div>
@section("css")
    @parent
    {{HTML::style("/css/widget/personal_change_email/personal_change_email.css")}}
@stop