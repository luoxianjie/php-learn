<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/26
// | Time  : 11:46
// +----------------------------------------------------------------------


class A
{
    public function deal()
    {
        $data = $_POST;
        /**
         * 表单处理
         */

        // 成功返回
        echo json_encode(['status'=>'200','remote_addr'=>$_SERVER['REMOTE_ADDR']]);
    }
}

(new A())->deal();