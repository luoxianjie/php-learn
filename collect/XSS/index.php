<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/4/3
// | Time  : 9:26
// +----------------------------------------------------------------------
require "Db.php";
require "../../function/Helper.php";

class Xss
{
    public function index()
    {
        require "index.html";
    }

    public function save()
    {
        $name = isset($_POST['name'])?trim($_POST['name'],ENT_QUOTES):null;
        $password = isset($_POST['password'])?trim($_POST['password']):null;
        $repassword = isset($_POST['repassword'])?trim($_POST['repassword']):null;

        if(!$name||!$password||!$repassword){
            Helper::jump('index.php?action=index','信息不全');
        }

        if($password != $repassword){
            Helper::jump('index.php?action=index','两次密码不一致');
        }

        $db = Db::getInstance();

        $res = $db->table('test_user')->insert([
            'name'      => $name,
            'password'  => $password
        ]);

        if($res){
            Helper::jump('index.php?action=detail','添加成功');
        }
        die($db->getLastSql());
        Helper::jump('index.php?action=index','添加失败');
    }

    public function detail()
    {
        $db = Db::getInstance();
        $res = $db->table('test_user')->where(['id'=>3])->find();
        header("Content-type:text/html;charset=utf-8");
        echo $res['name'];
    }

}

$action = isset($_GET['action'])?trim($_GET['action']):'index';
(new Xss())->$action();