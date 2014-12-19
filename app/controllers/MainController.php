<?php

use Illuminate\Database\Eloquent\Collection;
/**
 * 主页
 *
 * index()   			主页面
 *
 * cancelShop()			取消收藏店铺
 * collectShop()		收藏店铺
 * getAddImage()		5个广告图片
 * getLevel($thing)		计算某个店铺的评分统计
 * getMyStore()			获取我收藏的店铺
 * getMyStoreAlert()	点击我的收藏那个加号弹出的对话框
 * getPicSwap()			获取轮播图片的信息
 * getShopList()		获取餐厅列表
 * getSideBar()			获取右边功能栏的基本信息
 * uncollection_store()	获取用户的登录信息
 * getUserBar()			userbar的一些地址数据
 * 
 */
class MainController extends BaseController {

	public function index(){
		/*
		if( !Auth::check() ){
			return Redirect::to('/map');
		}
		*/
		$user_x = 29.5334930;
		$user_y = 106.6075040;
		$data = array();

		$data['userbar'] 		= $this->getUserBar();
		$data['pic_swap'] 		= $this->getPicSwap();
		$data['side_bar']       = $this->getSideBar(); // 右边功能栏
		$data['my_store']       = $this->getMyStore(); // 我收藏的店铺
		$allStore               = $this->getShopList($user_x, $user_y);
		$data['shop_list']      = $allStore['shop_list'];	// 餐厅列表
		$data['more_shop']      = $allStore['more_shop']; // 更多餐厅
		# 弹出的这个框框也是从根据地址获取的那些店铺里面找的
		# 一个是根据新旧排序，一个是根据热门排序，分别有8个餐厅
		$data['my_store_alert']['data'] = $this->getMyStoreAlert($user_x, $user_y); // 我的收藏点击按钮之后弹出的框
		$data['add_image']['data'] = $this->getAddImage();//5个广告图片
		$data['uncollection_store']['data'] = $this->uncollection_store();

		return View::make('template.home.home')->with($data);
	}

	/**
	 * 计算某个店铺或者某个商品评分的各个等级的
	 *
	 * @return array(评论数，总评论数，总评价)
	 */
	public function getLevel($thing){
		$result = array();

		$thing_level = array();
		$thing_level['level_5'] = $thing->comments()->whereBetween('value', array(4.5, 5.0))->count('value');
		$thing_level['level_4'] = $thing->comments()->whereBetween('value', array(3.5, 4.0))->count('value');
		$thing_level['level_3'] = $thing->comments()->whereBetween('value', array(2.5, 3.0))->count('value');
		$thing_level['level_2'] = $thing->comments()->whereBetween('value', array(1.5, 2.0))->count('value');
		$thing_level['level_1'] = $thing->comments()->whereBetween('value', array(0.0, 1.0))->count('value');
		
		$result['thing_level']   = $thing_level;
		$result['comment_count'] = array_sum($thing_level);
		if($result['comment_count'] == 0){
			$result['thing_total'] = 0;
		}else{
			$result['thing_total'] = round( ($thing->comments()->sum('value')) / $result['comment_count'], 1);// 保留一位小数
		}
		return $result;

	}

	/**
	 * 批量收藏店铺
	 */
	public function collectList(){
		$list = Input::get('add_collection');
		$user = Auth::user();
		foreach($list as $one){
			$new_collect = array(
				'uid' => $user->front_uid,
				'shop_id' => $one['shop_id'],
				'uptime' => time(),
			);
			$collect = new CollectShop($new_collect);
			$collect->save();
		}
		$output = array(
				'success' => 'true',
				'state' => 200,
				'nextSrc' => '',
				'errMsg' => '',
				'no' => 0
		);
		$stores = $this->getMyStore();
		$output['data']['collection_shop'] = $stores['data'];
		return $output;
	}
		//var_dump($hehe);
		//return array('success' => true);
		
