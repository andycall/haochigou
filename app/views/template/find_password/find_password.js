require.config({
	baseUrl : "/js/lib/",
	shim : {
		"underscore" : {
			exports : "_"
		}
	},
	paths : {
		"find_password_template" : "../template/find_password",
		"find_password" : "../widget/find_password",
		"product_image" : "../widget/product_image",
		"register_form" : "../widget/register_form",
		"login_form"    : "../widget/login_form",
		"footer"        : "../widget/footer"
	}
});
// 加载项目所需的所有依赖项
define([
	"find_password/find_password"
], function($){
	console.log("init");
});



s