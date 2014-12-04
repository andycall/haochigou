@extends("layout.map")

@section("map")
    @include("widget/map/map")
@stop


@section("template")
   <!-- Template  -->
  <script type="text/template" id="place_template">
      <div>共<span><%= poil.length %></span>地址</div>
      <ul id="search_list">
          <% for(var i = 0,len = poiArr.length; i < len ; i ++){ %>
          <li class="secid" data-mouseover="<%= i %>" data-mouseout="<%= i + 1 %>">
              <div class="icon icon<%= i + 1 %>_b"></div>
              <h2><%= poiArr[i].name %></h2>
              <span><%= poiArr[i].address %></span>
              <p>还有N个城市</p>
          </li>
          <% } %>

          <% for(var i = 0,len = poil.length; i < len; i ++ ){ %>
             <% autoComplete.addmarker(i, poil[i]); %>
          <% } %>
          <% if((resultIndex / 10) >  0){ %>
              <a href="#" class="prevGroup">&lt;&lt;&lt;</a>
          <% } %>

          <% for(var i = 0; i < resultCount; i ++){ %>
              <span class="<%= i == (resultIndex / 10) ? 'now' : '' %>"><%= i + 1 %></span>
          <% } %>
          <% if(resultIndex  + 10 <  poil.length){ %>
              <a href="#" class="nextGroup">&gt;&gt;&gt;</a>
          <% } %>
      </ul>
  </script>

  <!-- 显示信息框模板 -->
  <script type="text/template" id="windowInfo_template">
      <h2 class=""><%= d.name %></h2>
      <p><%= d.address %></p>
      <p>附近有<span>4</span>家餐厅</p>
  </script>

  <!-- 头顶信息展示模板 -->
  <script type="text/template" id="point_template">
      <h2 class=""><%= data.district %></h2>
      <p><%= data.street %> <%= data.streetNumber %>号</p>
      <p>附近有<span>4</span>家餐厅</p>
  </script>


  <!-- 地点列表模板 -->
  <script type="text/template" id="list_template">
      <% tipArr.forEach(function(value, index){ %>
          <div class="divid" id="divid<%= index + 1 %>" data-mouseover="<%= index + 1 %>" data-select="<%= index %>" data-mouseout="<%= index + 1 %>" data="<%= tipArr[index].adcode %>">
              <%= tipArr[index].name %>
              <span style="color:#C1C1C1"><%= tipArr[index].district %></span>
          </div>
      <% }); %>
  </script>

@stop

@section("script")
    {{HTML::script("/js/lib/require.js", ['data-main' => url("/js/template/map/map.js")])}}
@stop