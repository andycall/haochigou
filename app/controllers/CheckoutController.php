<?php
	/**
	 * 订单支付界面
	 */
class CheckoutController extends BaseController {

	/**
	 * 主界面
	 */
	public function index(){
		if( Auth::check() ){
			$cartkey = Auth::user()->front_uid;
		}else{
			$cartkey = $this->getIP();
		}
		$key = 'laravel:user:'.$cartkey.':cart';

		$shop_id = Redis::lrange($key, 0, 0);
		$ids = array_count_values(Redis::lrange($key, 1, -1));	
		$shop = Shop::find($shop_id[0]);

		$cart_list = array();
		$cart_amount = 0;	// 商品总价格
		$i = 0;
		foreach($ids as $id => $amount){
			$good = Menu::find($id);
			$cart_list[$i] = array(
				'good_name' => $good->title,
				'good_id' => $id,
				'good_price' => $good->price,
				'good_amount' => $amount,
				'good_total' => $good->price * $amount
			);
			$cart_amount += $cart_list[$i]['good_total'];
			$i++;
		}

		$output = array(
			'userbar' => $this->getUserBar(),
			'deliver_place' => Session::get('deliver_place'),
			'deliver_tel' => Session::get('deliver_tel'),
			'deliver_name' => Session::get('deliver_name'),
			'deliver_time' => array('08:00', '09:00', '10:12', '12:30'),
			'data' => array(
				'user_name' => '注释用户名',
				'shop_path' => '',
				'shop_id' => $shop->id,
				'shop_logo' => $shop->pic,
				'shop_name' => $shop->name,
				'shop_href' => url('shop/'.$shop->id),
				'shop_type' => $shop->type,
				'cart_list' => $cart_list,
				'cart_amount' => $cart_amount,
				'deliver_place' => Session::get('deliver_place'),
				//'deliver_tel' => Session::get('deliver_tel'),
				//'deliver_name' => Session::get('deliver_name'),
				'deliver_time' => array('08:00', '09:00', '10:12', '12:30'),
				'pay_method' => array( '0' => array( 'is_default' => 0, 'method_name' => '在线支付')),
				//'pay_statues' => '付款信息'
			),
			'pay_status' => '付款信息'
		);
		//var_dump($output);
		return View::make("template.order.order")->with($output);
	}

	/**
	 * userbar上面那一些列地址
	 */
	public function getUserBar(){
		$userbar = array();
		$userbar['url'] = array(
				"my_place"      => "这里是地址",
				"switch_palce"  => url('map'),
				"logo"          => url('/'),	// 网站主页地址
				"mobile"        => "123",                 				// 跳转到下载手机APP的地址
				"my_ticket"     => url('usercenter/recent_month'),                 			// 我的饿单的地址
				"my_gift"       => 'gift',                				// 礼品中心地址
				"feedback"      => 'feedback',                			// 反馈留言地址
				"shop_chart"    => "cart",                				// 购物车地址
				"user_mail"     => "mail",                				// 用户提醒的地址
				"personal"      => url('usercenter'),                			// 个人中心地址
				"my_collection" => url('usercenter/collect_shop'),               		// 我的收藏地址
				"my_secure"     => url('useraccount/personal_secure'),              	// 安全设置的地址
				"loginout"      => url("logout"),              			// 退出登录的地址
		);
        if( Auth::check() ){
            $user = Auth::user();
            if( $user->nickname == NULL and $user->mobile == NULL){
                $username = md5($user->email);
            }elseif( $user->nickname == NULL ){
                $username = md5($user->mobile);
            }else{
                $username = $user->nickname;
            }
            $userbar['data'] = array(
                'user_id' => $user->front_uid,
                'username' => $username,
                'user_place' => ''
            );          
        } else{
            $ipkey = md5($this->getIP());            
            $userbar['data'] = array(
                'user_id' => 0,
                'username' => $ipkey,
                'user_place' => '暂未获取地址'
            );
        }
		return $userbar;
	}

    //获取客户端ip地址
    private function getIP(){
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif(!empty($_SERVER["REMOTE_ADDR"])){
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else{
            $cip = "无法获取！";
        }
        return $cip;
    }
}