<?php

/*
 **用户个人认证模块
 */

use Gregwar\Captcha\CaptchaBuilder;

class UserAccessController extends BaseController{

    //注册接口
    public function register(){
        if(Auth::check()){
            echo json_encode(array(
                'succcess'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'您已登录'
                ),
                'no'=>2
            ));

            exit;
        }

        $mobile = Input::get('user_phone');
        $email = Input::get('user_email');

        $auth = Input::get('user_auth');
        if(!$this->MessageCheck($auth,$mobile)){
            echo json_encode(array(
                'success'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'验证码验证失败!'
                )
            ));

            exit();
        }



        //账号重复性检测
        if(is_object($this->accountCheck($mobile)) || is_object($this->accountCheck($email))){
            echo json_encode(array(
                'success'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'该手机号或邮箱已经被注册!'
                )
            ));

            exit();
        }


        $password = Input::get('user_psw');
        //对密码进行hash加密
        $password = Hash::make($password);

        $user = new User;
        $user->password = $password;
        $user->last_login_time = time();
        $user->last_login_ip = $this->getIP();
        $user->lock = 0;
        $user->user_type = 'front';
        $user->add_time = time();


        if($user->save()){
            $uid = $user->uid;
        }else{
            echo json_encode(array(
                'success'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'user base Error!'
                )
            ));

            exit;
        }

        $frontUser = new FrontUser;
        $frontUser->uid = $uid;
        $frontUser->email = $email;
        $frontUser->mobile = $mobile;
        $frontUser->email_passed = 1;
        $frontUser->mobile_passed = 1;
        $frontUser->integral = 0;
        $frontUser->balance = 0;


        if($frontUser->save()){

            Auth::login($frontUser);//用户登录

            echo json_encode(array(
                'success'=>true,
                'state'=>200,

            ));
        }

    }


    //登录接口
    public function login(){

        if(Auth::check()){
            echo json_encode(array(
                'succcess'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'该用户已登录，请不要重复登录'
                ),
                'no'=>2
            ));
            exit;
        }

        $account = Input::get('user_email');
        $password = Input::get('user_psw');

        $rememberMe = Input::get('user_remember');

        $captcha = Input::get('user_auth');
        $ip = $this->getIP();
        $codeKey = md5($ip);
        $captchaCode = Cache::tags('register','code')->get($codeKey);

        if($captcha != $captchaCode){
            echo json_encode(array(
                'success'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'验证码验证失败'
                ),
                'no'=>1
            ));

            exit();
        }

        $accountCheck = $this->accountCheck($account);
        if(!is_object($accountCheck)){
            echo json_encode(array(
                'success'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'用户不存在'
                ),
                'no'=>1
            ));
            exit();
        }

        $passwordCheck = Hash::check($password,$accountCheck->user->password);

        if($passwordCheck){
            if($rememberMe == 'true'){
                Auth::login($accountCheck,true);
            }else{
                Auth::login($accountCheck);
            }


        }else{
            echo json_encode(array(
                'succcess'=>false,
                'state'=>200,
                'errMsg'=>array(
                    'inputMsg'=>'密码验证失败'
                ),
                'no'=>2
            ));
        }

       echo json_encode(array(
            'success'=>true,
            'state'=>200,
            'nextSrc'=>url('usercenter'),
       ));
    }

    /*
     * 生成图片验证码
     **/
    public function CaptchaMake(){
        $code = (string)rand(1000,9999);

        $builder = new CaptchaBuilder($code);
        $builder->build();

        $phrase = $builder->getPhrase();
        $ip = $this->getIP();
        $codeKey = md5($ip);
        Cache::tags('register','code')->put($codeKey,$phrase,1);

        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
        exit;
    }


    /*
     * 修改图片验证码
     **/
    public function CaptchaChange(){
        $ip = $this->getIP();
        $codeKey = md5($ip);

        $data = array(
            //'code'=>Cache::tags('register','code')->get($codeKey),
            'success'=>true,
            'nextSrc'=>url('captcha'),
            'errMsg'=>''
        );

        echo json_encode($data);
    }


    //退出接口
    public function logout(){
        Auth::logout();

        return Redirect::to('/');
    }

    /**
     * 相消息队列推送发短信job
     */
    public function sendMessage(){

        $mobile = Input::get('telNumber');

        $tpl_id = 1;
        $code = rand(100000,999999);
        $tpl_value = '#code#='.$code.'&#company#=好吃go';

        $status = Queue::push('QueueSendMessage@send', array('mobile' => $mobile,'tpl_id'=>$tpl_id,'tpl_value'=>$tpl_value));

        $codeKey = md5($this->getIP().$mobile);
        Cache::tags('register','code')->put($codeKey,$code,1);

        $key = Cache::tags('register','code')->get($codeKey);

        echo json_encode(array(
            'success'=>true,
            'nextSrc'=>'',
            'errMsg'=>''
        ));


    }


    private  function MessageCheck($mobileKey,$mobile){

        $codeKey = md5($this->getIP().$mobile);

        $key = Cache::tags('register','code')->get($codeKey);

        if($key == $mobileKey){
           return true;
        }else{
            return false;
        }


    }



    //账号查询,支持邮箱和手机
    private function accountCheck($account){

        $accountData = FrontUser::where('email' ,'=', $account)->orWhere('mobile','=',$account)->first();

        if(!$accountData){
            return 400;//若账户不存在，返回错误码400
        }else{
            return $accountData;
        }
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


?>