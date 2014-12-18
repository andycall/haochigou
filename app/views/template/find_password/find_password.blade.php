@extends("layout.login")

@section("header")
	@include("widget.logo.logo")
@stop

@section("product_image")
    @include('widget.product_image.product_image')
@stop

@section("form")
    <h2>忘记密码</h2>
    <p>请选择验证身份的方式</p>
    @include("widget.find_password.find_password")
@stop

@section("footer")
	@include("widget.footer.footer")
@stop

@section("css")
    {{HTML::style("/css/lib/jquery-ui.css")}}
    {{HTML::style("/css/template/lib/normalize.css")}}
	{{HTML::style("/css/template/find_password/find_password.css")}}
@stop

@section("script")
    {{HTML::script("/js/lib/require.js", ["data-main" => url("/js/template/find_password/find_password.js")])}}
@stop

