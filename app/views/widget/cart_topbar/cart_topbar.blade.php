<header class="cart-topbar group">
  <a class="ctopbar-logo" role="logo" href="">ele.me</a>
  <div class="ctopbar-breadcrumb" role="navigation">
      <a class="ctopbar-nav rst" target="_blank" href="//r.ele.me/jklyx">{{$data['shop_name']}}</a>
      <span class="ctopbar-nav">确认饿单</span>
  </div>

    <div class="eleme_dropdown ctopbar-usernav">
    <a class="e_toggle tuser-link caret">{{$data['user_name']}}</a>
    <ul class="e_dropdown topbar-user-dropdown ctopbar-dropdown">
  <li><a class="tuser-link" href="{{$userbar['url']['personal']}}"><i class="glyph-user"></i>个人中心</a></li>
  <li><a class="tuser-link" href="{{$userbar['url']['my_collection']}}"><i class="glyph-fav"></i>我的收藏</a></li>
  <li><a class="tuser-link" href="{{$userbar['url']['my_ticket']}}"><i class="glyph-location"></i>我的地址</a></li>
  <li><a class="tuser-link" href="{{$userbar['url']['my_secure']}}"><i class="glyph-config"></i>安全设置</a></li>
  <li class="divider"></li>
  <li><a id="topbar_logout" class="tuser-link" href="{{$userbar['url']['loginout']}}"><i class="glyph-off"></i>退出登录</a></li>
</ul>
  </div>
</header>


@section("css")
    @parent
    {{HTML::style("/css/widget/cart_topbar/cart_topbar.css")}}
@stop