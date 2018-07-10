<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/6/22
// | Time  : 10:27
// +----------------------------------------------------------------------
function dd(...$args)
{
    dump(...$args);
    die;
}

function dump(...$args)
{
    foreach ($args as $arg){
        var_dump($arg);
    }
}