<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/1/9
// | Time  : 18:35
// +----------------------------------------------------------------------
require '../Db.php';
session_start();
class Passport
{

    /**
     *  单点登录
     */
    public function login()
    {
        if(isset($_POST['submit'])) {
            $account = isset($_POST['account'])?trim($_POST['account']):null;
            $password = isset($_POST['password'])?trim($_POST['password']):null;
            $server = isset($_POST['server'])?trim($_POST['server']):null;

            if(!$account || !$password){
                $_SESSION['error'] = '账号或密码为空';
                require 'sso.html';
                die;
            }

            if(!$server){
                $_SESSION['error'] = '非法操作';
                require 'sso.html';
                die;
            }

            $db = Db::getInstance();
            $user = $db->table('user')->where(['account'=>$account, 'password'=>md5($password)])->find();
            if(empty($user)){
                $_SESSION['error'] = '账号或密码有误';
                require 'sso.html';
                die;
            }

            unset($_SESSION['error']);
            header("location:".$server."?action=login&ticket=".$user['ticket']);

        }else{
            $server = isset($_GET['server'])?trim($_GET['server']):die('来源不明');
            require 'sso.html';
        }
    }

    /**
     *  退出登录
     */
    public function logout()
    {
        $url1 = 'http://a.com/index.php?action=logout&server='.$_GET['server'];
        header('Location:'.$url1);
    }

    /**
     *  验证ticket有效性
     */
    public function verify()
    {
        $ticket = trim($_GET['ticket']);

        //验证ticket有效性
        if($ticket){
            $db = Db::getInstance();
            $user = $db->table('user')->where(['ticket'=>$ticket])->find();
            if($user){
                echo 'success';
            }else{
                echo "fail";
            }
            die;
        }
        echo 'fail';
        die;
    }

    /**
     * 通过ticket获取用户信息
     */
    public function user()
    {
        $ticket = trim($_GET['ticket']);
        $db = Db::getInstance();
        $user = $db->table('user')->where(['ticket'=>$ticket])->find();

        echo json_encode($user);
    }

}

$action = isset($_GET['action'])?trim($_GET['action']):'login';

(new Passport())->$action();

