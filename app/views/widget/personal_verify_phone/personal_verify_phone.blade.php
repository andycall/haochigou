<div class="content-header">
    <h2>验证手机</h2>
</div>
<div class="content-inner">
    <div class="alert">
        验证手机...这里是提示语...........
    </div>
    {{ Form::open(array("class" => "horizontal", "method" => "post")); }}
        <div class="control-group clear-fix">
            <label class="control-label" for=""><span class="required">*</span>绑定邮箱:</label>
            <div class="controls">
                <input id="verifyEmail" name="verify_email" type="email" required>
            </div><!--end input-->
        </div><!--end clearfix-->

        <div class="form-actions">
            <button type="button" class="btn btn-yellow">发送验证邮件</button>
        </div>
    {{ Form::close(); }}
</div>
@section("css")
    @parent
    {{HTML::style("/css/widget/personal_verify_phone/personal_verify_phone.css")}}
@stop