<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/4/2
// | Time  : 17:16
// +----------------------------------------------------------------------
session_start();
class Test
{

    /**
     * 显示登录表单
     */
    public function login()
    {
        if(isset($_SESSION['user'])){
            $this->jump('您已登录','/index.php?action=transfer');
        }
        require "login.html";
        die;
    }

    /**
     * 执行登陆操作
     */
    public function doLogin()
    {
        $account = $_POST['account'];
        $password = $_POST['password'];

        if($account == 'jack' && $password == '123456'){
            $_SESSION['user'] = [
                'name'  => 'jack',
                'avatar'=> 'avatar.jpg'
            ];
            $this->jump('登陆成功!','/index.php?action=transfer');
        }

        $this->jump('信息有误!','/index.php?action=login');;
    }

    /**
     * 显示转账表单
     */
    public function transfer()
    {
        if(!isset($_SESSION['user'])){
            $this->jump('请先登录!','/index.php?action=login');
        }
        require "transfer.html";
        die;
    }

    /**
     * 执行转账操作
     */
    public function doTransfer()
    {
        $toUser = isset($_POST['toUser'])?trim($_POST['toUser']):null;
        $money = isset($_POST['money'])?trim($_POST['money']):null;

        if($toUser && $money){
            touch('a.php');
            die('转账成功');
        }

        die('转账失败');

    }

    private function jump($msg, $url)
    {
        ob_clean();
        echo "<a href='{$url}'>{$msg}</a><span id='time' >3</span>秒后跳转。";
        echo "<script type='text/javascript'> var time = document.getElementById('time'); setInterval(function(){ time.innerHTML = parseInt(time.innerHTML) -1; if(time.innerHTML<1){ location.href='{$url}'}; },1000);</script>";
        die;
    }

}

$action = isset($_GET['action'])?trim($_GET['action']):'transfer';

(new Test())->$action();