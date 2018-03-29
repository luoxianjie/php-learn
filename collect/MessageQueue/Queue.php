<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/3/28
// | Time  : 14:56
// +----------------------------------------------------------------------

interface Queue
{
    /**
     * 出队列
     * @return mixed
     */
    public function pop();

    /**
     * 入队列
     * @return mixed
     */
    public function push();
}