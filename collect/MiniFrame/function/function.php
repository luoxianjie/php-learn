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

function session($var)
{
    if(strpos($var,'?') === 0){
        return isset($_SESSION[substr($var,1)]);
    }

    return $_SESSION[$var];
}

function is_post()
{
    return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST';
}

function is_get()
{
    return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='GET';
}

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])=='XMLHTTPREQUEST';
}

function I($var,$default='',$filter = 'htmlspecialchars')
{
    $var = $filter($_REQUEST[$var]);
    if(empty($var)){
        return $default;
    }
    return $var;
}

function redirect($url)
{
    echo "<script type='text/javascript'> location.href='".$url."'; </script>";
    return ;
}