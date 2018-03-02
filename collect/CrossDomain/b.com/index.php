<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/27
// | Time  : 9:36
// +----------------------------------------------------------------------


class Test
{
    public function index()
    {
        echo 'index';
    }

    public function hello()
    {
        echo json_encode(['action'=>__METHOD__]);
    }

}

header('Access-Control-Allow-Origin:*');

$class = new Test();

$action = @$_REQUEST['a']?$_REQUEST['a']:'index';

$class->$action();

