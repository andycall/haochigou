<?php

/**
 * 店铺相关信息
 *
 * index($shop_id)							商家主页
 * shopComments($shop_id)					商家评论页
 *
 * addToCart()								添加一个菜单到购物车
 * cartClear()								清空购物车
 * cartDel()								从购物车删除
 * cartInit()								购物车初始化
 * cartSetCount()							设置某个上i陪你在购物车里的数量
 * getAnnouncement($shop_id)				获取某个店铺的公告
 * getBestSeller($shop_id)					获取某个店铺的本周热卖
 * getCategory($shop_id)					获取店铺分类的具体内容
 * getDistance($lat1, $lng1, $lat2, $lng2)	计算两个坐标之间的距离
 * getGoodCategory($shop_id)				获取店铺的分类信息
 * getGoodComment()							获取某个商品的评论
 * getLevel($thing)							获取店铺/菜单的评价统计信息
 * getMap($shop_id)							获取地图信息
 * getMyCollect($shop_id)					获取用户在该店铺内的收藏的商品
 * getShopComments($shop_id)				获取店铺所有的评价
 * getShopInfo($shop_id)					获取店铺的基本信息
 * getTopbar($shop_id)						获取顶部栏的一些地址数据
 * getUserBar()								获取userbar上面的一些地址数据
 * getUserBarCart()							获取用户的购物车信息
 */

class ShopController extends BaseController {

	/**
	 * 商家菜单页
	 */
	public function index($shop_id){
		$data = array();

		$data['collect'] = $this->getMyCollect($shop_id);								// 获取用户在该店铺内的收藏的商品
		$data['userbar'] = $this->getUserBar();
		$data['top_bar']                          = $this->getTopbar($shop_id);			// 获取顶部栏信息
		$data['good_category']['data']            = $this->getGoodCategory($shop_id);	// 获取美食分类
		$data['category']['data']['classify_sec'] = $this->getCategory($shop_id);		// 获取分类内容
		$data['announcement']['data']             = $this->getAnnouncement($shop_id);	// 获取餐厅公告
		$data['best_seller']              = $this->getBestSeller($shop_id);				// 获取本周热卖
#TODO：地图地址未完成
		$data['shop_map']['data']      = $this->getMap($shop_id);						// 地图地址
		//var_dump($data);
		return View::make("template.shop.shop")->with($data);
	}

	/**
	 * 商家评论页
	 */
#TODO：未做分页功能
	public function shopComments($shop_id){
		$data = array();
		$data['userbar'] 		= $this->getUserBar();
		$data['top_bar']        = $this->getTopbar($shop_id);
		$data['announcement']   = $this->getAnnouncement($shop_id);
		$data['good_category']  = $this->getGoodCategory($shop_id);		// 商家评论页要这个干嘛
		$data['category']       = $this->getCategory($shop_id);
		$data['shop_comments']  = $this->getShopComments($shop_id);
		return View::make("template.shop.shop_comment")->with($data);
	}

##
#	上面是页面：
#	下面是方法：
##

