define([ "map/map" ], function(mapObj) {
    console.log("init loaded"), mapObj({
        // 开启搜索框搜索
        autoComplete: {
            input: "keyword",
            // 输入框ID
            result: "result1",
            // 结果框ID
            city: "city"
        },
        // 工具栏
        toolbar: !0,
        scale: !0,
        drag: {}
    }).init();
});