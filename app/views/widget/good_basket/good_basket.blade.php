<article id="module_basket" class="cart-table">
    <header class="ctable-header group">
        <h2 class="ctable-title">美食篮子</h2>
        <span class="ctable-categ">单价</span>
        <span class="ctable-categ">数量</span>
        <span class="ctable-categ">总计</span>
    </header>

    <section class="cart-group cart_group" data-id="0">
        <ul class="cgroup-list" id="cgroup-list">
            @foreach($data['cart_list'] as $item)
                <li class="cgroup-item s_food" data-good_id="{{$item['good_id']}}" data-shop_id="{{$data['shop_id']}}">
                    <div class="cdish-name">{{$item['good_name']}}</div>
                    <div class="cdish-price symbol-rmb">{{$item['good_price']}}</div>
                    <div class="cdish-modify">
                        <a class="cdish-action desc dec_btn">-</a>
                        <input class="cdish-qty set_num" type="text" value="{{$item['good_amount']}}">
                        <a class="cdish-action incr inc_btn">+</a>
                    </div>
                    <div class="cdish-total symbol-rmb">{{$item['good_total']}}</div>
                    <div class="cdish-del">
                        <a class="del del_btn">×</a>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>


    <footer class="ctable-footer group">
        <div id="module_note" class="cart-note">
            <div class="ctable-form group">
                <i class="icon-note"></i>
                <input id="c_n_text" class="ctable-input" type="text" placeholder="给餐厅留言" maxlength="50">
            </div>

        </div>
        <div class="float-r">
            <div class="cart-summary basket_info">共<span id="total_count_basket">{{count($data['cart_list'])}}</span>份美食　应付金额：<span id="total_price_basket" class="symbol-rmb cart-cost">{{$data['cart_amount']}}</span></div>
        </div>
    </footer>
</article>

<script type="text/template" id="tpl-cgroup-item">
    <li class="cgroup-item s_food" data-good_id="<%= data.good_id %>" data-shop_id="{{$data['shop_id']}}">
        <div class="cdish-name"><%= data.title %></div>
        <div class="cdish-price symbol-rmb"><%= data.price %></div>
        <div class="cdish-modify">
            <a class="cdish-action desc dec_btn">-</a>
            <input class="cdish-qty set_num" type="text" value="<%= data.count %>">
            <a class="cdish-action incr inc_btn">+</a>
        </div>
        <div class="cdish-total symbol-rmb"><%= data.price * data.count %></div>
        <div class="cdish-del">
            <a class="del del_btn">×</a>
        </div>
    </li>
</script>
@section("css")
    @parent
    {{HTML::style("/css/widget/good_basket/good_basket.css")}}
@stop