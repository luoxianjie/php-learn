<?php

// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/29
// | Time  : 17:59
// +----------------------------------------------------------------------
require './Db.php';


class SecondKill
{
    public function getUrl()
    {
        $start_time = mktime(18,30,0,3,8,2018);
        $end_time = mktime(19,0,0,3,9,2018);
        if(time() < $start_time || time() > $end_time){
            echo json_encode(['status'=>0,'msg'=>'wait']);
        }
        echo json_encode(['status'=>'200','url'=>'index.php?act=buy']);
    }

    public function buy()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $key = 'goods:1';

        $store = $redis->get($key);

        if($store>0) {

            // 随机取出一个会员id
            $userId = $redis->sPop('userId');

            // 将会员id压入购买者队列
            $redis->lPush('buyerId', $userId);

            // 商品库存自减
            $redis->decr($key);

        }
    }

    public function __call($name, $arguments)
    {
        echo "调用不存在的方法!{$name}";
    }
}

$action = isset($_GET['act'])?trim($_GET['act']):'buy';
$data = (new SecondKill())->$action();



/*// 从数据库取出所有用户
$db = Db::getInstance();

$users = $db->table('user')->select();

$redis = new Redis();
$redis->connect('127.0.0.1',6379);
$key = 'goods:1';

foreach ($users as $user) {
    $redis->sAdd('userId',$user['id']);
}

// 设置商品库存为20
$redis->set($key,20);*/