	/**
	 * 取消收藏某个商家
	 *
	 * 请求类型：POST
	 */
	public function cancelShop(){

		$user = Auth::user();
		$rules = array(
			'uid'     => 'required | integer',
			'shop_id' => 'required | integer',
		);
		$new_collect = array(
			'uid'     => $user->front_uid,
			'shop_id' => Input::get('shop_id'),
		);

		$v = Validator::make($new_collect, $rules);
		if( $v->fails() ){
			$message         = $v->messages();	
			return json_encode(array(
				'success' => false,
				'state'   => 400,
				'errMsg'  => $message->toArray(),
				'no'      => 1
			));
		}
		
		if( CollectShop::where('shop_id', Input::get('shop_id'))->where('uid', $user->front_uid)->delete() ){
			$output = array(
				'success' => 'true',
				'state'   => 200,
				'nextSrc' => '',
				'errMsg'  => '',
				'no'      => 0
			);
			$stores = $this->getMyStore();
			$output['data']['collection_shop'] = $stores['data'];
			return $output;
		}
		
	}

	/**
	 * 收藏某个店铺
	 *
	 * 请求类型：POST
	 */
	public function collectShop(){
		$user = Auth::user();
		$rules = array(
			'uid'     => 'required | integer | exists:front_user,front_uid',
			'shop_id' => 'required | integer | exists:shop,id'
		);
		$new_collect = array(
			'uid'     => $user->front_uid,
			'shop_id' => Input::get('shop_id'),
			'uptime'  => time()
		);
		$v = Validator::make($new_collect, $rules);
		if( $v->fails() ){
			$message         = $v->messages();	
			return json_encode(array(
				'success' => false,
				'state'   => 400,
				'errMsg'  => $message->toArray(),
				'no'      => 1
			));
		}

		$collect = new CollectShop($new_collect);
		if( $collect->save() ){
			$output = array(
				'success' => 'true',
				'state'   => 200,
				'nextSrc' => '',
				'errMsg'  => '',
				'no'      => 0
			);
			$stores = $this->getMyStore();
			$output['data']['collection_shop'] = $stores['data'];
			return $output;
		}
	}

	/**
	 * 5个广告图片
	 */
	public function getAddImage(){
		$data = array(
			array(
				'image_url' => 'http://haofly.qiniudn.com/haochigo_addimage1.gif',
				'jump_url'  => '##',
			),
			array(
				'image_url' => 'http://haofly.qiniudn.com/haochigo_addimage2.gif',
				'jump_url'  => '##',
			),
			array(
				'image_url' => 'http://haofly.qiniudn.com/haochigo_addimage3.gif',
				'jump_url'  => '##',
			),
			array(
				'image_url' => 'http://haofly.qiniudn.com/haochigo_addimage4.gif',
				'jump_url'  => '##',
			),
			array(
				'image_url' => 'http://haofly.qiniudn.com/haochigo_addimage5.gif',
				'jump_url'  => '##',
			),
        ); //5个广告图片
        return $data;
	}

	/**
	 * 获取我收藏的店铺，最多5个
	 */
	public function getMyStore(){
		if( !Auth::check() ){
			return array(
				'url'  => url('personal/collection/shop'),
				'data' => array()
			);
		}

		$user   = Auth::user();
		$stores = CollectShop::where('uid', $user->front_uid)->orderBy('uptime', 'desc')->take(5)->lists('shop_id');

		$my_store         = array();
		$my_store['url']  = url('personal/collection/shop');
		$my_store['data'] = array();

		foreach($stores as $store){
			$onestore = array();
			
			$shop                           = Shop::find($store);
			$onestore['shop_id']            = $shop->id;
			$onestore['place_id']           = 'null';					// 地址ID，暂时不用
			$onestore['shop_url']           = url('shop/'.$shop->id);		 	// 点击跳转到相应商家
			$onestore['shop_logo']          = $shop->pic;		  	// 商家的logo图片地址
			$onestore['deliver_time']       = (float)$shop->interval;	// 送货时间间隔
			$onestore['deliver_start']      = $shop->operation_time;	// ----------------------------没有开始时间，只有一个时间字符串
			$onestore['shop_name']          = $shop->name;			// 商家名称
			$onestore['shop_type']          = $shop->type;			// 商家类型，以逗号分隔的字符串---------------------------这个还是问一下
			$Level                          = $this->getLevel($shop);
			$onestore['shop_level']         = $Level['thing_total'];			// 商家评级
			$onestore['order_count']        = (float)$shop->sold_num;		// 订单总量
			$onestore['is_opening']         = $this->isOnline($shop->operation_time, date('H:i')) ? 0 : 1;			// 营业状态
			$onestore['is_ready_for_order'] = $shop->reserve;// 是否接受预定

			array_push($my_store['data'], $onestore);
		}
		return $my_store;
	}

