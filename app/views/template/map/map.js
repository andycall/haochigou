require.config({
	baseUrl : "js/lib/",
	shim : {
		"underscore" : {
			exports : "_"
		}
	},
	paths : {
		"map" : "../widget/map",
		"search" : "../template/map"
	}
});

// 加载项目所需的所有依赖项
define([
	"map/map",
	"map/init"
], function($){
	console.log("init");
});





