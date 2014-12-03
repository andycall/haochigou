@if(count($my_store['data'] < 5))
    @for ($i = count($my_store['data']); $i < 5; $i++)
        <a href="##">
             <div class="collection-row-book collection-row-none" data-is_login="{{$uncollection_store['data']['is_login']}}" data-next_src="{{$uncollection_store['data']['next_src']}}">
                <div class="add"></div>
             </div>
        </a>
    @endfor
@endif


@section("css")
    @parent
    {{HTML::style("/css/widget/shop_uncollected/shop_uncollected.css")}}
@stop
