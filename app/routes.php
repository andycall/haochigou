<?php
// router file
Route::get('users', function()
{
    return 'Users!';
});


# 登陆与注册
Route::post('register', 'UserAccessController@register');
Route::get('register', 'UserAccessController@register');
Route::post('loginAjax','UserAccessController@login');
Route::get("/login", function(){
    $data = [
        "find_password" => "http://www.hao123.com",
        "auth_image" => "http://t11.baidu.com/it/u=254287606,1076184673&fm=58"
    ];

    return View::make("template.login_register.login")->with($data);
});
Route::get('logout','UserAccessController@logout');                      // 退出登录

#消息发送
Route::get('message','UserAccessController@sendMessage');
Route::post('message','UserAccessController@MessageCheck');

Route::post('userphoto','UserCenterController@portraitUpload');



#用户中心模块
Route::get('usercenter', array('before' => 'loginCheck', 'uses' => 'UserCenterController@index'));//用户中心首页

Route::get('usercenter/recent_month', array('before' => 'loginCheck', 'uses' => 'UserCenterController@recentMonth'));//月内订单

Route::get('usercenter/after_month', array('before' => 'loginCheck', 'uses' => 'UserCenterController@afterMonth'));//月前订单

Route::get('usercenter/collect_shop',array('before' => 'loginCheck', 'uses' => 'UserCenterController@shopCollect'));//收藏的店铺

Route::get('usercenter/collect_menu',array('before' => 'loginCheck', 'uses' => 'UserCenterController@menuCollect'));//收藏的菜品


# 用户账户模块
Route::get('useraccount/site', array('before' => 'loginCheck', 'uses' => 'UserAccountController@userSite'));//用户收货地址页面

Route::post('useraccount/site', array('before' => 'loginCheck', 'uses' => 'UserAccountController@userSiteEdit'));//用户收货地址编辑&新增接口


Route::filter('loginCheck', function()
{
    if (!Auth::check())
    {
        return Redirect::to('login');
    }
});



# API/main接口，主页
Route::get('/', 'MainController@index');
Route::post('cancelshop', array('before' => 'loginCheck', 'uses' => 'MainController@cancelShop'));     // 取消收藏店铺
Route::post('collectshop', array('before' => 'loginCheck', 'uses' => 'MainController@collectShop'));   // 收藏某个店铺



# 商家
Route::get('shop/{id}', 'ShopController@index');                // 商家页面
Route::get('shop/{id}/comments', 'ShopController@shopComments');// 商家评论页
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
Route::get('/test/{id}', 'ShopController@getShopComments');


Route::get("/personal_my_site", function(){
    $data = [
        "userbar" => [
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
                "switch_place" => "123"                  // 切换当前地址的地址
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

        "deliver_address" => [  // 送餐地址
            "sites" => [
                0 => [
                    "address_details"  => "ddddd",      // 送餐详细地址
                    "deliver_phone"    => "123232132",      // 送餐联系电话
                    "spare_phone"      => "12321323" ,              // 备用电话
                    "address_state"    => "0",      // 是否是默认地址 0 是默认地址 1不是默认地址
                    "edit_address"     => "http://baidu.com",      // 编辑地址的链接 (不用ajax  →_→)
                    "delete_address"   => "http://baidu.com",      // 删除地址的链接
                    "set_default"      => "http://baidu.com" ,     // 设为默认的地址
                ],
                1 => [
                    "address_details"  => "ddddd",      // 送餐详细地址
                    "deliver_phone"    => "123232132",      // 送餐联系电话
                    "spare_phone"      => "12321323" ,              // 备用电话
                    "address_state"    => "1",      // 是否是默认地址 0 是默认地址 1不是默认地址
                    "edit_address"     => "http://baidu.com",      // 编辑地址的链接 (不用ajax  →_→)
                    "delete_address"   => "http://baidu.com",      // 删除地址的链接
                    "set_default"      => "http://baidu.com"  ,    // 设为默认的地址
                ]
            ],
            "form_address_details"        => "asdasd" ,      // 表单中填入的送餐详细地址
            "form_deliver_phone"          => "123123" ,      // 表单中填入的手机号码
            "form_deliver_spare_phone"    => "12323"        // 表单中填入的备用号码
        ]
    ];

    return View::make("template.personal.personal_my_site")->with($data);
});
