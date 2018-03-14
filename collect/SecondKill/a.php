<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/8
// | Time  : 18:38
// +----------------------------------------------------------------------


class Test
{
    public function get_url()
    {
        $start_time = mktime(18,30,0,3,8,2018);
        $end_time = mktime(19,0,0,3,9,2018);
        if(time() < $start_time || time() > $end_time){
            echo json_encode(['status'=>0,'msg'=>'wait']);
        }
        echo json_encode(['status'=>'200','url'=>'a.php?act=buy']);
    }

    public function buy()
    {
        echo rand(10,20);
    }

    public function __call($name, $arguments)
    {
        echo "调用不存在的方法!{$name}";
    }
}

$test = new Test();

$action = trim($_GET['act']);

$data = $test->$action();