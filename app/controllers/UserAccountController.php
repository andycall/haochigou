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
    public function userSite(){

        $siteData = ReceiveAddr::where('front_uid','=',$this->uid)->get();

        $data['deliver_address'] = array();

        $data['deliver_address']['sites'] = array();

        $i = 0;
        foreach($siteData as $value){
            $data['deliver_address']['sites'][$i] = array(
                'address_details'=>$value->address,
                'deliver_phone'=>$value->phone,
                'spare_phone'=>$value->tel,
                'address_state'=>$value->first,
                'edit_address'=>'',
                'delete_address'=>url('useraccount/sitedelete'.'/'.$value->id),
                'set_default'=>'',
                'form_address_details'=>$value->address,
                'form_deliver_phone'=>$value->phone,
                'form_deliver_spare_phone'=>$value->tel,
            );

            $i++;
        }

        $data['deliver_address']['form_deliver_phone'] = Auth::user()->mobile;

        $data['deliver_address']['form_deliver_spare_phone'] = '';

        $data['deliver_address']['form_address_details'] = '';

        $data['sidebar'] = $this->sideBar();

        $data['userbar']['url'] = $this->userBar();

        return View::make("template.personal.personal_my_site")->with($data);


    }


    /**
     * 用户收货地址增加或编辑
     **/
    public function userSiteEdit(){
        $id = Input::get('site_id');

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

            if(Input::get('receive_name') ==''){
                if(Auth::user()->nickname == ''){
                    $addr->receive_name = '普通用户';
                }
                $addr->receive_name = Auth::user()->nickname;
            }else{
                $addr->receive_name = Input::get('receive_name');
            }

            $addr->update_time = time();

            if($addr->save()){
                echo json_encode(array(
                    'status'=>'ok',
                    'msg'=>'添加成功'
                ));
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
                echo json_encode(array(
                    'status'=>'ok',
                    'msg'=>'修改成功'
                ));
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


    private function sideBar(){
        return array(
            "personal_center" => url("usercenter"),  // 个人中心的地址
            "personal_recent_month" => url("usercenter/recent_month"), // 最近一个月的地址
            "personal_after_month" => url("usercenter/after_month") , // 一个月之前
            "personal_uncomment" => "#" ,  // 未点评的订单
            "personal_return" => "#",     // 退单中的订单
            "personal_collection_shop" => url("usercenter/collect_shop"),// 我收藏的餐厅的地址
            "personal_collection_goods" => url("usercenter/collect_menu"), // 我收藏的商品的地址
            "personal_my_site" => url("useraccount/site") ,  // 我的地址
            "personal_change_password" => url("personal_change_password"), // 修改密码
            "personal_secure"=> url("personal_secure"),        // 安全设置
            "personal_details" => "#"       // 收支明细
        );
    }


    private function userBar(){
        return array(
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
            "loginout" => url("logout"),              // 退出登录的地址
            "switch_place" => "123"                  // 切换当前地址的地址
        );
    }
}