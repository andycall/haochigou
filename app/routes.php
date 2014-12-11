<?php

# 地图

Route::get("/map", function(){
	return View::make("template.map.map");
});

Route::post("mapSearch", function(){
	$data = [
		0 => [
			"id" => "B00178WI1P",
			"name" => "重庆市",
			"type" => "地名地址信息;普通地名;省级地名",
			"location" => [
				"B" => 29.56301,
				"r" => 106.551557,
				"lng" => 106.551557,
				"lat" => 29.56301
			],
			"jump_url" => "http://baidu.com" // 点击之后的跳转地址
		],
		1 => [
			"id" => "B00178WI1P",
			"name" => "重庆市",
			"type" => "地名地址信息;普通地名;省级地名",
			"location" => [
				"B" => 29.56301,
				"r" => 106.551557,
				"lat" => 29.549747,
				"lng" =>106.547669
			],
			"jump_url" => "http://taobao.com" // 点击之后的跳转地址
		],
	];

	return Response::json($data);

});

# 登陆与注册
Route::post('registerAjax', 'UserAccessController@register');

Route::get("/register", function(){
    $data = [
        "auth_image" => "http://t11.baidu.com/it/u=254287606,1076184673&fm=58"        //验证码
    ];

    return View::make("template.login_register.register")->with($data);

});
Route::post('switch_auth','UserAccessController@CaptchaChange');

Route::post('loginAjax','UserAccessController@login');

Route::get("/login", function(){
    $data = [
        "find_password" => "#",
        "auth_image" => url('captcha')
    ];

    return View::make("template.login_register.login")->with($data);
});
Route::get('captcha','UserAccessController@CaptchaMake');

Route::get('logout','UserAccessController@logout');                      // 退出登录

#消息发送
Route::get('message','UserAccessController@sendMessage');
Route::post('message','UserAccessController@MessageCheck');

#头像上传
Route::post('userphoto','UserCenterController@portraitUpload');



#用户中心模块
Route::get('usercenter', array('before' => 'loginCheck', 'uses' => 'UserCenterController@index'));//用户中心首页

Route::get('usercenter/recent_month', array('before' => 'loginCheck', 'uses' => 'UserCenterController@recentMonth'));//月内订单

Route::get('usercenter/after_month', array('before' => 'loginCheck', 'uses' => 'UserCenterController@afterMonth'));//月前订单

Route::get('usercenter/collect_shop',array('before' => 'loginCheck', 'uses' => 'UserCenterController@shopCollect'));//收藏的店铺

Route::get('usercenter/collect_menu',array('before' => 'loginCheck', 'uses' => 'UserCenterController@menuCollect'));//收藏的菜品

Route::get('usercenter/personal_uncomment', array('before', 'loginCheck', 'uses' => 'UserCenterController@Uncomment'));  // 获取用户未评论的订单



# 用户账户模块
Route::get('useraccount/site', array('before' => 'loginCheck', 'uses' => 'UserAccountController@userSite'));//用户收货地址页面

Route::get('useraccount/site/{id}', array('before' => 'loginCheck', 'uses' => 'UserAccountController@userSite'));//用户收货地址编辑页面

Route::post('useraccount/site', array('before' => 'loginCheck', 'uses' => 'UserAccountController@userSiteEdit'));//用户收货地址编辑&新增接口

Route::post('useraccount/site/{id}', array('before' => 'loginCheck', 'uses' => 'UserAccountController@userSiteEdit'));//用户收货地址编辑&新增接口

Route::get('useraccount/sitedelete/{id}', array('before' => 'loginCheck', 'uses' => 'UserAccountController@siteDelete'));//用户收货地址删除接口

Route::post('/change_user_name',array('before' => 'loginCheck', 'uses' => 'UserAccountController@nickNameChange'));//用户昵称修改接口

Route::get('useraccount/password_change', array('before' => 'loginCheck', 'uses' => 'UserAccountController@passwordChange'));//用户修改登录密码页面

Route::get('useraccount/personal_secure', array('before' => 'loginCheck', 'uses' => 'UserAccountController@userSecurity'));//用户安全设置页面

#登录验证
Route::filter('loginCheck', function()
{
    if (!Auth::check())
    {
        return Redirect::to('login');
    }
});

# 主页
Route::get('/', 'MainController@index');
Route::post('cancelshop', array('before' => 'loginCheck', 'uses' => 'MainController@cancelShop'));     // 取消收藏店铺
Route::post('collectshop', array('before' => 'loginCheck', 'uses' => 'MainController@collectShop'));   // 收藏某个店铺


# 商家
Route::get('shop/{id}', 'ShopController@index');                // 商家页面
Route::get('shop/{id}/comments', 'ShopController@shopComments');// 商家评论页
Route::post('shop/addtocart', 'ShopController@addToCart');            // 添加一个菜单至购物车
Route::post('shop/cartInit', 'ShopController@cartInit');         // 购物车初始化
Route::post('shop/cartSetCount', 'ShopController@cartSetCount');    // 设置某个商品在购物车的数量
Route::get('userBarCart', 'ShopController@getUserBarCart');    // 获取购物车信息
Route::post('shop/cartClear', 'ShopController@cartClear');  // 清空购物车
Route::post('shop/cartDel', 'ShopController@cartDel');  // 从购物车删除
//Route::post('collectshop', 'ShopController@collectShop');       // 收藏某个店铺
//Route::post('collectmenu', 'ShopController@cancelShop');        // 取消收藏某个店铺



# 用户
Route::get('mail', function(){});                               // 用户提醒
Route::get('profile/security', function(){});                   // 安全设置
Route::post('addorder', array('before' => 'loginCheck', 'uses' => 'PersonalController@addOrder'));			// 添加订单
Route::post('cancelmenu', array('before' => 'loginCheck', 'uses' => 'PersonalController@cancelMenu'));     // 取消收藏商品
Route::post('collectmenu', array('before' => 'loginCheck', 'uses' => 'PersonalController@collectMenu'));	// 收藏某个商品
Route::post('confirmorder', array('before' => 'loginCheck', 'uses' => 'PersonalController@confirmOrder'));	// 确认收货
Route::post('modifyorder', array('before' => 'loginCheck', 'uses' => 'PersonalController@modifyOrder'));	// 修改订单状态：0表示已提交未付款，1表示已付款未收货，2表示已收获，3表示取消订单

#测试
Route::post('test', 'ShopController@cartDel');
Route::get('test', 'UserCenterController@Uncomment');


