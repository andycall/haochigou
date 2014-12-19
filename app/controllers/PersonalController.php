<?php

/**
 * 主页
 *
 * addOrder()		添加订单
 * cancelMenu()		取消收藏某个商品
 * cancelShop()		取消收藏某个商家
 * collectMenu()	收藏某个商品
 * collectShop()	收藏某个商家
 * confirmOrder()	确认收货
 * getOrderInfo()	获取一个订单的详情
 * modifyOrder()	修改订单状态：0表示已提交未付款，1表示已付款未收货，2表示已收获，3表示取消订单，订单无删除操作
 */
class PersonalController extends BaseController {

	/**
	 * 添加订单
	 * 请求类型：POST
	 */
	public function addOrder(){
		$user  = Auth::user();
		
		$rules = array(
			'shop_id'         => 'required | integer | exists:shop,id',
			'front_user_id'   => 'required | integer | exists:front_user,front_uid',
			'total'           => 'required | integer | between:1,20',				// 订购总量
			'order_menus'     => 'required | max:255',								// 菜单id，逗号分隔
			'total_pay'       => 'required | numeric',			  					// 总价
			'dispatch'        => 'numeric',											// 配送费
			'beta'            => 'max:255',											// 备注信息
			'receive_address' => 'required | max:255'											// 收获地址
		);
		$record = array(
			'shop_id'         => Input::get('shop_id'),
			'front_user_id'   => $user->front_uid,
			'ordertime'		  => time(),
			'total'           => Input::get('total'),
			'order_menus'     => Input::get('order_menus'),
			'total_pay'       => Input::get('total_pay'),
			'dispatch'        => (Input::get('dispatch') == NULL)?0:Input::get('dispatch'),
			'beta'            => Input::get('beta'),
			'receive_address' => Input::get('receive_address')
		);
		$v = Validator::make($record, $rules);
		if( $v->fails() ){
			$message         = $v->messages();	
			return json_encode(array(
				'success' => false,
				'state'   => 400,
				'errMsg'  => $message->toArray(),
				'no'      => 1
			));
		}

		$order = new Order($record);
		if( $order->save() ){
			return json_encode(array(
				'success' => true,
				'state'   => 200,
				'errMsg'  => 'finished',
				'no'      => 0
			));
		}
	}

    /**
     * 取消收藏商品
     *
     * 请求类型：POST
     */
    public function cancelMenu(){
		$user = Auth::user();
		$rules = array(
			'uid'      => 'required | integer',
			'goods_id' => 'required | integer'
		);
		$new_collect = array(
			'uid'      => $user->front_uid,
			'goods_id' => Input::get('goods_id')
		);
		$v = Validator::make($new_collect, $rules);
		if( $v->fails() ){
			return Redirect::to('http://baidu.com');

			return Redirect::to('error')
				->with('user', Auth::user())
				->withErrors($v)
				->withInput();
		}

		if( CollectMenu::where('menu_id', Input::get('goods_id'))->where('user_id', $user->front_uid)->delete() ){
			$output            = array();
			$output['success'] = 'true';
			$output['state']   = 200;
			$output['nextSrc'] = '';
			$output['errMsg']  = '';
			$output['no']      = 0;
			Response::json($output);
		}
    }



	/**
	 * 收藏某个商品
	 * @return [type] [description]
	 */
	public function collectMenu(){
		$user = Auth::user();
		$rules = array(
			'menu_id' => 'required | integer | exists:menu,id'
		);
		$record = array(
			'user_id' => $user->front_uid,
			'menu_id' => Input::get('menu_id'),
			'uptime'  => time()
		);
		$v = Validator::make($record, $rules);
		if( $v->fails() ){
			$message         = $v->messages();	
			return json_encode(array(
				'success' => false,
				'state'   => 400,
				'errMsg'  => $message->toArray(),
				'no'      => 1
			));
		}

		$collect = new CollectMenu($record);
		if( $collect->save() ){
			return json_encode(array(
				'success' => true,
				'state'   => 200,
				'errMsg'  => 'finished',
				'no'      => 0
			));
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
			$output            = array();
			$output['success'] = 'true';
			$output['state']   = 200;
			$output['nextSrc'] = '';
			$output['errMsg']  = '';
			$output['no']      = 0;
			$output['data']    = $this->getShopInfo(Input::get('shop_id'));
			//var_dump($output);
			Response::json($output);
		}
	}

	/**
	 * 确认收货
	 *
	 * 请求类型：POST
	 */
	public function confirmOrder(){
		$user  = Auth::user();
		
		$rules = array(
			'order_id' => 'required | integer | exists:order,id'
		);
		$record = array(
			'order_id' => Input::get('order_id'),
		);
		$v = Validator::make($record, $rules);
		if( $v->fails() ){
			$message         = $v->messages();	
			return json_encode(array(
				'success' => false,
				'state'   => 400,
				'errMsg'  => $message->toArray(),
				'no'      => 1
			));
		}

		if( Order::where('id', $record['order_id'])->update(array('state' => 2)) ){
			return json_encode(array(
				'success' => true,
				'state'   => 200,
				'errMsg'  => 'finished',
				'no'      => 0
			));
		}
	}

	/**
	 * 修改订单状态
	 * 请求类型：POST
	 */
	public function modifyOrder(){		
		$record = array(
			'order_id'      => Input::get('order_id'),
			'state' => Input::get('state')
		);
		$rules = array(
			'order_id'      => 'required | integer | exists:order,id',
			'state' => 'required | integer | between:0,5'
		);
		$v = Validator::make($record, $rules);
		if( $v->fails() ){
			$message         = $v->messages();	
			return json_encode(array(
				'success' => false,
				'state'   => 400,
				'errMsg'  => $message->toArray(),
				'no'      => 1
			));
		}

		if( Order::where('id', $record['order_id'])->update(array('state' => $record['state'])) ){
			return json_encode(array(
				'success' => true,
				'state'   => 200,
				'errMsg'  => 'finished',
				'no'      => 0
			));
		}else{
			return json_encode(array(
				'success' => false,
				'state' => 400,
				'errMsg' => 'cuowu',
				'no' => 1
			));
		}
	}
}