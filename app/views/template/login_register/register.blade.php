@extends("layout.register")

@section("header")
	@include("widget.logo.logo")
@stop

@section("product_image")
    @include('widget.product_image.product_image')
@stop

@section("form")
    @include("widget.register_form.register_form", array("jump_port" => "/login")) {{-- 跳转到登陆页面 --}}
@stop

@section("footer")
	@include("widget.footer.footer")
@stop

@section("css")
    {{HTML::style("/css/lib/jquery-ui.css")}}
    {{HTML::style("/css/template/lib/normalize.css")}}
    {{HTML::style("/css/template/lib/function.css")}}
	{{HTML::style("/css/template/login_register/register.css")}}
@stop

@section("script")
    {{HTML::script("/js/lib/require.js", ["data-main" => url("js/template/login_register/register.js")])}}
@stop