	/**
	 * 添加一个菜单到购物车
	 * 目前只实现了已经登录用户的添加
	 */
	public function addToCart(){
		$menu_id = Input::get('good_id');
		$shop_id = Input::get('shop_id');

		if( Auth::check() ){
			$cartkey = Auth::user()->front_uid;
		}else{
			$cartkey = $this->getIP();
		}
		$key = 'laravel:user:'.$cartkey.':cart';

		// 第一个元素为店铺的ID，购物车里只能放一个店铺的东西
		if( Redis::llen($key) == 0){
			Redis::lpush($key, $shop_id);
			Redis::rpush($key, $menu_id);

			$shop = Shop::find($shop_id);
			$menu = Menu::find($menu_id);
			$data['success'] = 'true';
			$data['data']['addedItem'] = array(
				'goods_id'    => $menu_id,
				'goods_name'  => $menu->title,
				'goods_count' => 1,
				'goods_price' => $menu->price
			);
			$data['data']['cart_all']   = $menu->price;
			$data['data']['shop_id']    = $shop_id;
			$data['data']['is_ready']   = ($shop->deliver_price <= $menu->price) ? 'true' : 'false';
			$data['data']['card_count'] = 1;
			return Response::json($data);
		}elseif( Redis::lindex($key, 0) != $shop_id ) {
			return json_encode(array(
				'status' => '400',
				'msg'    => '不是同一家店'
			));
		}else{
			Redis::rpush($key, $menu_id);
			
			$ids  = array_count_values(Redis::lrange($key, 1, -1));
			$shop = Shop::find($shop_id);
			$menu = Menu::find($menu_id);
			$menu_count = $ids[(string)$menu_id];
			$data['success'] = 'true';
			$data['data']['addedItem'] = array(
				'goods_id'    => $menu_id,
				'goods_name'  => $menu->title,
				'goods_count' => $menu_count,
				'goods_price' => $menu_count * $menu->price
			);
			$data['data']['cart_all'] = 0;
			$data['data']['cart_card_count'] = 0;
			foreach($ids as $id=>$count){
				$good = Menu::find($id);
				$data['data']['cart_card_count'] += $count;
				$data['data']['cart_all'] += ($count * $good->price);
			}
			$data['data']['shop_id']  = $shop_id;
			$data['data']['is_ready'] = ($shop->deliver_price <= $data['data']['cart_all']) ? 'true' : 'false';
			return Response::json($data);
		}
	}

	/**
	 * 清空购物车
	 */
	public function cartClear(){
		if( Auth::check() ){
			$cartkey = Auth::user()->front_uid;
		}else{
			$cartkey = $this->getIP();
		}
		$key = 'laravel:user:'.$cartkey.':cart';
		if( Redis::del($key) ){
			return Response::json(array(
				'success' => 'true'
			));
		}	
	}

	/**
	 * 从购物车删除
	 */
	public function cartDel(){
		if( Auth::check() ){
			$cartkey = Auth::user()->front_uid;
		}else{
			$cartkey = $this->getIP();
		}
		$key = 'laravel:user:'.$cartkey.':cart';

		$good_id = Input::get('good_id');
		$shop_id = Redis::lrange($key, 0, 0);
		if( Redis::lrem($key, 0, $good_id) ){
			if( $shop_id[0] == $good_id ){
				Redis::lpush($key, $shop_id);
			}
			if( Redis::llen($key) == 1){
				Redis::del($key);
			}
			return Response::json(array(
				'success' => 'true'
			));
		}
	}

	/**
	 * 购物车初始化
	 */
	public function cartInit(){
		if( Auth::check() ){
			$cartkey = Auth::user()->front_uid;
		}else{
			$cartkey = $this->getIP();
		}
		$key = 'laravel:user:'.$cartkey.':cart';

		//var_dump(Redis::lrange($key, 0, -1));
		//var_dump(Redis::lrange($key, 0, -1));
		$shop_id = Redis::lrange($key, 0, 0);
		$ids     = array_count_values(Redis::lrange($key, 1, -1));		

		$output['success'] = 'true';
		$output['data'] = array();
		foreach($ids as $id=>$count){

			if( strlen($id) == 0) continue;	// 不知道为什么，反正就是可能会出现这种情况
			$menu = Menu::find($id);

			array_push($output['data'], array(
				'id'    => $id,
				'price' => $menu->price,
				'count' => $count,
				'title' => $menu->title
			));
		}
		return $output;
	}