	/**
	 * 点击我的收藏那个加号弹出的对话框
	 * 必须登录才能操作
	 */
	public function getMyStoreAlert($user_x, $user_y){
		if( !Auth::check() ){
			return ;
		} else{
			$user = Auth::user();
		}

		$data = array();
		//$data['new_shop'] = array();
		$data['hot_shop'] = array();

		$geohash   = new Geohash();
		$shopArray = $geohash->geohashGet($user_x, $user_y);
		$shops     = new Collection();
		foreach($shopArray['data'] as $oneshop){
			$onestore = array();
			$shop     = $oneshop['shopData'];
			$shops->add($shop);
		}
		/* 取消最新餐厅这儿
		$new_shops = $shops->sortByDesc('sold_num');
		foreach($new_shops as $shop){
			$one = array();
			$one['shop_id']            = $shop->id;
			$one['place_id']           = '123';
			$one['shop_url']           = url('shop/'.$shop->id);
			$one['shop_logo']          = $shop->pic;
			$one['deliver_time']       = (float)$shop->interval;
			$one['deliver_start']      = $shop->operation_time;
			$one['shop_name']          = $shop->name;
			$one['shop_type']          = $shop->type;
			$Level                     = $this->getLevel($shop);
			$one['shop_level']         = $Level['thing_total'];
			$one['order_count']        = (float)$shop->sold_num;
			$one['is_opening']         = $shop->is_online;
			$one['is_ready_for_order'] = $shop->reserve;
			if( !Auth::check() ){
				$one['is_collected'] = false;
			} else{
				$user = Auth::user();
				$one['is_collected'] = in_array($shop->id, $user->collectShop->lists('shop_id'))?true:false;	// 是否被收藏了
			}
			array_push($data['new_shop'], $one);
		}
		*/
		$hot_shops = $shops->sortByDesc('addtime');
		foreach($hot_shops as $shop){
				$one = array();
				$one['shop_id']            = $shop->id;
				$one['place_id']           = '123';
				$one['shop_url']           = url('shop/'.$shop->id);
				$one['shop_logo']          = $shop->pic;
				$one['deliver_time']       = (float)$shop->interval;
				$one['deliver_start']      = $shop->operation_time;
				$one['shop_name']          = $shop->name;
				$one['shop_type']          = $shop->type;
				$Level                     = $this->getLevel($shop);
				$one['shop_level']         = $Level['thing_total'];
				$one['order_count']        = (float)$shop->sold_num;
				$one['is_opening']         = $this->isOnline($shop->operation_time, date('H:i')) ? 0 : 1;
				$one['is_ready_for_order'] = $shop->reserve;
				$one['is_collected'] = in_array($shop->id, $user->collectShop->lists('shop_id'))?true:false;
				array_push($data['hot_shop'], $one);	
		}
		return $data;
	}

	/**
	 * 获取顶部轮播的图片
	 */
	public function getPicSwap(){
		$data = array(
			array( 
				"image_url" => "http://haofly.qiniudn.com/haochigo_pic_swap2.gif",
				"jump_url"  => ""
            ),
			array( 
				"image_url" => "http://haofly.qiniudn.com/haochigo_pic_swap.gif",
				"jump_url"  => ""
            ),
			array( 
				"image_url" => "http://haofly.qiniudn.com/haochigo_pic_swap3.gif",
				"jump_url"  => ""
            ),
			array( 
				"image_url" => "http://haofly.qiniudn.com/haochigo_pic_swap4.gif",
				"jump_url"  => ""
            ),
        );
		return $data;
	}

