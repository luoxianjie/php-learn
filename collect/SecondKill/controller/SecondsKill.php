<?php
namespace controller;
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/7/11
// | Time  : 10:42
// +----------------------------------------------------------------------
use mini\Controller;
use model\User;

/**
 * 秒杀思路：客户端->服务器->redis->数据库 逐层限制请求
 *
 * 客户端：   避免重复提交，秒杀未开始与提交过一次后按钮置灰，禁止提交           可以过滤普通用户由于网络堵塞重复提交的请求    5W  -> 3W
 * 服务器端： 过滤掉秒杀未开始和15秒内重复的请求                                 可以过滤程序员通过for循环提交的请求           3W  -> 1W
 * redis：    库存不足时生成缓存文件,所有请求直接返回缓存页面提示库存不足        可以过滤掉库存不足后发起的请求                1W  -> 150
 * 数据库：   将成功下单的用户数据写入数据库                                                                                   150 -> 100
 *
 *
 * Class SecondsKill
 * @package controller
 */
class SecondsKill extends Controller
{
    public function index()
    {
        if(session('?uid')){
            $this->display('kill');
        }else{
            $this->error('你还未登录!','/secondskill/login');
        }
    }

    public function kill()
    {
        if(!session('?uid')){
            $this->error('你还未登录!','/secondskill/login');
        }

        if(time()<strtotime('2018-7-11 17:00:00')){
            $this->error('时候未到!');
        }

        $last_request_time = session('last_request_time');
        if(!empty($last_request_time)){
            $request_time = time();
            if(($request_time - $last_request_time) < 15){
                $this->error('15秒后再请求!');
            }
        }

        $_SESSION['last_request_time'] = time();
        $cache_file = 'no_inventory.html';
        if(is_file($cache_file)){
            require $cache_file;
            die;
        }
        $id = I('id',0,'intval');
        $redis = new \Redis();
        $redis->connect('192.168.234.130', 6379);
        $num = $redis->hGet('inventory',$id);
        if($num < 1){
            file_put_contents('<h2>手慢了!商品库存不足</h2>',$cache_file);
            $this->error('库存不足，还剩'.$num.'件');
        }
        $redis->hIncrBy('inventory',$id,-1);
        $this->success('抢购成功!还剩'.($num-1).'件');
    }

    public function login()
    {
        if(is_get()){
            $this->display();
            exit;
        }
        $username = I('username','','trim');
        $password = I('password','','md5');

        $user = new User();

        $userInfo = $user->where(['nackname'=>$username,'passwd'=>$password])->find();
        if($userInfo){
            $_SESSION['uid'] = $userInfo['id'];
            $_SESSION['nickname'] = $username;
            $this->success('登录成功!','/secondskill/index');
        }else{
            $this->error('登录失败!');
        }
    }

    public function logout()
    {
        unset($_SESSION['uid']);
        session_destroy();
        redirect('/secondskill/login');
    }


}