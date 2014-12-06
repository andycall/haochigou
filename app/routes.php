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

Route::get('useraccount/sitedelete/{id}', array('before' => 'loginCheck', 'uses' => 'UserAccountController@siteDelete'));//用户收货地址删除接口


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
Route::get('test/{shop_id}', 'ShopController@getCategory');

