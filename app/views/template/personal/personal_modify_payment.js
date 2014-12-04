require.config({
	baseUrl : "js/lib/",
	shim : {
		"underscore" : {
			exports : "_"
		}
	},
	paths : {
		personal : "../template/personal",
		footer  :"../widget/footer",
		userBar : "../widget/userBar",
		sideBar : "../widget/sideBar",
		personal_center : "../widget/personal_center",
		personal_after_month : "../widget/personal_after_month",
		personal_change_password : "../widget/personal_change_password",
		personal_collection_shop : "../widget/personal_collection_shop",
		personal_collection_goods : "../widget/personal_collection_goods",
		personal_details : "../widget/personal_details",
		personal_my_site : "../widget/personal_my_site",
		personal_recent_month : "../widget/personal_recent_month",
		personal_return : "../widget/personal_return",
		personal_modify_payment_quaoate: "../widget/personal_modify_payment"
	}
});

// 加载项目所需的所有依赖项
define([
	'userBar/userBar',
	"footer/footer",
	"sideBar/sideBar",
	"personal_details/personal_details",
	"personal_modify_payment_quaoate/personal_modify_payment"
], function($){
	console.log("init");
});

