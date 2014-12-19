<?php

# 地图

Route::get("/map", function(){
	return View::make("template.map.map");
});

Route::post("mapSearch",'MapController@shopsGet');

Route::post("mapSearch2", function(){
	$data = [1];

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

# 找回密码
Route::get("/find_password", function(){
	$data = [
		"find_email" => "",
		"find_phone" => "",
		"auth_image" => url('captcha'),    //验证码
		"email_change" => "1"
	];

	return View::make("template.find_password.find_password")->with($data);
});

Route::get("/find_password_change", function(){

	$data = [
		"find_email" => "",
		"email_change" => "0",
		"auth_image" => url('captcha'),    //验证码
	];

	return View::make("template.find_password.find_password")->with($data);
});


Route::post('image_auth','UserAccessController@CaptchaChange');

Route::post('loginAjax','UserAccessController@login');
Route::post('login','UserAccessController@login');

Route::get("/login", function(){
    $data = [
        "find_password" => url("find_password"),
        "auth_image" => url('captcha')
    ];

    return View::make("template.login_register.login")->with($data);
});
Route::get('captcha','UserAccessController@CaptchaMake');

Route::get('logout','UserAccessController@logout');                      // 退出登录

#消息发送
Route::get('message','UserAccessController@sendMessage');
Route::post('/sms_auth','UserAccessController@sendMessage');

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
Route::post('useraccount/password_change', array('before' => 'loginCheck', 'uses' => 'UserAccountController@passwordChange'));//用户修改登录密码接口

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

Route::post('/', function(){}); // 首页的一个post请求
Route::post('cancelshop', array('before' => 'loginCheck', 'uses' => 'MainController@cancelShop'));     // 取消收藏店铺
Route::post('collectshop', array('before' => 'loginCheck', 'uses' => 'MainController@collectShop'));   // 收藏某个店铺

Route::post('collectlist', array('before' => 'loginCheck', 'uses' => 'MainController@collectList'));    // 批量收藏功能
#搜索
Route::post('userBarSearch', 'SearchController@mainSearch');


# 商家
Route::get('shop/{id}', 'ShopController@index');                // 商家页面
Route::get('shop/{id}/comments', 'ShopController@shopComments');// 商家评论页
Route::post('addtocart', 'ShopController@addToCart');            // 添加一个菜单至购物车
Route::post('cartInit', 'ShopController@cartInit');         // 购物车初始化
Route::post('cartSetCount', 'ShopController@cartSetCount');    // 设置某个商品在购物车的数量
Route::post('cartSetCount', 'ShopController@cartSetCount'); // 这是在提交订单的页面
Route::get('userBarCart', 'ShopController@getUserBarCart');    // 获取购物车信息
Route::post('cartClear', 'ShopController@cartClear');  // 清空购物车
Route::post('cartDel', 'ShopController@cartDel');  // 从购物车删除
Route::post('goods_comments', 'ShopController@getGoodComment'); // 

# 用户
Route::get('mail', function(){});                               // 用户提醒
Route::get('profile/security', function(){});                   // 安全设置
Route::post('addorder', array('before' => 'loginCheck', 'uses' => 'PersonalController@addOrder'));			// 添加订单
Route::post('cancelmenu', array('before' => 'loginCheck', 'uses' => 'PersonalController@cancelMenu'));     // 取消收藏商品
Route::post('collectmenu', array('before' => 'loginCheck', 'uses' => 'PersonalController@collectMenu'));	// 收藏某个商品
Route::post('confirmorder', array('before' => 'loginCheck', 'uses' => 'PersonalController@confirmOrder'));	// 确认收货
Route::post('modifyorder', array('before' => 'loginCheck', 'uses' => 'PersonalController@modifyOrder'));	// 修改订单状态：0表示已提交未付款，1表示已付款未收货，2表示已收获，3表示取消订单
Route::get('checkout', 'CheckoutController@index');
#测试
Route::post('test', 'MainController@isOnline');
Route::get('test', 'MapController@test');



#测试
Route::get("/personal_modify_payment",function(){
   $data = [
        "userbar" => [
            "user_id"   => "bjckd",           //用户唯一id
            "url" => [
                "my_place" => "这里是地址",
                "switch_palce" => "##",
                "logo" => "123" ,                         // 网站主页地址
                "mobile" => "123",                 // 跳转到下载手机APP的地址
                "my_ticket" => "123",                 // 我的饿单的地址
                "my_gift"  => "123",                // 礼品中心地址
                "feedback" => "123",                // 反馈留言地址
                "shop_chart" => "123",                // 购物车地址
                "user_mail" => "123",                // 用户提醒的地址
                "personal" => "123",                // 个人中心地址
                "my_collection" => "123",               // 我的收藏地址
                "my_secure" => "123",              // 安全设置的地址
                "loginout" => "123",              // 退出登录的地址
                "switch_place" => "123"               // 切换当前地址的地址
            ]
        ],
        "sidebar" => [  // 左侧栏地址
            "personal_center" => url("/personal_center"),  // 个人中心的地址
            "personal_recent_month" => url("personal_recent_month"), // 最近一个月的地址
            "personal_after_month" => url("personal_after_month") , // 一个月之前
            "personal_uncomment" => url("personal_uncomment") ,  // 未点评的订单
            "personal_return" => url("personal_return"),     // 退单中的订单
            "personal_collection_shop" => url("personal_collection_shop"),// 我收藏的餐厅的地址
            "personal_collection_goods" => url("personal_collection_goods"), // 我收藏的商品的地址
            "personal_my_site" => url("personal_my_site") ,  // 我的地址
            "personal_change_password" => url("personal_change_password"), // 修改密码
            "personal_secure"=> url("personal_secure"),        // 安全设置
            "personal_details" => url("personal_details")       // 收支明细
        ],
        "rightContent" => [
            "telNumber" => "138****6073"                        //用户电话号码
        ]
    ];

    return View::make("template.personal.personal_modify_payment")->with($data);
});

##测试  =====验证码======
//Route::post("/sms_auth",function(){
//    $data = [
//        'success' => true
//    ];
//
//    return Response::json($data);
//});

//Route::post("/image_auth",function(){
//    $data = [
//            'success' => true,
//            'nextSrc' => 'http://img.store.sogou.com/net/a/08/link?appid=100520033&url=http%3A%2F%2Fwww.admin10000.com%2FUploadFiles%2FDocument%2F201202%2F20%2F20120220123258464881.JPG'
//        ];
//
//        return Response::json($data);
//});
