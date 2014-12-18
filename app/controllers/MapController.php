<?php
/*
**Author:tianling
**createTime:14-12-14 上午12:51
**地图模块
*/
class MapController extends BaseController{

    /**
     * 根据前端的商铺坐标信息获取商铺数据
     **/
    public function shopsGet(){
        $locations = Input::get('restaurant');
//        $locations = json_decode($locations);

        if(empty($locations) || !is_array($locations)){
            echo json_encode(array(
                'success'=>false,
                'state'=>200,
                'errMsg'=>'坐标数据为空',
                'data'=>''
            ));

            exit;
        }

        $shopData = array();
        $geoHash = new Geohash();
        foreach($locations as $key=>$value){
           array_push($shopData, $geoHash->getAmount($value['1'], $value['0']));
        }
     echo json_encode(array(
           'success'=>true,
           'state'=>200,
           'errMsg'=>'',
           'data'=>$shopData,
       ));
    }


}