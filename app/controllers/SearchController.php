<?php
/*
**Author:tianling
**createTime:14-12-17 下午5:43
**搜索控制器
*/

class SearchController extends BaseController{

    /*
     * 首页搜索接口
     **/
    public function mainSearch(){
        $string = Input::get('string');

        $shopData = Shop::where('intro','like','%'.$string.'%')->orWhere('name','like','%'.$string.'%')->orWhere('address','like','%'.$string.'%')->get();

        $key = 0;

       foreach($shopData as $value){
            $shopData[$key] = array(
                'shop_name'=>$value->name,
                'shop_id'=>$value->id,
                'shop_url'=>url('shop'.'/'.$value->id),
                'img_url'=>$value->pic
            );

           $key++;
       }

       echo json_encode(array(
           'success'=>true,
           'state'=>200,
           'nextSrc'=>'',
           'errMsg'=>'',
           'no'=>'',
           'data'=>$shopData
       ));
    }
}