	/**
	 * 获取餐厅列表
	 * 默认15个，多的在更多餐厅里面显示
	*/
	public function getShopList($user_x, $user_y){
		$result = array(
			'shop_list' => array(),
			'more_shop' => array()
		);

		$result['shop_list']['data']          = array();
		$result['more_shop']['data']          = array();
		$result['shop_list']['data']['shops'] = array();

		# 首先获取所有的活动
		$result['shop_list']['data']['activity'] = array();
		$activity = Activity::all();

		foreach($activity as $act){
			if($act->aid != '1'){
				$result['shop_list']['data']['activity'][(string)$act->aid] = $act->name;
			}
		}

		$data['shops'] = array();

		$geohash   = new Geohash();
		$shopArray = $geohash->geohashGet($user_x, $user_y);
		$shops     = $shopArray['data'];
		$num       = 0; // 计数器，只15个

		foreach($shops as $oneshop){
			$onestore = array();
			$shop     = $oneshop['shopData'];

			$support_activity = explode(',', $shop->support_activity);
			$onestore['support_activity']        = $support_activity[0]==''?[]:$support_activity;		// 所有支持的活动id
			$onestore['isHot']                   = $shop->is_hot?'true':'false';								// 是否是热门餐厅


			$onestore['isOnline']                = $this->isOnline($shop->operation_time, date('H:i')) ? true : false;			// 是否营业	
			$onestore['isSupportPay']            = in_array('1', explode(',', $shop->pay_method));	// 是否支持在线支付
			$onestore['shop_id']                 = $shop->id;											// 商家id
			$onestore['place_id']                = 111111;									// -------------------位置经纬度和位置id后期再改数据库
			$onestore['shop_url']                = url('shop/'.$shop->id);									// 点击跳转到相应商家
			$onestore['shop_logo']               = $shop->pic;		  								// 商家的logo图片地址
			$onestore['deliver_time']            = (float)$shop->interval;								// 送货时间间隔
			$onestore['deliver_start']           = $shop->begin_time;								// 送货开始时间
			$onestore['shop_name']               = $shop->name;										// 商家名称
/*
echo $onestore['isOnline'];
echo $onestore['shop_name'];
*/
			$onestore['shop_type']               = $shop->type;
			$Level                               = $this->getLevel($shop);
			$onestore['shop_level']              = $Level['thing_total'];										// 五分制
			$onestore['shop_announce']           = $shop->announcement;							// 商家公告
			$onestore['deliver_state_start']     = $shop->begin_price;
			$onestore['deliver_start_statement'] = $shop->begin_price;		// 起送价描述
			$onestore['shop_address']            = $shop->address;									// 商家地址
			$onestore['is_opening']              = $this->isOnline($shop->operation_time, date('H:i')) ? 0 : 1;	// 0是正在营业，1是打烊了，2是太忙了
			$onestore['is_ready_for_order']      = $shop->reserve;							// 是否接收预定
			$onestore['close_msg']               = $shop->close_msg;									// 关门信息
			$onestore['business_hours']          = $shop->operation_time;						// 营业时间
			$onestore['shop_summary']            = $shop->intro;									// 商家简介
			$onestore['order_count']             = (float)$shop->sold_num;									// 订单数量
			if( !Auth::check() ){
				$onestore['is_collected'] = false;
			} else{
				$user = Auth::user();
				$onestore['is_collected']            = in_array($shop->id, $user->collectShop->lists('shop_id'))?true:false;	// 是否被收藏了
			}
			$onestore['additions']               = array();													// 额外的内容

			$num = $num + 1;
			if($num < 4){													// 更多餐厅和上面那排餐厅的数量
				array_push($result['shop_list']['data']['shops'] , $onestore);
			}else{
				array_push($result['more_shop']['data'], $onestore);
			}
		}
		return $result;
	}

	/**
	 * 是否在营业
	 * 09:50 - 13:30 / 16:00 - 19:30
	 * 这样的一个字符串
	 */
	public function isOnline($timeStr, $now){
		$times = explode(' ', $timeStr);// 不需要正则，用空格判断即可
		$len = count($times);
		// 然后看现在的时间在哪个区间，直接比较字符串即可
		for($i = 0; $i < $len; $i += 4){
			if( $times[$i] <= $now and $times[$i + 2] > $now){
				return true;
			}
		}
		return false;
	}

	/**
	 * 获取右边功能栏的基本信息
	 * @return [type] [description]
	 */
	public function getSideBar(){
		return array(
			'QR_code'      => 'http=>//db.jpg',
			'open_shop'    => 'http=>//shop',
			'hot_question' => 'http=>//hot_question'
		);
	}

	/**
	 * 获取用户的登录信息
	 */
	public function uncollection_store(){
		$data['is_login'] = Auth::check()?'1':'0';
		$data['next_src'] = url('login');
		return $data;
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
				"my_ticket"     => url('usercenter/recent_month'),      // 我的饿单的地址
				"my_gift"       => 'gift',                				// 礼品中心地址
				"feedback"      => 'feedback',                			// 反馈留言地址
				"shop_chart"    => "cart",                				// 购物车地址
				"user_mail"     => "mail",                				// 用户提醒的地址
				"personal"      => url('usercenter'),                	// 个人中心地址
				'checkout'		=> url('checkout'),						// 支付订单页面
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