	/**
	 * 设置某个商品在购物车里的数量
	 * 此项操作必须是购物车至少有一件的情况
	 */
	public function cartSetCount(){
		if( Auth::check() ){
			$cartkey = Auth::user()->front_uid;
		}else{
			$cartkey = $this->getIP();
		}
		$key = 'laravel:user:'.$cartkey.':cart';

		$good_id = Input::get('good_id');
		$shop_id = Input::get('shop_id');	// 不用
		$count   = Input::get('count');

		$ids = array_count_values(Redis::lrange($key, 1, -1));

		$num = $count - $ids[$good_id];
		if( $num > 0 ){
			for($i = $num; $i > 0; $i--){
				Redis::rpush($key, $good_id);
			}
		}elseif( $num < 0 ){
			Redis::lrem($key, $num, $good_id);
			if( Redis::llen($key) == 1){
				Redis::del($key);
			}
		}// 相等就不作处理了s
		//var_dump(Redis::lrange($key, 0, -1));
		return Response::json(array(
			'success' => 'true'
		));
	}

	/**
	 * 获取某餐厅的公告
	 * @return array
	 */
	public function getAnnouncement($shop_id){
		$data = array();
		$shop = Shop::find($shop_id);
		
		$data['announce_content'] = $shop->announcement;
		$data['start_price']      = $shop->begin_price;
		$data['activities']       = array(
			array(
				'activity_name' => '满58起送',
				'activity_icon' => ''
			)
		);
		return $data;


		/* 取消了活动
		$menus = $shop->groups()->get();
		foreach($menus as $menu){
			if($menu->activity_id != 1){
				$oneact = array();
				$act = Activity::find($menu->activity_id);

				$oneact['activity_name'] = $act->name;
				$oneact['activity_icon'] = $act->icon;

				array_push($data['activities'], $oneact);
			}
		}
		*/
	}

	/**
	 * 获取店铺的本周热卖,5个商品,销量前五的
	 */
	public function getBestSeller($shop_id){
		$data = array();

		$shop = Shop::find($shop_id);
		$menus = $shop->menus()->get()->sortByDesc('sold_week')->take(5);
		foreach($menus as $menu){
			$one                  = array();
			$one['goods_id']      = $menu->id;
			$one['goods_name']    = $menu->title;
			$Level                = $this->getLevel($menu);
			$one['goods_level']   = round($Level['thing_total'] * 2);
			$one['comment_count'] = $Level['comment_count'];
			$one['goods_price']   = (float)$menu->price;
			$one['shop_id']		  = $menu->shop_id;
			$one['shop_state']    = $shop->state;
			$one['error_state']   = $menu->state;
			array_push($data, $one);
		}
		return $data;
	}

	/**
	 * 获取某个店铺分类的内容
	 */
	public function getCategory($shop_id){
		$result = array();

		$shop = Shop::find($shop_id);
		$categories = $shop->groups->all();
		$i = $j = $k = 0;	// 数组的key，我也是醉了

		foreach($categories as $group){
			$one = array();

			$one['classify_name'] = $group->name;
			$one['classify_id']   = $group->id;
			$one['classify_icon'] = $group->icon;

			$one['activity_ads']['activity_name'] = '';
			$one['activity_ads']['activity_statement'] = '';
			/* 不要活动了
			if($group->activity_id == 1 ){
				$one['activity_ads']['activity_name'] = '';
				$one['activity_ads']['activity_statement'] = '';
			} else{
				$act = Activity::find($group->activity_id);
				$one['activity_ads']['activity_name'] = $act->name;
				$one['activity_ads']['activity_statement'] = $act->intro;
			}
			*/

			$goods           = Menu::where('shop_id', $shop_id)->where('group_id', $group->id)->get();
			$classify_images = array();
			$classify_goods  = array();
			$j = $k = 0;
			foreach($goods as $good){
				$onegood = array();				

				if($good->pic != NULL){
					$onegood['goods_id']       = $good->id;				// 商品id
					$onegood['goods_name']     = $good->title;			// 商品名称
					$Level                     = $this->getLevel($good);		
					$onegood['goods_level']    = $Level['thing_total'];	// 商品等级
					$onegood['goods_price']    = (float)$good->price;	// 商品价格
					$onegood['goods_icon']     = '';					// 没有就没有嘛
					$onegood['goods_original'] = (float)$good->original_price;	// 如果是促销就显示原价
					$onegood['good_sails']	   = (float)$good->sold_num;
					$classify_images[$j++] = $onegood;
					//array_push($classify_images, $onegood);
				}else{
					$onegood['goods_id']       = $good->id;				// 商品id
					$onegood['goods_image']    = $good->icon; 			// 商品图片地址
					$onegood['goods_name']     = $good->title;			// 商品名称
					$Level                     = $this->getLevel($good);
					$onegood['goods_level']    = $Level['thing_total'];	// 商品等级
					$onegood['comment_count']  = $Level['comment_count'];// 投票人数
					$onegood['goods_sails']    = (float)$good->sold_num;		// 商品销量(这里写的是总销量)
					$onegood['goods_price']    = (float)$good->price;	// 商品价格
					$onegood['goods_icon']     = $good->icon;			// 一些用户促销的图标
					$onegood['goods_original'] = (float)$good->original_price;	// 如果是促销，这个用于显示原价
					$onegood['good_sails']	   = (float)$good->sold_num;
					$classify_goods[$k++] = $onegood;
					//array_push($classify_goods, $onegood);
				}
			}
			$one['classify_images'] = $classify_images;
			$one['classify_goods']  = $classify_goods;
			$result[$i++] = $one;
			//array_push($result, $one);
		}
//		var_dump($result);
		return $result;
	}

