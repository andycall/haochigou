<?php
/*
**Author:tianling
**createTime:14-12-4 上午12:03
*/
class UserAccountController extends BaseController{
    public $uid;

    public function __construct()
    {
        $this->uid = Auth::user()->front_uid;
    }


    /**
     * 地址信息列表
     **/
    public function userSite($id = null){

        $siteData = ReceiveAddr::where('front_uid','=',$this->uid)->get();

        $data['deliver_address'] = array();

        $data['deliver_address']['sites'] = array();

        $i = 0;
        foreach($siteData as $value){
            if($value->id == $id){
                $data['deliver_address']['form_deliver_phone'] = $value->phone;

                $data['deliver_address']['form_deliver_spare_phone'] = $value->tel;

                $data['deliver_address']['form_address_details'] = $value->address;

                $data['deliver_address']['address_receive_name'] = $value->receive_name;
            }
            $data['deliver_address']['sites'][$i] = array(
                'address_details'=>$value->address,
                'deliver_phone'=>$value->phone,
                'spare_phone'=>$value->tel,
                'address_state'=>$value->first,
                'edit_address'=>url('useraccount/site'.'/'.$value->id),
                'delete_address'=>url('useraccount/sitedelete'.'/'.$value->id),
                'set_default'=>'',
                'form_address_details'=>$value->address,
                'form_deliver_phone'=>$value->phone,
                'form_deliver_spare_phone'=>$value->tel,
                'address_receive_name'=>$value->receive_name
            );

            $i++;
        }

        if(!isset($data['deliver_address']['form_deliver_phone'])){
            $data['deliver_address']['form_deliver_phone'] = '';

            $data['deliver_address']['form_deliver_spare_phone'] = '';

            $data['deliver_address']['form_address_details'] = '';

            $data['deliver_address']['address_receive_name'] = '';
        }

        $data['sidebar'] = $this->sideBar();

        $data['userbar'] = $this->userBar();

        return View::make("template.personal.personal_my_site")->with($data);


    }


    /**
     * 用户收货地址增加或编辑
     **/
    public function userSiteEdit($id=null){
//        $id = Input::get('site_id');

        if($id == ''){
            $addr = new ReceiveAddr();

            if(Input::get('address_details') == '' || Input::get('deliver_phone') == ''){
                echo json_encode(array(
                    'status'=>'error',
                    'msg'=>'地址或联系电话不得为空'
                ));

                exit;
            }

            $addr->front_uid = $this->uid;
            $addr->address = Input::get('address_details');
            $addr->phone = Input::get('deliver_phone');
            $addr->tel = Input::get('spare_phone');
            $addr->first = 0;

            if(Input::get('user_name') ==''){
                if(Auth::user()->nickname == ''){
                    $addr->receive_name = '普通用户';
                }
                $addr->receive_name = Auth::user()->nickname;
            }else{
                $addr->receive_name = Input::get('user_name');
            }

            $addr->update_time = time();

            if($addr->save()){
                return Redirect::to('useraccount/site');
            }

        }else{
            $addr = ReceiveAddr::where('id','=',$id)->where('front_uid','=',$this->uid)->first();

            if(empty($addr)){
                echo json_encode(array(
                    'status'=>'error',
                    'msg'=>'该id不存在'
                ));

                exit;
            }

            if(Input::get('address_details') == '' || Input::get('deliver_phone') == ''){
                echo json_encode(array(
                    'status'=>'error',
                    'msg'=>'地址或联系电话不得为空'
                ));

                exit;
            }

            if(Input::get('receive_name') ==''){
                if(Auth::user()->nickname == ''){
                    $addr->receive_name = '普通用户';
                }
                $addr->receive_name = Auth::user()->nickname;
            }else{
                $addr->receive_name = Input::get('receive_name');
            }

            $addr->address = Input::get('address_details');
            $addr->phone = Input::get('deliver_phone');
            $addr->tel = Input::get('spare_phone');

            if($addr->save()){
                return Redirect::to('useraccount/site');
            }

        }


    }


    /**
     * 地址记录删除
     **/
    public function siteDelete($id){

        $addr = ReceiveAddr::where('id','=',$id)->where('front_uid','=',$this->uid)->first();
        

        if(empty($addr)){
            return Redirect::to('useraccount/site');
        }

        $addr->delete();

        return Redirect::to('useraccount/site');
    }


