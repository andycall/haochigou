# 忘记密码文档


### 忘记密码有2个widget
+ find_password
+ find_password_change 



> find_password 为忘记密码主要的页面
> find_password_change 为用户通过收到的邮件内的链接跳转到得表单

### 前端测试的路由 `已同步到测试机上面`


find_password -> /find_password
find_password_change -> /find_password_change 

`关于页面的渲染， 建议直接去看routes.php`

# find_password

> 用户在登录页面点击`忘记密码了？`跳转到这个页面

主要分2块



#### 验证邮箱

`方式` : POST
    
    input : {
         register_email : "" // 用户注册的邮箱地址
         auth_code   : ""    // 图片验证码
    }


    output : {
        success : true, 
        state :  200, // 状态码
        errTarget : "register_email" // 如果用户哪里填错了， 就把填错值的name字段返回来。 如果有多个， 则用 , 分隔
    }
    
    发送成功之后， 后台会向用户注册的邮箱发送一封邮件。邮件内容中包含一个加密过的地址。 
    用户点击那个地址，可以直接跳转修改密码的页面，也就是 `find_password_change`.
    

#### 验证手机号码

`方式` : 一般表单后台跳转
    
    input : {
        register_phone : "" // 用户注册的手机号码
        auth_sms       : "" // 发送的手机验证码
        new_psw        : "" // 新密码
        repeat_psw     : "" // 重复密码
    }
    

    
表单提交成功之后直接跳到主页就行了


#### find_password_change 的表单

`方式` : 一般表单后台跳转

    input : {
        new_psw        : "" // 新密码
        repeat_psw     : "" // 重复密码
        auth_code      : "" // 图片验证码
    }

同样跳转到主页






 

   



