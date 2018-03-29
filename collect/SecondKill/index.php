<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/29
// | Time  : 17:59
// +----------------------------------------------------------------------

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

    }

    public function __call($name, $arguments)
    {
        echo "调用不存在的方法!{$name}";
    }
}

$action = isset($_GET['act'])?trim($_GET['act']):'buy';

$data = (new SecondKill())->$action();