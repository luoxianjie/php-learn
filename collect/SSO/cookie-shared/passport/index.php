<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/9
// | Time  : 18:35
// +----------------------------------------------------------------------
ini_set("session.save_handler", "memcache");
ini_set("session.save_path", "tcp://127.0.0.1:11211");
session_start();
header('Set-Cookie:PHPSESSID='. session_id() .'; domain=.lxj.com');
class Passport
{

    public function login()
    {
        $_SESSION['user'] = 'lxj';
        $url = urldecode($_GET['server']);
        header('Location:' . $url);
    }


}

(new Passport())->login();