	/**
     * 计算两个坐标之间的距离
     * @param 显示店铺的横纵坐标，然后是用户的横纵坐标
     * @return int 单位是米
     */
    private function getDistance($lat1, $lng1, $lat2, $lng2){
        $EARTH_RADIUS = 6378.137;
        $radLat1 = $lat1 * M_PI / 180.0;
        $radLat2 = $lat2 * M_PI / 180.0;
        $radLng1 = $lng1 * M_PI / 180.0;
        $radLng2 = $lng2 * M_PI / 180.0;
        $a = $radLat1 - $radLat2;  
        $b = $radLng2 - $radLng2; 
        $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));  
        $s = $s * $EARTH_RADIUS;  
        $s = round($s * 10000);  
        return $s;  
    }

	/**
	 * 功能：商家菜单页美食分类，商品的分类和活动是一起的，不过活动还是单独列一张表出来的
	 */
	public function getGoodCategory($shop_id){
		$data = array();
		$shop = Shop::find($shop_id);
				
		$groups         = $shop->groups->all();

		$goods_category = array();
		$good_activity  = array();
		foreach($groups as $group){
			$one = array();

			$one['classify_name']      = $group->name;
			$one['classify_name_abbr'] = (mb_strlen($group->name, 'utf8') > 10) ? mb_substr($group->name, 0, 3, 'utf8').'...' : $group->name;
			$one['classify_id']        = $group->id;
			$one['classify_count']     = Menu::where('shop_id', $shop_id)->where('group_id', $group->id)->get()->count('shop_id');
			$one['classify_icon']      = $group->icon;
			array_push($goods_category, $one);

			/* 不要活动了
			if($group->activity_id == 1){		// 不是活动
				$one['classify_name']      = $group->name;
				$one['classify_name_abbr'] = (mb_strlen($group->name, 'utf8') > 10) ? mb_substr($group->name, 0, 3, 'utf8').'...' : $group->name;
				$one['classify_id']        = $group->id;
				$one['classify_count']     = Menu::where('shop_id', $shop_id)->where('group_id', $group->activity_id)->get()->count('shop_id');
				$one['classify_icon']      = $group->icon;
				array_push($goods_category, $one);
			}else{								// 是活动的
				$act                       = Activity::find($group->activity_id);
				$one['activity_name']      = $act->name;
				$one['activity_id']        = $act->aid;
				$one['activity_icon']      = $act->icon;
				$one['activity_statement'] = $act->intro;
				array_push($good_activity, $one);
			}
			*/
		}
		$data['goods_category'] = $goods_category;
		$data['good_activity']  = $good_activity;
		return $data;
	}

	/**
	 * API/shop/获取一个商品的评论
	 */
	public function getGoodComment(){
		$good_id = Input::get('goods_id');
		
		$menu = Menu::find($good_id);
		$comments = $menu->comments;
		//var_dump($comments);
		
		if( $comments != NULL){
			$output = array();
			$output['success'] = true;
			$output['state']   = 200;
			$output['nextSrc'] = '';
			$output['errMsg']  = '';
			$output['no']      = 0;
			$Level = $this->getLevel($menu);
			$output['data']['shop_level']    = $Level['thing_level'];
			$output['data']['shop_total']    = $Level['thing_total'];
			$output['data']['comment_total'] = $Level['thing_total'];
			$output['data']['comment_body']  = array();

			foreach($comments as $comment){
				$one = array();
				$one['comment_person']  = FrontUser::find($comment->front_uid)->nickname;
				$one['comment_date']    = $comment->time;
				$one['comment_level']   = $comment->value;
				$one['comment_content'] = $comment->content;
				array_push($output['data']['comment_body'], $one);
			}
			var_dump($output);
//			return Response::json($output);
		}else{
			return json_encode(array(
				'success' => false,
				'state'   => 400,
				'errMsg'  => '获取失败',
				'no'      => 1
			));
		}
	}

	/**
	 * 计算某个店铺评分的各个等级的
	 * @param  $thing 某个店铺或者某个商品
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
	 * 获取地图图片
	 */
	public function getMap($shop_id){
		$xy = Geohash::find($shop_id);
#TODO：前端给出用户的经纬度		
		$user_x   = 39.9812386;
		$user_y   = 116.3068369;
		$distance = $this->getDistance($xy->x, $xy->y, $user_x, $user_y);
		$data = array(
			'map_url'  => '',
			'distance' => $distance
		);
		return $data;
	}

	/**
	 * 获取该用户在该店铺内的收藏的商品
	 * 默认只显示5个，多的直接去掉
	 * @return [type] [description]
	 */
	public function getMyCollect($shop_id){
		$data = array();
		if( !Auth::check() ){
			return $data;
		}

		$user = Auth::user();
		$allCollects = $user->collectMenu;
		
		foreach($allCollects as $collect){
			$menu = Menu::find($collect->menu_id);
			if( ($menu->shop_id) == $shop_id ){
				array_push($data, array(
					'detail'  => $menu->title,
					'price'   => (float)$menu->price,
					'good_id' => $menu->id,
					'shop_id' => $menu->shop_id
				));
			}
		}
		return $data;
	}

	/**
	 * 获取店铺所有的评价，不分页
	 */
	public function getShopComments($shop_id){
		$data = array();
		$comments = Shop::find($shop_id)->comments;

		foreach($comments as $comment){
			$one  = array();
			$menu = Menu::find($comment->menu_id);
			$user = FrontUser::find($comment->front_uid);

			$one['good_name']  = $menu->title;
			$one['user_name']  = $user->nickname;
			$one['time']       = date('Y-m-d', $comment->time);
			$one['content']    = $comment->content;
			$one['good_price'] = $menu->price;
#TODO：这里的评分居然是以图片形式的。。。
			$one['star_url'] = 'http://static11.elemecdn.com/forward/dist/img/restaurant/rst-sprites.b35686d3.png';

			array_push($data, $one);
		}
		return $data;
	}

	/**
	 * 获取某个店铺的基本信息
	 */
	public function getShopInfo($shop_id){
		$shop = Shop::find($shop_id);
		$info = array();
		
		$info['shop_id']        = $shop_id;					// 商家ID
#TODO：place_id不需要
		$info['shop_logo']      = $shop->pic;				// 商家的logo图片地址
		$info['shop_name']      = $shop->name;				// 商家名称
		$info['shop_type']      = $shop->type;				// 商家类型,逗号分隔的字符串
		$Level                  = $this->getLevel($shop);
		$info['shop_level']     = $Level['thing_level'];	// 总共10个等级
		$info['shop_total']     = $Level['thing_total'];	// 总评价
		$info['comment_count']  = $Level['comment_count'];	// 评论人数
		$info['shop_statement'] = $shop->intro; 			// 商家简介
		$info['shop_time']      = $shop->operation_time;	// 营业时间，字符串表示
		$info['shop_address']   = $shop->address;			// 商家地址
		$info['deliver_begin']  = $shop->begin_time;		// 送餐开始时间
		$xy                     = Geohash::find($shop_id);
#TODO：前端给出用户的经纬度		
		$user_x = 39.98123;
		$user_y = 116.3068369;
		$info['shop_distance']  = $this->getDistance($xy->x, $xy->y, $user_x, $user_y); // 人与店铺的距离(米)
		$info['price_begin']    = (float)$shop->deliver_price;		// 起送价
		if( Auth::check()){
			$front_user = Auth::user();
			$info['is_collected'] = in_array($shop_id, $front_user->collectShop->lists('shop_id'))?'true':'false';	// 是否被收藏了
		} else{
			$info['is_collected'] = 'false';
		}
		$info['interval']       = (float)$shop->interval;			// 送餐速度
		$info['shop_remark']    = '';
		return $info;
	}

	/**
	 * 功能：商家菜单页top_bar
	 * 模块：前台
	 *
	 * 测试完成
	 * 对应API：API/shop/商家菜单页
	 */
	public function getTopbar($shop_id){
		$shop = Shop::find($shop_id);		
		$top_bar = array(
			'url'  => array(),
			'data' => array()
		);
														
		$top_bar['url']['return_back'] = url('/');					// 返回主页的地址
		$top_bar['url']['shop_url']    = url('shop/'.$shop_id);		// 当前商家的地址
		$top_bar['url']['comment_url'] = url('shop/'.$shop_id.'/comments');	// 商家评论页的地址
		$top_bar['url']['menu_url']    = url('shop/'.$shop_id);		// 商家菜单的地址
		//$top_bar['url']['photo_url']   = $shop_id.'/photo';		// 美食墙的地址
		//$top_bar['url']['message_url'] = $shop_id.'/message';	// 商家留言的地址
		$top_bar['url']['map_url']	   = '地图地址';
		$top_bar['data'] = $this->getShopInfo($shop_id);
		return $top_bar;
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
				'checkout'		=> url('checkout'),						// 支付订单页面
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

	public function getUserBarCart(){
		if( Auth::check() ){
			$cartkey = Auth::user()->front_uid;
		}else{
			$cartkey = $this->getIP();
		}
		$key = 'laravel:user:'.$cartkey.':cart';

		if( $shop_id = Redis::lindex($key, 0)){
			$data['successs'] = 'true';
			$data['state'] = 200;
			$data['errMsg'] = '';
			$data['no'] = 0;

			$shop = Shop::find($shop_id);
			$data['data']['url'] = 'shop/'.$shop_id;
			$data['data']['shop_name'] = $shop->name;
			$data['data']['all_value'] = 0;
			$data['data']['state'] = $shop->state == 0 ? 0 : 1;
			if($shop->state == 1) 
				$data['data']['state_msg'] = '店铺打烊了';
			elseif($shop->state == 2) 
				$data['data']['state_msg'] = '店铺太忙了';
			else 
				$data['data']['state_msg'] = '';

			$ids = array_count_values(Redis::lrange($key, 1, -1));
			$data['data']['goods'] = array();
			foreach($ids as $id=>$count){
				$menu = Menu::find($id);
				$value = $menu->price * $count;
				$data['data']['all_value'] += $value;

				array_push($data['data']['goods'], array(
					'good_name' => $menu->title,
					'good_value' => $value,
					'good_count' => $count
				));
			}
			return Response::json($data);
		}else{
			return array(
				'success' => 'false',
				'state' => 200,
				'errMsg' => '',
				'no' => 0,
				'data' => array()
			);
		}
	}
}