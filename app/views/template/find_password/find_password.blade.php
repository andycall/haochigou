@extends("layout.login")

@section("header")
	@include("widget.logo.logo")
@stop

@section("product_image")
    @include('widget.product_image.product_image')
@stop

@section("form")
    @if($email_change == "1")
        @include("widget.find_password.find_password")
    @else
        @include("widget.find_password_change.find_password_change")
    @endif
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

