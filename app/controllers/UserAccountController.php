<?php
/*
**Author:tianling
**createTime:14-12-4 上午12:03
*/
class UserAccountController extends BaseController{
    public $uid;

    public function userSite(){
        $this->uid = Auth::user()->front_uid;
    }
}