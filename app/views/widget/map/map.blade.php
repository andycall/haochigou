<div class="search">
    <input type="text" id="keyword" name="keyword" value=""  style="width: 95%;" placeholder="请输入你的订餐地址（学校，写字楼或街道）"/>
    <div id="result1" name="result1" style="overflow: auto; width: 95%; border: 1px solid gray;display: none;"></div>
</div>
<div class="drag-wrap">
    <div class="drag-pin"></div>
</div>
<div class="city" id="city" style="display: none;">
</div>


@section("css")
    @parent
    {{HTML::style("/css/widget/map/main.css")}}
    {{HTML::style("/css/widget/map/search.css")}}
    {{HTML::style("/css/widget/map/drag.css")}}
    {{HTML::style("/css/widget/map/city.css")}}
    {{HTML::style("/css/widget/map/windowInfo.css")}}
@stop