define([ "jquery", "underscore" ], function($, _) {
    console.log("map loaded");
    var mapObj = function() {
        var mapObj = new AMap.Map("container", {
            view: new AMap.View2D({
                zoom: 12
            }),
            keyboardEnable: !1
        }), d = document, city_location = "重庆", // 默认位置
        city_bound = {};
        // 初始位置信息
        // 获取用户位置信息
        // 逆地理编码
        // 坐标信息显示框
        return mapObj.getCityInfo = function() {
            mapObj.plugin([ "AMap.CitySearch" ], function() {
                //实例化城市查询类
                var citysearch = new AMap.CitySearch();
                //自动获取用户IP，返回当前城市
                citysearch.getLocalCity(), //citysearch.getCityByIp("123.125.114.*");
                AMap.event.addListener(citysearch, "complete", function(result) {
                    result && result.city && result.bounds ? (city_location = result.city, city_bound = result.bounds) : city_location = result.info;
                }), AMap.event.addListener(citysearch, "error", function(result) {
                    alert(result.info);
                });
            });
        }(), mapObj.getCoder = function(X, Y, callback) {
            var lnglatXY = new AMap.LngLat(X, Y);
            mapObj.plugin([ "AMap.Geocoder" ], function() {
                MGeocoder = new AMap.Geocoder({
                    radius: 1e3,
                    extensions: "all"
                }), //返回地理编码结果
                AMap.event.addListener(MGeocoder, "complete", callback), //逆地理编码
                MGeocoder.getAddress(lnglatXY);
            });
        }, mapObj.pointWindow = function(X, Y) {
            var self = this;
            self.getCoder(X, Y, function(data) {
                //debugger;
                console.log(data);
                var template = _.template($("#point_template").html())({
                    data: data.regeocode.addressComponent
                }), infoWindow = new AMap.InfoWindow({
                    content: template,
                    size: new AMap.Size(300, 0),
                    autoMove: !0,
                    offset: new AMap.Pixel(0, -30)
                });
                infoWindow.open(mapObj, new AMap.LngLat(X, Y));
            });
        }, function(config) {
            var autoComplete = config.autoComplete, drag = config.drag;
            // 自动补全模块 + 地图响应
            //输入提示
            //输出输入提示结果的回调函数
            //输入提示框鼠标滑过时的样式
            //输入提示框鼠标移出时的样式
            //从输入提示框中选择关键字并查询
            //定位选择输入提示关键字
            //输出关键字查询结果的回调函数
            //鼠标滑过查询结果改变背景样式，根据id打开信息窗体
            return autoComplete ? (autoComplete.windowsArr = [], autoComplete.marker = [], autoComplete.keydown = function() {
                var self = this, input = self.input, result = self.result, key = (event || window.event).keyCode, result = d.getElementById(result), cur = result.curSelect;
                if (40 === key) //down
                cur + 1 < result.children.length && (result.children[cur] && (result.children[cur].style.background = ""), 
                result.curSelect = cur + 1, result.children[cur + 1].style.background = "#CAE1FF", 
                d.getElementById(input).value = result.tipArr[cur + 1].name); else if (38 === key) //up
                cur - 1 >= 0 && (result.children[cur] && (result.children[cur].style.background = ""), 
                result.curSelect = cur - 1, result.children[cur - 1].style.background = "#CAE1FF", 
                d.getElementById(input).value = result.tipArr[cur - 1].name); else if (13 === key) {
                    //debugger;
                    var res = d.getElementById(input);
                    res && -1 !== res.curSelect && self.selectResult(result.curSelect);
                } else self.autoSearch();
            }, autoComplete.autoSearch = function() {
                var auto, self = this, input = self.input, result = self.result, keywords = d.getElementById(input).value;
                //加载输入提示插件
                mapObj.plugin([ "AMap.Autocomplete" ], function() {
                    var autoOptions = {
                        city: ""
                    };
                    auto = new AMap.Autocomplete(autoOptions), //查询成功时返回查询结果
                    keywords.length > 0 ? (AMap.event.addListener(auto, "complete", function(data) {
                        self.autocomplete_CallBack.call(self, data);
                    }), auto.search(keywords)) : d.getElementById(result).style.display = "none";
                });
            }, autoComplete.autocomplete_CallBack = function(data) {
                var self = this, result = ($("#" + self.input), $("#" + self.result)), resultStr = "", tipArr = data.tips;
                tipArr && tipArr.length > 0 && (resultStr = _.template($("#list_template").html())({
                    tipArr: tipArr
                })), result[0].curSelect = -1, result[0].tipArr = tipArr, result.html(resultStr), 
                result[0].style.display = "block", result.on("mouseover", function(ev) {
                    var target = ev.target;
                    if ("divid" == target.className) {
                        //console.log(target);
                        var dataMouseover = target.dataset.mouoseover;
                        self.openMarkerTipById(dataMouseover, target);
                    }
                }), result.on("mouseout", "div", function(ev) {
                    var target = ev.target;
                    if ("divid" == target.className) {
                        //console.log(target);
                        var dataMouseOut = target.dataset.mouseout;
                        self.onmouseout_MarkerStyle(dataMouseOut, target);
                    }
                }), result.on("click", "div", function(ev) {
                    var target = ev.target;
                    if ("divid" == target.className) {
                        var dataClick = parseInt(target.dataset.select);
                        self.selectResult(dataClick);
                    }
                });
            }, autoComplete.openMarkerTipById = function(pointid, self) {
                self.style.background = "#CAE1FF";
            }, autoComplete.onmouseout_MarkerStyle = function(pointid, self) {
                self.style.background = "";
            }, autoComplete.selectResult = function(index) {
                var self = this, input = self.input, result = self.result;
                if (!(0 > index)) {
                    navigator.userAgent.indexOf("MSIE") > 0 && (d.getElementById(input).onpropertychange = null, 
                    d.getElementById(input).onfocus = self.focus_callback);
                    //截取输入提示的关键字部分
                    //debugger;
                    var text = d.getElementById("divid" + (index + 1)).innerHTML.replace(/\s|<[^>].*?>.*<\/[^>].*?>/g, ""), cityCode = d.getElementById("divid" + (index + 1)).getAttribute("data");
                    d.getElementById(input).value = text, d.getElementById(result).style.display = "none", 
                    //根据选择的输入提示关键字查询
                    mapObj.plugin([ "AMap.PlaceSearch" ], function() {
                        var msearch = new AMap.PlaceSearch();
                        //构造地点查询类
                        AMap.event.addListener(msearch, "complete", self.placeSearch_CallBack), //查询成功时的回调函数
                        msearch.setCity(cityCode), msearch.search(text);
                    });
                }
            }, autoComplete.focus_callback = function() {
                {
                    var self = this, input = self.input;
                    self.result;
                }
                navigator.userAgent.indexOf("MSIE") > 0 && (document.getElementById(input).onpropertychange = self.autoSearch);
            }, autoComplete.placeSearch_CallBack = function(data) {
                var self = autoComplete, city = ($("#" + self.input), $("#" + self.city));
                // 清空搜索缓存
                autoComplete.windowsArr = [], autoComplete.marker = [], //清空地图上的InfoWindow和Marker
                mapObj.clearMap();
                var resultStr1, poiArr = data.poiList.pois, resultCount = poiArr.length;
                resultStr1 = _.template($("#place_template").html())({
                    resultCount: resultCount,
                    autoComplete: autoComplete,
                    poiArr: poiArr
                }), mapObj.setFitView(), city.html(resultStr1), city.show(), drag && ($(".drag-wrap").css({
                    left: 450
                }), drag.funcs.updateDragPosition()), city.on("mouseover", "li", function(ev) {
                    var target = ev.currentTarget;
                    if ("secid" == target.className) {
                        var dataMouseover = parseInt(target.dataset.mouseover);
                        self.openMarkerTipById(dataMouseover, target);
                    }
                }), city.on("mouseout", "li", function(ev) {
                    var target = ev.currentTarget;
                    if ("secid" == target.className) {
                        var dataMouseOut = target.dataset.mouseout;
                        self.onmouseout_MarkerStyle(dataMouseOut, target);
                    }
                }), city.on("click", "li", function(ev) {
                    var target = ev.currentTarget;
                    if ("secid" == target.className) {
                        var dataMouseover = parseInt(target.dataset.mouseover);
                        self.openMarkerTipById1(dataMouseover, target, resultCount);
                    }
                });
            }, autoComplete.openMarkerTipById1 = function(pointid, that, resultCount) {
                that.style.background = "#CAE1FF", autoComplete.windowsArr[pointid].open(mapObj, autoComplete.marker[pointid]);
                for (var i = 0; resultCount > i; i++) $(".icon" + (i + 1)).each(function(index, value) {
                    value.className = "icon icon" + (i + 1) + "_b";
                });
                $(".icon" + (parseInt(pointid) + 1) + "_b").each(function(index, value) {
                    value.className = "icon icon" + (parseInt(pointid) + 1);
                });
            }, autoComplete.onmouseout_iconStyle = function(pointid, self) {
                self.style.background = "", //                $(self).find('.icon')[0].className = "icon icon" + (parseInt(pointid)) + "_b";
                $(".icon" + parseInt(pointid)).each(function(index, value) {
                    value.className = "icon icon" + pointid + "_b";
                });
            }, autoComplete.addmarker = function(i, d) {
                var self = this, lngX = (self.input, self.result, d.location.getLng()), latY = d.location.getLat(), markerOption = {
                    map: mapObj,
                    content: "<div class='icon icon" + (i + 1) + "_b'></div>",
                    position: new AMap.LngLat(lngX, latY)
                }, mar = new AMap.Marker(markerOption);
                autoComplete.marker.push(new AMap.LngLat(lngX, latY));
                var windowTemplate = _.template($("#windowInfo_template").html())({
                    i: i,
                    d: d
                }), infoWindow = new AMap.InfoWindow({
                    content: windowTemplate,
                    size: new AMap.Size(300, 0),
                    autoMove: !0,
                    offset: new AMap.Pixel(0, -30)
                });
                autoComplete.windowsArr.push(infoWindow);
                var aa = function() {
                    infoWindow.open(mapObj, mar.getPosition());
                };
                AMap.event.addListener(mar, "click", aa);
            }, autoComplete.TipContents = function(type, address, tel) {
                {
                    var self = this;
                    self.input, self.result;
                }
                ("" == type || "undefined" == type || null == type || " undefined" == type || "undefined" == typeof type) && (type = "暂无"), 
                ("" == address || "undefined" == address || null == address || " undefined" == address || "undefined" == typeof address) && (address = "暂无"), 
                ("" == tel || "undefined" == tel || null == tel || " undefined" == tel || "tel" == typeof address) && (tel = "暂无");
                var str = "  地址：" + address + "<br />  电话：" + tel + " <br />  类型：" + type;
                return str;
            }) : $(".search").css("display", "none"), mapObj.init = function() {
                config.autoComplete && $("#" + config.autoComplete.input).on("keyup", function() {
                    autoComplete.keydown.call(config.autoComplete);
                });
            }, drag ? drag.funcs = function() {
                function mousedown() {
                    var _patchH = parseInt($(this).css("height")), _patchW = parseInt($(this).css("width")) / 2;
                    _dNode.addClass("drag-ing");
                    var _patchDragWrapOffset, _patchContOffset = _cont.offset();
                    _cont.on("mousemove", mousemove(_patchH, _patchW, _patchContOffset, _patchDragWrapOffset)), 
                    _cont.on("mouseup", mouseup(_patchH, _patchW, _patchContOffset, _patchDragWrapOffset));
                }
                function mousemove(_patchH, _patchW, _patchContOffset) {
                    return function(eve) {
                        var _x = eve.clientX, _y = eve.clientY, l = (_dNode.offset(), _x - _patchContOffset.left - _patchDragOriOffset.left - _patchW), //相对于 drag-wrap 的位置
                        t = _y - _patchContOffset.top - _patchDragOriOffset.top - _patchH;
                        _dNode.css({
                            top: t,
                            left: l
                        });
                    };
                }
                function mouseup(_patchH, _patchW, _patchContOffset) {
                    return function() {
                        _dNode.removeClass("drag-ing");
                        var containerPixelPos = fromContainerPixelToLngLat(_dNode.offset().left - _patchContOffset.left, _dNode.offset().top - _patchContOffset.top + _patchH);
                        //                        console.log(_dNode.offset().left - _patchContOffset.left, _dNode.offset().top - _patchContOffset.top);
                        _dNode.attr("data-lat", containerPixelPos.lat), _dNode.attr("data-lng", containerPixelPos.lng);
                        var marker = new AMap.Marker({
                            icon: new AMap.Icon({
                                //复杂图标
                                size: new AMap.Size(28, 34),
                                //图标大小
                                image: "image/map-sprites.png",
                                //大图地址
                                imageOffset: new AMap.Pixel(0, -140)
                            }),
                            position: new AMap.LngLat(containerPixelPos.lng, containerPixelPos.lat),
                            draggable: !0,
                            //点标记可拖拽
                            cursor: "move",
                            //鼠标悬停点标记时的鼠标样式
                            raiseOnDrag: !0
                        });
                        marker.setMap(mapObj);
                        //在地图上添加点
                        //实例化信息窗体
                        new AMap.InfoWindow({
                            isCustom: !0,
                            //使用自定义窗体
                            content: mapObj.pointWindow(containerPixelPos.lng, containerPixelPos.lat),
                            offset: new AMap.Pixel(110, -25)
                        });
                        AMap.event.addListener(marker, "mouseup", function() {
                            //鼠标点击marker弹出自定义的信息窗体
                            {
                                var o = (window.event, $(marker.$.r.B).offset()), containerPixelPos = fromContainerPixelToLngLat(o.left - _patchContOffset.left + 7, o.top - _patchContOffset.top + 2 * _patchH);
                                new AMap.InfoWindow({
                                    isCustom: !0,
                                    //使用自定义窗体
                                    content: mapObj.pointWindow(containerPixelPos.lng, containerPixelPos.lat),
                                    offset: new AMap.Pixel(110, -25)
                                });
                            }
                        }), _cont.off("mousemove"), _cont.off("mouseup"), _dNode.css({
                            top: 0,
                            left: 0
                        });
                    };
                }
                function fromContainerPixelToLngLat(left, top) {
                    var ll = mapObj.containTolnglat(new AMap.Pixel(left, top));
                    return {
                        lng: ll.getLng(),
                        lat: ll.getLat()
                    };
                }
                var _dNode = $(".drag-pin"), _cont = $(".container"), _patchDragOriOffset = _dNode.offset();
                //TODO for debug
                //AMap.event.addListener(mapObj, 'click', function(e){
                //    console.log(e.lnglat.getLng(), e.lnglat.getLat());
                //});
                return _dNode.on("mousedown", mousedown), {
                    updateDragPosition: function() {
                        _patchDragOriOffset = _dNode.offset(), _patchDragOriOffset.left -= _cont.offset().left;
                    }
                };
            }() : $(".drap-main").css("display", "none"), mapObj;
        };
    }();
    mapObj({
        // 开启搜索框搜索
        autoComplete: {
            input: "keyword",
            // 输入框ID
            result: "result1",
            // 结果框ID
            city: "city"
        },
        // 工具栏
        toolBar: {},
        drag: {}
    }).init();
});