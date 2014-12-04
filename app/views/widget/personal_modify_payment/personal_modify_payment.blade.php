{{-- personal_modify_payment --}}

{{-- headert --}}
<div class="content-header">
      <h2>修改支付限额</h2>
</div>

<div class="content-inner">
      {{--  状态栏 --}}
      <ol class="ui-profile-steps f-cb">
        <li class="active"><span>1. 验证手机号</span></li>
        <li><span>2. 设置新支付限额</span></li>
        <li class="last"><span>3. 支付限额修改成功</span></li>
      </ol>
       
       {{-- step1 --}}
      <div class="rating-time-limit-tips step1">
        点击获取验证码，我们会发送一条短信到您绑定的手机。请将短信中的验证码输入下方的输入框内完成验证。如果长时间未收到短信请等待60秒后再次点击获取验证码。
      </div>
      {{ Form::open(array("class" => "validate_terminal_code_form step1", "id" => "", "name" => "send_former_mobile_code", "method" => "post"));  }}

            <fieldset>

	            <div class="control-group">
	             	<label class="control-label">已绑定手机号:</label>
	             	<div class="controls">
	             		<p class="bound-phone">{{ $rightContent["telNumber"] }}</p>
	             	</div>

	           </div><!--end control-group-->
	          <div class="form-actions">
	            <button type="button" class="btn btn-yellow sms-btn" tabindex="4" name="send_terminal_code_btn" id="" data-loading="正在保存">获取验证码</button>
	          </div>
            </fieldset>
       {{ Form::close();  }}

      <div class="user-id" data-user-id="{{ $userbar['user_id'] }}"></div>

      {{ Form::open(array("class" => "validate_terminal_code_form step1", "name" => "validate_terminal_code", "action" => "javascript:void(0)")); }}
           <fieldset>
	          <div class="control-group">
	            	<label class="control-label" for="terminal_code">手机验证码：</label>
		            <div class="controls">
			              <input type="text" name="" id="terminal_code">
			              <span class="help-inline hidden" id="validate_terminal_error"></span>
		            </div>
	          </div>

                    <div class="form-actions">
                     		<button type="submit" class="btn btn-yellow" tabindex="4" id="validate_terminal_code_btn" terminal_type="0" is_new="0" sendto="/profile/ValidateFormerTerminalCode" returnto="/profile/modify_payment_quota_step2">确定</button>
                    </div>
            </fieldset>
      {{ Form::close(); }}

      {{-- step2 --}}
      <div class="rating-time-limit-tips step2">
        请设置每日在线支付额度，当您的当天累计支付金额超过此额度时，需要输入手机动态口令完成支付。
      </div>
       {{ Form::open(array("class" => "validate_terminal_code_form step2", "name" => "validate_terminal_code", "action" => "javascript:void(0)")); }}
       <fieldset>
	          <div class="control-group">
	            	<label class="control-label" for="terminal_code">*支付额度：</label>
		            <div class="controls">
			              <input type="text" name="" id="terminal_code">
			              <span class="help-inline hidden" id="validate_terminal_error"></span>

			              <p class="help-block">(请输入想要更改的支付额度)</p>
		            </div>
	          </div>

                    <div class="form-actions">
                     		<button type="submit" class="btn btn-yellow" tabindex="4" id="validate_terminal_code_btn" terminal_type="0" is_new="0" sendto="/profile/ValidateFormerTerminalCode" returnto="/profile/modify_payment_quota_step2">确定</button>
                    </div>
            </fieldset>
        {{ Form::close(); }}

</div>

@section("css")
    @parent
    {{HTML::style("/css/widget/personal_modify_payment/personal_modify_payment.css")}}
@stop