    /**
     * 用户昵称修改
     **/
    public function nickNameChange(){
        $newName = Input::get('user_name');

        $nameChange = FrontUser::where('front_uid','=',$this->uid)->update(array('nickname'=>$newName));

        if($nameChange){
            echo json_encode(array(
                'success'=>true,
                'state'=>200,
                'errMsg'=>'',
                'no'=>''
            ));
        }else{
            echo json_encode(array(
                'success'=>false,
                'state'=>200,
                'errMsg'=>'修改失败',
                'no'=>''
            ));
        }


    }



    /**
     * 用户密码修改页面
     **/
    public function passwordChange(){

        $data['sidebar'] = $this->sideBar();

        $data['userbar'] = $this->userBar();

        return View::make("template.personal.personal_change_password")->with($data);
    }



    /*
     * 用户安全中心页面
     **/
    public function userSecurity(){
        $userData = FrontUser::find($this->uid);

        $data['personal_secure'] = array(
            'secure_center'=>'',
            "secure_phone" => "110110110110",  // 用户的手机号码
            "change_phone" => "http://baidu.com/s?wd=change_phone",  // 更换手机号码的链接
            "change_email" => "http://baidu.com/s?wd=change_email",  // 更换邮箱的链接
            "send_email" => "http://baidu.com/s?wd=send_email", //重发激活邮件
            "cancel_phone" => "http://baidu.com/s?wd=cancel_phone",  // 解除绑定的地址
            "secure_email" => "abc@fsdghjk.com",  // 用户邮箱地址
            "change_cash_limit" => url("/personal_modify_payment"), // 更改支付额度
            "cash_limit" => "50", //支付额度
            "email_state"  => "inactive",  // 邮箱状态 inactive or active
            "phone_state"  => "active"  // 手机状态 inactive or active
        );


        if($userData->email_passed == 1){
            $data['personal_secure']['secure_level'] = 'high';
            $data['personal_secure']['secure_level_chinese'] = '高';
            $data['personal_secure']['email_state'] = 'active';

        }else{
            $data['personal_secure']['secure_level'] = 'middle';
            $data['personal_secure']['secure_level_chinese'] = '中';
            $data['personal_secure']['email_state'] = 'inactive';
        }

        $data['sidebar'] = $this->sideBar();

        $data['userbar'] = $this->userBar();


        return View::make("template.personal.personal_secure")->with($data);
    }


    private function sideBar(){
        return array(
            "personal_center" => url("usercenter"),  // 个人中心的地址
            "personal_recent_month" => url("usercenter/recent_month"), // 最近一个月的地址
            "personal_after_month" => url("usercenter/after_month") , // 一个月之前
            "personal_uncomment" => url('usercenter/personal_uncomment'),  // 未点评的订单
            "personal_return" => "#",     // 退单中的订单
            "personal_collection_shop" => url("usercenter/collect_shop"),// 我收藏的餐厅的地址
            "personal_collection_goods" => url("usercenter/collect_menu"), // 我收藏的商品的地址
            "personal_my_site" => url("useraccount/site") ,  // 我的地址
            "personal_change_password" => url("useraccount/password_change"), // 修改密码
            "personal_secure"=> url("useraccount/personal_secure"),        // 安全设置
            "personal_details" => "#"       // 收支明细
        );
    }


    private function userBar(){
        $userbar = array();
        $userbar['url'] = array(
            "my_place"      => "这里是地址",
            "switch_palce"  => "##",
            "logo"          => url('/'),    // 网站主页地址
            "mobile"        => "123",                               // 跳转到下载手机APP的地址
            "my_ticket"     => url('usercenter/recent_month'),                             // 我的饿单的地址
            "my_gift"       => 'gift',                              // 礼品中心地址
            "feedback"      => 'feedback',                          // 反馈留言地址
            "shop_chart"    => "cart",                              // 购物车地址
            "user_mail"     => "mail",                              // 用户提醒的地址
            "personal"      => url('usercenter'),                           // 个人中心地址
            "my_collection" => "profile/shop",                      // 我的收藏地址
            "my_secure"     => "profile/security",                  // 安全设置的地址
            "loginout"      => url("logout"),                       // 退出登录的地址
            "switch_place"  => "switch_place"                       // 切换当前地址的地址
        );
        if( Auth::check() ){
            $user = Auth::user();
            $userbar['data'] = array(
                'user_id' => $user->front_uid,
                'username' => $user->nickname,
                'user_place' => ''
            );
        } else{
            $userbar['data'] = array(
                'user_id' => 0,
                'username' => '未登录用户',
                'user_place' => '暂未获取地址'
            );
        }
        return $userbar;
    }
}