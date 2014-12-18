<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>好吃狗-网上订餐</title>
    @yield("css")
    <script>
       window.onload = function(){
        var windowWidth = window.innerWidth,
           windowHeight = window.innerHeight,
           container = document.querySelector('.container');

   container.style.width = windowWidth * 0.9 + 'px';
   container.style.height = windowHeight * 0.9 + 'px';
       };
    </script>
</head>
<body>
    <div class="container" id="container">
        @yield("map")
    </div>
</body>

<!-- Template  -->
{{-- 页面模板 --}}
@yield("template")

<script src="http://webapi.amap.com/maps?v=1.3&key=ca56cd94a180aec9342b05d84954a75e"></script>
@yield("script")
</html>
