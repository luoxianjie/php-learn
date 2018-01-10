<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/9
// | Time  : 18:04
// +----------------------------------------------------------------------
session_start();
header('Set-Cookie:PHPSESSID='. session_id() .'; domain=.lxj.com');
class B
{
    public function login()
    {
        $user = $_SESSION['user'];
        if(!empty($user)){
            $this->index();
        }else{
            echo "您还没有登录跳转到验证服务器。。。";
            sleep(3);
            $server = 'http://passport.lxj.com/?server=http://b.lxj.com/';
            header("Location: $server" );
        }

    }

    public function index()
    {
        echo "B登陆成功";
    }


}

(new B())->login();
