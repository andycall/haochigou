<header class="checkout-header">
  <div id="restaurant" class="group" data-shop_id="{{$data['shop_id']}}" data-user_id>
    <a class="crst-logo" target="_blank" href="" title="{{$data['shop_name']}}">
      <img class="crst-img" src="{{$data['shop_logo']}}">
    </a>
    <div class="crst-info">
      <a class="crst-name" target="_blank" title="{{$data['shop_name']}}" href="//r.ele.me/cqchengcinmianzhuang">{{$data['shop_name']}}</a>
      <p class="crst-flavor">{{$data['shop_type']}}</p>
    </div>
  </div>

  <ol class="cart-flow">
    <li class="cflow-nav first">选择美食
    </li><li class="cflow-nav second">饿单信息
    </li><li class="cflow-nav last">完成饿单
  </li></ol>
</header>

@section("css")
    @parent
    {{HTML::style("/css/widget/content_header/content_header.css")}}
@stop